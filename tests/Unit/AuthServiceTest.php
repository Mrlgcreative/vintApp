<?php

namespace Tests\Unit;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AuthService::class);
    }

    /** @test */
    public function it_creates_a_new_firebase_user()
    {
        $user = $this->service->findOrCreateFirebaseUser('uid-1', 'john@example.com', 'John', 'http://avatar.png');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'john@example.com',
            'firebase_uid' => 'uid-1',
            'avatar' => 'http://avatar.png',
        ]);
        $this->assertNull($user->email_verified_at);
        $this->assertNotSame('', $user->password);
        $this->assertFalse(Hash::needsRehash($user->password));
    }

    /** @test */
    public function it_updates_an_existing_firebase_user_without_touching_email_verification()
    {
        $existing = User::factory()->create([
            'email' => 'john@example.com',
            'firebase_uid' => 'uid-1',
            'email_verified_at' => now(),
        ]);

        $user = $this->service->findOrCreateFirebaseUser('uid-1', 'john@example.com', 'John Doe', 'http://new.png');

        $this->assertSame($existing->id, $user->id);
        $this->assertSame('John Doe', $user->name);
        $this->assertSame('http://new.png', $user->avatar);
        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function it_finds_a_user_by_firebase_uid_even_with_a_different_email()
    {
        User::factory()->create([
            'email' => 'old@example.com',
            'firebase_uid' => 'uid-1',
        ]);

        $user = $this->service->findOrCreateFirebaseUser('uid-1', 'new@example.com', 'John', null);

        $this->assertSame('old@example.com', $user->email);
        $this->assertSame('uid-1', $user->firebase_uid);
    }

    /** @test */
    public function it_merges_create_extra_attributes()
    {
        $user = $this->service->findOrCreateFirebaseUser('uid-1', 'john@example.com', 'John', null, [
            'phone' => '123456',
            'newsletter_subscribed' => true,
        ]);

        $this->assertSame('123456', $user->phone);
        $this->assertTrue((bool) $user->newsletter_subscribed);
    }

    /** @test */
    public function it_returns_the_user_payload()
    {
        $user = User::factory()->create(['name' => 'John', 'avatar' => 'http://a.png']);

        $payload = $this->service->userPayload($user);

        $this->assertSame($user->id, $payload['id']);
        $this->assertSame('John', $payload['name']);
        $this->assertSame($user->email, $payload['email']);
        $this->assertSame('http://a.png', $payload['avatar']);
    }

    /** @test */
    public function it_saves_the_fcm_token()
    {
        $user = User::factory()->create();

        $this->service->saveFcmToken($user, 'fcm-abc');

        $this->assertSame('fcm-abc', $user->fresh()->fcm_token);
    }

    /** @test */
    public function it_sends_a_verification_code_by_email()
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->service->sendVerificationCode($user);

        Mail::assertSent(VerificationCodeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
        $this->assertNotNull($user->fresh()->verification_code);
    }

    /** @test */
    public function it_logs_in_the_user_and_regenerates_the_session()
    {
        $user = User::factory()->create();
        $request = Request::create('/');

        $this->service->loginUser($user, $request);

        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    /** @test */
    public function it_finalizes_a_login_without_two_factor()
    {
        $user = User::factory()->create(['google2fa_enabled' => false]);

        $response = $this->service->completeFirebaseLogin($user);

        $this->assertTrue($response['success']);
        $this->assertSame('Connexion réussie', $response['message']);
        $this->assertSame(route('home'), $response['redirect']);
        $this->assertTrue(session('2fa_verified'));
        $this->assertNull(session('2fa_required'));
    }

    /** @test */
    public function it_requires_two_factor_when_enabled()
    {
        $user = User::factory()->create(['google2fa_enabled' => true]);

        $response = $this->service->completeFirebaseLogin($user);

        $this->assertTrue($response['success']);
        $this->assertSame(route('two-factor.challenge'), $response['redirect']);
        $this->assertTrue(session('2fa_required'));
        $this->assertSame($user->id, session('2fa_user_id'));
    }

    /** @test */
    public function it_logs_out_and_clears_the_fcm_token()
    {
        $user = User::factory()->create(['fcm_token' => 'fcm-abc']);
        Auth::login($user);
        $request = Request::create('/');

        $this->service->logout($request, true);

        $this->assertFalse(Auth::check());
        $this->assertNull($user->fresh()->fcm_token);
    }
}

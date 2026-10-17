<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Services\ItemAiVerificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ItemAiVerificationServiceTest extends TestCase
{
    /** Crée une vraie image JPEG sur le disque public factice et renvoie son chemin. */
    private function fakeImage(string $path = 'items/test.jpg'): string
    {
        $im = imagecreatetruecolor(300, 300);
        imagefill($im, 0, 0, imagecolorallocate($im, 20, 120, 200));
        ob_start();
        imagejpeg($im);
        Storage::disk('public')->put($path, ob_get_clean());

        return $path;
    }

    /** @test */
    public function it_parses_a_valid_ai_verdict_and_sends_the_vision_model_with_images()
    {
        Storage::fake('public');
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => json_encode([
                        'score' => 82,
                        'recommendation' => 'approve',
                        'real_product_photos' => true,
                        'photo_matches_description' => true,
                        'issues' => ['Photo légèrement floue'],
                        'summary' => 'Annonce conforme.',
                    ])],
                ]],
            ]),
        ]);

        $item = new Item([
            'name' => 'Sac à main',
            'description' => 'Sac en cuir, très bon état.',
            'images' => [$this->fakeImage()],
        ]);

        $verdict = app(ItemAiVerificationService::class)->verify($item);

        $this->assertSame(82, $verdict['score']);
        $this->assertSame('approve', $verdict['recommendation']);
        $this->assertTrue($verdict['real_product_photos']);
        $this->assertSame(['Photo légèrement floue'], $verdict['issues']);
        $this->assertSame(1, $verdict['photos_count']);

        Http::assertSent(function ($request) {
            $data = $request->data();
            $text = $data['messages'][0]['content'][0]['text'] ?? '';
            $image = $data['messages'][0]['content'][1]['image_url']['url'] ?? '';

            return $data['model'] === config('services.xkiro.vision_model')
                && ($data['response_format']['type'] ?? null) === 'json_object'
                && str_starts_with($image, 'data:image/jpeg;base64,')
                && str_contains($text, 'Sac à main');
        });
    }

    /** @test */
    public function it_handles_fenced_json_and_clamps_invalid_values()
    {
        Storage::fake('public');
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => "```json\n" . json_encode([
                        'score' => 150,
                        'recommendation' => 'inconnu',
                        'real_product_photos' => 'true',
                        'photo_matches_description' => false,
                        'issues' => 'pas un tableau',
                        'summary' => 'ok',
                    ]) . "\n```"],
                ]],
            ]),
        ]);

        $item = new Item([
            'name' => 'X',
            'description' => 'Y',
            'images' => [$this->fakeImage('items/test2.jpg')],
        ]);

        $verdict = app(ItemAiVerificationService::class)->verify($item);

        $this->assertSame(100, $verdict['score']);                  // clampé à 100
        $this->assertSame('review', $verdict['recommendation']);    // valeur inconnue -> review
        $this->assertTrue($verdict['real_product_photos']);         // "true" -> true
        $this->assertFalse($verdict['photo_matches_description']);
        $this->assertSame([], $verdict['issues']);                  // non-tableau -> []
    }

    /** @test */
    public function it_throws_when_there_is_no_usable_photo()
    {
        Storage::fake('public');

        $item = new Item(['name' => 'Sans photo', 'description' => 'x', 'images' => []]);

        $this->expectException(RuntimeException::class);

        app(ItemAiVerificationService::class)->verify($item);
    }
}

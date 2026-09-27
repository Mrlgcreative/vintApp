<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Le rapport de revenus ne doit jamais additionner des montants de devises
 * différentes : la plateforme est bilingue USD / CDF et la devise est portée
 * par chaque transaction. Le rapport ventile donc par devise.
 */
class RevenueReportCurrencyTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin', 'slug' => 'admin']);
        $user->roles()->attach($role);

        return $user;
    }

    private function completedTransaction(string $currency, float $amount): Transaction
    {
        return $this->transaction($currency, $amount, 'completed');
    }

    private function transaction(string $currency, float $amount, string $status): Transaction
    {
        $user = $this->admin();

        return Transaction::create([
            'user_id' => $user->id,
            'buyer_id' => $user->id,
            'provider' => 'test',
            'purpose' => 'payment',
            'transaction_id' => (string) Str::uuid(),
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'type' => 'deposit',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function revenueReport(): array
    {
        $method = new \ReflectionMethod(\App\Http\Controllers\Admin\AdminController::class, 'getRevenueReport');
        $method->setAccessible(true);

        return $method->invoke(
            app(\App\Http\Controllers\Admin\AdminController::class),
            now()->subDays(30)
        );
    }

    public function test_revenue_is_split_by_currency(): void
    {
        $this->completedTransaction('USD', 100.00);
        $this->completedTransaction('USD', 50.00);
        $this->completedTransaction('CDF', 20000.00);

        $report = $this->revenueReport();

        $this->assertSame(['USD' => 150.00, 'CDF' => 20000.00], array_map(
            fn (array $d) => $d['total'],
            $report['by_currency']
        ));

        // Chaque devise porte son symbole, plus de "$" unique.
        $this->assertSame('$', $report['by_currency']['USD']['symbol']);
        $this->assertSame('FC', $report['by_currency']['CDF']['symbol']);

        $this->assertFalse($report['single_currency']);
        $this->assertCount(2, $report['currencies']);
    }

    public function test_single_currency_period_is_flagged_as_such(): void
    {
        $this->completedTransaction('CDF', 20000.00);

        $report = $this->revenueReport();

        $this->assertTrue($report['single_currency']);
        $this->assertSame(['CDF'], $report['currencies']);
        $this->assertSame(20000.00, $report['by_currency']['CDF']['total']);
    }

    public function test_legacy_totals_are_preserved_for_the_api(): void
    {
        $this->completedTransaction('USD', 100.00);
        $this->completedTransaction('CDF', 20000.00);

        $report = $this->revenueReport();

        // Les clés historiques restent présentes et numériquement identiques
        // à un SUM/AVG sur l'ensemble, pour ne pas casser apiReports.
        $this->assertSame(20100.00, $report['total']);
        $this->assertSame(2, $report['count']);
        $this->assertSame(10050.00, $report['average']);
    }

    public function test_a_period_without_any_revenue_is_empty(): void
    {
        $report = $this->revenueReport();

        $this->assertSame([], $report['by_currency']);
        $this->assertSame([], $report['currencies']);
        $this->assertTrue($report['single_currency']);
        $this->assertSame(0.0, (float) $report['total']);
        $this->assertSame(0, $report['count']);
        $this->assertSame(0.0, (float) $report['average']);
    }

    public function test_non_completed_transactions_are_excluded(): void
    {
        $this->transaction('USD', 999.00, 'failed');

        $report = $this->revenueReport();

        $this->assertSame([], $report['by_currency']);
        $this->assertSame(0, $report['count']);
    }
}

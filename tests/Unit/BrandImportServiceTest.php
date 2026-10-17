<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Services\BrandImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class BrandImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): BrandImportService
    {
        return app(BrandImportService::class);
    }

    /** @test */
    public function it_imports_a_flat_json_list_of_names_as_active_brands()
    {
        $report = $this->service()->importJson(json_encode(['Nike', 'Adidas', 'Zara']));

        $this->assertSame(3, $report['created']);
        $this->assertSame(0, $report['updated']);
        $this->assertSame([], $report['errors']);
        $this->assertTrue(Brand::where('name', 'Nike')->first()->is_active);
    }

    /** @test */
    public function it_updates_an_existing_brand_instead_of_duplicating_it()
    {
        Brand::create(['name' => 'Nike', 'slug' => 'nike', 'description' => 'ancienne', 'is_active' => true]);

        $report = $this->service()->importJson(json_encode([
            ['name' => 'Nike', 'description' => 'nouvelle description', 'website' => 'https://nike.com'],
        ]));

        $this->assertSame(0, $report['created']);
        $this->assertSame(1, $report['updated']);
        $this->assertSame(1, Brand::where('slug', 'nike')->count());
        $this->assertSame('nouvelle description', Brand::where('slug', 'nike')->value('description'));
        $this->assertSame('https://nike.com', Brand::where('slug', 'nike')->value('website'));
    }

    /** @test */
    public function it_accepts_a_wrapped_container_and_optional_fields()
    {
        $report = $this->service()->importJson(json_encode([
            'brands' => [
                ['name' => 'Levi\'s', 'country' => 'USA', 'is_active' => false],
            ],
        ]));

        $this->assertSame(1, $report['created']);
        $brand = Brand::where('name', "Levi's")->first();
        $this->assertSame('USA', $brand->country);
        $this->assertFalse($brand->is_active);
    }

    /** @test */
    public function a_dry_run_counts_but_writes_nothing()
    {
        $report = $this->service()->importJson(json_encode(['Nike']), true);

        $this->assertTrue($report['dry_run']);
        $this->assertSame(1, $report['created']);
        $this->assertDatabaseMissing('brands', ['slug' => 'nike']);
    }

    /** @test */
    public function it_structures_pdf_text_with_the_ai_then_imports_it()
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'brands' => [
                        ['name' => 'Puma', 'description' => null, 'website' => null, 'country' => null],
                    ],
                ])]]],
            ]),
        ]);

        $report = $this->service()->importPdfText("Marques\nPuma");

        $this->assertSame(1, $report['created']);
        $this->assertDatabaseHas('brands', ['slug' => 'puma']);
    }

    /** @test */
    public function it_extracts_text_from_a_real_pdf_file_and_imports_it()
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'brands' => [
                        ['name' => 'Nike', 'description' => null, 'website' => null, 'country' => null],
                    ],
                ])]]],
            ]),
        ]);

        $path = tempnam(sys_get_temp_dir(), 'brands') . '.pdf';
        file_put_contents($path, \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Marques</h1><p>Nike</p>')->output());

        try {
            $report = $this->service()->importPdf($path);
        } finally {
            @unlink($path);
        }

        $this->assertSame(1, $report['created']);
        $this->assertDatabaseHas('brands', ['slug' => 'nike']);
    }

    /** @test */
    public function it_rejects_an_invalid_json_file()
    {
        $this->expectException(RuntimeException::class);

        $this->service()->importJson('{pas du json');
    }
}

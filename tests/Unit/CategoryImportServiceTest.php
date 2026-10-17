<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\CategoryImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class CategoryImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): CategoryImportService
    {
        return app(CategoryImportService::class);
    }

    /** @test */
    public function it_imports_a_flat_json_list_of_names_as_active_categories()
    {
        $report = $this->service()->importJson(json_encode(['Mode', 'Électronique', 'Maison']));

        $this->assertSame(3, $report['created']);
        $this->assertSame(0, $report['updated']);
        $this->assertSame([], $report['errors']);
        $this->assertTrue(Category::where('name', 'Mode')->first()->is_active);
    }

    /** @test */
    public function it_updates_an_existing_category_instead_of_duplicating_it()
    {
        Category::create(['name' => 'Mode', 'slug' => 'mode', 'description' => 'ancienne']);

        $report = $this->service()->importJson(json_encode([
            ['name' => 'Mode', 'description' => 'nouvelle description'],
        ]));

        $this->assertSame(0, $report['created']);
        $this->assertSame(1, $report['updated']);
        $this->assertSame(1, Category::where('slug', 'mode')->count());
        $this->assertSame('nouvelle description', Category::where('slug', 'mode')->value('description'));
    }

    /** @test */
    public function it_imports_nested_children_and_attaches_them_to_their_parent()
    {
        $report = $this->service()->importJson(json_encode([
            ['name' => 'Vêtements', 'children' => [
                ['name' => 'Homme'],
                ['name' => 'Femme'],
            ]],
        ]));

        $this->assertSame(3, $report['created']);
        $parentId = Category::where('name', 'Vêtements')->value('id');
        $this->assertNotNull($parentId);
        $this->assertSame($parentId, Category::where('name', 'Homme')->value('parent_id'));
        $this->assertSame($parentId, Category::where('name', 'Femme')->value('parent_id'));
    }

    /** @test */
    public function it_accepts_a_wrapped_container_and_creates_missing_parents()
    {
        $report = $this->service()->importJson(json_encode([
            'categories' => [
                ['name' => 'Smartphones', 'parent' => 'Téléphones'],
            ],
        ]));

        $this->assertSame(2, $report['created']);
        $parentId = Category::where('name', 'Téléphones')->value('id');
        $this->assertNotNull($parentId);
        $this->assertSame($parentId, Category::where('name', 'Smartphones')->value('parent_id'));
    }

    /** @test */
    public function a_dry_run_counts_but_writes_nothing()
    {
        $report = $this->service()->importJson(json_encode(['Mode']), true);

        $this->assertTrue($report['dry_run']);
        $this->assertSame(1, $report['created']);
        $this->assertDatabaseMissing('categories', ['slug' => 'mode']);
    }

    /** @test */
    public function it_structures_pdf_text_with_the_ai_then_imports_it()
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'categories' => [
                        ['name' => 'Informatique', 'parent' => null, 'description' => null],
                        ['name' => 'Écrans', 'parent' => 'Informatique', 'description' => null],
                    ],
                ])]]],
            ]),
        ]);

        $report = $this->service()->importPdfText("Informatique\n - Écrans");

        $this->assertSame(2, $report['created']);
        $this->assertSame(
            Category::where('name', 'Informatique')->value('id'),
            Category::where('name', 'Écrans')->value('parent_id')
        );
    }

    /** @test */
    public function it_extracts_text_from_a_real_pdf_file_and_imports_it()
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => json_encode([
                    'categories' => [
                        ['name' => 'Mobile', 'parent' => null, 'description' => null],
                    ],
                ])]]],
            ]),
        ]);

        $path = tempnam(sys_get_temp_dir(), 'cats') . '.pdf';
        file_put_contents($path, \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Catalogue</h1><p>Mobile</p>')->output());

        try {
            $report = $this->service()->importPdf($path);
        } finally {
            @unlink($path);
        }

        $this->assertSame(1, $report['created']);
        $this->assertDatabaseHas('categories', ['slug' => 'mobile']);
    }

    /** @test */
    public function it_rejects_an_invalid_json_file()
    {
        $this->expectException(RuntimeException::class);

        $this->service()->importJson('{pas du json');
    }
}

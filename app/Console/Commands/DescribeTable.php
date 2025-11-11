<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DescribeTable extends Command
{
    protected $signature = 'db:describe {table}';
    protected $description = 'Décrit la structure d\'une table';

    public function handle()
    {
        $tableName = $this->argument('table');
        
        $this->info("Structure de la table {$tableName}:");
        
        $columns = DB::select("DESCRIBE {$tableName}");
        foreach ($columns as $column) {
            $this->line("  {$column->Field} - {$column->Type} - Null: {$column->Null} - Default: {$column->Default}");
        }

        return 0;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListDatabaseTables extends Command
{
    protected $signature = 'db:list-tables';
    protected $description = 'Liste toutes les tables de la base de données';

    public function handle()
    {
        $this->info("Tables dans la base de données:");
        
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $this->line("  {$tableName}");
        }

        return 0;
    }
}
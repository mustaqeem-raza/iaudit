<?php

namespace Database\Seeders;

use App\Imports\IpmEfkIAuditImport;
use App\Models\IpmEfkIAudit;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class IpmEfkIAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/_IPM CSV EFKS (2025-07-16).csv');

        if (!file_exists($path)) {
            $this->command->warn("Missing data file: {$path}");
            return;
        }

        IpmEfkIAudit::truncate();

        Excel::import(new IpmEfkIAuditImport, $path);
        $this->command->info("IPM EFKS CSV import completed.");
    }
}

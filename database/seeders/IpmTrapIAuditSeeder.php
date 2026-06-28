<?php

namespace Database\Seeders;

use App\Imports\IpmTrapIAuditImport;
use App\Models\IpmTrapIAudit;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class IpmTrapIAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/_IPM CSV Traps  (2025-12-01).csv');

        if (!file_exists($path)) {
            $this->command->warn("Missing data file: {$path}");
            return;
        }

        IpmTrapIAudit::truncate();

        Excel::import(new IpmTrapIAuditImport, $path);
        $this->command->info("IPM Traps CSV import completed.");
    }
}

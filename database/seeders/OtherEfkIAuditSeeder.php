<?php

namespace Database\Seeders;

use App\Imports\OtherCrtIAuditImport;
use App\Imports\OtherEfkIAuditImport;
use App\Models\OtherEfkIAudit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class OtherEfkIAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/Other_EFK.xlsx');

        if (!file_exists($path)) {
            $this->command->warn("Missing Excel file: {$path}");
            return;
        }

        OtherEfkIAudit::truncate();

        Excel::import(new OtherEfkIAuditImport, $path);
        $this->command->info("Other_EFK.xlsx import completed.");
    }
}

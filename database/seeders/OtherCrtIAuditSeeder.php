<?php

namespace Database\Seeders;

use App\Imports\OtherCrtIAuditImport;
use App\Models\OtherCrtIAudit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class OtherCrtIAuditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/Other_CRT.xlsx');

        if (!file_exists($path)) {
            $this->command->warn("Missing Excel file: {$path}");
            return;
        }

        OtherCrtIAudit::truncate();

        Excel::import(new OtherCrtIAuditImport, $path);
        $this->command->info("Other_CRT.xlsx import completed.");
    }
}

<?php

namespace Database\Seeders;

use App\Imports\EfkIAuditImport;
use App\Models\EfkIAudit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class EfkIAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/EFK_IAudit.xlsx');

        if (!file_exists($path)) {
            $this->command->warn("Missing Excel file: {$path}");
            return;
        }

        EfkIAudit::truncate();

        Excel::import(new EfkIAuditImport, $path);
        $this->command->info("EFK_IAudit.xlsx import completed.");
    }
}

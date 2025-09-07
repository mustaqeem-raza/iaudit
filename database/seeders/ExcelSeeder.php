<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\WorkbookImport;

class ExcelSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/DataBase_Structure.xlsx');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new WorkbookImport, $path);
    }
}


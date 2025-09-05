<?php
// database/seeders/CriteriaTableIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class CriteriaTableIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/criteria_table.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('criteria_table_iaudit'), $path);
        $this->command->info('Seeded: criteria_table_iaudit');
    }
}

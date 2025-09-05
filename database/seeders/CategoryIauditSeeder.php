<?php
// database/seeders/CategoryIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class CategoryIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/category.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('category_iaudit'), $path);
        $this->command->info('Seeded: category_iaudit');
    }
}

<?php
// database/seeders/SubheadingIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class SubheadingIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/subheading.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('subheading_iaudit'), $path);
        $this->command->info('Seeded: subheading_iaudit');
    }
}

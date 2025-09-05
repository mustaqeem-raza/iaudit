<?php
// database/seeders/HeadingIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class HeadingIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/heading.csv');
        if (!file_exists($path)) {
            $this->command->warn("Missing file: $path");
            return;
        }
        Excel::import(new GenericCsvToTableImport('heading_iaudit'), $path);
        $this->command->info('Seeded: heading_iaudit');
    }
}

<?php
// database/seeders/TemplateIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class TemplateIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/template.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('template_iaudit'), $path);
        $this->command->info('Seeded: template_iaudit');
    }
}

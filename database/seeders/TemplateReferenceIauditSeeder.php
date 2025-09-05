<?php
// database/seeders/TemplateReferenceIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class TemplateReferenceIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/template_reference.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('template_reference_iaudit'), $path);
        $this->command->info('Seeded: template_reference_iaudit');
    }
}

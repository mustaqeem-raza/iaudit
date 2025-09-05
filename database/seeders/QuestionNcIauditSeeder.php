<?php
// database/seeders/QuestionNcIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class QuestionNcIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/question_nc.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('question_nc_iaudit'), $path);
        $this->command->info('Seeded: question_nc_iaudit');
    }
}

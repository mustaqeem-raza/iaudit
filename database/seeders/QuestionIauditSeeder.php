<?php
// database/seeders/QuestionIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class QuestionIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/question.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('question_iaudit'), $path);
        $this->command->info('Seeded: question_iaudit');
    }
}

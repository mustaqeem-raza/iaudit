<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\DepartmentImport;
use App\Imports\CategoryImport;
use App\Imports\HeadingImport;
use App\Imports\SubCategoryImport;
use App\Imports\TemplateRefImport;
use App\Imports\TemplateImport;
use App\Imports\QuestionImport;
use App\Imports\QuestionNcImport;
use App\Imports\CriteriaTableImport;
use App\Imports\TextBoxImport;
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


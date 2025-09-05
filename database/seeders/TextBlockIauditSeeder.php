<?php
// database/seeders/TextBlockIauditSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GenericCsvToTableImport;

class TextBlockIauditSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/text_block.csv');
        if (!file_exists($path)) { $this->command->warn("Missing file: $path"); return; }
        Excel::import(new GenericCsvToTableImport('text_block_iaudit'), $path);
        $this->command->info('Seeded: text_block_iaudit');
    }
}

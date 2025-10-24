<?php

namespace Database\Seeders;

use App\Imports\CrtTrapLocationImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class CrtTrapLocationSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/CRT_Trap_Location.xlsx');

        if (!file_exists($path)) {
            $this->command->warn("Missing Excel file: {$path}");
            return;
        }

        $this->command->info("Importing CRT_Trap_Location.xlsx ...");
        Excel::import(new CrtTrapLocationImport, $path);
        $this->command->info("CRT_Trap_Location.xlsx import completed.");
    }
}

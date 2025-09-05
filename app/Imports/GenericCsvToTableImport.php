<?php
// app/Imports/GenericCsvToTableImport.php
namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class GenericCsvToTableImport implements ToCollection, WithHeadingRow
{
    protected string $table;
    protected int $chunk;
    protected ?array $allowedColumns;

    /**
     * @param string $table Target DB table
     * @param int    $chunk Bulk insert size
     * @param array|null $allowedColumns Optional whitelist of columns (csv headers) to keep
     */
    public function __construct(string $table, int $chunk = 1000, ?array $allowedColumns = null)
    {
        $this->table = $table;
        $this->chunk = $chunk;
        $this->allowedColumns = $allowedColumns;
    }

    public function collection(Collection $rows)
    {
        $batch = [];
        foreach ($rows as $row) {
            // Normalize row to simple array
            $data = array_filter($row->toArray(), fn($v) => $v !== null && $v !== '');
            if ($this->allowedColumns) {
                $data = array_intersect_key($data, array_flip($this->allowedColumns));
            }
            if (!empty($data)) {
                $batch[] = $data;
            }

            if (count($batch) >= $this->chunk) {
                DB::table($this->table)->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table($this->table)->insert($batch);
        }
    }
}

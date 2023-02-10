<?php

namespace App\Imports;

use App\Repositories\AttendantCollectiveRegistration\AttendantCollectiveRegistrationRepository;
use App\Services\AttendantCollectiveRegistrationService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;

class AttendantCollectiveRegistrationImport implements ToArray, WithStartRow
{
    use Exportable;
    /**
    * Optional headers
    */
    private $headers = [
        'Content-Type' => 'text/csv',
        'Content-Encoding'=> 'SHIFT-JIS'
    ];

    public function array(Array $rows)
    {
        return $rows;
    }
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
}

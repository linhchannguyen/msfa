<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InFacilityTargetPersonManagementOutput implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View 
    {
        try {
            return view('exports.in-facility-target-person-management-output', [
                'attendees' => $this->data,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TargetFacilityPersonSettingOutput implements FromView
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
            return view('exports.target-facility-person-setting-output', [
                'attendees' => $this->data,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

<?php

namespace App\Exports;

use App\Models\Duty;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportDuties implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Duty::all();
    }

    public function headings():array 
    {
        return [
            '#',
            'building',
            'emp_id',
            'date',
            'start_time',
            'end_time',
            'duration',
            'message',
            'max_scholars',
            'current_scholars',
            'is_locked',
            'duty_status',
            'is_completed',
        ];

    } 

    public function map($duty):array
    {
        return [
            $duty->id,
            $duty->building,
            $duty->emp_id,
            $duty->date,
            $duty->start_time,
            $duty->end_time,
            $duty->duration,
            $duty->message,
            $duty->max_scholars,
            $duty->current_scholars,
            $duty->is_locked,
            $duty->duty_status,
            $duty->is_completed,
        ];
    }


}

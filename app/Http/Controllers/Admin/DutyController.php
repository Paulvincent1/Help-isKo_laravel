<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportDuties;
use App\Http\Controllers\Controller;
use App\Models\Duty;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DutyController extends Controller
{
    public function index(){
        $duties = Duty::all();
        return view('duty.duty', ['duties' => $duties]);
    }

    public function export(){
        return Excel::download(new ExportDuties, 'duty.xlsx');
    }
}

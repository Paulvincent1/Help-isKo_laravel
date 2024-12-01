<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Duty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RenewalForm;

class AdminController extends Controller
{
    

    public function index()
    {
        $employees = User::with('employeeProfile')->where('role', 'employee')->get();
        $renewalCount = RenewalForm::count();
        $students = User::with('studentProfile')->where('role', 'student')->get();

        $startOfWeek = Carbon::now()->startOfWeek();  // Monday of current week
        $endOfWeek = Carbon::now()->endOfWeek(); 
        

        $weeklyDuties = Duty::select(DB::raw('DATE(date) as day, COUNT(*) as total'))->whereBetween('date',[$startOfWeek,$endOfWeek])
        ->groupBy(DB::raw('DATE(date)'))->orderBy('day', 'asc')->get();

 
        $dutyCounts = [];
        for($i = 0; $i < 7; $i++){
            $day = $startOfWeek->copy()->addDay($i)->format('Y-m-d');
            $dutyCounts[$day] = 0;
        }
     
        foreach($weeklyDuties as $totalPerDay){
            $dutyCounts[$totalPerDay->day] = $totalPerDay->total;
        }

        $totalDutiesPerWeek = [];
        foreach($dutyCounts as $key => $value){
            $totalDutiesPerWeek[] = $value;
        }

        // dd(  $totalDutiesPerWeek);


        return view('index',['students' => $students, 'employees' => $employees, 'totalDutiesPerWeek' => $totalDutiesPerWeek, 'renewalCount' => $renewalCount]);
    }
}

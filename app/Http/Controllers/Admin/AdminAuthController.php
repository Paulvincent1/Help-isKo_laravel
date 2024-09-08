<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function index(){
        return view('login');
    }

    public function login(Request $request){
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if(Auth::attempt($fields)){
           $auth = Auth::user();
           if($auth->role === 'admin'){

               return redirect()->route('index');
            }else{
                Auth::logout();
 
                $request->session()->invalidate();
             
                $request->session()->regenerateToken();
             
                return redirect()->route('login');
            }

        }

        return redirect()->back()->withErrors('Wrong creds');
        
    }

    public function logout(Request $request){
        Auth::logout();
 
        $request->session()->invalidate();
     
        $request->session()->regenerateToken();
     
        return redirect()->route('login');
    }
}

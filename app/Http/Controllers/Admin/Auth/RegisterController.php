<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index(){
        $title = 'register';
        return view('admin.auth.register',compact('title'));
    }

    public function store(Request $request){
        $this->validate($request ,[
            'name' => ['required', 'max:50', 'regex:/^[A-Za-z. ]+$/'],
            'email' => ['required', 'email', 'max:30', 'regex:/^[A-Za-z0-9.]+@gmail\.com$/', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:15',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
            ],
        ], [
            'name.regex' => 'Full name may only contain letters, spaces, and periods.',
            'email.regex' => 'Only Gmail addresses (e.g. name@gmail.com) are accepted.',
            'password.regex' => 'Password must be at least 8 characters and include both letters and numbers.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);
        $user->assignRole('sales-person');

        return view('admin.auth.register-pending', ['name' => $user->name]);
    }
}
<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(){
        $title = 'login';
        return view('admin.auth.login',compact('title'));
    }

    public function login(Request $request){
        $this->validate($request ,[
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $authenticate = auth()->attempt($request->only('email','password'));

        if (!$authenticate){
            return back()->with('login_error',"Invalid user credentials");
        }

        $user = auth()->user();

        if ($user->hasRole('sales-person') && !$user->isApproved()) {
            auth()->logout();

            $message = $user->isRejected()
                ? 'Your registration was not approved by the Admin.'
                : 'Your account is still pending Admin approval. Please wait for confirmation.';

            return back()->with('login_error', $message);
        }

        return redirect()->route('dashboard');
    }
}
<?php
namespace App\Http\Controllers\Admin\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
class ResetPasswordController extends Controller
{
    public function index(Request $request, $token)
    {
        $title = 'Reset Password';
        return view('admin.auth.password.reset', [
            'title' => $title,
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }
    public function resetPassword(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:15',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,15}$/',
            ],
        ], [
            'password.regex' => 'Password must be 8 to 15 characters and include both letters and numbers only.',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
    
                $user->setRememberToken(Str::random(60));
    
                event(new PasswordReset($user));
            }
        );
    
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }
}
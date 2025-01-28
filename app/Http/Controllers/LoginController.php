<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $id = Auth::id();
            $activity = [
                'user_id' => $id,
                'action' => 'logged in'
            ];
            ActivityLog::create($activity);

            $request->session()->regenerate();

            // Inisialisasi outlet_id di session
            $user = Auth::user()->load('employee');
            if($user->hasRole('owner')) {
                $outlet = $user->outlets()->first();
                session(['outlet_id' => $outlet->id]);
            }else{
                $outlet = $user->employee->outlet_id;
                session(['outlet_id' => $outlet]);
            }

            return redirect()->intended('/');
        }

        return back()->with('LoginError', 'Login Failed');
    }

    public function logout(Request $request)
    {
        $id = Auth::id();
        $activity = [
            'user_id' => $id,
            'action' => 'logged out'
        ];
        ActivityLog::create($activity);

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Events\Registered;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;



class StaffLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('staff')->attempt($credentials)) {
            return redirect()->route('staff.attendance');
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function logout()
    {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login');
    }

    public function store(Request $request){

    $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staffs,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // スタッフ作成
        $staff = Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 自動ログイン（必要であれば）
        Auth::guard('staff')->login($staff);

        return redirect()->route('staff.attendance');
    }

    public function register()
    {
        return view('auth.register');
    }

}




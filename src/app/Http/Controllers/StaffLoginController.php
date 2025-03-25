<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Auth\Events\Registered;




class StaffLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // 管理者ログイン画面を表示
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

    public function store(
        Request $request,
        CreateNewUser $creator
    ) {
        event(new Registered($user = $creator->create($request->all())));
        session()->put('unauthenticated_user', $user);
        return redirect()->route('verification.notice');
    }

    public function register()
    {
        return view('auth.register');
    }

}




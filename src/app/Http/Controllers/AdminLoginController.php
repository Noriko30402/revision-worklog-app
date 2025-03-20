<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;


class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.admin-login'); // 管理者ログイン画面を表示
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.admin-index'); // 管理者ダッシュボードへ
        }

        return back()->withErrors(['email' => 'ログインに失敗しました']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.admin-login'); // ログアウト後にログイン画面へ
    }
}

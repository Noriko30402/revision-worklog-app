<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class WorkController extends Controller
{
    public function index(){
        return view('worklog.clock-in');
    }

    public function logout()
    {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login'); // ログアウト後にログイン画面へ
    }

}

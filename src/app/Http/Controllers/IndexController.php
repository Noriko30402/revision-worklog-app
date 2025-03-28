<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::now() -> format('Y-m');

        $works = Work::whereYear('date',Carbon::now()->year)
                ->whereMonth('date',Carbon::now() -> month)
                ->orderBy('date','asc')
                ->get();

        $rests = Rest::whereYear('date',Carbon::now() -> year)
                ->whereMonth('date',Carbon::now()->month)
                ->orderBy('date','asc')
                ->get();
        return view('index',compact('works','rests','currentMonth'));
    }
}

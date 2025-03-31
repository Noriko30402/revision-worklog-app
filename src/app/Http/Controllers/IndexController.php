<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;


class IndexController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y/m');

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $staff = Auth::guard('staff')->user();

        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        $works = Work::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->where('staff_id', $staff->id)
            ->orderBy('date', 'asc')
            ->get();

        $rests = Rest::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->where('staff_id', $staff->id)
            ->orderBy('date', 'asc')
            ->get();

            $worksByDate = $works->keyBy(fn($work) => Carbon::parse($work->date)->toDateString());
            $restsByDate = $rests->keyBy(fn($rest) => Carbon::parse($rest->date)->toDateString());

        return view('index', compact('restsByDate','worksByDate','works', 'currentMonth', 'rests', 'dates'));
    }

    public function detail(){

    return view('detail');
    }
}


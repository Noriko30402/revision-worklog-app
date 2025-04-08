<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitWorkRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;


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

         // 月が変更された場合、前月または次月のデータを取得
        if ($request->has('previousMonth')) {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
            $currentMonth = Carbon::now()->subMonth()->format('Y/m');
        }

        if ($request->has('nextMonth')) {
            $startDate = Carbon::now()->addMonth()->startOfMonth();
            $endDate = Carbon::now()->addMonth()->endOfMonth();
            $currentMonth = Carbon::now()->addMonth()->format('Y/m');
        }

        // 日付配列を作成
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // その月のデータを取得
        $works = Work::whereYear('date', $startDate->year)
            ->whereMonth('date', $startDate->month)
            ->where('staff_id', $staff->id)
            ->orderBy('date', 'asc')
            ->get();

        $rests = Rest::whereYear('date', $startDate->year)
            ->whereMonth('date', $startDate->month)
            ->where('staff_id', $staff->id)
            ->orderBy('date', 'asc')
            ->get();

        // 日付をキーにしてデータをまとめる
        $worksByDate = $works->keyBy(fn($work) => Carbon::parse($work->date)->toDateString());
        $restsByDate = $rests->keyBy(fn($rest) => Carbon::parse($rest->date)->toDateString());

        return view('index', compact('restsByDate', 'worksByDate', 'works', 'currentMonth', 'rests', 'dates'));
    }

    public function detail(Request $request,$work_id){

        $staff = Auth::guard('staff')->user();
        $work = Work::where('staff_id', $staff->id)
        ->where('id', $work_id)
        ->first();

        $rest = Rest::whereIn('work_id', $work->pluck('id'))
        ->first();


        return view('detail',compact('work','staff','rest'));
    }

    public function edit(SubmitWorkRequest $request,$work_id){

        $staff = Auth::guard('staff')->user();

        Application::create([
        'work_id' => $work_id,
        'staff_id' => Auth::guard('staff')->user(),
        'clock_in' => $request ->clock_in,
        'clock_out' => $request ->clock_out,
        'rest_in' => $request ->rest_in,
        'rest_out' => $request ->rest_out,
        'date'=> $request -> date,
        'comment' => $request ->comment,
        ]);
    }

}


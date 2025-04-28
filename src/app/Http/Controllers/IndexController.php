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
        $monthParam = $request->query('month');
        $currentMonth = $monthParam ? Carbon::createFromFormat('Y/m', $monthParam): Carbon::now();
        $displayDate1 = $currentMonth->format('Y/m');

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');

        $staff = Auth::guard('staff')->user();

            // その月のデータを取得
        $works = Work::whereYear('date', $currentMonth->year)
                ->whereMonth('date', $currentMonth->month)
                ->where('staff_id', $staff->id)
                ->get();

        $rests = Rest::whereYear('date', $currentMonth->year)
                        ->whereMonth('date', $currentMonth->month)
                        ->where('staff_id', $staff->id)
                        ->get();


    // 月の開始日と終了日
    $startDate = $currentMonth->copy()->startOfMonth();
    $endDate = $currentMonth->copy()->endOfMonth();

    $dates = [];
    $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // 日付をキーにしてデータをまとめる
        $worksByDate = $works->keyBy(fn($work) => Carbon::parse($work->date)->toDateString());

        return view('index', compact( 'worksByDate', 'works',
                    'displayDate1', 'rests', 'dates','prevMonth','nextMonth'));
    }

    public function detail(Request $request,$work_id){

        $staff = Auth::guard('staff')->user();
        $work = Work::where('staff_id', $staff->id)
        ->where('id', $work_id)
        ->first();

        $rests = Rest::where('staff_id', $staff->id)
        ->where('work_id', $work_id)
        ->get();

        return view('detail',compact('work','staff','rests'));
    }

    public function edit(Request $request,$work_id){

        $staff = Auth::guard('staff')->user();
        $rest_ins = $request->input('rest_in');
        $rest_outs = $request->input('rest_out');

    // 休憩ごとに1行ずつ保存する
        foreach ($rest_ins as $index => $rest_in) {
            $application =  Application::create([
                'staff_id' => $staff->id,
                'work_id' => $work_id,
                'clock_in' => $request->input('clock_in'),
                'clock_out' => $request->input('clock_out'),
                'rest_in' => $rest_in,
                'rest_out' => $rest_outs[$index] ?? null,
                'date' => $request->input('date'),
                'comment' => $request->input('comment'),
        ]);
    }
        $rests = Application::where('work_id', $work_id)
                ->get();

        return view('confirm',compact('staff','application','rests'));
    }

    public function approval(Request $request){

        $tab = $request->input('tab', 'approval');

        $approvedApplications  = Application::with('staff')
                                ->where('approved', 1)
                                ->get();

        $pendingApplications = Application::with('staff')
                                ->where('approved', 0)
                                ->get();

        $approvedApplicationsGrouped = $approvedApplications->groupBy('work_id');
        $pendingApplicationsGrouped = $pendingApplications->groupBy('work_id');

        return view('approval', compact('tab', 'approvedApplicationsGrouped','pendingApplicationsGrouped'));
    }

    public function approvalDetail($work_id){

        $staff = Auth::guard('staff')->user();

        $pendingApplication = Application::with('staff')
                                ->where('work_id', $work_id)
                                ->where('approved', 0)
                                ->first();

        $rests = Application::where('work_id', $work_id)
                                ->get();

        return view('approval-confirm', compact('staff','pendingApplication','rests'));
    }

    public function approvalComplete($work_id){

        $staff = Auth::guard('staff')->user();

        $approvedApplication = Application::with('staff')
                                ->where('work_id', $work_id)
                                ->where('approved', 1)
                                ->first();

        $rests = Application::where('work_id', $work_id)
                                ->get();

        return view('approval-complete', compact('staff','approvedApplication','rests'));
    }
}
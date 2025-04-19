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

        $rests = Rest::where('staff_id', $staff->id)
        ->where('work_id', $work_id)
        ->get();

        return view('detail',compact('work','staff','rests'));
    }

    public function edit(SubmitWorkRequest $request,$work_id){
        $staff = Auth::guard('staff')->user();
        $restIns = $request->input('rest_in');
        $restOuts = $request->input('rest_out');

        $rests = [
            'rest_in' => $request->input('rest_in') ?? [],
            'rest_out' => $request->input('rest_out') ?? [],
        ];
                $application =  Application::create([
                'staff_id' => $staff->id,
                'work_id' => $work_id,
                'clock_in' => $request->input('clock_in'),
                'clock_out' => $request->input('clock_out'),
                'rests' => json_encode($rests),
                'date' => $request->input('date'),
                'comment' => $request->input('comment'),
            ]);
        $restArray = json_decode($application->rests, true) ?? ['rest_in' => [], 'rest_out' => []];

        return view('confirm',compact('staff','application','restArray'));
    }

    public function approval(Request $request){

        $tab = $request->input('tab', 'approval');

        $approvedApplications  = Application::with('staff')
                                ->where('approved', 1)
                                ->get();

        $pendingApplications = Application::with('staff')
                                ->where('approved', 0)
                                ->get();

        return view('approval', compact('tab', 'approvedApplications','pendingApplications'));
    }

    public function approvalDetail($work_id){

        $staff = Auth::guard('staff')->user();

        $approvedApplications  = Application::with('staff')
                                ->where('work_id', $work_id)
                                ->where('approved', 0)
                                ->first();
        $pendingApplication = Application::with('staff')
                                ->where('work_id', $work_id)
                                ->where('approved', 0)
                                ->first();
        $restArray = json_decode($pendingApplication->rests, true) ?? ['rest_in' => [], 'rest_out' => []];


        return view('approval-confirm', compact('staff', 'approvedApplications','pendingApplication','restArray'));
    }

}
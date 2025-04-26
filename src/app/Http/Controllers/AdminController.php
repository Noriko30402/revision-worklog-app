<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Staff;


class AdminController extends Controller
{
    public function index(Request $request)
{
    $dateParam = $request->query('date');
    $currentDate = $dateParam ? Carbon::parse($dateParam) : Carbon::now();
    $displayDate1 = $currentDate->format('Y年m月d日');
    $displayDate2 = $currentDate->format('Y/m/d');

    $prevDate = $currentDate->copy()->subDay()->format('Y/m/d');
    $nextDate = $currentDate->copy()->addDay()->format('Y/m/d');

    // その月のデータを取得
    $works = Work::with('staff')
            ->whereDate('date', $currentDate->toDateString())
            ->get();

    $rests = Rest::whereDate('date', $currentDate->toDateString())
        ->get();

    foreach ($works as $work){
    $rest = $rests->firstWhere('work_id', $work->id);
    }

    return view('admin.admin-index',compact('displayDate1','displayDate2','works',
                                            'rest','prevDate','nextDate'));
}



    public function detail($work_id){

        $work = Work::with('staff')
                ->where('id', $work_id)
                ->first();

        $rests = Rest::with('staff')
                ->where('work_id', $work_id)
                ->get();

        return view('admin.admin-detail',compact('work','rests'));
    }



    public function edit(Request $request,$work_id){

        $work = Work::findOrFail($work_id);
        $work->update([
            'clock_in' => $request->input('clock_in'),
            'clock_out' => $request->input('clock_out'),
            // 'date' => $request->input('date'),
            'comment' => $request->input('comment'),
        ]);

        $restIns = $request->input('rest_in');
        $restOuts = $request->input('rest_out');

        $rests = Rest::where('work_id', $work_id)->get();

        foreach ($rests as $index => $rest) {
            $rest->update([
                'rest_in' => $restIns[$index] ?? null,
                'rest_out' => $restOuts[$index] ?? null,
            ]);
            session()->flash('success', '勤怠情報を更新しました。');
        }
        return view('admin.admin-detail', compact('work','rests'));
    }

    public function staffIndex(){
        $staffs = Staff::all();

        return view('admin.admin-staff',compact('staffs'));
    }

    public function staffWorklog(Request $request , $staff_id)
    {
        $currentMonth = Carbon::now()->format('Y/m');
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

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

        $staff = Staff::findOrFail($staff_id);

        // その月のデータを取得
        $works = Work::with('staff')
            ->whereYear('date', $startDate->year)
            ->whereMonth('date', $startDate->month)
            ->where('staff_id', $staff_id)
            ->orderBy('date', 'asc')
            ->get();

        $rests = Rest::whereYear('date', $startDate->year)
            ->whereMonth('date', $startDate->month)
            ->where('staff_id', $staff_id)
            ->orderBy('date', 'asc')
            ->get();

        // 日付をキーにしてデータをまとめる
        $worksByDate = $works->keyBy(fn($work) => Carbon::parse($work->date)->toDateString());
        $restsByDate = $rests->keyBy(fn($rest) => Carbon::parse($rest->date)->toDateString());

        return view('admin.staff-worklog', compact('restsByDate', 'worksByDate', 'works', 'currentMonth', 'rests', 'dates','staff'));
    }


    public function approval(Request $request){

        $tab = $request->input('tab', 'approval');

        $approvedApplications  = Application::with('staff')
                                ->where('approved', 1)
                                ->get();

        $pendingApplications = Application::with('staff')
                                ->where('approved', 0)
                                ->get();

        return view('admin.approval', compact('tab', 'approvedApplications','pendingApplications'));
    }

}

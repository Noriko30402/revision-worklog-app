<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitWorkRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Staff;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\StreamedResponse;


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
                                            'rests','prevDate','nextDate'));
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

// 管理者が自由に修正
    public function edit(SubmitWorkRequest $request, $work_id)
    {
        $validated = $request->validated();
        $restIns = $request->input('rest_in');
        $restOuts = $request->input('rest_out');
        $rests = Rest::where('work_id', $work_id)->get();

        foreach ($rests as $index => $rest) {
            $rest_in = $restIns[$index] ?? null;
            $rest_out = $restOuts[$index] ?? null;

            $rest->rest_in = $rest_in;
            $rest->rest_out = $rest_out;

            if ($rest_in && $rest_out) {
                $restInCarbon = Carbon::parse($rest_in);
                $restOutCarbon = Carbon::parse($rest_out);
                $total_rest_seconds = $restOutCarbon->diffInSeconds($restInCarbon);

                $rest->total_rest_time = gmdate('H:i:s', $total_rest_seconds);
            } else {
                $rest->total_rest_time = null;
            }
            $rest->save();
        }

        $totalRestSeconds = Rest::where('work_id', $work_id)
                                ->whereNotNull('rest_in')
                                ->whereNotNull('rest_out')
                                ->get()
                                ->sum(function ($rest) {
                                    [$h, $m, $s] = explode(':', $rest->total_rest_time);
                                    return ($h * 3600) + ($m * 60) + $s;
                                });
        // 出勤・退勤時刻
        $clock_in = Carbon::parse($request->input('clock_in'));
        $clock_out = Carbon::parse($request->input('clock_out'));
        $workSeconds = $clock_out->diffInSeconds($clock_in);
        $totalWorkSeconds = $workSeconds - $totalRestSeconds;

        $formattedTotalWorkTime = gmdate('H:i:s', $totalWorkSeconds);
        $formattedTotalRestTime = gmdate('H:i:s', $totalRestSeconds);

        $work = Work::findOrFail($work_id);
        $work->update([
            'clock_in' => $request->input('clock_in'),
            'clock_out' => $request->input('clock_out'),
            'comment' => $request->input('comment'),
            'total_work_time' => $formattedTotalWorkTime,
            'total_rest_time' => $formattedTotalRestTime,
        ]);

        session()->flash('success', '勤怠情報を更新しました。');

        return view('admin.admin-detail', compact('work', 'rests'));
    }

    public function staffIndex(){
        $staffs = Staff::all();

        return view('admin.admin-staff',compact('staffs'));
    }

    public function staffWorklog(Request $request , $staff_id)
    {

        $monthParam = $request -> query('month');
        $currentMonth = $monthParam ? Carbon::createFromFormat('Y/m', $monthParam): Carbon::now();
        $displayDate1 = $currentMonth->format('Y/m');

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');

        // その月のデータを取得
        $works = Work::with('staff')
                ->whereYear('date', $currentMonth->year)
                ->whereMonth('date', $currentMonth->month)
                ->where('staff_id', $staff_id)
                ->get();

        $rests = Rest::whereYear('date', $currentMonth->year)
                ->whereMonth('date', $currentMonth->month)
                ->where('staff_id', $staff_id)
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

        $staff = Staff::findOrFail($staff_id);

        // 日付をキーにしてデータをまとめる
        $worksByDate = $works->keyBy(fn($work) => Carbon::parse($work->date)->toDateString());

        return view('admin.staff-worklog', compact('worksByDate', 'works',
                    'displayDate1', 'rests', 'dates','prevMonth','nextMonth','staff','staff_id'));
    }

public function export($staff_id, Request $request)
{
    $works = Work::where('staff_id', $staff_id)->get();

    $csvHeader = ['日付', '出勤', '退勤', '休憩', '勤務時間'];

    $staff = Staff::find($staff_id);

    // ストリームレスポンスを使用してCSVファイルを出力
    $response = new StreamedResponse(function () use ($works, $csvHeader) {
        $output = fopen('php://output', 'w');

        // ヘッダーをCSVに書き込む
        fputcsv($output, $csvHeader);

        // 勤怠データをCSVに書き込む
        foreach ($works as $work) {
            $row = [
                $work->date->isoFormat('M/D（ddd）'),
                \Carbon\Carbon::parse($work->clock_in)->format('H:i'),
                \Carbon\Carbon::parse($work->clock_out)->format('H:i'),
                \Carbon\Carbon::parse($work->total_rest_time)->format('H:i'),
                \Carbon\Carbon::parse($work->total_work_time)->format('H:i')
            ];
            fputcsv($output, $row);
        }

        fclose($output);
    }, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $staff->name . 'の勤怠.csv"',
    ]);

    return $response;
}
}

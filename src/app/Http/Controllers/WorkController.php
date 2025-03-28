<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Work;
use App\Models\Staff;
use App\Models\Rest;
use Illuminate\Support\Facades\DB;


class WorkController extends Controller
{
    public function attendance(){

        $formatted_date = Carbon::now()->isoFormat('Y年M月D日（ddd）');
        $now_time = Carbon::now()->isoFormat('HH:mm');

        $staff = Auth::guard('staff')->user();

        $now_date = Carbon::now()->format('Y-m-d');
        $confirm_date = Work::where('staff_id', $staff)
            ->where('date', $now_date)
            ->first();

        if (!$confirm_date) {
            $status = 0;
        } else {
            $status = Auth::guard('staff')->status;
        }

        return view('worklog',compact('formatted_date','now_time','status'));
    }

    public function work(Request $request){

        $formatted_date = Carbon::now()->isoFormat('Y年M月D日（ddd）');
        $now_time = Carbon::now()->isoFormat('HH:mm');
        $now_date = Carbon::now()->format('Y-m-d');
        $staff = Auth::guard('staff')->user();

        $lastWork = Work::where('staff_id', $staff->id)->latest()->first();
        $today = Carbon::today();

        if (!$lastWork) {
            $status = 0;
        } else {
            $status = $staff->status;
        }

        try {
            DB::beginTransaction();

            if ($request->has('start_work')) {
                $status = $this->startWork($lastWork, $staff, $now_time, $now_date);
            }

            if ($request->has('end_work')) {
                $status = $this->endWork($lastWork, $staff, $now_time, $now_date);
            }

            if ($request->has('start_rest')) {
                $status = $this->startRest($lastWork, $staff, $now_time, $now_date);
            }

            if ($request->has('end_rest')) {
                $status = $this->endRest($lastWork, $staff, $now_time);
            }
            DB::commit();

            return view('worklog',compact('formatted_date', 'now_time', 'status'));
        } catch (\Exception $e) {
            DB::rollBack(); // 例外が発生した場合、ロールバック
            return back()->with('error', 'すでに出勤打刻がされています。');
        }
    }

    // 勤務開始処理
    public function startWork($lastWork,$staff,$now_time,$now_date)
    {
        $today = Carbon::today();

        if(!$lastWork || !$lastWork->date->isSameDay($today)){
            $work = new Work();
            $work->date = $now_date;
            $work->clock_in = $now_time;
            $work->staff_id = $staff->id;
            $work->status = 1;
            $work->save();
            return 1;
        }else{
            return response()->view('worklog',compact('formatted_date','now_time'))
            ->with('error','すでに出勤打刻がされています。');
        }
    }

     // 勤務終了処理
    private function endWork($lastWork, $staff, $now_time, $now_date)
    {
        $clock_in = Carbon::parse($lastWork->rest_in);
        $clock_out = Carbon::parse($lastWork->rest_out);

        if ($lastWork && $lastWork->date->isSameDay($now_date) && empty($lastWork->clock_out)) {
            $lastWork->clock_out = $now_time;
            $lastWork->status = 3;
            $lastWork->total_work_time = $clock_out->diffInSeconds($clock_in);
            $lastWork->save();
            return 3;
    }
    return 0;
    }

     // 休憩開始処理
    private function startRest($lastWork, $staff, $now_time, $now_date)
    {
        if (!$lastWork) {
            return response()->view('worklog', compact('formatted_date', 'now_time'))->with('error', '勤務記録がありません');
        }

        $work_id = $lastWork->id;

        $rest = new Rest();
        $rest->date = $now_date;
        $rest->rest_in = $now_time;
        $rest->staff_id = $staff->id;
        $rest->work_id = $work_id;
        $rest->save();

     // 勤務ステータスを休憩中に変更
        $lastWork->status = 2;
        $lastWork->save();

         return 2;  // 休憩中ステータス
    }

    // 休憩終了処理
    private function endRest($lastWork, $staff, $now_time)
    {
    $rest = Rest::where('work_id', $lastWork ? $lastWork->id : null)
                ->whereNull('rest_out')
                ->where('staff_id', $staff->id)
                ->first();

    $lastWork = Work::where('staff_id', $staff->id)->latest()->first();

    $rest_in = Carbon::parse($rest->rest_in);
    $rest_out = Carbon::parse($rest->rest_out);

    if ($rest && $lastWork) {
        $rest->rest_out = $now_time;
        $rest->total_rest_time = $rest_out->diffInSeconds($rest_in);
        $rest->save();

        // 勤務ステータスを勤務中に戻す
    if ($lastWork) {
        $lastWork->status = 1;
        $lastWork->save();
    }

        return 1;
    }
}
}

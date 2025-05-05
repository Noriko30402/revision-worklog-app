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
            $status = session('work_status', 0);
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
        $status = (!$lastWork || !Carbon::parse($lastWork->date)->isSameDay($today))
        ? 0 : $lastWork->status;


            if ($request->has('start_work')) {
                $status = $this->startWork($lastWork, $staff, $now_time, $now_date,$formatted_date);
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
    }

    private function startWork($lastWork,$staff,$now_time,$now_date,$formatted_date)
    {
        $today = Carbon::today();

        if(!$lastWork || !$lastWork->date->isSameDay($today)){
            $work = new Work();
            $work->date = $now_date;
            $work->clock_in = $now_time;
            $work->staff_id = $staff->id;
            $work->status = 1;
            $work->save();

            session(['work_status' => 1]);
            return 1;
        }else{
            $status = 0;
            session()->flash('error', 'すでに出勤打刻がされています。');
            return view('worklog',compact('status','formatted_date','now_date','now_time'));
        }
    }

    private function startRest($lastWork, $staff, $now_time, $now_date){

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

        $lastWork->status = 2;
        $lastWork->save();
        session(['work_status' => 2]);
        return 2;
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
        $rest_out = Carbon::parse($now_time);
        $total_rest_time = $rest_out->diffInSeconds($rest_in);
        $formattedRestTime = gmdate("H:i:s", $total_rest_time);

        if ($rest && $lastWork) {
            $rest->rest_out = $now_time;
            $rest->total_rest_time = $formattedRestTime;
            $rest->save();

        if ($lastWork) {
            $lastWork->status = 1;
            $lastWork->save();
        }
        session(['work_status' => 1]);
            return 1;
        }
    }

     // 勤務終了処理
    private function endWork($lastWork, $staff, $now_time, $now_date)
    {
        $clock_in = Carbon::parse($lastWork->clock_in);
        $clock_out = Carbon::parse($now_time);
        $work_time = $clock_out->diffInSeconds($clock_in);

        $total_rest_time = Rest::where('work_id', $lastWork->id)
                        ->whereNotNull('rest_out')
                        ->get()
                        ->sum(function ($rest) {
                            list($h, $m, $s) = explode(':', $rest->total_rest_time);
                            return ($h * 3600) + ($m * 60) + $s;
                        });

        // 休憩時間を引いた最終労働時間
        $total_work_time = $work_time - $total_rest_time;
        $formattedWorkTime = gmdate("H:i:s", $total_work_time);
        $formattedRestTime = gmdate("H:i:s", $total_rest_time);


        if ($lastWork && $lastWork->date->isSameDay($now_date) && empty($lastWork->clock_out)) {
            $lastWork->clock_out = $now_time;
            $lastWork->status = 3;
            $lastWork->total_work_time = $formattedWorkTime;
            $lastWork->total_rest_time = $formattedRestTime;
            $lastWork->save();
            session(['work_status' => 3]);
            return 3;
    }
    return 0;
    }

}

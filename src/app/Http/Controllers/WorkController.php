<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Work;
use App\Models\Staff;
use App\Models\Rest;


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

        if ($request->has('start_rest') || $request->has('end_rest')) {
            $work_id = Work::where('staff_id', $staff->id)
                ->where('date', $now_date)
                ->first()
                ->id;
        }

        // 勤務開始
        if ($request->has('start_work')) {
            $work = new Work();
            $work->date = $now_date;
            $work->clock_in = $now_time;
            $work->staff_id = $staff->id;
            $work->status = 1;
            $work->save();
            $status = 1;
        }
        // 勤務終了
        if ($request->has('end_work')) {
            $work = Work::where('staff_id', $staff->id)
                ->where('date', $now_date)
                ->first();
            $work->clock_out = $now_time;
            $work->status = 3;
            $work->save();
            $status = 3;
        }

        // 休憩開始
        if ($request->has('start_rest')) {
            $rest = new Rest();
            $rest->date = $now_date;
            $rest->rest_in = $now_time;
            $rest->staff_id = $staff->id;
            $rest->work_id = $work_id;
            $rest->save();

        // 勤務ステータスを休憩中に変更
        $work = Work::where('id', $work_id)
                ->where('date', $now_date)
                ->first();

        if ($work) {
            $work->status = 2;
            $work->save();
        }

        $status = 2;
        }

        // 休憩終了
        if ($request->has('end_rest')) {

       // 休憩終了を記録
        $rest = Rest::where('work_id', $work_id)
                ->whereNull('rest_out')
                ->first();

        if ($rest) {
        $rest->rest_out = $now_time;
        $rest->save();

        // 勤務ステータスを勤務中に戻す
        $work = Work::where('id', $work_id)
            ->where('date', $now_date)
            ->first();

        if ($work) {
            $work->status = 1;
            $work->save();
        }

        $status = 1;
        }
    }
        return view('worklog',compact('now_time','formatted_date','status'));
    }
}
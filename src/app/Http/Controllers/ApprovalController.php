<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SubmitWorkRequest;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;

class ApprovalController extends Controller
{
// スタッフからの申請一覧
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

        return view('admin.approval', compact('tab', 'approvedApplicationsGrouped','pendingApplicationsGrouped'));
    }

    public function approvalDetail($work_id){

        $pendingApplication = Application::where('work_id', $work_id)
                                ->where('approved', 0)
                                ->first();

        $rests = Application::where('work_id', $work_id)
                                ->get();

        $approvedApplications  = Application::with('staff')
                                ->where('approved', 1)
                                ->get();


        return view('admin.approval-confirm', compact('pendingApplication','rests','approvedApplications'));
    }

    public function update(Request $request, $work_id)
    {
        Application::where('work_id', $work_id)
                    ->update(['approved' => true]);

    $approvedApplications = Application::where('work_id', $work_id)
                                    ->where('approved', 1)
                                    ->get();

        $work = Work::findOrFail($work_id);

        $total_rest_seconds = 0;

        $rests = Rest::where('work_id', $work_id)->get();

        foreach ($rests as $index => $rest) {
            $application = $approvedApplications[$index] ?? null;

            if ($application && !empty($application->rest_in) && !empty($application->rest_out)) {
                $restIn = Carbon::parse($application->rest_in);
                $restOut = Carbon::parse($application->rest_out);
                $total_rest_seconds += $restOut->diffInSeconds($restIn);

                $rest->update([
                    'rest_in' => $restIn,
                    'rest_out' => $restOut,
                    'total_rest_time' =>$total_rest_seconds,
                ]);
            }
        }

        $firstApplication = $approvedApplications->first();
        if ($firstApplication) {
            $clockIn = Carbon::parse($firstApplication->clock_in);
            $clockOut = Carbon::parse($firstApplication->clock_out);

            $work_seconds = $clockOut->diffInSeconds($clockIn);
            $total_work_seconds = $work_seconds - $total_rest_seconds;

            $formattedTotalWorkTime = gmdate('H:i:s', $total_work_seconds);
            $formattedTotalRestTime = gmdate('H:i:s', $total_rest_seconds);

            $work->update([
                'clock_in' => $firstApplication->clock_in,
                'clock_out' => $firstApplication->clock_out,
                'comment' => $firstApplication->comment,
                'total_work_time' => $formattedTotalWorkTime,
                'total_rest_time' => $formattedTotalRestTime,
            ]);
        }
        $pendingApplication = Application::where('work_id', $work_id)
        ->first();
        $rests = Application::where('work_id', $work_id)
        ->get();


        return view('admin.approval-confirm', compact('pendingApplication','rests'))
        ->with('success', '勤怠情報を更新しました。');
    }


    public function approvalComplete($work_id){

        $staff = Auth::guard('staff')->user();

        $approvedApplication = Application::with('staff')
                                ->where('work_id', $work_id)
                                ->where('approved', 1)
                                ->first();

        $rests = Application::where('work_id', $work_id)
                                ->get();

        return view('admin.approval-complete', compact('staff','approvedApplication','rests'));
    }
}
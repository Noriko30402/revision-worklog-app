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

    public function approvalDetail($work_id){

        $pendingApplication = Application::where('work_id', $work_id)
                                ->where('approved', 0)
                                ->first();

        $restArray = json_decode($pendingApplication->rests, true) ?? ['rest_in' => [], 'rest_out' => []];


        return view('admin.approval-confirm', compact('pendingApplication','restArray'));
    }

    public function update(Request $request ,$work_id){

        $application =Application::findOrFail($work_id);
        $application->approved = true;
        $application->save();

        $work = Work::findOrFail($work_id);
        $work->update([
            'clock_in' => $request->input('clock_in'),
            'clock_out' => $request->input('clock_out'),
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
        return view('', compact('work','rests'));
}
}

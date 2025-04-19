<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Work;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;


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
            'date' => $request->input('date'),
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
        }
        // return redirect()->route('admin.index', ['work_id' => $work->id])->with('success', '更新完了しました！');
        return redirect()->route('admin.edit', ['work_id' => $work->id]);

    }

    }

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Rest extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id' ,' rest_in','rest_out','date','work_id','total_rest_time'];

    public function staff()
    {
        return $this ->belongsTo(Staff::class);
    }

    public function work()
    {
        return $this ->belongsTo(Work::class);
    }

    // public function calculateTotalRestTime()
    // {
    //     if ($this->rest_in && $this->rest_out) {

    //     $restInTime = Carbon::parse($this->rest_in);
    //     $restOutTime = Carbon::parse($this->rest_out);

    //     $totalRestTimeInSeconds = $restOutTime->diffInSeconds($restInTime);
    //     $totalRestTimeInMinutes = floor($totalRestTimeInSeconds / 60);

    //       $hours = floor($totalRestTimeInMinutes / 60); // 時間部分
    //       $minutes = $totalRestTimeInMinutes % 60; // 分部分
    //           // total_rest_time に保存
    //     $this->total_rest_time = sprintf("%02d:%02d", $hours, $minutes);
    //     $this->save();
    //     }
    // }

    // public function getFormattedRestTime()
    // {
    //     if ($this->total_rest_time) {
    //         return $this->total_rest_time;
    //     }
    //     return "00:00";
    // }
}


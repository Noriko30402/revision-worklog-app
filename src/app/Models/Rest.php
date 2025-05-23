<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Rest extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id' ,'rest_in','rest_out','date','work_id','total_rest_time'];

    public function staff()
    {
        return $this ->belongsTo(Staff::class);
    }

    public function work()
    {
        return $this ->belongsTo(Work::class);
    }
}


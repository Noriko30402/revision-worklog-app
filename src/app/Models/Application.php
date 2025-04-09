<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id', 'staff_id', 'clock_in', 'clock_out', 'rest_in', 'rest_out', 'date', 'comment'
    ];

    public function staff()
    {
        return $this ->belongsTo(Staff::class);
    }

    public function rest()
    {
        return $this ->belongsTo(Rest::class);
    }

    public function Work()
    {
        return $this ->belongsTo(Work::class);
    }
}



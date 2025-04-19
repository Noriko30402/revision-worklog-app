<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id' ,' clock_in','clock_out','date','status','comment'];
    protected $dates = ['date'];

    public function staff()
    {
        return $this ->belongsTo(Staff::class);
    }

    // app/Models/Work.php

    public function rests()
    {
    return $this->hasMany(Rest::class);
    }

}

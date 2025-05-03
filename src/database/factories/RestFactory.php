<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rest;
use App\Models\Work;
use Carbon\Carbon;



class RestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'staff_id' => 1,
            'work_id' => 1,
            'date' => Carbon::now()->toDateString(),
            'rest_in' => '12:00:00',
            'rest_out' => '12:30:00',
            'total_rest_time' => '00:30:00',
        ];
    }
}
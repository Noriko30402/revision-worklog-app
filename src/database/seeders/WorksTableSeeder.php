<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Work;
use App\Models\Staff;
use Carbon\Carbon;

class WorksTableSeeder extends Seeder
{
    public function run(): void
    {
        $staffList = Staff::all();

        foreach ($staffList as $staff) {
            for ($i = 0; $i < 20; $i++) {
                $date = Carbon::now()->startOfMonth()->addDays($i);

                $clockIn = $date->copy()->setHour(rand(8, 9))->setMinute([0, 15, 30, 45][rand(0, 3)]);
                $clockOut = $date->copy()->setHour(rand(17, 18))->setMinute([0, 15, 30, 45][rand(0, 3)]);

                Work::create([
                    'staff_id' => $staff->id,
                    'date' => $date->toDateString(),
                    'clock_in' => $clockIn->toTimeString(),
                    'clock_out' => $clockOut->toTimeString(),
                    'total_work_time' => '00:00:00',
                ]);
            }
        }
    }
}

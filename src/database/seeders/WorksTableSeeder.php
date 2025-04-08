<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use App\Models\Staff;


class WorksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staffs = Staff::all();
        $start = Carbon::now()->startOfMonth();

        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i);
            foreach ($staffs as $staff) {
                Work::factory()->create([
                    'staff_id' => $staff->id,
                    'date' => $date->toDateString()
                ]);
            }
        }

}}

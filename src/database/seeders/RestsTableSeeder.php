<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class RestsTableSeeder extends Seeder
{
    public function run(): void
    {
        Work::all()->each(function ($work) {
            $clockIn = Carbon::parse($work->clock_in);
            $clockOut = Carbon::parse($work->clock_out);

            $restOptions = [15, 30];
            $restCount = rand(1, 3);
            $usedSlots = [];

            for ($i = 0; $i < $restCount; $i++) {
                $duration = $restOptions[array_rand($restOptions)];
                $available = $clockOut->diffInMinutes($clockIn);
                $latest = $available - $duration;

                if ($latest <= 60) continue;

                $startMin = rand(60, $latest);
                $restIn = $clockIn->copy()->addMinutes($startMin);
                $restOut = $restIn->copy()->addMinutes($duration);

                // 重複チェック
                $overlap = false;
                foreach ($usedSlots as $slot) {
                    if ($restIn->between($slot['start'], $slot['end']) || $restOut->between($slot['start'], $slot['end'])) {
                        $overlap = true;
                        break;
                    }
                }

                if ($overlap) continue;

                $usedSlots[] = ['start' => $restIn, 'end' => $restOut];

                Rest::create([
                    'staff_id' => $work->staff_id,
                    'work_id' => $work->id,
                    'date' => $work->date,
                    'rest_in' => $restIn->toTimeString(),
                    'rest_out' => $restOut->toTimeString(),
                    'total_rest_time' => sprintf('%02d:%02d:00', floor($duration / 60), $duration % 60),
                ]);
            }

            // 合計休憩時間と実働時間を更新
            $totalRestSeconds = Rest::where('work_id', $work->id)
            ->sum(DB::raw('TIMESTAMPDIFF(SECOND, rest_in, rest_out)'));

            $workMinutes = $clockOut->diffInMinutes($clockIn);
            $restMinutes = floor($totalRestSeconds / 60);
            $actualMinutes = floor(($workMinutes - $restMinutes) / 15) * 15;

            $formattedWork = sprintf('%02d:%02d:00', floor($actualMinutes / 60), $actualMinutes % 60);
            $formattedRest = sprintf('%02d:%02d:00', floor($restMinutes / 60), $restMinutes % 60);

            $work->update([
                'total_work_time' => $formattedWork,
                'total_rest_time' => $formattedRest,
            ]);
        });
    }
}

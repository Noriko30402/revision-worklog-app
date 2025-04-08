<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class RestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        Work::all()->each(function ($work) {
            // 勤務開始と終了時間を設定
            $clockIn = Carbon::parse($work->clock_in);
            $clockOut = Carbon::parse($work->clock_out);

            // 休憩時間オプション（15分または30分）
            $restMinutesOptions = [15, 30]; 
            // 休憩回数をランダムに決定（1〜3回）
            $totalRestBreaks = rand(1, 3);
            // すでに使った休憩時間スロットを保持する配列
            $usedSlots = [];

            for ($i = 0; $i < $totalRestBreaks; $i++) {
            // 休憩の長さをランダムで決定
            $restDuration = $restMinutesOptions[array_rand($restMinutesOptions)];
            $availableMinutes = $clockOut->diffInMinutes($clockIn);
            $latestStart = $availableMinutes - $restDuration;

            // 休憩入れるスペースがなければスキップ
            if ($latestStart <= 60) {
                continue;
            }

            // 休憩開始時間を設定
            $restStartMinute = rand(60, $latestStart);
            $restIn = $clockIn->copy()->addMinutes($restStartMinute);
            $restOut = $restIn->copy()->addMinutes($restDuration);

            // 休憩時間が重複しないようにチェック
            $overlap = false;
            foreach ($usedSlots as $slot) {
            if ($restIn->between($slot['start'], $slot['end']) || $restOut->between($slot['start'], $slot['end'])) {
            $overlap = true;
                break;
            }
            }

            if ($overlap) {
                continue; // 重複があればスキップ
            }

            // 休憩時間をスロットとして記録
            $usedSlots[] = [
                    'start' => $restIn,
                    'end' => $restOut,
            ];
        
            // Restレコードを作成
            Rest::create([
                    'staff_id' => $work->staff_id,
                    'work_id' => $work->id,
                    'date' => $work->date,
                    'rest_in' => $restIn->toTimeString(),
                    'rest_out' => $restOut->toTimeString(),
                    'total_rest_time' => sprintf('%02d:%02d:00', floor($restDuration / 60), $restDuration % 60),
                    ]);
                }

            // 勤務時間の合計時間を計算
            $workMinutes = $clockOut->diffInMinutes($clockIn);

            // 休憩時間の合計を計算（秒単位）
            $totalRestSeconds = Rest::where('work_id', $work->id)
                    ->get()
                    ->sum(function ($rest) {
                        return Carbon::parse($rest->rest_out)->diffInSeconds(Carbon::parse($rest->rest_in));
                    });

            // 秒を分に変換
            $totalRestMinutes = floor($totalRestSeconds / 60);

            // 勤務時間から休憩時間を差し引く
            $actualWorkMinutes = $workMinutes - $totalRestMinutes;
        
             // 15分単位に丸める
            $actualWorkMinutes = floor($actualWorkMinutes / 15) * 15;

            // HH:MM:SS 形式に変換
            $hours = floor($actualWorkMinutes / 60);
            $minutes = $actualWorkMinutes % 60;
            $formattedTime = sprintf('%02d:%02d:00', $hours, $minutes);

            // Workモデルのtotal_work_timeを更新
            $work->update([
                'total_work_time' => $formattedTime,
                ]);
            });
        }
    }


<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Work;
use Carbon\Carbon;

class WorkLogFactory extends Factory
{
    protected $model = Work::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
            $startDate = Carbon::now()->startOfMonth();

            return [
                'staff_id' => 1, // スタッフIDは仮で設定
                'date' => $startDate->addDays(rand(0, 30)), // ランダムに日付を追加
                'clock_in' => $startDate->copy()->addHours(rand(8, 9))->addMinutes(rand(0, 59)), // ランダムな出勤時間
                'clock_out' => $startDate->copy()->addHours(rand(17, 18))->addMinutes(rand(0, 59)), // ランダムな退勤時間
                'total_work_time' => $startDate->copy()->addMinutes(rand(450, 540)), // 7.5h ~ 9hの間でランダムな作業時間
                'total_rest_time' => $startDate->copy()->addMinutes(rand(30, 60)), // 休憩時間（30分〜1時間）
            ];
    }
}

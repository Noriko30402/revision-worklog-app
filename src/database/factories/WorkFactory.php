<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Work;
use App\Models\Rest;
use Carbon\Carbon;
use App\Models\Staff;

class WorkFactory extends Factory
{
    protected $model = Work::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $baseDate = Carbon::now()->startOfMonth()->copy();
        $randomDate = $baseDate->copy()->addDays(rand(0, 30));

        $clockMinutes = [0, 15, 30, 45];
        $clockIn = $randomDate->copy()
                ->addHours(rand(8, 9))
                ->addMinutes($clockMinutes[array_rand($clockMinutes)]);

        $clockOut = $randomDate->copy()
                ->addHours(rand(17, 18))
                ->addMinutes($clockMinutes[array_rand($clockMinutes)]);

        // 休憩時間をランダムに設定（Restテーブルから紐付ける）
        $rest = Rest::where('date', $randomDate->toDateString())
                        ->where('staff_id', 1)
                        ->first();

        // ランダムなスタッフIDを取得
        $randomStaff = Staff::inRandomOrder()->first();
        $staffId = $randomStaff ? $randomStaff->id : 1; // fallbackで1を入れておく

        // 該当スタッフの休憩データを探す（ある場合）
        $rest = Rest::where('date', $randomDate->toDateString())
                ->where('staff_id', $staffId)
                ->first();

        return [
            'staff_id' => $staffId,
            'date' => $randomDate->toDateString(),
            'clock_in' => $clockIn->toTimeString(),
            'clock_out' => $clockOut->toTimeString(),
            'total_work_time' => '00:00:00',
        ];
    }
}

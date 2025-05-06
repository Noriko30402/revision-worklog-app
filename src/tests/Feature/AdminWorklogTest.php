<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\Work;


class AdminWorklogTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // その日の前ユーザーの勤怠情報
    public function test_it_should_display_all_worklog_for_all_users()
    {
        $admin = \App\Models\Admin::first();
        $response = $this->actingAs($admin, 'admin')->get('/admin/index');

        $today = now()->format('Y-m-d');
        $works = Work::whereDate('date', $today)->get();

        foreach ($works as $work) {
            $response->assertSee($work->staff->name);
            $response->assertSee(Carbon::parse($work->clock_in)->format('H:i'));
            $response->assertSee(Carbon::parse($work->clock_out)->format('H:i'));
            $response->assertSee(Carbon::parse($work->total_rest_time)->format('H:i'));
            $response->assertSee(Carbon::parse($work->total_work_time)->format('H:i'));
        }
        $response->assertStatus(200);
    }

    // 現在の日付
    public function test_it_should_display_current_date()
    {
        $admin = \App\Models\Admin::first();
        $response = $this->actingAs($admin, 'admin')->get('/admin/index');

        $response->assertSee(now()->format('Y/m/d'));
    }
}
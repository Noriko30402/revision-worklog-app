<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class StaffDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }
//勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_it_should_display_selected_attendance_name()
    {
        $staff = \App\Models\Staff::factory()->create();
        $work = \App\Models\Work::factory()->create([
            'staff_id' => $staff->id,
        ]);

        $this->actingAs($staff, 'staff');

        $response = $this->get('/staff/detail/' . $work->id);
        $response->assertStatus(200);
        $response->assertSee($staff->name);
}


public function test_it_should_display_selected_attendance_date()
{
    $staff = \App\Models\Staff::factory()->create();
    $work = \App\Models\Work::factory()->create([
        'staff_id' => $staff->id,
    ]);

    $this->actingAs($staff, 'staff');

    $response = $this->get('/staff/detail/' . $work->id);
    $response->assertStatus(200);
    $response->assertSee($staff->name);
    $response->assertSee(\Carbon\Carbon::parse($work->date)->isoFormat('YYYY年M月D日'));
}

    public function test_it_should_display_selected_attendance_worklog_in_attendance_detail_page()
{
    $staff = \App\Models\Staff::factory()->create();
    $work = \App\Models\Work::factory()->create([
        'staff_id' => $staff->id,
    ]);

    $this->actingAs($staff, 'staff');

    $response = $this->get('/staff/detail/' . $work->id);

    $response->assertStatus(200);
    $response->assertSee(\Carbon\Carbon::parse($work->clock_in)->format('H:i'));
    $response->assertSee(\Carbon\Carbon::parse($work->clock_out)->format('H:i') );
}

}

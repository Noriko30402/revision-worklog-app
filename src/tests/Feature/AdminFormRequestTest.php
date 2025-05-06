<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class AdminFormRequestTest extends TestCase
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


// 勤怠詳細画面に表示されるデータが選択したものになっている
    public function test_should_display_selected_worklog_data_in_detail()
    {
        $staff = \App\Models\Staff::factory()->create();
        $work = \App\Models\Work::factory()->create([
            'staff_id' => $staff->id,
        ]);

        $admin = \App\Models\Admin::first();
        $response = $this->actingAs($admin, 'admin');

        $response = $this->get('/admin/detail/' . $work->id);
        $response->assertStatus(200);
        $response->assertSee($staff->name);
        $response->assertSee(\Carbon\Carbon::parse($work->date)->isoFormat('YYYY年M月D日'));
        $response->assertSee(\Carbon\Carbon::parse($work->clock_in)->format('H:i'));
        $response->assertSee(\Carbon\Carbon::parse($work->clock_out)->format('H:i') );
    }

// 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
public function test_it_should_display_error_when_clock_in_is_after_clock_out()
{
    $staff = \App\Models\Staff::factory()->create();
    $work = \App\Models\Work::factory()->create([
        'staff_id' => $staff->id,
    ]);

    $admin = \App\Models\Admin::first();
    $response = $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/detail/' . $work->id);
    $response->assertStatus(200);

    $response = $this->put('/admin/edit/' . $work->id,[
        'clock_in' => '10:00',
        'clock_out' => '9:00',
        'comment' => 'テストコメント'
    ]);

    $response->assertSessionHasErrors(['clock_in']);
    $this->assertContains('出勤時間もしくは退勤時間が不適切な値です', session('errors')->get('clock_in'));
}
// 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
public function test_it_should_display_error_when_rest_in_is_after_work_out()
{
    $staff = \App\Models\Staff::factory()->create();
    $work = \App\Models\Work::factory()->create([
        'staff_id' => $staff->id,
    ]);

    $admin = \App\Models\Admin::first();
    $response = $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/detail/' . $work->id);
    $response->assertStatus(200);

    $response = $this->put('/admin/edit/' . $work->id,[
        'clock_in' => '10:00',
        'clock_out' => '18:00',
        'rest_in' => ['19:00'],
        'rest_out' => ['19:30'],
        'comment' => 'テストコメント'
    ]);

    $response->assertSessionHasErrors(['rest_in.0']);
    $this->assertContains('休憩開始時間が勤務時間外です', session('errors')->get('rest_in.0'));
}

// 備考欄が未入力の場合のエラーメッセージが表示される
public function test_it_should_display_error_when_comment_is_null()
{
    $staff = \App\Models\Staff::factory()->create();
    $work = \App\Models\Work::factory()->create([
        'staff_id' => $staff->id,
    ]);

    $admin = \App\Models\Admin::first();
    $response = $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/detail/' . $work->id);
    $response->assertStatus(200);

    $response = $this->put('/admin/edit/' . $work->id,[
        'clock_in' => '10:00',
        'clock_out' => '18:00',
        'rest_in' => ['19:00'],
        'rest_out' => ['19:30'],
        'comment' => ''
    ]);

    $response->assertSessionHasErrors(['comment']);
    $this->assertContains('備考を記入してください。', session('errors')->get('comment'));
}
}
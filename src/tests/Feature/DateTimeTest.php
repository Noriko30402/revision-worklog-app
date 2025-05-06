<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;
use Carbon\Carbon;

class DateTimeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //日時が現在日時と一致
    public function  test_current_date_time_is_displayed_correctly()
    {

        $staff = \App\Models\Staff::factory()->create();
        $response = $this->actingAs($staff, 'staff')->get('/staff/attendance');

        $formatted_date = Carbon::now()->isoFormat('Y年M月D日（ddd）');
        $now_time = Carbon::now()->isoFormat('HH:mm');

        $response->assertStatus(200);
        $response->assertSee($formatted_date);
        $response->assertSee($now_time);
    }
}


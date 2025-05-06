<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class StatusTest extends TestCase
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

    public function  test_non_working_hours_is_displayed()
    {

        $staff = \App\Models\Staff::factory()->create();
        $response = $this->actingAs($staff, 'staff')->get('/staff/attendance');


        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

}

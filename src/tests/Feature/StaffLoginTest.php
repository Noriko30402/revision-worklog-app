<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Staff;

class StaffLoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_staff_can_login()
    {
        $staff = Staff::find(2);

        $response = $this->post('/login', [
            'email' => 'staff@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($staff, 'staff');
    }
}

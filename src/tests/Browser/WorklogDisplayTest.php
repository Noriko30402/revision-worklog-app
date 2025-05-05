<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;



class WorklogDisplayTest extends  DuskTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_date_time_displayed_correctly()
    {
        $now = now()->format('Y-m-d H:i');

        $this->browse(function (Browser $browser) use ($now) {
            // staffでログイン
            $browser->visit('/staff/login')
                    ->type('email', 'general1@gmail.com')
                    ->type('password', 'password')
                    ->press('ログインする')
                    ->waitForLocation('staff/attendance');
            $browser->assertSeeIn('.date', $now);
            $browser->assertSeeIn('.time', $now);
        });
    }
    }

<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Staff;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;



class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fortify::createUsersUsing(CreateNewUser::class);

        // Fortify::registerView(function () {
        // return view('auth.register');
        // });

        // Fortify::loginView(function () {
        // return view('auth.login');
        // });

        // RateLimiter::for('login', function (Request $request) {
        // $email = (string) $request->email;

        // return Limit::perMinute(10)->by($email . $request->ip());
        // });

        Fortify::authenticateUsing(function (Request $request) {
            $credentials = $request->only('email', 'password');

            // 管理者ログイン
            if ($request->is('admin/*')) {
                return Auth::guard('admin')->attempt($credentials) ? Auth::guard('admin')->user() : null;
            }

            // スタッフログイン
            return Auth::guard('staff')->attempt($credentials) ? Auth::guard('staff')->user() : null;
        });
        app()->bind(FortifyLoginRequest::class, LoginRequest::class);
    }

}

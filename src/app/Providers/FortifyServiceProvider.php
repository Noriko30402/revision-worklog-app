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
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;



class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/index');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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

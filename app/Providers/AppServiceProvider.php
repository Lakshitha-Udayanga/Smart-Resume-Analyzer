<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;


class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // $this->registerPolicies();

        // Passport::routes();

        view()->composer('layouts.header', function ($view) {
            $notifications = \App\Models\Notification::where('is_read', false)->latest()->limit(10)->get();
            $view->with([
                'unreadNotifications' => $notifications,
                'unreadCount' => $notifications->count(),
            ]);
        });
    }
}

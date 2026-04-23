<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use App\Models\Profile;

class AppServiceProvider extends ServiceProvider
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
        if (app()->environment('production')) {
            try {
                // Only run if table is empty
                if (Profile::count() === 0) {
                    Artisan::call('migrate', ['--force' => true]);
                    Artisan::call('db:seed', [
                        '--class' => 'ProfileSeeder',
                        '--force' => true
                    ]);
                }
            } catch (\Exception $e) {
                // optional: log error
            }
        }
    }
}

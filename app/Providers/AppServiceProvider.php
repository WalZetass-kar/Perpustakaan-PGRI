<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Pengaturan;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        try {
            if (Schema::hasTable('pengaturan')) {
                View::composer('*', function ($view) {
                    static $cachedSettings = null;
                    if ($cachedSettings === null) {
                        $cachedSettings = Pengaturan::all()->pluck('value', 'key');
                    }
                    $view->with('pengaturan', $cachedSettings);
                });
            }
        } catch (\Exception $e) {
        }
    }
}

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
                    $pengaturan = Pengaturan::all()->pluck('value', 'key');
                    $view->with('pengaturan', $pengaturan);
                });
            }
        } catch (\Exception $e) {
        }
    }
}

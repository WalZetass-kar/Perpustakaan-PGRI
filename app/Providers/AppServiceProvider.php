<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Pengaturan;
use App\Models\Peminjaman;

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

            if (Schema::hasTable('peminjaman')) {
                View::composer('layouts.dashboard', function ($view) {
                    $pendingCount = Peminjaman::where('status', 'pending')->count();
                    $view->with('pendingRequestsCount', $pendingCount);
                });
            }
        } catch (\Exception $e) {
        }
    }
}

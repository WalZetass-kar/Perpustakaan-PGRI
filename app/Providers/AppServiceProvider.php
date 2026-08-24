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
            View::composer('*', function ($view) {
                static $cachedSettings = null;
                if ($cachedSettings === null) {
                    try {
                        if (Schema::hasTable('pengaturan')) {
                            $cachedSettings = Pengaturan::all()->pluck('value', 'key');
                        } else {
                            $cachedSettings = collect();
                        }
                    } catch (\Throwable $e) {
                        $cachedSettings = collect();
                    }
                }
                $view->with('pengaturan', $cachedSettings);
            });

            View::composer('layouts.dashboard', function ($view) {
                try {
                    if (Schema::hasTable('peminjaman')) {
                        $pendingCount = Peminjaman::where('status', 'pending')->count();
                        $view->with('pendingRequestsCount', $pendingCount);
                    } else {
                        $view->with('pendingRequestsCount', 0);
                    }
                } catch (\Throwable $e) {
                    $view->with('pendingRequestsCount', 0);
                }
            });
        } catch (\Throwable $e) {
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

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

            // Angka-angka yang menempel di sidebar, jadi harus tersedia di
            // setiap halaman pengelola — bukan hanya di dashboard.
            View::composer('layouts.dashboard', function ($view) {
                $kosong = [
                    'pendingRequestsCount' => 0,
                    'activeLoansCount'     => 0,
                    'overdueLoansCount'    => 0,
                ];

                try {
                    if (!Schema::hasTable('peminjaman')) {
                        $view->with($kosong);
                        return;
                    }

                    $view->with([
                        'pendingRequestsCount' => Peminjaman::where('status', 'pending')->count(),
                        'activeLoansCount'     => Peminjaman::where('status', 'dipinjam')->count(),
                        'overdueLoansCount'    => Peminjaman::where('status', 'dipinjam')
                            ->whereDate('tanggal_jatuh_tempo', '<', now()->toDateString())
                            ->count(),
                    ]);
                } catch (\Throwable $e) {
                    $view->with($kosong);
                }
            });
        } catch (\Throwable $e) {
        }
    }
}

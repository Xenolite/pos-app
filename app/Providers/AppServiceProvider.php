<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
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
        // Aplikasi ini pakai Bootstrap (lewat CDN) untuk semua styling,
        // bukan Tailwind. Tapi pagination link() bawaan Laravel defaultnya
        // pakai view 'pagination::tailwind', yang mengandalkan class
        // Tailwind (mis. h-5 w-5) untuk mengecilkan ikon panah SVG-nya.
        // Kalau build Tailwind/Vite di server (Railway) tidak berhasil,
        // class itu tidak ter-apply dan ikon panahnya muncul dalam ukuran
        // mentah/besar. useBootstrapFive() bikin pagination pakai markup
        // Bootstrap standar yang sudah pasti ke-load lewat CDN di layout,
        // jadi tidak bergantung sama sekali pada proses build CSS lokal.
        Paginator::useBootstrapFive();

        DB::listen(function ($query) {
        if (str_contains($query->sql, 'is_active')) {
            Log::info('IS_ACTIVE QUERY', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10)
            ]);
        }
    });
    }
}

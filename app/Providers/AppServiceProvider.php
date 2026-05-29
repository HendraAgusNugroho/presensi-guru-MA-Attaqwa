<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Railway MySQL plugin — map MYSQL* ke koneksi Laravel
        if (env('MYSQLHOST')) {
            config([
                'database.connections.mysql.host'     => env('MYSQLHOST'),
                'database.connections.mysql.port'     => env('MYSQLPORT', '3306'),
                'database.connections.mysql.database' => env('MYSQLDATABASE'),
                'database.connections.mysql.username' => env('MYSQLUSER'),
                'database.connections.mysql.password' => env('MYSQLPASSWORD'),
            ]);
        }

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Gunakan Bootstrap pagination agar sesuai dengan CSS custom project
        Paginator::useBootstrapFour();
    }
}

<?php

namespace App\Providers;

use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\Cache\LaravelCache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('botman', function () {
            return BotManFactory::create([
                'telegram_token' => env('TELEGRAM_TOKEN'),
            ], new LaravelCache);
        });
    }
}

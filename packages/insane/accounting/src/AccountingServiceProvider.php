<?php
namespace Insane\Accounting;

use Illuminate\Support\ServiceProvider;
use Insane\Accounting\Console\InstallCommand;

class AccountingServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    public function register()
    {

    }
}

<?php

namespace Mralgorithm\JobRunner;

use Illuminate\Support\ServiceProvider;
use Mralgorithm\JobRunner\Console\Commands\RunBackgroundJobs;

class JobRunnerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            RunBackgroundJobs::class
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views','job-runner');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->publishes([
            __DIR__ . '/../config/jobrunner.php' => config_path('jobrunner.php'),
        ]);
    }
}

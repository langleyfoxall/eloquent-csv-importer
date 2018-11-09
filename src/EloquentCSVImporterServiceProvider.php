<?php

namespace LangleyFoxall\EloquentCSVImporter;

use Illuminate\Support\ServiceProvider;

class EloquentCSVImporterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishMigrations();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Publish the CSV Definitions migrations file
     */
    protected function publishMigrations()
    {
        $stub = __DIR__ . '/Migrations/';
        $target = database_path('migrations') . '/';
        $this->publishes([
            $stub . 'create_csv_definitions_table.php' => $target . date('Y_m_d_His', time()).'_create_csv_definitions_table.php',
        ], 'migrations');
    }
}

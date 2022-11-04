<?php

namespace Nio\LaravelInstaller\Helpers;

use Illuminate\Support\Facades\DB;

trait MigrationsHelper
{
    /**
     * Get the migrations in /database/migrations and custom paths.
     *
     * @return array Array of migrations name, empty if no migrations are existing
     */
    public function getMigrations()
    {
        $migrator = app('migrator');
        $paths = $migrator->paths();
        array_push($paths, database_path('migrations'));
        $migrations = $migrator->getMigrationFiles($paths);

        return $migrations;
    }

    /**
     * Get the migrations that have already been ran.
     *
     * @return \Illuminate\Support\Collection List of migrations
     */
    public function getExecutedMigrations()
    {
        // migrations table should exist, if not, user will receive an error.
        return DB::table('migrations')->get()->pluck('migration');
    }
}

<?php

namespace Szwss\GradeTree;

use Illuminate\Support\ServiceProvider;

/**
 * Class NodeCategoriesProvider
 * @package VergilLai\NodeCategories
 * @author Vergil <vergil@vip.163.com>
 */
class GradeTreeProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register commands
        $this->commands('command.grade-tree.migration');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    private function registerCommands()
    {
        $this->app->singleton('command.grade-tree.migration', function ($app) {
            return new MigrationCommand();
        });
    }


}

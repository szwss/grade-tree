<?php

namespace Szwss\GradeTree;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Artisan Migration Command
 * @package VergilLai\NodeCategories
 * @autohr Vergil <vergil@vip.163.com>
 */
class MigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grade-tree:migration {--table=grade_trees : Tablename} {--softDelete=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a GradeTree migration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table = $this->option('table');
        $softDelete = $this->option('softDelete') == 'true';

        if ($this->tableExists($table)) {
            $this->error('table is exists!');
        } else {
            if ($this->createMigration($table, $softDelete)) {
                $this->info('Migration created successfully.');//Artisan::call('migrate');
            } else {
                $this->error(
                    "Couldn't create migration.\n Check the write permissions".
                    " within the database/migrations directory."
                );
            }
        }

        $this->line('');
    }


    private function tableExists($table)
    {
        $dbname = DB::getDatabaseName();

        $field = 'Tables_in_' . $dbname;

        $tables = [];
        foreach(DB::select('SHOW TABLES') as $data) {
            $tables[] = $data->$field;
        }
        return in_array($table, $tables);
    }

    private function createMigration($table, $softDelete = false)
    {
        $this->laravel->view->addNamespace(__NAMESPACE__, __DIR__ . '/views');
        $migrationFile = base_path("/database/migrations")."/".date('Y_m_d_His')."_create_{$table}_table.php";

        $className = 'Create'.studly_case($table).'Table';

        $data = compact('table', 'className', 'softDelete');
        $output = $this->laravel->view->make(__NAMESPACE__ . '::migration')->with($data)->render();

        if (!is_file($migrationFile) && is_writable(dirname($migrationFile))) {
            file_put_contents($migrationFile, $output);
            return true;
        }
        return false;
    }



}

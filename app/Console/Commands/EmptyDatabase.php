<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmptyDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empties the database by truncating all tables, while keeping the table structure.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();

        // Get all tables from the database
        $tables = DB::select('SHOW TABLES');

        // Loop through the tables and truncate them
        $this->info('Truncating tables...');

        foreach ($tables as $tableObject) {
            $tableName = array_values((array) $tableObject)[0];

            // Exclude system/framework tables
            if (! in_array($tableName, ['migrations', 'failed_jobs', 'jobs'])) {
                DB::table($tableName)->truncate();
                $this->comment("Table '{$tableName}' truncated.");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('Database has been successfully emptied.');

        return 0;
    }
}

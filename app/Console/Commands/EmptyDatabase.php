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

        $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        $this->info('Truncating tables...');

        foreach ($tables as $table) {
            if ($table !== 'migrations') {
                DB::table($table)->truncate();
                $this->comment("Table '{$table}' truncated.");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('Database has been successfully emptied.');

        return 0;
    }
}

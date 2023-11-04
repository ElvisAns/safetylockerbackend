<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncInstance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:instance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run git pull, migrate, and optimize:clear commands';

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
     * @return int
     */
    public function handle()
    {
        $this->info('Running git pull...');
        shell_exec('git pull');

        $this->info('Running migrations...');
        $this->call('migrate');

        $this->info('Clearing optimization cache...');
        $this->call('optimize:clear');

        $this->info('Command completed successfully.');
    }
}

<?php

namespace Presentation\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FlushCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all the cache DB';

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
     */
    public function handle()
    {
        Cache::getStore()->flush();
        $this->info('All cache registers were successfully cleaned');
    }
}

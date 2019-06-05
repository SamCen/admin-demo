<?php

namespace App\Console\Commands;

use App\Contract\RedisKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ClearMenusCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $keys = [
            RedisKey::ADMIN_PRIVILEGES,
            RedisKey::ADMIN_MENUS,
        ];
        foreach ($keys as $key){
            Redis::del($key);
        }
        $this->info('done');
    }
}
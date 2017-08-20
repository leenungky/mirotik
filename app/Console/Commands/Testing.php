<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \DB;
use App\Lib\RouterosApi;

class Testing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing crontab working';

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
        $dir = date("Y-m-d-h-i-s");
        mkdir($dir);
    }    
}

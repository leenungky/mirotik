<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \DB;
use App\Lib\RouterosApi;

class Removed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Removed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removed by system when room or meeting room finished';

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
        $api = new RouterosApi();
        $api->debug = false;
        $api->port = 8729;
        $api->ssl = true;
        $api->timeout = 30;
        $path = "/ip/hotspot";        
        $connect = array("host" => "202.169.46.205", "user" => "nungky", "password" => "cabin888");
        if ($api->connect($connect["host"], 
                $connect["user"], 
                $connect["password"])) {
        
            $mikrotiks = DB::table("mikrotik")->where("to", "<", date("Y-m-d"))->get();
            foreach ($mikrotiks as $key => $value) {
                $arrUpdate = array("deleted_at" => date("Y-m-d h:i:s"), "deleted_by"=>-1);
                DB::table("mikrotik")->where("id", $value->id)->update($arrUpdate);            
                $remove=$api->comm($path."/user/remove",Array(              
                  ".id" => $value->mikrotik_id,
                ));         
            }           
            
            $api->disconnect();             
        }
    }    
}

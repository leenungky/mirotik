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
            $mikrotiks = DB::table("mikrotik")->where("checkout", "=", date("Y-m-d"))->get();
            foreach ($mikrotiks as $key => $value) {
                $report =  array( "room" =>$value->room, "name"=>$value->room, "user_id"=>-1, "action" => "delete", "created_at" => date("Y-m-d h:i:s")) ;
                DB::table("report")->insert($report);
                DB::table("mikrotik")->where("id", $value->id)->delete();
                $remove=$api->comm($path."/user/remove",Array(              
                  ".id" => $value->mikrotik_id,
                ));         
            }           
            DB::table("mikrotik")->where("checkout", "<=", "'".date("Y-m-d")."'")->delete();            
            
            $api->disconnect();             
        }
    }   


}

<?php

namespace App\Jobs;

use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SynRedisEm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $redis = app("redis");
        $current_id = $redis ->get("maxemid");
        if(!$current_id){
            $current_id =0;
        }
        Email::where("id",">",$current_id)->chunk(10000 , function ($emails) use ($redis){
            $redis->pipeline(function ($pipe) use ($emails){
                foreach ($emails as $email){
                    $key = "list:email:{$email ->status}";
                    $pipe ->lpush($key, $email ->email);
                    $pipe->hmset("email:{$email->email}",$email -> toArray());
                }
                $pipe -> set("maxacid",$email->id);
            });
        });
        
    }
}

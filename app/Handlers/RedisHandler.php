<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 15:04
 */
namespace App\Handlers;

use App\Jobs\SyncMysql;
use App\Jobs\SynRedisEm;
use Carbon\Carbon;
use Encore\Admin\Config\ConfigModel;
use Illuminate\Support\Facades\DB;

class RedisHandler{
    //数据库数据到redis   ok
    static public function saveModel(Model $model, $key)
    {
        $redis = app('redis');
        $redis->del([$key]);
        $data = $model->toArray();
        foreach ($data as $v => $val)
        {
            if(is_array($val)){
                $data[$v] = json_encode($val);
            }
        }
        $redis->hmset($key, $data);
    }

    static public function syncConfig()
    {
        $configs = ConfigModel::all();
        $configs->each(function ($config) {
            self::saveModel($config, "config:{$config->name}");
        });
    }
    //同步email到redis jobs   ok
    static public function syncEmail()
    {
        dispatch(new SynRedisEm());
    }
    static public function  Hhx(){
        file_put_contents('Hhx.txt' , var_export(time(), true),FILE_APPEND);
    }
    static public function  ClearToday(){
        $redis = app('redis');
        //找到每日的
        $keys = $redis->keys("today:*");
        if ($keys) $redis->del($keys);
    }
    static public function  SyncToday(){
        $redis = app('redis');
        //1成功 2 失败
        $succ =$redis->get("today:1");
        $file =$redis->get("today:2");
        $now =Carbon::now()->toDateTimeString();
        $today = Carbon::today()->toDateTimeString();
        $data =DB::table("schedule")->where('created_at', '>=', $today)->where('created_at', '<=', $now)->get()->first();
        if(!$data){
            DB::table("schedule")->insert(["success_amount"=>$succ,"failed_amount"=>$file,"created_at"=>$now,"today_num"=>$succ+$file]);
        }else{
            DB::table("schedule")->where('created_at', '>=', $today)->where('created_at', '<=', $now)->update(["success_amount"=>$succ,"failed_amount"=>$file,"created_at"=>$now,"today_num"=>$succ+$file]);
        }
    }

    static public function SyncTomysql()
    {
        dispatch(new SyncMysql());
    }

}
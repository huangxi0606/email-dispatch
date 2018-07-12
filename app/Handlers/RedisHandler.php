<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 15:04
 */
namespace App\Handlers;

use App\Jobs\SynRedisEm;
use Encore\Admin\Config\ConfigModel;

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
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 16:34
 */
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;

class DeviceController extends ApiController{
       //取出email
    public function getEmail(Request $request)
    {
        ini_set('default_socket_timeout', -1);
        try {
            $redis = app("redis");
            $status = $request->get("status");
            if(empty($status)){
                return $this->error("状态不能为空");
            }
            //找出符合条件的账号(只有email未知)
            $email = $redis->rpop("list:email:" . $status);
            if (!$email) {
                return $this->error("合适的邮箱不存在");
            }
            $key = "email:" . $email;
            $email = $redis->hgetall($key);
            $data = [
                'email' => $email['email'],
            ];
            if (count($data) == 0) {
                return $this->error("合适的邮箱不存在");
            }
//            $log['now_time'] = Carbon::now()->toDateTimeString();
//            $log['email'] = $account['email'];
//            $log['status'] = $status;
//            $redis->hmset("old:account:" . $account['email'], $log);
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
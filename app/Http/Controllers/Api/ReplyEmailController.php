<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 16:34
 */
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;

class ReplyEmailController extends ApiController{
       //回执email
    public function replyEmail(Request $request)
    {
        ini_set('default_socket_timeout', -1);
        try {
            $redis = app("redis");
//            $data =$request ->getContent();
//            var_dump(9990);exit;
            $redis = app("redis");
            $reply =json_decode($request ->getContent());
            foreach($reply->json as $key =>$value){
                foreach($value as $n =>$v){
                    //n为邮箱 v为状态
                    //(测试为0 是否可以)
                    //全部状态(个数)
                    $redis->incr("count");
                    //全部状态个数
                    $key = "num:" . $v;
                    $redis->incr($key);
                    //today(定时任务0点清零)
                    $today = "today:" . $v;
                    $redis->incr($today);
                    $redis->hset("email:".$n, "status", $v);
                }
            }
            $data = [
                'message' => '回执成功',
            ];
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
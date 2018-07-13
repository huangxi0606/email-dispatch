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


           //解析json
            $data = [
                'message' => '回执成功',
            ];
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
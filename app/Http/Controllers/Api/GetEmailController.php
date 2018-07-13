<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 16:34
 */
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;

class GetEmailController extends ApiController{
       //取出email
    public function getEmail(Request $request)
    {
        ini_set('default_socket_timeout', -1);
        try {
            $redis = app("redis");
            $status = $request->get("status");
            if(empty($status)){
                $status =0;
            }
            $num= $request->get("num");
            if(!$num){
                $num =1;
//                return $this->error("个数不能为空");
            }
            $email =[];
            $key = "list:email:" . $status;
            $len = $redis -> llen($key);
            if($len==0){
                return $this->error("邮箱已用完");
            }
            if($len <$num){
                $num =$len;
            }
            for ($i =0 ; $i < $num ; $i ++)
            {
                $email[] = $redis->rpop($key);
            }

            $data = [
                'email' => $email,
            ];
            if (count($data) == 0) {
                return $this->error("合适的邮箱不存在");
            }
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
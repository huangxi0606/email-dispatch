<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/12
 * Time: 16:34
 */
namespace App\Http\Controllers\Api;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReplyEmailController extends ApiController{
       //回执email
    public function replyEmail(Request $request)
    {
        ini_set('default_socket_timeout', -1);
        try {
            $redis = app("redis");
            $replys =json_decode(substr(urldecode($request ->getContent()),0,-1));
            if($replys){
                foreach ($replys as $reply){
                    $redis->incr("count");
                    $key ="email:".$reply->emailInfo->email;
                    if($reply->result->status==false){
                        $status =2;
                    }else{
                        $status =1;
                    }
                    $redis->incr("num:" . $status);
                    //today(定时任务0点清零)
                    $redis->incr("today:" . $status);
                    $redis->hset($key, "status", $status);
                    $redis->hset($key, "account", $reply->userInfo->account);
                    $redis->hset($key, "pwd", $reply->userInfo->password);
                    $redis->hset($key, "countryCode", $reply->userInfo->countryCode);
                    $redis->hset($key, "lastName", $reply->userInfo->lastName);
                    $redis->hset($key, "firstName", $reply->userInfo->firstName);
                    $redis->hset($key, "birthYear", $reply->userInfo->birthYear);
                    $redis->hset($key, "birthMonth", $reply->userInfo->birthMonth);
                    $redis->hset($key, "birthDay", $reply->userInfo->birthDay);
                    $redis->hset($key, "question", serialize($reply->userInfo->question));
                    $redis->hset($key, "answer", serialize($reply->userInfo->answer));
                    $redis->hset($key, "billingLastName", $reply->userInfo->billingLastName);
                    $redis->hset($key, "billingFirstName", $reply->userInfo->billingFirstName);
                    $redis->hset($key, "addressOfficialLineFirst", $reply->userInfo->addressOfficialLineFirst);
                    $redis->hset($key, "addressOfficialPostalCode", $reply->userInfo->addressOfficialPostalCode);
                    $redis->hset($key, "addressOfficialCity", $reply->userInfo->addressOfficialCity);
                    $redis->hset($key, "addressOfficialStateProvince", $reply->userInfo->addressOfficialStateProvince);
                    $redis->hset($key, "addressOfficialCountryCode", $reply->userInfo->addressOfficialCountryCode);
                    $redis->hset($key, "phoneOfficeNumber", $reply->userInfo->phoneOfficeNumber);
                    $redis->hset($key, "paymentMethodType", $reply->userInfo->paymentMethodType);
                    $redis->hset($key, "reason", $reply->result->reason);
                    $redis->hset($key, "proxy", $reply->proxy);
                    $redis->hset($key, "updated_at", Carbon::now()->toDateTimeString());
                    $redis ->lpush("old:email", $reply->emailInfo->email);
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
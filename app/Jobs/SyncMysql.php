<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/18
 * Time: 10:27
 */


namespace App\Jobs;

use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class SyncMysql implements ShouldQueue
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
        $redis = app("redis");
        //scan
//        $scan = $redis->scan(0,'match','email:*','count',40); //match 和 count 的添加写法
//        $scan = $redis->scan(0,'match','old:*'); //match 和 count 的添加写法
//        var_dump($scan[1]);exit;
//        $reference_at =Carbon::now()->subHour(1)->toDateTimeString();
//        if($scan){
//            foreach ($scan[1] as $v){
//                $email =$redis->hgetall($v);
//                if(isset($email['updated_at']) && $email['updated_at'] <= $reference_at){
//                    $data[]=$email;
//                }
//            }
//            $data = array_chunk($data,2000);
//            foreach ($data as $datums){
//                foreach($datums as $datum){
//                    if(isset($datum['email']) && !empty($datum['updated_at'])){
//                        DB::table("email")->where("email",$datum['email'])->update(["updated_at" =>$datum['updated_at'],"status"=>$datum['status'],"account"=>$datum['account'], "pwd"=>$datum['pwd'], "countryCode"=>$datum['countryCode'], "lastName"=>$datum['lastName'],"firstName"=>$datum['firstName'],
//                        "birthYear"=>$datum['birthYear'],
//                        "birthMonth"=>$datum['birthMonth'],
//                         "birthDay"=>$datum['birthDay'],
//                         "question"=>$datum['question'],
//                         "answer"=>$datum['answer'],
//                         "billingLastName"=>$datum['billingLastName'],
//                         "addressOfficialLineFirst"=>$datum['addressOfficialLineFirst'],
//                         "addressOfficialPostalCode"=>$datum['addressOfficialPostalCode'], "addressOfficialCity"=>$datum['addressOfficialCity'],
//                         "addressOfficialStateProvince"=>$datum['addressOfficialStateProvince'],
//                         "addressOfficialCountryCode"=>$datum['addressOfficialCountryCode'],
//                         "phoneOfficeNumber"=>$datum['phoneOfficeNumber'],
//                         "paymentMethodType"=>$datum['paymentMethodType'],
//                         "reason"=>$datum['reason'],
//                         "proxy"=>$datum['proxy']]);
//                    }
//                }
//            }
//        }
        //换成列表
        $key="old:email";
        $len=$redis->llen($key);
        for ($i =0 ; $i <= $len ; $i ++)
        {
            $email = $redis->rpop($key);
            $datas[] =$redis->hgetall("email:".$email);
//            file_put_contents('email.txt' , var_export($email, true),FILE_APPEND);
        }
        $data = array_chunk($datas,2000);
        foreach ($data as $datums){
                foreach($datums as $datum){
                    if(isset($datum['email']) && !empty($datum['updated_at'])){
                        DB::table("email")->where("email",$datum['email'])->update([
                            "updated_at" =>$datum['updated_at'],
                            "status"=>$datum['status'],
                            "account"=>$datum['account'],
                            "pwd"=>$datum['pwd'],
                            "countryCode"=>$datum['countryCode'],
                            "lastName"=>$datum['lastName'],
                            "firstName"=>$datum['firstName'],
                            "birthYear"=>$datum['birthYear'],
                            "birthMonth"=>$datum['birthMonth'],
                             "birthDay"=>$datum['birthDay'],
                             "question"=>$datum['question'],
                             "answer"=>$datum['answer'],
                             "billingLastName"=>$datum['billingLastName'],
                            "billingFirstName"=>$datum['billingFirstName'],
                             "addressOfficialLineFirst"=>$datum['addressOfficialLineFirst'],
                             "addressOfficialPostalCode"=>$datum['addressOfficialPostalCode'], "addressOfficialCity"=>$datum['addressOfficialCity'],
                             "addressOfficialStateProvince"=>$datum['addressOfficialStateProvince'],
                             "addressOfficialCountryCode"=>$datum['addressOfficialCountryCode'],
                             "phoneOfficeNumber"=>$datum['phoneOfficeNumber'],
                             "paymentMethodType"=>$datum['paymentMethodType'],
                             "reason"=>$datum['reason'],
                             "proxy"=>$datum['proxy']]);
                    }
                }
            }

    }
}

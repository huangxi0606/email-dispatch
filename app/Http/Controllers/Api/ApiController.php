<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/20
 * Time: 13:19
 */

namespace App\Http\Controllers\Api;

use App\Helpers\Api\ApiResponse;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiController extends Controller
{
    use ApiResponse;
    protected $pageSize = 10;
    /**
     * 响应一个http的成功请求
     * @param object $data  要返回的数据
     * @param int $code   成功代码，一般为0 如果传其它代码，客户端将以错误处理
     * @param array $header
     * @return mixed
     */
    public function success ($data,$header = [],$code = 0) {
        $result = array();
        if (is_string($data)){
            $result['message'] = $data;
        }
        else {
            $result['message'] = 'success';
            $result['result'] = $data;
        }
        $result['code'] = $code;

        return $this-> respond($result,$header);
    }

    /*
    * 验证
    * */
    public function apiValidate(Request $request , $rules , $message = array())
    {

        $v = \Illuminate\Support\Facades\Validator::make($request->all(), $rules , $message);
//        if (!empty($message)){
//            dd($message);
//        }
        if ($v->fails())
        {

//            return $this -> error($v -> errors() -> first());
            throw new HttpException(FoundationResponse::HTTP_BAD_REQUEST, $v -> errors() -> first());
        }
    }


}
<?php

namespace App\Http\Controllers\Common;

use App\Repositories\Common\ApiRepository;
use App\Repositories\Common\WechatCommonRepository;
use Illuminate\Http\Request;

class ApiController extends BaseController
{

    public $repository;
    public $token = 'leswLinP2Qxq4pBG5nXBzOCO';

    public function __construct(ApiRepository $apiRepository,
    WechatCommonRepository $wechatCommonRepository)
    {
        $this->repository = $apiRepository;
        $this->wechatCommonRepository = $wechatCommonRepository;
    }

    // 获取七牛上传token
    public function token(Request $request)
    {
        //$result = $this->repository->createToken();
        //return $this->responseResult($result);
        \Log::info(json_encode($_GET));
        $re = $this->checkSignature();
        if ($re && isset($_GET['echostr'])) {
            exit($_GET['echostr']);
        } else {
            exit('fail');
        }
    }

    public function wxpayNotify(Request $request)
    {
        //$result = $this->repository->createToken();
        //return $this->responseResult($result);
        \Log::info('wxpayNotify start');
        \Log::info(json_encode($_GET));
        $this->wechatCommonRepository->wxpayNotify();
        \Log::info('wxpayNotify end');
    }

    private function checkSignature()
    {
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';
        $tmpArr = array($timestamp, $nonce, $this->token);
        sort($tmpArr, SORT_STRING);
	    \Log::info(json_encode($tmpArr));
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
	    \Log::info($tmpStr);		

        if( $signature === $tmpStr){
            return true;
        }else{
            return false;
        }
}

}

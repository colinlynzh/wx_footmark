<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Servers\UserServer;
use App\Repositories\Common\WechatCommonRepository;

class UserController extends CommonController
{
    
    public function __construct(UserServer $userServer)
    {
        parent::__construct();
        $this->server = $userServer;
    }
    
    /**
     * 展示给定用户的信息。
     *
     * @param  int  $id
     * @return Response
     */
    public function index()
    {
        return view('user.profile');
    }

    public function profiles(Request $request)
    {
        $base  = $request->input('base');
        if (!empty($base)) {
            $result = $this->server->currentUserBaseInfo();
        } else {
            $result = $this->server->currentUser();
        }
        return $this->responseResult($result);
    }

    public function updateprofiles(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->server->update($input);
        return $this->responseResult($result);
    }

    public function vippower($recommend=null)
    {
        $wechatCommonRepository = new WechatCommonRepository();
        $wxjs_config = $wechatCommonRepository->getJSConfig();
        $user = $this->server->currentUserBaseInfo();
        return view('user.vip_power', ['wxjs_config'=>$wxjs_config, 'user'=>$user[1], 'recommend'=>$recommend]);
    }

    public function vipPay(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->server->vippay($input);
        return $this->responseResult($result);
    }

    public function getVipPayConfig(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $wechatCommonRepository = new WechatCommonRepository();
        $wxpay_config = $wechatCommonRepository->getWxPayConfig($input);
        $wxpay_config = json_decode($wxpay_config, true);
        return $this->responseResult(['success', $wxpay_config]);
    }

    public function uploadFigureurl(Request $request)
    {
        $base  = $request->input('data');
        $result = [];
        if (!empty($_FILES['files'])) {
            $file = $_FILES['files'];//$request->file('files');
            $result = $this->server->uploadFigureurl($file);
        } 
        return $this->responseResult($result);
    }

    public function vcoinDetail(Request $request)
    {
        $result = $this->server->vcoinDetail();
        return $this->responseResult($result);
    }

    public function customerList(Request $request)
    {
        $result = $this->server->customerList();
        return $this->responseResult($result);
    }
    
}
<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Servers\OrderServer;
use App\Repositories\Common\WechatCommonRepository;

class OrderController extends CommonController
{
    
    public function __construct(OrderServer $orderServer)
    {
        parent::__construct();
        $this->server = $orderServer;
    }
    
    /**
     * 展示给定用户的信息。
     *
     * @param  int  $id
     * @return Response
     */
    public function index()
    {
    }

    public function getWxPayConfig(Request $request)
    {
        $input  = $request->input('data');
        $input['vip_type'] = isset($input['vip_type']) ? $input['vip_type'] : 2;
        $re = $this->server->getWxPayConfig($input);
        return $this->responseResult($re);
    }
}
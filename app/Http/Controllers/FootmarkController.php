<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Servers\FootmarkServer;

class FootmarkController extends CommonController
{
    
    public function __construct(FootmarkServer $footmarkServer)
    {
        parent::__construct();
        $this->server = $footmarkServer;
    }
    
    /**
     * 展示给定用户的信息。
     *
     * @param  int  $id
     * @return Response
     */
    public function index()
    {
        return view('footmark.list');
    }

    public function list(Request $request)
    {
        /*$re = [{
            src: 'images/icon/demo.png',
            fallbackSrc: 'images/icon/demo.png',
            title: '标题一',
            desc: '由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。',
            url: '/component/cell'
        },
         {
            src: 'images/icon/demo.png',
            title: '标题二',
            desc: '由各种物质组成的巨型球状天体，叫做星球。星球有一定的形状，有自己的运行轨道。',
            url: {
            path: '/component/radio',
            replace: false
            },
            meta: {
            source: '来源信息',
            date: '时间',
            other: '其他信息'
            }
        }];*/
        $input  = ['user_id' => session('user_id')]; //$request->input('data');
        $result = $this->server->lists($input);
        return $this->responseResult($result);
    }

    //阅读过的用户列表
    public function footmarklist(Request $request)
    {
        $input  = ['user_id' => session('user_id')]; //$request->input('data');
        $result = $this->server->getFootmarkLists($input);
        // var_dump($result);die;
        return $this->responseResult($result);
    }

    //分享过的用户列表
    public function sharelist(Request $request)
    {
        $input  = ['user_id' => session('user_id')]; //$request->input('data');
        $result = $this->server->getShareLists($input);
        return $this->responseResult($result);
    }

    public function count(Request $request)
    {
        $input  = ['user_id' => session('user_id')]; //$request->input('data');
        $result = $this->server->count($input);
        return $this->responseResult($result);
    }

    public function share(Request $request)
    {
        $result = [];
        $input = [
            'user_id' => session('user_id'),
            'article_id' => $request->input('article_id'),
            'type' => $request->input('type'),
            'from' => $request->input('fromuid'),
        ];
        if ($input['user_id'] == $input['from']) {
            return $this->responseResult([]);
        }
        if (!empty($input['article_id'])) {
            $result = $this->server->share($input);
        }
        return $this->responseResult($result);
    }

    public function detail(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');
        $result = [];
        if (!empty($id) && !empty($type)) {
            $result = $this->server->interactDetail($id, $type);
        }
        return $this->responseResult($result);
    }
}
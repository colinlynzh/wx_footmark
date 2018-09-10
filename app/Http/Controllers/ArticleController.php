<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Servers\ArticleServer;
use App\Servers\UserServer;
use App\Repositories\Common\WechatCommonRepository;

class ArticleController extends CommonController
{
    public function __construct(ArticleServer $articleServer)
    {
        parent::__construct();
        $this->server = $articleServer;
    }
    
    
    /**
     * 展示给定用户的信息。
     *
     * @param  int  $id
     * @return Response
     */
    // public function index()
    // {
    //     return view('article.create');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input  = json_decode($request->input('data'), true);
        $result = $this->server->lists($input);
        return $this->responseResult($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input  = $request->input('data');
        $result = $this->server->store($input);
        return $this->responseResult($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!is_numeric($id)) {
            exit('item not found');
        }
        $from = isset($_GET['fromuid']) ? (int)$_GET['fromuid'] : 0; 
        $result = $this->server->share($id, $from);
        $article = $this->server->show($id);
        $wechatCommonRepository = new WechatCommonRepository();
        $wxjs_config = $wechatCommonRepository->getJSConfig();
        $article = $article[1]['list'];
        $article['thumbnail'] = 'http://'.$_SERVER['SERVER_NAME'].'/getimg.php?url='.$article['thumbnail'];
        return view('article.show', ['content'=>$result, 'article'=>$article, 'wxjs_config'=>$wxjs_config ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = $this->server->edit($id);
        return $this->responseResult($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input  = $request->input('data');
        $result = $this->server->update($id, $input);
        return $this->responseResult($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->server->destroy($id);
        return $this->responseResult($result);
    }
}
<?php
namespace App\Http\Controllers;

// use App\Servers\Backend\ApiServer;

class IndexController extends CommonController
{

    // public function __construct(ApiServer $apiServer)
    // {
    //     parent::__construct();
    //     $this->server = $apiServer;
    // }

    public function index()
    {
        return view('index');
    }

    // public function refreshCache()
    // {
    //     $result = $this->server->refreshCache();
    //     return $this->responseResult($result);
    // }

}

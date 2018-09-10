<?php
namespace App\Servers;

use App\Repositories\InteractRepository;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class UserServer extends CommonServer
{

    public function __construct(
        UserRepository $userRepository,
        InteractRepository $interactRepository
    ) {
        $this->userRepository     = $userRepository;
        $this->interactRepository = $interactRepository;
    }

    /**
     * 获取当前登录用户
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function currentUser()
    {
        $info = $this->userRepository->currentUser();
        $article_count = DB::table('articles')
                ->where('user_id', $info['id'])
                ->count();
        switch($info['vip_level']) {
            case 1:
                $vip_desc = "永久终身会员";
            break;
            case 2:
                $vip_desc = time()> $info['vip_time'] ? '钻石会员： 已过期' : '钻石会员： 截止'.date('Y-m-d', $info['vip_time']);
            break;
            default:
                $vip_desc = "普通会员";
            break;
        }
        if(!empty($info['recommend'])) {
            $user_recommend = $this->userRepository->getUserById($info['recommend']);
            $user_recommend = !empty($user_recommend) ? $user_recommend->name : '未知错误';
        } else {
            $user_recommend = '灵恩思维官方';
        } 
        $result = [
                'id' => $info['id'],
                'name' => $info['name'],
                'sex' => $info['sex'],
                'figureurl' => !empty($info['figureurl']) ? $info['figureurl'] : '/images/default.jpg',
                'vip_level' => $info['vip_level'],
                'vip_time' => $info['vip_time'],
                'vip_desc' => $vip_desc,
                'article_count' => $article_count,
                'coin_count' => ((int)$info['vcoin'])/100,
                'customer_count' => $this->userRepository->getRecommendCount($info['id']),
                'vip_power_url' => 'http://'.$_SERVER['SERVER_NAME'].'/vip',
                'vip_recommend_url' => 'http://'.$_SERVER['SERVER_NAME'].'/vip?fromuid='.session('user_id'),
                'xq_code' => !empty($info['xq_code']) ? $info['xq_code'] : '/images/xq_code_default.jpg',
                'recommend' => $user_recommend
            ];
        return ['获取成功', $result];
    }

    /**
     * 获取当前登录用户基础信息
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function currentUserBaseInfo()
    {
        $info = $this->userRepository->currentUser();
        $data = [];
        if (!empty($info)) {
            $data = [
                'name' => $info['name'],
                'sex' => $info['sex'],
                'figureurl' => $info['figureurl'],
                'mobile' => $info['mobile'],
                'city' => $info['city'],
                'province' => $info['province'],
                'district' => $info['district'],
                'company' => $info['company'],
                'work_date' => $info['work_date'],
                'positional' => $info['positional'],
                'summary' => $info['summary'],
                'id' => $info['id'],
                'xq_code' => !empty($info['xq_code']) ? $info['xq_code'] : '/images/xq_code_default.jpg',
                'coin_count' => ((int)$info['vcoin'])/100,
            ];  
        }
        return ['获取成功', $data];
    }

    /**
     * 更新资料
     * @param  Array $input   用户资料
     * @return Array
     */
    public function update($info)
    {
        if (empty($info)) {
            return ['code' => ['x00004', 'system']];
        }

        // 判断用户名是否重复
        // if (!$is_exist = $this->adminRepository->existList([
        //     'username' => $username,
        //     'email'    => ['or', $email],
        //     'id'       => ['!=', getCurrentUserId()],
        // ])) {
        //     return ['code' => ['x00001', 'user']];
        // }
        $data = [
            'name' => $info['name'],
            'sex' => $info['sex'],
            // 'figureurl' => $info['figureurl'],
            'mobile' => $info['mobile'],
            'city' => $info['city'],
            'province' => $info['province'],
            'district' => $info['district'],
            'company' => $info['company'],
            'work_date' => $info['work_date'],
            'positional' => $info['positional'],
            'summary' => $info['summary'],
            'id' => session('user_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ]; 
        $result = $this->userRepository->update($info);
        if ($result) {
            return ['更新成功', $result];
        } else {
            return ['code' => ['x00004', 'system']];
        }
    }

    /**
     * 收藏列表
     * @param  Array $input []
     * @return Array
     */
    public function collectLists($input)
    {
        $search          = isset($input['search']) ? $input['search'] : [];
        $result['lists'] = $this->interactRepository->getCollectLists($search);

        return ['获取成功', $result];
    }

    public function uploadFigureurl($file)
    {
        $max_size = 2*1024*1024;
        if (empty($file['size']) || $file['size']>$max_size) {
            return ['code' => ['x00004', 'file size error']];
        }
        // 获取文件相关信息
        // $originalName = $file->getClientOriginalName(); // 文件原名
        // $ext = $file->getClientOriginalExtension();     // 扩展名
        // $realPath = $file->getRealPath();   //临时文件的绝对路径
        // $type = $file->getClientMimeType();     // image/jpeg

        // 上传文件
        // $filename = date('Y-m-d-H-i-s') . '-' . uniqid() . '.' . $ext;
        // 使用我们新建的uploads本地存储空间（目录）
        // $r = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
        // var_dump($r);die;

        //move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
        $string = strrev($file['name']);
        $array = explode('.',$string);
        $type =  $array[0];
        $save = './figureurl/'.session('user_id').'_'.date('Ymd').'_'.md5($file['name']).'.'.strrev($type);
        $r = Storage::put($save, file_get_contents($file['tmp_name']));
        if ($r) {
            $figureurl = 'http://'.$_SERVER['SERVER_NAME'].'/storage/'.ltrim($save, '.');
            $info = [
                'id' => session('user_id'),
                'updated_at' => date('Y-m-d H:i:s'),
                'xq_code' => $figureurl,
            ];
            $this->userRepository->update($info);
            return ['上传成功', ['save'=>$figureurl]];
        } 
        return ['code' => ['x00004', 'system']];
    }

    public function vcoinDetail($input=[]) {
        $input['user_id'] = session('user_id');
        $list  = $this->userRepository->getVcoinList($input);
        $data = [];
        foreach ($list as $item) {
            $arr['src'] = '#';
            $arr['fallbackSrc'] = '#';
            $arr['title'] =  $item->vcoin > 0 ? '+'.$item->vcoin/100 : '-'.$item->vcoin/100;
            $arr['desc'] = $item->from==1 ? '推荐用户加入VIP奖励' : '提现';
            $arr['desc'] = $item->created_at.' '.$arr['desc'];
            $arr['url'] = '#';
            $data[] = $arr;
        } 
        $result['lists'] = $data;
        return ['获取成功', $result];
    }

    public function customerList($input=[]) {
        $input['user_id'] = session('user_id');
        $list  = $this->userRepository->customerList(session('user_id'));
        $data = [];
        foreach ($list as $item) {
            $arr['src'] = empty($item->figureurl) ? '/images/default.jpg' : $item->figureurl;
            $arr['fallbackSrc'] = empty($item->figureurl) ? '/images/default.jpg' : $item->figureurl;
            $arr['title'] = $item->name;
            $arr['desc'] = $item->created_at.' 推荐成功 -- ';
            $arr['desc'] .= $item->vip_level>0 ? '已充值为VIP' :  ' 普通用户未充值VIP';
            $arr['url'] = '#';
            $data[] = $arr;
        } 
        $result['lists'] = $data;
        return ['获取成功', $result];
    }

    
}

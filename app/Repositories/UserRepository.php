<?php
namespace App\Repositories;


use App\Models\User;
use App\Models\Interact;
use Illuminate\Support\Facades\DB;

class UserRepository extends CommonRepository
{

    public function __construct(
        User $user
    ) {
        parent::__construct($user);
        $this->user           = $user;
    }

    /**
     * 列表
     * @param  array $input 查询条件
     * @return array
     */
    public function getLists($input)
    {
        $default_search = [
            'filter' => ['id', 'title', 'content', 'auther', 'category_id', 'status'],
            'sort'   => [
                'created_at' => 'desc',
            ],
        ];
        $search = $this->parseParams($default_search, $input);
        return [];//$this->model->parseWheres($search)->with('read', 'interact')->paginate();
    }

    
    public function store($source, $title, $thumbnail, $auther)
    {
        $result = $this->model->create([
            'title'       => $title,
            'thumbnail'   => $thumbnail,
            'auther'      => $auther,
            'source'      => $source,
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/store',
            'params' => [
                'title'       => $title,
                'thumbnail'   => $thumbnail,
                'auther'      => $auther,
                'source'      => $source,
            ],
            'text'   => !!$result ? '新增文章成功' : '新增文章失败',
            'status' => !!$result,
        ]);

        return $result;
    }

    public function initWechatUser($open_id, $info=[]) 
    {
        // "openid":" OPENID",
        // " nickname": NICKNAME,
        // "sex":"1",
        // "province":"PROVINCE"
        // "city":"CITY",
        // "country":"COUNTRY",
        // "headimgurl":    "http://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46",
        // "privilege":[ "PRIVILEGE1" "PRIVILEGE2"     ],
        // "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
        $result = []; 
        if ($is_exist = $this->getListByParseWheres([
            'search' => ['open_id' => $open_id]
        ])) {
            return $is_exist;
        }
        if (!empty($info)) {
            $result = $this->model->create([
                    'open_id'       => $open_id,
                    'name' => isset($info['nickname']) ? $info['nickname'] : '',
                    'sex' => isset($info['sex']) ? $info['sex'] : 1,
                    'figureurl' => isset($info['headimgurl']) ? $info['headimgurl'] : '',
                    'recommend' => isset($info['recommend']) ? (int)$info['recommend'] : session('user_recommend'),
            ]);
            //给推荐者奖励
            // if (isset($info['recommend']) && !empty($info['recommend'])) {
            //     $this->model->updateRecommendVcoin($info['recommend']);
            // }

            // 记录操作日志
            Parent::saveOperateRecord([
                    'action' => 'User/store',
                    'params' => [
                    'open_id'       => $open_id,
                    ],
                    'text'   => !!$result ? '新增用户成功' : '新增用户失败',
                    'status' => !!$result,
            ]);
        }

        return $result;
    }

    /**
     * 获取当前登录的用户
     * @return Array
     */
    public function currentUser()
    {
        $result = [];
        if ($is_exist = $this->getDetail(session('user_id'))) {
            return $is_exist;
        }
        return $result;
    }


    public function update($data)
    {
        if (!empty($data) && !empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $result = $this->model->where('id', $id)->update($data);
            return $result;
        }
        return false;
    }    

    public function getUserById($id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function getRecommendCount($uid)
    {
        $re = 0;
        if (!empty($uid)) {
            $re = DB::table('users')
                    ->where('recommend', (int)$uid)
                    ->count();
            }
        return $re;
    }

    public function getVcoinList($input)
    {
        $user_id = $input['user_id'];
        $re = [];
        if (!empty($user_id)) {
            $sql = 'SELECT * FROM `log_vcoins` WHERE user_id = :uid  ORDER BY `id` DESC';
            $re = DB::select($sql, ['uid' => $user_id]);
        }
        return $re;
    }

    public function customerList($uid)
    {
        $re = [];
        if (!empty($uid)) {
            $re = DB::table('users')
                    ->where('recommend', (int)$uid)->get();
        }
        return $re;
    }
    

  
    
}

<?php
namespace App\Repositories;

use App\Models\Article;
use App\Models\Footmark;
use App\Models\User;
use App\Models\Interact;
use Illuminate\Support\Facades\DB;

class FootmarkRepository extends CommonRepository
{

    public function __construct(
        Footmark $footmark,
        Article $article,
        Interact $interact,
        User $user
    ) {
        parent::__construct($footmark);
        $this->article    = $article;
        $this->interact       = $interact;
        $this->user           = $user;
    }

    /**
     * 列表
     * @param  array $input 查询条件
     * @return array
     */
    public function getLists($input)
    {
        $default_input = ['search'=> ['user_id' =>$input['user_id']]];
        $default_search = [
            'filter' => ['id', 'user_id', 'article_id', 'ip_address'],
            'sort'   => [
                'created_at' => 'desc',
            ],
        ];
        $search = $this->parseParams($default_search, $input);
        return $this->model->parseWheres($search)->paginate();
    }

    public function getFootmarkList($input)
    {
        $user_id = $input['user_id'];
        $re = [];
        if (!empty($user_id)) {
            $sql = 'SELECT * FROM `footmarks` WHERE article_owner = :owner GROUP BY `user_id`,`article_id` ORDER BY `id` DESC';
            $re = DB::select($sql, ['owner' => $user_id]);
        }
        return $re;
    }

    public function getCount($input) 
    {
        $user_id = $input['user_id'];
        $re = [];
        if (!empty($user_id)) {
            $sql = 'SELECT count(DISTINCT user_id) as s FROM `footmarks` WHERE article_owner = :owner and created_at BETWEEN :start AND :end';
            $today = DB::select($sql, ['owner' => $user_id, 'start' => date('Y-m-d'), 'end' => date('Y-m-d').' 23:59:59']);
            $sql = 'SELECT count(DISTINCT user_id) as s FROM `footmarks` WHERE article_owner = :owner ';
            $total = DB::select($sql, ['owner' => $user_id]);
            $re = ['today' => $today[0]->s, 'total' => $total[0]->s];
        }
        return $re;
    }

    
    public function store($user_id, $article_id, $article_owner, $from)
    {
        $result = $this->model->create([
            'user_id'       => $user_id,
            'article_id'   => $article_id,
            'article_owner' => $article_owner,
            'from' => $from,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Footmark/store',
            'params' => [
                'user_id'       => $user_id,
                'article_id'   => $article_id,
                'from' => $from,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ],
            'text'   => !!$result ? '新增足迹成功' : '新增足迹失败',
            'status' => !!$result,
        ]);

        return $result;
    }

  
    /**
     * 删除
     * @param  array $id
     * @return array
     */
    public function destroy($id)
    {
        $result = $this->deleteById($id);

        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Article/update',
            'params' => [
                'article_id' => $id,
            ],
            'text'   => $result ? '删除足迹成功' : '删除足迹失败',
            'status' => $result,
        ]);

        return $result;
    }  

    /**
     * 获取浏览列表
     * @param  int $id
     * @return array
     */
    public function getReadLists($id)
    {
        $default_search = [
            'search' => [
                'article_id' => $id,
            ],
        ];
        $result = $this->articleRead->parseWheres($default_search)->with('user')->paginate();
        return $result;
    }


    public function interactDetail($id) {
        $item = $this->model->where('id', $id)->first();
        $user = $this->user->where('id', $item->user_id)->first();
        if (!empty($item->from)) {
            $from_user = $this->user->where('id', $item->from)->first();
        } else {
            $from_user = $this->user->where('id', $item->article_owner)->first();
        }
        $re = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'sex' => $user->sex,
                'figureurl' => $user->figureurl,
                'xq_code' => $user->xq_code,
            ],
            'from_user' => [
                'id' => $from_user->id,
                'name' => $from_user->name,
                'sex' => $from_user->sex,
                'figureurl' => $from_user->figureurl,
                'xq_code' => $from_user->xq_code,
            ]
        ];
        return $re;
    }
}

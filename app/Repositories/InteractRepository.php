<?php
namespace App\Repositories;

use App\Models\Interact;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InteractRepository extends CommonRepository
{

    public function __construct(
        Interact $interact,
        Article $article,
        User $user
    ) {
        parent::__construct($interact);
        $this->article    = $article;
        $this->user           = $user;
    }

    public function getCollectLists($search)
    {
        $search['user_id'] = getCurrentUserId();
        return $this->getInteractLists($search);
    }

    
    
    public function getInteractList($input)
    {
        $user_id = $input['user_id'];
        $re = [];
        if (!empty($user_id)) {
            $sql = 'SELECT interacts.id,interacts.article_id,interacts.user_id,interacts.share,interacts.created_at  FROM `interacts` inner join articles ON interacts.article_id = articles.id  WHERE articles.user_id = :owner ORDER BY interacts.`id` DESC';
            $re = DB::select($sql, ['owner' => $user_id]);
        }
        return $re;
    }
    
    /**
     * 获取收藏列表
     * @param  Int $user_id 用户id
     * @return Object
     */
    public function getInteractLists($search)
    {
        $default_search = [
            'user_id' => getCurrentUserId()
        ];
        $search = array_merge($default_search, $search);
        return $this->model->parseWheres([
            'search' => $search,
            'sort' => [
                'created_at' => 'desc'
            ]
        ])->with('article', 'videoList')->paginate();
        return $this->getPaginateLists($search);
    }

    /**
     * @tpye 1 moment share 2 friend share 3 like 4 hate
     *       
     */
    public function store($user_id, $article_id, $type='1', $from=1)
    {
        $input = [
            'user_id'       => $user_id,
            'article_id'   => $article_id,
            'from' => $from,
        ];
        
        if ($type == 1) {
            $input['share'] = 1;    
        } elseif ($type == 2) {
            $input['share'] = 2;
        } elseif ($type == 3) {
            $input['like'] = 1;
        } elseif ($type == 4) {
            $input['hate'] = 1;
        }

        $result = $this->model->create($input);
        // 记录操作日志
        Parent::saveOperateRecord([
            'action' => 'Footmark/store',
            'params' => [
                'user_id'       => $user_id,
                'article_id'   => $article_id,
                'from' => $from,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ],
            'text'   => !!$result ? '新增互动成功' : '新增互动失败',
            'status' => !!$result,
        ]);

        return $result;
    }

    public function interactDetail($id) {
        $item = $this->model->where('id', $id)->first();
        $user = $this->user->where('id', $item->user_id)->first();
        if (!empty($item->from)) {
            $from_user = $this->user->where('id', $item->from)->first();
        } else {
            $article = $this->article->where('id', $item->article_id)->first(); 
            if (empty($article)) {
                return false;
            }
            $from_user = $this->user->where('id', $article->user_id)->first();
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

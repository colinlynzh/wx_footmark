<?php
namespace App\Repositories;

use App\Models\Article;
use App\Models\User;
use App\Models\Interact;
use App\Models\Footmark;

class ArticleRepository extends CommonRepository
{

    public function __construct(
        Article $article,
        Footmark $footmark,
        Interact $interact,
        User $user
    ) {
        parent::__construct($article);
        $this->footmark    = $footmark;
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
        $default_search = [
            'filter' => ['id', 'title', 'thumbnail', 'auther', 'category_id', 'status', 'share_count', 'read_count', 'created_at', 'source'],
            'sort'   => [
                'sort' => [
                    'created_at' => 'desc',
                ]
            ],
        ];
        $search = $this->parseParams($default_search, $input);
        return  $this->model->parseWheres($search)->with('read', 'interact')->paginate();
    }

    /**
     * 新增
     * @param  int $category_id 菜单id
     * @param  string $title       标题
     * @param  string $thumbnail   缩略图
     * @param  string $auther      作者
     * @param  string $content     内容
     * @param  string $tag_ids     标签id
     * @param  string $source      来源
     * @param  int $is_audit    审核
     * @param  boolean $recommend   推荐
     * @param  int $status      状态
     * @return object
     */
    public function store($source, $title, $thumbnail, $user_id, $auther)
    {
        $result = $this->model->create([
            'title'       => $title,
            'thumbnail'   => $thumbnail,
            'user_id' => $user_id,
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
            'text'   => $result ? '删除文章成功' : '删除文章失败',
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

    /**
     * 获取用户文章总数
     */
    public function articleCount($id) 
    {
        $default_search = [
            'search' => [
                'article_id' => $id,
            ],
        ];
        $result = $this->articleRead->parseWheres($default_search)->with('user')->paginate();
        return $result;
    }

}

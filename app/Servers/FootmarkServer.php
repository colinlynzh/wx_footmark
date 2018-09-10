<?php
namespace App\Servers;

use App\Repositories\FootmarkRepository;
use App\Repositories\InteractRepository;
use App\Models\Article;
use App\Models\User;

class FootmarkServer extends CommonServer
{

    public function __construct(
        FootmarkRepository $footmarkRepository,
        InteractRepository $interactRepository
    ) {
        $this->footmarkRepository  = $footmarkRepository;
        $this->interactRepository  = $interactRepository;
    }

    public function index($input)
    {
        $result['lists']   = $this->footmarkRepository->getLists($input);

        return ['获取成功', $result];
    }

    /**
     * 文章详情
     * @param  int $id 主键
     * @return array
     */
    public function show($id)
    {
        $list = $this->footmarkRepository->getDetail($id);
        if (empty($list)) {
            return ['code' => ['x00001', 'article']];
        }
        $result['list'] = $list;
        return ['获取成功', $result];
    }

    public function lists($input) {
        $result['lists']   = $this->footmarkRepository->getLists($input);
        return ['获取成功', $result];
    }

    public function getFootmarkLists($input) {
        $list  = $this->footmarkRepository->getFootmarkList($input);
        $data = [];
        foreach ($list as $item) {
            $article = Article::find($item->article_id);
            $reader = User::find($item->user_id);
            $arr['src'] = empty($reader->figureurl) ? '/images/default.jpg' : $reader->figureurl;
            $arr['fallbackSrc'] = empty($reader->figureurl) ? '/images/default.jpg' : $reader->figureurl;;
            $arr['title'] = $reader->name;
            $arr['desc'] = $item->created_at.'  阅读文章：'.$article->title;
            $arr['url'] = '/interactdetail/footmark/'.$item->id;
            $data[] = $arr;
        } 
        $result['lists'] = $data;
        return ['获取成功', $result];
    }

    public function getShareLists($input) {
        $list  = $this->interactRepository->getInteractList($input);
        $data = [];
        foreach ($list as $item) {
            $article = Article::find($item->article_id);
            $reader = User::find($item->user_id);
            $share_type = $item->share == 1 ? '朋友圈分享' : '微信好友分享';
            $arr['src'] = empty($reader->figureurl) ? '/images/default.jpg' : $reader->figureurl;
            $arr['fallbackSrc'] = empty($reader->figureurl) ? '/images/default.jpg' : $reader->figureurl;;
            $arr['title'] = $reader->name;
            $arr['desc'] = $item->created_at.'  '.$share_type.'文章： '.$article->title;
            $arr['url'] = '/interactdetail/share/'.$item->id;
            $data[] = $arr;
        } 
        $result['lists'] = $data;
        return ['获取成功', $result];
    }

    public function count($input) {
        $result = $this->footmarkRepository->getCount($input);

        return ['获取成功', $result];
    }

    public function share($input) {
        $result = $this->interactRepository->store($input['user_id'], $input['article_id'], $input['type'], $input['from']);
        return ['获取成功', $result];
    }

    /**
     * 新增文章
     * @param  array $input
     * @return array
     */
    public function store($input)
    {
        $category_id = isset($input['category_id']) ? intval($input['category_id']) : 0;
        $title       = isset($input['title']) ? strval($input['title']) : '';
        $thumbnail   = isset($input['thumbnail']) ? strval($input['thumbnail']) : '';
        $auther      = isset($input['auther']) ? strval($input['auther']) : '';session('user_id');
        $content     = isset($input['content']) ? strval($input['content']) : '';
        $tag_ids     = isset($input['tag_ids']) ? implode(',', $input['tag_ids']) : '';
        $source      = isset($input['link']) ? strval($input['link']) : '';
        $is_audit    = isset($input['is_audit']) ? intval($input['is_audit']) : 0;
        $recommend   = isset($input['recommend']) ? intval($input['recommend']) : 0;
        $status      = isset($input['status']) ? intval($input['status']) : 0;
        $user_id = session('user_id');

        if (!$source) {
            return ['code' => ['x00004', 'system']];
        }
        // $re = \http_get($source);
        //$r = Storage::putFile(md5($source), new File('public'));
        $html = new \simple_html_dom();  
        //$html->load_file('./98b559abac12ee6c63d1c7a0bc6be7fc');
        $html->load_file($source);
        $title = $html->find('title')[0]->text();
        preg_match('/msg_cdn_url = "(.+?)";/', (string)$html, $thumbnail);
        $thumbnail = $thumbnail[1];
        $r = Storage::put(md5($source), (string)$html);
        // $url = Storage::url(md5($source));
        $result = (bool) $this->footmarkRepository->store($category_id, $title, $thumbnail, $user_id, $auther, $content, $tag_ids, $source, $is_audit, $recommend, $status);

        if (!$result) {
            return ['code' => ['x00001', 'system']];
        }
        return ['新增成功', $result];
    }


    /**
     * 删除
     * @param  int|array $id 文章id
     * @return array
     */
    public function destroy($id)
    {
        $result = $this->footmarkRepository->destroy($id);

        if (!$result) {
            return ['code' => ['x00002', 'system']];
        }

        return ['删除成功', $result];
    }

    public function interactDetail($id, $type) {
        if (empty($id)) {
            return ['code' => ['x00002', 'system']];
        }
        if ($type == 'footmark') {
            $result = $this->footmarkRepository->interactDetail($id);
        } elseif ($type == 'share') {
            $result = $this->interactRepository->interactDetail($id);
        }
        if (!$result) {
            return ['code' => ['x00002', 'system']];
        }
        return ['获取成功', $result];
    }
}

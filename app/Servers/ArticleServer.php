<?php
namespace App\Servers;

use App\Repositories\ArticleRepository;
use App\Repositories\FootmarkRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class ArticleServer extends CommonServer
{

    public function __construct(
        ArticleRepository $articleRepository,
        FootmarkRepository $footmarkRepository,
        UserRepository $userRepository

    ) {
        $this->articleRepository  = $articleRepository;
        $this->footmarkRepository = $footmarkRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $result['lists']   = $this->articleRepository->getLists($input);

        return ['获取成功', $result];
    }

    /**
     * 文章详情
     * @param  int $id 主键
     * @return array
     */
    public function show($id)
    {
        $list = $this->articleRepository->getDetail($id);
        if (empty($list)) {
            return ['code' => ['x00001', 'article']];
        }
        $owner = $this->userRepository->getDetail($list['user_id']);
        $list['owner'] = $owner;
        $result['list'] = $list;
        return ['获取成功', $result];
    }

    public function share($id, $from=0)
    {
        $item = $this->articleRepository->getDetail($id);
        if ($item['user_id'] != session('user_id')) {
            //别人查看我的文章
            $this->footmarkRepository->store(session('user_id'), $id, $item['user_id'], $from);
        }
        $file = $item['source'];
        $re = \file_get_contents('.'.Storage::url($item['user_id'].'_'.md5($file)));
        return $re;
    }

    public function lists($input) {
        $input['search']['user_id'] = session('user_id');
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
        $re = $this->articleRepository->getLists($input);
        $data = [];
        foreach ($re as $val) {
            $data[] = [
                'src' => Storage::url(md5($val['thumbnail'])),
                'fallbackSrc' => Storage::url(md5($val['thumbnail'])),
                'title' => $val['title'],
                'desc' => $val['title'],
                'url' => 'http://'.$_SERVER['SERVER_NAME'].'/articles/'.$val['id'],
                'meta' =>  [
                    'source' => sizeof($val['read']).' 阅读',
                    'date' =>  sizeof($val['interact']).' 分享',
                    // 'other' => $val['created_at'],
                ]
            ];
        }
        $result['lists'] = $data;

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
        $auther      = isset($input['auther']) ? strval($input['auther']) : session('user_name');
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
        if (empty($title)) {
            $e = $html->find('.rich_media_title', 0);
            if (!empty($e)) {
                $s = $e->innertext();
                preg_match('/document\.write\("<span class=\'rich_media_title_ios\'>(.*)<\/span>/i', trim($s), $match);
                if (!empty($match)) {
                    $title = $match[1];
                }
            }
        }
        preg_match('/msg_cdn_url = "(.+?)";/', (string)$html, $thumbnail);
        $thumbnail = str_replace('?wx_fmt=jpeg', '', $thumbnail[1]);
        $thumbnail_re = Storage::put(md5($thumbnail), \http_get($thumbnail));

        $date = date('Y-m-d');
        $auth = $auther;
        $source_from = '灵恩思维';
        $source_link = 'http://www.lingensiwei.com';
        $getimg_url = 'http://'.$_SERVER['SERVER_NAME'].'/getimg.php?url=';

        $show_more = <<<LABEL
        <div id="btn" class="lesw_question_mask news_part_all"  style="display: block;color:#01a5ec;text-align: center;">
        <button class="public-handle-btn J-more-article-btn" onclick="article.openDetail(this);">点击查看全文<i class="icon-show-arrow"></i></button>
        </div>
        <style>  
            .lesw_question_mask{ font-size: 1.5rem; position:absolute;bottom:0;width:100%;height:20%;background-image:-webkit-linear-gradient(top,hsla(0,0%,100%,.5),#fff);background-image:linear-gradient(180deg,hsla(0,0%,100%,.5),#fff)}
            .news_part_all span {
                background: url(/images/cont-up.png) no-repeat +3px +11px;
                display: inline-block;
                width: 2rem;
                height: 1.5rem;
                background-size: 1.5rem;
            }
            .public-handle-btn {
                border: 1px solid #EDEDED;
                border-radius: 4px;
                height: 38px;
                font-size: 12px;
                color: #FF4259;
                width: 90%;
                display: -moz-box;
                display: -webkit-box;
                display: box;
                -moz-box-align: center;
                -webkit-box-align: center;
                box-align: center;
                -moz-box-pack: center;
                -webkit-box-pack: center;
                box-pack: center;
                text-align: center;
                background-color: #fff;
                cursor: pointer;
                margin: auto;
                margin-top: 20px;
            }
            .icon-show-arrow {
                display: inline-block;
                vertical-align: middle;
                width: 12px;
                height: 6px;
                background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHUlEQVQ4T82SQU7CYBCF36P+4EbTpYmW6EbbFXgC6Q3wBOINuInxBOIN6gnoDayrohsJjYnLRlbypx3TqgTaoiQkxtlO5pv33gyxYXHDefwTQGipnpEiOH7RwTqWwoOtDmts2RN9nVsYNesCSFxL4P4GedpX7dTAEADtiTZzQKaA5E0GkZk+dV4xrlIS7uGQdXUP0KQk5ydR4s1DHDVVH+AVIEHjTbtHMeJFyLMJ831XDQG2ReTSifQg6y9dIbTUgORFFSS0lE/yTERunUj3vuGlM84hIr4daffL4idYcGdHs+6ishIgl7qjfJAtCDxAxiD7EHloTHWnaK3ykZYg2boVw6UMisk/WkY3Rc3cnmqvuHllBus80o8Z/DngAzZtdBHO1vwDAAAAAElFTkSuQmCC);
                background-size: 100% 100%;
                background-repeat: no-repeat;
            }
            </style>
LABEL;
        
        $e = $html->find('#js_profile_qrcode', 0);
        if (!empty($e)) {
            $e->outertext='';
        }
        $e = $html->find('#post-date', 0);
        if (empty($e)) {
            $e = $html->find('#publish_time', 0);
        }
        if (!empty($e)) {
            $e->innertext =$date;
        }
        $auth_e = $e->next_sibling();
        $auth_em = empty($auth_e) ? '' : $auth_e->outertext;
        if (!preg_match('<em>', $auth_em)) {
            $s = '<em class="rich_media_meta rich_media_meta_text">'.$auth.'</em>';
            $e->outertext .=  $s;
            //改版后的需要把原创作者删除
            if ($html->find('#publish_time', 0)) {
                if (!empty($auth_e)){
                    $e->next_sibling()->outertext = '';
                }
            }
        } else {
            $e->next_sibling()->innertext=$auth;
        }
        
        $e = $html->find('#post-user', 0);
        if (!empty($e)) {
            $e->innertext=$source_from;
            $e->href= $source_link;
            $source_em = $e->next_sibling()->outertext;
            if (!preg_match('<span>', $source_em)) {
                $s = '<span class="rich_media_meta rich_media_meta_text rich_media_meta_nickname">'.$source_from.'</span>';
                $e->outertext .=  $s;
            } else {
                $e->next_sibling()->innertext=$source_from;
            }
        }
        $e = $html->find('#copyright_logo', 0);
        if (!empty($e)) {
            $e->outertext='';
        }
        
        
        $e = $html->find('#js_article', 0);
        $e->innertext = $e->innertext.$show_more;
        
        $html_save = (string)$html;
        $html_save =  str_ireplace('data-src="', 'src="'.$getimg_url, $html_save);

        $r = Storage::put(session('user_id').'_'.md5($source), (string)$html_save);
        $html->clear();
        $result = (bool) $this->articleRepository->store($source, $title, $thumbnail, $user_id, $auther);

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
        $result = $this->articleRepository->destroy($id);

        if (!$result) {
            return ['code' => ['x00002', 'system']];
        }

        return ['删除成功', $result];
    }
}

<?php
namespace App\Repositories\Common;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Qiniu\Auth;
use Cookie;
use App\WxpayAPI\lib\WxPayApi;
use App\WxpayAPI\lib\JsApiPay;
use App\WxpayAPI\lib\WxPayNotifyBusiness;
use App\Repositories\OrderRepository;

class WechatCommonRepository 
{

    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /**
     * 记录操作日志
     * @param  Array  $input [action, params, text, status]
     * @return Array
     */
     public function saveOperateRecord($input)
     {
 
     }

    public function getAccessToken() 
    {
        if (!empty(Cookie::get('wx_access_token'))) {
            return Cookie::get('wx_access_token');
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
            $re = \http_get(sprintf($url, config('wechat.appid'), config('wechat.appsecret')));
            $re = json_decode($re, true);
            Cookie::queue('wx_access_token', $re['access_token'], $re['expires_in']/60);
            return $re['access_token'];
        }
        
    }

    public function oauth($url) 
    {
        //$token = $this->getAccessToken();
        $this->oauthScope($url);
    }

    public function oauthScope($redirect)
    {
        //scope为snsapi_base
        //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx520c15f417810387&redirect_uri=https%3A%2F%2Fchong.qq.com%2Fphp%2Findex.php%3Fd%3D%26c%3DwxAdapter%26m%3DmobileDeal%26showwxpaytitle%3D1%26vb2ctag%3D4_2030_5_1194_60&response_type=code&scope=snsapi_base&state=123#wechat_redirect
        //scope为snsapi_userinfo
        //https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxf0e81c3bee622d60&redirect_uri=http%3A%2F%2Fnba.bluewebgame.com%2Foauth_response.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
        //$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        //$url = sprintf($url, config('wechat.appid'), urlencode($redirect));
        //$redirect = 'http://www.lingensiwei.com';
        $mwebUrl = sprintf($url, config('wechat.appid'), urlencode($redirect));
        header('HTTP/1.1 301 Moved Permanently');
        header("Location :{$mwebUrl}");
        die;
        //$re=mb_convert_encoding($re, 'UTF-8', 'GBK,GB2312,BIG5');
        //var_dump($re, $redirect);die;
        //echo $re;
    }

    public function getOauthData($code) 
    {
        if (empty(session('open_id'))) {
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
            $re = \http_get(sprintf($url, config('wechat.appid'), config('wechat.appsecret'), $code));
            $re = json_decode($re, true);
            if (isset($re['access_token']) && isset($re['openid'])) {
                session(['open_id' => $re['openid']]);
                $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
                $re = \http_get(sprintf($url, $re['access_token'], $re['openid']));
                $re = json_decode($re, true);
                return $re;
            }
        }
        return false;
    }

    public function getJSticket() {
        if (!empty(Cookie::get('wx_js_ticket'))) {
            return Cookie::get('wx_js_ticket');
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
            $access_token = $this->getAccessToken();
            $re = \http_get(sprintf($url, $access_token));
            $re = json_decode($re, true);
            Cookie::queue('wx_js_ticket', $re['ticket'], $re['expires_in']/60);
            return $re['ticket'];
        }
    }

    public function getJSConfig()
    {
        $t = time();
        $jsapi_ticket = $this->getJSticket();
        $noncestr = 'LEWm3WZYTPz0wzccnWSw';
        $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        $string = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$noncestr.'&timestamp='.$t.'&url='.$url;
        $signature = sha1($string);
        $re = [
            'appId' => config('wechat.appid'), // 必填，公众号的唯一标识
            'timestamp' => $t, // 必填，生成签名的时间戳
            'nonceStr' => $noncestr, // 必填，生成签名的随机串
            'signature' => $signature,// 必填，签名
            'jsApiList' => '["onMenuShareTimeline", "onMenuShareAppMessage", "chooseWXPay"]' // 必填，需要使用的JS接口列表
        ];
        return $re;
    }
    
    public function getWxPayConfig($config=[])
    {
        $re = [];
        $tools = new JsApiPay;
        $data = [
            'open_id' => $config['open_id'],//$tools->GetOpenid();
            'body' => $config['body'],
            'attach' => $config['attach'],
            'out_trade_no' => $config['out_trade_no'],
            'total_fee' => $config['total_fee'],
            'goods_tag' => $config['goods_tag'],
            'notify_url' => $config['notify_url']
        ];
        \Log::info('wxpay config : ' . json_encode($data));
        $input = $tools->getUnioOrderInput($data);
        $order = WxPayApi::unifiedOrder($input);
        $re = $tools->GetJsApiParameters($order);
        return $re;
    }

    public function wxpayNotify()
    {
        $notify = new WxPayNotifyBusiness();
        $notify->handle(false);
        // $orderId = 2018040900000001;
        // $notify_amount = 1;
        // $data = [];
        // OrderRepository::checkOrderAmount($orderId, $notify_amount, $data, __FUNCTION__);
        // OrderRepository::updatePurseOrder($orderId);
    }

}

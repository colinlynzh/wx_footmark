<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Common\WechatCommonRepository;
use App\Repositories\UserRepository;
use App\Models\User;
class WechatInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // if (Auth::guard($guard)->check()) {
        //     return redirect('/home');
        // }
        $wechatCommonRepository = new WechatCommonRepository();
        $open_id = $request->session()->get('open_id', '');
        $fromuid = $request->input('fromuid');
        $recommend = $request->route('recommend');
        $fromuid = !empty($recommend) ? (int)$recommend : (int)$fromuid;
        $request->session()->put('user_recommend', $fromuid);
        $request->session()->put('pay_user_recommend', $recommend);
        if (config('wechat.debug')) {
            //colin
            //$open_id = 'odZnM0i6CfAKSXQC7cwXzIzvERwk';
            //john
            $open_id = 'odZnM0ro1cUqSqAu14KQ4-Yau20A';
            $request->session()->put('open_id', $open_id);
        }
        $info = !empty($open_id) ? $this->initUser($open_id) : [];
        if (empty($open_id) || empty($info)) {
            if (isset($_GET['code']) && isset($_GET['state'])) {
                $info = $wechatCommonRepository->getOauthData($_GET['code']);
                if (!empty($info) && isset($info['openid'])) {
                    $request->session()->put('open_id', $info['openid']);
                    $request->session()->put('wechat_info', $info);
                    $info['recommend'] = $fromuid;
                    $info = $this->initUser($info['openid'], $info);
                }
            } else {
                $wechatCommonRepository->oauth($request->url());
            }
        }
        
        if (!empty($info['id'])) {
            $request->session()->put('user_id', $info['id']);
            $request->session()->put('user_name', $info['name']);
        }
        return $next($request);
    }

    public function initUser($open_id, $info=[])
    {
        if (!empty($open_id))
        {
            $userRepository = new UserRepository(new User);
            return $userRepository->initWechatUser($open_id, $info);
        }
        return [];
    }

    
}

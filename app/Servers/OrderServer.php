<?php
namespace App\Servers;

use App\Repositories\Common\WechatCommonRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;

class OrderServer extends CommonServer
{
    const VIP_FOREVER_TOTAL_FEE = 9900;
    const VIP_YEAR_TOTAL_FEE = 3800;

    const PAY_TYPE_WECHAT = 2;
    
    public function __construct(
        OrderRepository $orderRepository,
        WechatCommonRepository $wechatCommonRepository,
        UserRepository $userRepository
    ) {
        $this->wechatCommonRepository  = $wechatCommonRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
    }

    public function getWxPayConfig($input)
    {
        $total_fee = isset($input['vip_type']) && $input['vip_type'] == 1 ? self::VIP_FOREVER_TOTAL_FEE : self::VIP_YEAR_TOTAL_FEE;
        $order_id = $this->generatePayOrderId();
        $recommend_uid = isset($input['recommend_uid']) && !empty($input['recommend_uid']) ?  $input['recommend_uid'] : session('pay_user_recommend');
        if ($recommend_uid == session('user_id')) {
            //不能推荐自己
            $recommend_uid = 0;
        }
        
        $user = $this->userRepository->getUserById(session('user_id'));
        if (empty($user)) {
            return ['code' => ['x00004', '用户不存在!']];
        }
        if ($user->vip_level == 1) {
            return ['code' => ['x00004', '用户已经是永久会员!']];
        }

        $re = $this->orderRepository->store($order_id, self::PAY_TYPE_WECHAT, session('user_id'), $total_fee, (int)$input['vip_type'], (int)$recommend_uid);
        if ($re) { 
            $data = [
                'open_id' => session('open_id'),//$tools->GetOpenid();
                'body' => '灵恩思维会员充值',
                'attach' => '灵恩思维会员充值',
                'out_trade_no' => $order_id,
                'total_fee' => $total_fee,
                'goods_tag' => 'VIP',
                'notify_url' => "http://".$_SERVER['SERVER_NAME'].'/api/wx-notify'
            ];
            $wxpay_config = $this->wechatCommonRepository->getWxPayConfig($data);
            $wxpay_config = json_decode($wxpay_config, true);
            return ['success', $wxpay_config];
        } else {
            return ['fail', []];
        }
    }

    public function generatePayOrderId()
    {
        return $this->orderRepository->generatePayOrderId();
    }


}

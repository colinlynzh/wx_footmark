<?php
namespace App\WxpayAPI\lib;

require_once __DIR__."/WxPayApi.php";
require_once __DIR__."/WxPay.Exception.php";
require_once __DIR__."/WxPay.Config.php";
require_once __DIR__."/WxPay.Data.php";
error_reporting(E_ERROR | E_WARNING | E_PARSE);
use App\Repositories\OrderRepository;

/**
 * 
 * 回调基础类
 * @author widyhu
 *
 */
class WxPayNotifyBusiness extends WxPayNotify
{
	//重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        \Log::info('wxpay notify callback success');
        \Log::info('wxpay notify data'.json_encode($data));
        $verify_result = true;
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            $verify_result = false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            $verify_result = false;
        }
        if ($verify_result) {
            $orderId = $data['out_trade_no'];
            $trade_no = $data['transaction_id'];
            $trade_status = $data['result_code'];
            if ($trade_status == 'SUCCESS') { //支付成功
                //检查第三方回传的金额和数据库的金额是否一致
                $notify_amount = $data['total_fee'];
                OrderRepository::checkOrderAmount($orderId, $notify_amount, $data, __FUNCTION__);
                //业务处理
                $verify_result = OrderRepository::updatePurseOrder($orderId);
                \Log::info('order_id:' . $orderId . ' wxpay notify pay success');
            } else {
                $verify_result = false;
                if ($orderId) {
                    \Log::info('order_id:' . $orderId . ' wxpay notify pay unfinished');
                }
            }
        } else {
            //验证失败
            \Log::info('order_id:' . $orderId . ' wxpay notify verify fail');
        }
        return $verify_result;
    }

    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        //Log::DEBUG("query:" . json_encode($result));
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }
}
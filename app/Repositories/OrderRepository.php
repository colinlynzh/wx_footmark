<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderRepository extends CommonRepository
{

    public function __construct(
        Order $order,
        User $user
    ) {
        parent::__construct($order);
        $this->order    = $order;
        $this->user    = $user;
    }

    public function generatePayOrderId()
    {
        $sql = "REPLACE INTO pay_tickets(stub) VALUES ('a')";
        $re = DB::statement($sql);
        $sql = "SELECT @@IDENTITY AS returnID";
        $re = DB::select($sql);
        // $re = DB::table('pay_tickets')->insertGetId(['stub'=>'a']);
        return date('Ymd') . sprintf("%08d", $re[0]->returnID % 100000000);
    }


    public function store($order_id, $type, $uid, $amount, $product_id, $recommend_uid)
    {
        $result = $this->model->create([
            'order_id'       => $order_id,
            'type'   => $type,
            'uid' => $uid,
            'amount'      => $amount,
            'product_id'      => $product_id,
            'recommend_uid' => $recommend_uid,
        ]);
        return $result;
    }

    public static function checkOrderAmount($order_id, $notify_amount, $data)
    {
        $search = [
            'search' => [
                'order_id' => $order_id
            ]
        ];
        $model = new Order;
        $order = $model->parseWheres($search)->first();
        if (!empty($order)) {
            if (empty($notify_amount) || $order->amount != $notify_amount) {
                \Log::info('order_id:' . $order_id . ' amount fail: '.$notify_amount);
                die('amount fail');
            }
        } else {
            \Log::info('order_id:' . $order_id . ' amount fail: '.$notify_amount);
            die('amount fail');
        }
        return true;
    }

    public static function updatePurseOrder($order_id)
    {
        $search = [
            'search' => [
                'order_id' => $order_id
            ]
        ];
        $model = new Order;
        $order = $model->parseWheres($search)->first();
        if (empty($order) || $order['status']!=0) {
            return false;
        }
        if (!empty($order->product_id) && !empty($order->uid)) {
            $uid = $order->uid;
            $order_id = $order->order_id;
            $time = date('Y-m-d H:i:s');
            if ($order->product_id == 1) {
                DB::beginTransaction();
                $re_u = DB::update('update users set vip_level = 1, updated_at=:up_time where id =:id', ['id'=>$uid, 'up_time'=>$time]);
                $re_p = DB::update('update orders set status = 1, updated_at=:up_time where order_id =:order_id', ['order_id'=>$order_id, 'up_time'=>$time]);
                if (!$re_u || !$re_p) {
                    DB::rollBack();
                    return false;
                }
                DB::commit();
                return true;
            } elseif ($order->product_id == 2) {
                $vip_time = !empty($order->vip_time) ? (int)$order->vip_time : time();
                $vip_time += 365*86400;
                $uid = $order->uid;
                $order_id = $order->order_id;
                DB::beginTransaction();
                $re_u = DB::update('update users set vip_level = 2,vip_time=:vip_time,updated_at=:up_time  where id=:uid', ['up_time'=>$time, 'vip_time'=>$vip_time, 'uid'=>$uid]);
                $re_p = DB::update('update orders set status = 1,updated_at=:up_time where order_id =:order_id', ['up_time'=>$time,'order_id'=>$order_id]);
                if (!$re_u || !$re_p) {
                    DB::rollBack();
                    return false;
                }

                //推荐成功给推荐者奖励
                $recommend_uid = 0;
                if (!empty($order->recommend_uid)) {
                    $recommend_uid = $order->recommend_uid;
                    //更改用户的推荐人
                    $up_recommend = DB::update('update users set recommend=:recommend  where id=:uid', ['recommend'=>$recommend_uid, 'uid'=>$uid]);
                } else {
                    $search = [
                        'search' => [
                            'id' => $uid
                        ]
                    ];
                    $model = new User;
                    $user = $model->parseWheres($search)->first();
                    $recommend_uid = $user->recommend;
                }
                
                if (!empty($recommend_uid) && $recommend_uid != $order->uid) {
                    $model = new User;
                    $re = $model->updateRecommendVcoin($recommend_uid);
                    if (!$re) {
                        DB::rollBack();
                        return false;
                    }
                }
                
                DB::commit();
                return true;
            }
        }
        return false;
    }
}

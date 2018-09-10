<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Base
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'type', 'uid', 'amount', 'pay_from', 'product_id', 'status', 'recommend_uid'
    ];


    // 关联菜单表
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}

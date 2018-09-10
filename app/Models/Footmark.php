<?php

namespace App\Models;

class Footmark extends Base
{

    protected $fillable = [
        'user_id', 'article_id', 'ip_address', 'article_owner', 'from'
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}

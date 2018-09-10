<?php

namespace App\Models;

class Interact extends Base
{

    protected $fillable = [
        'article_id',  'user_id', 'like', 'hate', 'share', 'from',
    ];

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function article()
    {
        return $this->belongsTo('App\Models\Article');
    }

   
}

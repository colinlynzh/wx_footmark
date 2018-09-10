<?php

namespace App\Models;

class LogVcoin extends Base
{

    protected $fillable = [
        'vcoin',  'user_id', 'from'
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as ContractsAuthenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable as AuthAuthenticatable;
use Illuminate\Support\Facades\DB;


class User extends Base implements ContractsAuthenticatable
{
    use Notifiable;
    use SoftDeletes;
    use AuthAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'open_id', 'figureurl', 'name', 'mobile', 'city_id', 'city', 'company', 'work_year', 'work_month', 'work_date',
        'positional', 'xq_code','sex', 'summary', 'vip_level', 'vip_time', 'recommend', 'vcoin', 'created_at',  'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    // 关联所有的阅读
    public function article()
    {
        return $this->hasMany('App\Models\Article', 'user_id', 'id');
    }

    // public function updateRecommendVcoin($uid) 
    // {
    //     if (!empty($uid)) {
    //         $user = $this->where('id', $uid)->first();
    //         $time = date('Y-m-d H:i:s');
    //         if (!empty($user)) {
    //             DB::beginTransaction();
    //             if ($user->vip_level==0) {
    //                 //普通会员
    //                 $vip_time = time();
    //                 $vip_time += 180*86400;
    //                 $re = DB::update('update users set vip_level = 2,vip_time=:vip_time,updated_at=:up_time  where id=:uid',
    //                  ['up_time'=>$time, 'vip_time'=>$vip_time, 'uid'=>$uid]);
    //             } elseif ($user->vip_level==1) {
    //                 //永久会员
    //                 $vcoin = $user->vcoin;
    //                 $vcoin += 10000;
    //                 $re_u = DB::update('update users set updated_at=:up_time,vcoin=:vcoin  where id=:uid',
    //                  ['up_time'=>$time,  'uid'=>$uid, 'vcoin'=>$vcoin]);
    //                 $re_vcoin = DB::insert('insert into log_vcoins (`user_id`, `vcoin`, `created_at`, `from`, `total_vcoin`)values(:uid, 10000, :created_at, 1, :vcoin)',
    //                  ['created_at'=>$time, 'uid'=>$uid, 'vcoin'=>$vcoin]);

    //                 $re = $re_u && $re_vcoin;
    //             } elseif ($user->vip_level==2) {
    //                 //钻石会员
    //                 $vip_time = $user->vip_time;
    //                 $vip_time += 180*86400;
    //                 $vcoin = $user->vcoin;
    //                 $vcoin += 10000;

    //                 $re_u = DB::update('update users set vip_time=:vip_time,updated_at=:up_time,vcoin=:vcoin  where id=:uid',
    //                  ['up_time'=>$time, 'vip_time'=>$vip_time, 'uid'=>$uid, 'vcoin'=>$vcoin]);
    //                 $re_vcoin = DB::insert('insert into log_vcoins (`user_id`, `vcoin`, `created_at`, `from`, `total_vcoin`)values(:uid, 10000, :created_at, 1, :vcoin)',
    //                 ['created_at'=>$time, 'uid'=>$uid, 'vcoin'=>$vcoin]);
                    
    //                 $re = $re_u && $re_vcoin;
    //             }
    //             if (!$re) {
    //                 DB::rollBack();
    //                 return false;
    //             }
    //             if (!empty($user->recommend)) {
    //                 //给上一级代理的奖励
    //                 $recommend_user = $this->where('id', $user->recommend)->first();
    //                 if (!empty($recommend_user) && $recommend_user->vip_level==1) {
    //                     $vcoin = $recommend_user->vcoin;
    //                     $vcoin += 3000;
    //                     $recommend_uid = $recommend_user->id;

    //                     $re_u = DB::update('update users set updated_at=:up_time,vcoin=:vcoin  where id=:uid',
    //                     ['up_time'=>$time, 'uid'=>$recommend_uid, 'vcoin'=>$vcoin]);
    //                     $re_vcoin = DB::insert('insert into log_vcoins (`user_id`, `vcoin`, `created_at`, `from`, `total_vcoin`)values(:uid, 3000, :created_at, 1, :vcoin)',
    //                 ['created_at'=>$time, 'uid'=>$recommend_uid, 'vcoin'=>$vcoin]);

    //                     if (!$re_u || !$re_vcoin) {
    //                         DB::rollBack();
    //                         return false;
    //                     }
    //                 }
    //             }

    //             DB::commit();       
    //             return true;            
    //         }
    //     }
    //     return false;
        
    // }

    public function updateRecommendVcoin($uid) 
    {
        if (!empty($uid)) {
            $user = $this->where('id', $uid)->first();
            $time = date('Y-m-d H:i:s');
            if (!empty($user) && $user->vip_level>0) {
                DB::beginTransaction();
                $re_u = DB::update('update users set vip_level = 1, updated_at=:up_time where id =:id', ['id'=>$uid, 'up_time'=>$time]);
                DB::commit();
                return true;
            }
        }
        return false;
    }
}

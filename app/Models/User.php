<?php

namespace App\Models;

use App\Models\Traits\ActiveUserHelper;
use App\Models\Traits\LastActivedAtHelper;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmailContract, JWTSubject
{
    use MustVerifyEmailTrait;

    //通知
    use Notifiable {
        notify as protected laravelNotify;
    }

    //权限
    use HasRoles;

    use ActiveUserHelper, LastActivedAtHelper;

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar', 'phone', 'weixin_openid', 'weixin_unionid', 'registration_id', 'weixin_session_key', 'weapp_openid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'weixin_openid', 'weixin_unionid', 'weixin_session_key', 'weapp_openid'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_actived_at' => 'datetime'
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }


    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }


    /**
     *
     */
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }


    public function setPasswordAttribute($value)
    {
        if (strlen($value) != 60){
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }


    public function setAvatarAttribute($avatar)
    {
        // 如果不是 `uploads` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! \Str::startsWith($avatar, 'uploads')) {

            // 拼接完整的 URL
            $avatar = "uploads/images/avatar/$avatar";
        }

        $this->attributes['avatar'] = $avatar;
    }


    public function getAvatarAttribute($avatar)
    {
        return empty($avatar) ? 'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png' : $avatar;
    }

}

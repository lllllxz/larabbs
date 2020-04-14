<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        //获取缓存内的图片验证码信息
        $captchaData = \Cache::get($request->captcha_key);
        if (!$captchaData){
            throw new PublicException('图片验证码已失效', 403);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            throw new PublicException('验证码错误', 401);
        }

        $phone = $captchaData['phone'];

        if (!app()->environment('production')){
            $code = '123456';
        }else{
            $code = generateSmsCode();
            //发送短信验证码
            try{
                $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code
                    ]
                ]);
            }catch (NoGatewayAvailableException $e){
                $message = $e->getException('aliyun')->getMessage();
                abort(500, $message ?: '短信发送异常');
            }
        }

        $key = 'verificationCode_'.Str::random(15);
        $expiredAt = now()->addMinutes(5);
        //缓存5分钟短信验证码
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        //清除图片验证码
        \Cache::forget($request->captcha_key);

        return $this->created([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString()
        ]);
    }
}

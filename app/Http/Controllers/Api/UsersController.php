<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * 用户注册
     *
     * @param UserRequest $request
     * @return UserResource
     * @throws PublicException
     */
    public function store(UserRequest $request)
    {
        $verifyData = $this->verifyCaptcha($request->verification_key, $request->verification_code);

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return new UserResource($user);
    }


    /**
     * 获取用户信息
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }


    /**
     * 获取当前用户信息
     *
     * @param Request $request
     * @return UserResource
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }


    /**
     * 更新用户信息
     *
     * @param UserRequest $request
     * @return mixed
     */
    public function update(UserRequest $request)
    {
        $user = $request->user();

        $attributes = $request->only(['name', 'email', 'introduction']);

        if ($request->avatar_image_id) {
            $avatar = Image::where(['id' => $request->avatar_image_id, 'user_id' => $user->id])->first();

            $attributes['avatar'] = $avatar->path;
        }

        $user->update($attributes);

        return $this->success((new UserResource($user))->showSensitiveFields());
    }


    /**
     * 获取活跃用户
     *
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function activedIndex(User $user)
    {
        return UserResource::collection($user->getActiveUsers());
    }


    /**
     * 微信小程序注册用户
     *
     * @param UserRequest $request
     * @return mixed
     * @throws PublicException
     */
    public function weappStore(UserRequest $request)
    {
        $verifyData = $this->verifyCaptcha($request->verification_key, $request->verification_code);

        // 获取微信的 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($request->code);

        if (isset($data['errcode'])) {
            return $this->failed('code 不正确', 401);
        }

        // 如果 openid 对应的用户已存在，报错403
        $user = User::where('weapp_openid', $data['openid'])->first();

        if ($user) {
            return $this->failed('微信已绑定其他用户，请直接登录', 403);
        }

        // 创建用户
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
            'weapp_openid' => $data['openid'],
            'weixin_session_key' => $data['session_key']
        ]);

        //清除验证码缓存
        \Cache::forget($request->verification_key);

        $returnData = [
            'user' => new UserResource($user),
            'meta' => [
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ]
        ];

        return $this->created($returnData);
    }


    /**
     * 验证图形验证码
     *
     * @param $verification_key
     * @param $verification_code
     * @return mixed
     * @throws PublicException
     */
    protected function verifyCaptcha($verification_key, $verification_code)
    {
        $verifyData = \Cache::get($verification_key);
        if (!$verifyData) {
            throw new PublicException('验证码已失效', 422);
        }

        if (!hash_equals((string)$verifyData['code'], $verification_code)) {
            // 返回401
            throw new PublicException('验证码错误',401);
        }

        return $verifyData;
    }
}

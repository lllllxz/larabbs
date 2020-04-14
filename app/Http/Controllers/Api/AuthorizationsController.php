<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Models\User;

class AuthorizationsController extends Controller
{

    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;

        //输入手机号或邮箱
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)){
            throw new AuthenticationException('用户名或密码错误');
        }

        return $this->respondWithToken($token);
    }


    /**
     * 刷新token
     *
     * @return mixed
     */
    public function update()
    {
        $token = auth('api')->refresh();

        return $this->respondWithToken($token);
    }


    public function destroy()
    {
        auth('api')->logout();

        return response(null, 204);
    }


    /**
     * 三方授权登录
     *
     * @param $socialType
     * @param SocialAuthorizationRequest $request
     * @return mixed
     * @throws AuthenticationException
     * @throws PublicException
     */
    public function socialStore($socialType, SocialAuthorizationRequest $request)
    {
        $driver = \Socialite::driver($socialType);

        try{
            if ($request->code){
                $response = $driver->getAccessTokenResponse($request->code);

                $accessToken = $response['access_token'];

            }else{

                $accessToken = $request->access_token;
                if ($socialType == 'weixin'){
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($accessToken);
        }catch (\Exception $e){
            throw new AuthenticationException('参数错误，未获取用户信息');
        }

        switch ($socialType){
            case 'weixin' :
                $token = $this->loginByWeixin($oauthUser);
                break;

            default:
                throw new PublicException('登录异常,请稍后再试', 422);
        }

        return $this->respondWithToken($token);
    }


    /**
     * 微信授权登录返回数据库用户token，如果用户不存在则创建
     *
     * @param $oauthUser
     * @return mixed
     */
    protected function loginByWeixin($oauthUser)
    {
        $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

        if ($unionid) {
            $user = User::where('weixin_unionid', $unionid)->first();
        } else {
            $user = User::where('weixin_openid', $oauthUser->getId())->first();
        }

        // 没有用户，默认创建一个用户
        if (!$user) {
            $user = User::create([
                'name' => $oauthUser->getNickname(),
                'avatar' => $oauthUser->getAvatar(),
                'weixin_openid' => $oauthUser->getId(),
                'weixin_unionid' => $unionid,
            ]);
        }

        return auth('api')->login($user);
    }


    /**
     * 返回token内容
     *
     * @param $token
     * @return mixed
     */
    protected function respondWithToken($token)
    {
        return $this->created([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL()*60
        ]);
    }


    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->failed('code 不正确', 401);
        }

        // 找到 openid 对应的用户
        $user = User::where('weapp_openid', $data['openid'])->first();

        $attributes['weixin_session_key'] = $data['session_key'];

        // 未找到对应用户则需要提交用户名密码进行用户绑定
        if (!$user) {
            // 如果未提交用户名密码，403 错误提示
            if (!$request->username) {
                return $this->failed('请输入用户名密码与微信绑定', 403);
            }

            $username = $request->username;

            // 用户名可以是邮箱或电话
            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username :
                $credentials['phone'] = $username;

            $credentials['password'] = $request->password;

            // 验证用户名和密码是否正确
            if (!\Auth::guard('api')->once($credentials)) {
                return $this->failed('用户名或密码错误', 401);
            }

            // 获取对应的用户
            $user = \Auth::guard('api')->getUser();
            $attributes['weapp_openid'] = $data['openid'];

        }

        // 更新用户数据
        $user->update($attributes);

        // 为对应用户创建 JWT
        $token = \Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);
        if (!$verifyData) {
            throw new PublicException('验证码已失效', 403);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new PublicException('验证码错误',401);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return new UserResource($user);
    }
}

<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    /**
     * 个人中心
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }


    /**
     * 编辑资料
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }


    /**
     * 更新用户资料
     * @param User $user
     * @param UserRequest $request
     * @param ImageUploadHandler $uploadHandler
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(User $user, UserRequest $request, ImageUploadHandler $uploadHandler)
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        $oldAvatar = $user->avatar;

        if ($request->avatar){
            $path = $uploadHandler->save($request->avatar, 'avatar', $user->id, 416);
            if ($path){
                $data['avatar'] = $path['path'];
            }
        }

        $user->update($data);

        if ($request->avatar && $oldAvatar){
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}

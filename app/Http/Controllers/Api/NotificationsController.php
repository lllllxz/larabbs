<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * 通知列表
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate();

        return NotificationResource::collection($notifications);
    }


    /**
     * 返回未读通知数
     *
     * @param Request $request
     * @return mixed
     */
    public function stats(Request $request)
    {
        return $this->success([
            'unread_count' => $request->user()->notification_count,
        ]);
    }


    public function read(Request $request)
    {
        $request->user()->markAsRead();

        return $this->deleted();
    }
}

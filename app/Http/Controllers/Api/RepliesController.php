<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PublicException;
use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RepliesController extends Controller
{
    /**
     * 回复列表
     *
     * @param Topic $topic
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Topic $topic)
    {
        $query = $topic->replies()->getQuery();

        $replies = QueryBuilder::for($query)
            ->allowedIncludes(['user', 'topic'])
            ->allowedFilters(AllowedFilter::exact('user_id'))
            ->paginate();

        return ReplyResource::collection($replies);
    }

    /**
     * 话题回复
     *
     * @param ReplyRequest $request
     * @param Topic $topic
     * @param Reply $reply
     * @return ReplyResource
     */
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->content;
        $reply->topic()->associate($topic);
        $reply->user()->associate($request->user());

        $reply->save();

        return new ReplyResource($reply);
    }


    /**
     * 删除回复
     *
     * @param Topic $topic
     * @param Reply $reply
     * @return mixed
     * @throws PublicException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic, Reply $reply)
    {
        if ($topic->id != $reply->topic_id){
            throw new PublicException('该回复不存在', 404);
        }

        $this->authorize('destroy', $reply);

        return $this->deleted();
    }


    public function userIndex(User $user)
    {
        $query = $user->replies()->getQuery();

        $replies = QueryBuilder::for($query)
            ->allowedIncludes('user', 'topic', 'topic.user')
            ->paginate();

        return ReplyResource::collection($replies);
    }
}

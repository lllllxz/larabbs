<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TopicsController extends Controller
{
    /**
     * 话题列表
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $topics = QueryBuilder::for(Topic::class)
            ->allowedIncludes(['user', 'category', 'user.roles'])
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied')
            ])->paginate();

        return TopicResource::collection($topics);
    }


    /**
     * 新增话题
     *
     * @param TopicRequest $request
     * @param Topic $topic
     * @return mixed
     */
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->validated());
        $topic->user_id = $request->user()->id;
        $topic->save();

        return $this->created(new TopicResource($topic));
    }


    public function show($topicId)
    {
        $topic = QueryBuilder::for(Topic::class)
            ->allowedIncludes(['user', 'category', 'topReplies.user'])
            ->findOrFail($topicId);

        return $this->success(new TopicResource($topic));
    }


    /**
     * 更新话题
     *
     * @param TopicRequest $request
     * @param Topic $topic
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update($request->validated());

        return $this->success(new TopicResource($topic));
    }


    /**
     * 删除话题
     *
     * @param Topic $topic
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();

        return $this->deleted();
    }


    /**
     * 某用户的所有话题
     *
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userIndex(User $user)
    {
        $query = $user->topics()->getQuery();

        $topics = QueryBuilder::for($query)
            ->allowedIncludes(['user', 'category'])
            ->allowedFilters([
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied')
            ])->paginate();

        return TopicResource::collection($topics);
    }
}

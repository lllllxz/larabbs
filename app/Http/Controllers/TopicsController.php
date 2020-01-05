<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\TopicRequest;
use Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, User $user, Link $link)
	{
		$topics = Topic::with('user', 'category')
                        ->withOrder($request->order)
                        ->paginate(30);

        $active_users = $user->getActiveUsers();
        $links = $link->getAllCache();
		return view('topics.index', compact('topics', 'active_users', 'links'));
	}

    public function show(Topic $topic, $slug=null)
    {
        $topic->view_count += 1;
        $topic->save();
        // URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $slug) {
            return redirect($topic->link(), 301);
        }
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();
		return redirect()->to($topic->link())->with('success', '成功创建话题。');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);

        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('success', '文章已更新。');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功！');
	}

    public function uploadImage(Request $request, ImageUploadHandler $imageUploadHandler)
    {
        $data = [
            'success' => false,
            'msg' => '上传失败！',
            'file_path' => '',
        ];

        if ($request->upload_file){
            $result = $imageUploadHandler->save($request->upload_file, 'topics', Auth::id(), 1024);
            if ($result){
                $data['success'] = true;
                $data['msg'] = '已上传';
                $data['file_path'] = url($result['path']);
            }
        }

        return response()->json($data);
	}
}

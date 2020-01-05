<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request, User $user, Link $link)
    {
        // 读取分类 ID 关联的话题，并按每 20 条分页
        $topics = Topic::where('category_id', $category->id)
            ->withOrder($request->order)
            ->with('user', 'category')
            ->paginate(20);
        // 传参变量话题和分类到模板中

        $active_users = $user->getActiveUsers();
        // 资源链接
        $links = $link->getAllCache();
        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}

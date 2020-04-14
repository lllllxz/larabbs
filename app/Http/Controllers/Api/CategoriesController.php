<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * 获取话题列表
     *
     * @return mixed
     */
    public function index()
    {
        return $this->success(CategoryResource::collection(Category::all()));
    }


    public function store()
    {

    }
}

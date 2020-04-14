<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $roles = $request->user()->roles()->get();

        return RoleResource::collection($roles);
    }
}

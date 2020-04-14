<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index(Request $request)
    {
        $permissions = $request->user()->getAllPermissions();

        return PermissionResource::collection($permissions);
    }
}

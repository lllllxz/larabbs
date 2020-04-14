<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Support\Str;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $handler, Image $image)
    {
        $user = $request->user();

        $size = $request->type == 'avatar' ? 416 : 1024;
        $result = $handler->save($request->image, Str::plural($request->type), $user->id, $size);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->user_id = $user->id;
        $image->save();

        return $this->created(new ImageResource($image));
    }
}

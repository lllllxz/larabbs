<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //隐藏created_at和updated_at两个字段
        $this->resource->addHidden(['created_at', 'updated_at']);

        return parent::toArray($request);
    }
}

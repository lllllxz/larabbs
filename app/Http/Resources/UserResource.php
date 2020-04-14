<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $showSensitiveFields = false;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!$this->showSensitiveFields){
            $this->resource->addHidden(['email', 'phone']);
        }

        $data = parent::toArray($request);

        $data['avatar'] = is_url($data['avatar']) ? $data['avatar'] : url($data['avatar']);
        $data['bound_phone'] = $this->resource->phone ? true :false;
        $data['bound_weixin'] = ($this->resource->weixin_openid || $this->resource->weixin_unionid) ? true : false;
        $data['roles'] = RoleResource::collection($this->whenloaded('roles'));

        return $data;
    }


    public function showSensitiveFields()
    {
        $this->showSensitiveFields = true;

        return $this;
    }
}

<?php

namespace App\Http\Requests\Api;


class AuthorizationRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string',
            'password' => 'required|alpha_dash|min:6'
        ];
    }


    public function messages()
    {
        return [
            'username.required' => '请填写用户名',
            'username.string' => '请填写正确的用户名',
            'password.required' => '请输入密码',
            'password.alpha_dash' => '密码错误'
        ];
    }
}

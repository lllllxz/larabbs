<?php

namespace App\Http\Requests\Api;


class CaptchaRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'phone',
                'unique:users'
            ]
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '请输入手机号再获取验证码',
            'phone.phone' => '手机号格式错误',
            'phone.unique' => '该手机号已注册，请登录'
        ];
    }
}

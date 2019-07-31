<?php
namespace App\Http\Requests\Admin;
use App\Model\Admin\AdminUser;
use Illuminate\Validation\Rule;

class AdminUserManageRequest extends \Illuminate\Foundation\Http\FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $status_in = [
            \App\Model\Admin\AdminUserManage::STATUS_DISABLE,
            \App\Model\Admin\AdminUserManage::STATUS_ENABLE,
        ];

        $typeIds = [
            0,1
        ];
        $passwordRule = '';
        if ($this->method() == 'POST' ||
            ($this->method() == 'PUT' && request()->post('pwd'))) {
            $passwordRule = [
                'required',
                'regex:/^(?![0-9]+$)(?![a-zA-Z]+$)[\w\x21-\x7e]{6,18}$/'
            ];
        }
        if (request()->get('id')) {
            $userNameRule = 'required|max:20';
        } else {
            $userNameRule = 'required|max:20|unique:tb_admin';
        }
        return [
            'username' => $userNameRule,
            'pwd' => $passwordRule,
            'enable' => [
                Rule::in($status_in),
            ],
            'realname' => 'max:50',
            'typeid' => [
                Rule::in($typeIds)
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.required' => '用户名不能为空',
            'username.unique'   => '用户名不允许重复',
            'username.max'      => '用户名长度不能超过20个字符长度',
            'pwd.required'      => '密码不能为空',
            'regex'             => '密码不符合规则',
            'realname.max'      => '真实姓名不能超过50个字符长度'
        ];
    }
}
<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TaskGroupRequest extends FormRequest
{
    public function rules()
    {
        // 修改的时候
        if (request()->get('id'))
        {
            $groupRules = '';
        }
        else {
            // 判断是否唯一
            $groupRules = '|unique:tb_task_group';
        }
        return [
            'group_name' => 'required|max:64'. $groupRules,
            'description'=> 'max:255'
        ];
    }

    public function messages()
    {
        return [
            'group_name.required' => '分组名称必须!',
            'group_name.max'    => '分组名称最长64个字符长度!',
            'group_name.unique' => '分组名称不允许重复!',
            'description.max'   => '备注最长255个字符长度!'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
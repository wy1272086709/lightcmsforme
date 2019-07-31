<?php
namespace App\Http\Requests\Admin;
use App\Rules\VerCheck;
use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if (!request()->get('id'))
        {
            $moduleNameRule = 'required|max:256|unique:tb_area_module';
        }
        else
        {
            $moduleNameRule = 'required|max:256';
        }
        return [
            'module_name' => $moduleNameRule,
            'file_name'   => 'required|max:256',
            'version'     => [
                'required',
                new VerCheck()
            ],
            'before_hash' => 'required|max:32',
            'after_hash'  => 'required|max:32',
            'description' => 'max:255'
        ];
    }

    public function messages()
    {
        return [
            'module_name.required' => '模块名必须!',
            'module_name.max' => '模块名最长不超过256个字符长度!',
            'module_name.unique' => '模块名不允许重复!',
            'file_name.required' => '文件名必须!',
            'file_name.max' => '文件名最长不超过256个字符长度',
            'version.required' => '版本号必须!',
            'before_hash.max'=> 'hash前最长不超过32个字符长度',
            'after_hash.max'=> 'hash后最长不超过32个字符长度',
        ];
    }

    public function attributes()
    {
        return [
            'module_name' => '模块名',
            'file_name'   => '模块文件名',
            'version'     => '版本号',
            'before_hash' => 'hash前',
            'after_hash'  => 'hash后',
            'description' => '描述'
        ];
    }
}
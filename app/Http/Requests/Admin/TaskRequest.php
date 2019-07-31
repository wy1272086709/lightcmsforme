<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function rules()
    {
        return [
            'task_name' => 'required|max:50|',
            'stay_time' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'task_name.required' => '任务名称必须!',
            'stay_time' => '停留时长必须!'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
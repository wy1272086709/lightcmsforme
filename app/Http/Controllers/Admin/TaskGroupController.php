<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TaskGroupRequest;
use App\Repository\Admin\AreaTaskRepository;
use App\Repository\Admin\ModuleRepository;
use App\Repository\Admin\TaskGroupRepository;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskGroupController extends Controller
{
    protected $formNames = [
        'group_name', 'enable', 'service_type', 'module_id', 'description', 'platform'
    ];
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[] = [
            'title' => '参数配置',
            'url'   => route('admin::areaModule.index')
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $this->breadcrumb[] = [
            'title' => '任务分组',
            'url'   => ''
        ];
        return view('admin.taskGroup.index', [
            'breadcrumb' => $this->breadcrumb
        ]);
    }

    public function add()
    {
        $moduleNameMap = ModuleRepository::getModuleNameMap([], 1);
        $this->breadcrumb[] = [
            'title' => '任务分组',
            'url'   => ''
        ];
        return view('admin.taskGroup.add', [
            'breadcrumb'    => $this->breadcrumb,
            'moduleNameMap' => $moduleNameMap
        ]);
    }

    // 获取任务分组的数据.
    public function list()
    {
        $perPage = request()->get('perPage', 50);
        $where   = request()->only($this->formNames);
        return TaskGroupRepository::list($perPage, $where);
    }

    /**
     * @param $id
     */
    public function edit($id)
    {
        $moduleNameMap = ModuleRepository::getModuleNameMap([], 1);
        $this->breadcrumb[] = [
            'title' => '修改任务分组',
            'url'   => ''
        ];
        $taskGroup = TaskGroupRepository::find($id);
        return view('admin.taskGroup.add', [
            'id'         => $id,
            'taskGroup'  => $taskGroup,
            'breadcrumb' => $this->breadcrumb,
            'moduleNameMap' => $moduleNameMap
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     *
     */
    public function create(TaskGroupRequest $request)
    {
        //
        $postData = request()->only($this->formNames);
        return TaskGroupRepository::addTaskGroup($postData);
    }

    /**
     * Show the form for creating a new resource.
     *
     *
     */
    public function update(TaskGroupRequest $request)
    {
        //
        $id = request()->get('id');
        $postData = request()->only($this->formNames);
        return TaskGroupRepository::updateTaskGroup($id, $postData);
    }


    /**
     * Remove the specified resource from storage.
     * @param  $request
     * @return array
     */
    public function destroy(Request $request)
    {
        //todo 如果任务分组被使用了,则不能被删除，这里有段逻辑需要做判断。
        $ids = $request->get('ids');
        $isExists = AreaTaskRepository::isExistsTask($ids);
        if ($isExists)
        {
            return [
                'code' => 1,
                'msg'  => '任务分组已被使用，暂时不能删除'
            ];
        }
        return TaskGroupRepository::delTaskGroup($ids);
    }
}

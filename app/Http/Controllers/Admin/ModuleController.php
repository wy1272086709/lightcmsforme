<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModuleRequest;
use App\Repository\Admin\ModuleRepository;
use App\Repository\Admin\TaskGroupRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public $formNames = [
        'module_name', 'enable'
    ];

    public $createFormNames = [
        'module_name', 'file_name', 'enable', 'version', 'before_hash', 'after_hash',
        'description'
    ];

    public $updateFormNames = [
        'enable', 'version', 'before_hash', 'after_hash',
        'description'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[] = [
            'title' => '参数配置',
            'url' => route('admin::areaModule.index')
        ];
    }

    /**
     * 模块管理首页
     */
    public function index()
    {
        $this->breadcrumb[] = [
          'title' => '模块管理', 'url' => ''
        ];
        return view('admin.module.index', [
            'breadcrumb' => $this->breadcrumb
        ]);
    }

    /**
     * 获取模块列表数据
     */
    public function list(\Illuminate\Http\Request $request)
    {
        $perPage   = request()->get('limit', 50);
        $condition = $request->only($this->formNames);
        $res = ModuleRepository::list($perPage, $condition);
        return $res;
    }

    public function add()
    {
        $this->breadcrumb[] = [ 'title' => '添加模块', 'url' => '' ];
        return view('admin.module.add', [
            'breadcrumb' => $this->breadcrumb,
        ]);
    }

    public function edit($id)
    {
        $moduleData = ModuleRepository::find($id);
        $this->breadcrumb[] = [ 'title' => '修改模块', 'url' => '' ];
        return view('admin.module.add', [
            'id' => $id,
            'breadcrumb' => $this->breadcrumb,
            'module'    => $moduleData
        ]);
    }

    public function update(ModuleRequest $request)
    {
        $postData = $request->only($this->updateFormNames);
        $id = $request->get('id');
        return ModuleRepository::updateModule($id, $postData);
    }

    public function create(ModuleRequest $request)
    {
        $postData = $request->only($this->createFormNames);
        return ModuleRepository::addModule($postData);
    }

    /**
     * 删除之前先判断模块是否被使用
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        $ids = $request->get('ids');
        $taskGroupData = TaskGroupRepository::getTaskGroupByModuleIds($ids);
        if ($taskGroupData)
        {
            return [
              'code' => '1',
              'msg'  => '模块被分组使用,暂时不能被删除'
            ];
        }
        return ModuleRepository::delete($ids);
    }

    /**
     * 启用或者禁用
     * @param $request
     * @param $id
     * @return array
     */
    public function enable(Request $request, $id)
    {
        try {
            $status = $request->get('enable');
            ModuleRepository::enableStatus($id, $status);
            return [
                'code' => 0,
                'msg'  => '操作成功',
            ];
        } catch (Exception $ex) {
            return [
                'code' => 1,
                'msg'  => '操作失败,原因:'. $ex->getMessage()
            ];
        }
    }
}
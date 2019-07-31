<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\AreaTaskService;
use App\Http\Services\TaskService;
use App\Repository\Admin\AreaTaskRepository;
use App\Repository\Admin\TaskGroupRepository;
use App\Utils\ConstantUtils;
use Illuminate\Support\Facades\Request;

class MobileTaskController extends Controller
{
    // where条件对应的key
    protected $searchKeys = [
        'task_name',
        'enable',
        'task_type',
        'task_category'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[] = [
            'title' => '移动端任务首页',
            'url'   => route('admin::mobileTask.index')
        ];
    }

    public function add() {
        $this->breadcrumb[] = [
            'title' => '添加移动端任务',
            'url'   => ''
        ];
        $taskType = request()->get('taskType', ConstantUtils::ADS_SERVICE);
        $platForm = ConstantUtils::MOBILE_PLATFORM;
        $taskGroupMap = TaskService::getTaskGroupList($taskType, $platForm);
        return view('admin.mobileTask.add', [
            'breadcrumb' => $this->breadcrumb,
            'platForm' => $platForm,
            'taskType' => $taskType,
            'groupList'=> $taskGroupMap,
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return view
     */
    public function edit(Request $request, $id) {
        $this->breadcrumb[] = [
            'title' => '修改移动端任务',
            'url'   => ''
        ];
        $taskType = (int)request()->get('taskType', ConstantUtils::ADS_SERVICE);
        $map = [
            'service_type' => $taskType,
            'platform'     => ConstantUtils::MOBILE_PLATFORM,
            'enable'       => 1
        ];
        $groupList = TaskGroupRepository::getTaskGroupList($map);
        $groupMap  = [];
        foreach ($groupList as $row)
        {
            $groupMap[$row['id']] = $row['group_name'];
        }
        //  获取taskData
        $taskData = AreaTaskService::getTaskData($id);
        return view('admin.mobileTask.add', [
            'platForm'   => ConstantUtils::MOBILE_PLATFORM,
            'breadcrumb' => $this->breadcrumb,
            'taskType'   => $taskType,
            'taskData'   => $taskData,
            'id'         => $id,
            'groupList'  => $groupMap
        ]);
    }

    public function index() {
        return view('admin.pcTask.index', [
            'platForm' => ConstantUtils::MOBILE_PLATFORM,
            'taskType' => request('taskType', ConstantUtils::ADS_SERVICE),
            'breadcrumb' => $this->breadcrumb
        ]);
    }

    public function list()
    {
        $taskType = \request()->get('task_type', ConstantUtils::ADS_SERVICE);
        $platForm = \request()->get('task_category', ConstantUtils::MOBILE_PLATFORM);
        $where = \request()->only($this->searchKeys);
        $perPage = \request()->get('page');
        $taskGroupMap = TaskService::getTaskGroupList($taskType, $platForm);
        $res = AreaTaskRepository::getTaskPageData($perPage, $where);
        $issuedValueMap = AreaTaskService::getIssuedValueMap($res);
        $res = AreaTaskRepository::listTask($res, $taskGroupMap, $issuedValueMap);
        return $res;
    }
}
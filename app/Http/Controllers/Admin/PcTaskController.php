<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaskRequest;
use App\Http\Services\AreaTaskService;
use App\Http\Services\TaskService;
use App\Repository\Admin\AreaTaskRepository;
use App\Repository\Admin\TaskGroupRepository;
use App\Utils\ConstantUtils;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class PcTaskController extends Controller
{
    // where条件对应的key
    protected $searchKeys = [
        'task_name',
        'enable',
        'task_category',
        'task_type',
    ];

    protected $addFormNames = [
        'id',
        'task_name',
        'r_mnsc',
        'is_ua',
        'choice_type',
        'task_url_style',
        'issue_style',
        'source_style',
        'task_category',
        'task_type',
        'ip_pv_value',
        'time_zone',
        'level',
        'enable',
        'statistics_code',
        'statistics_link',
        'aq_type',
        'config_id',
        'task_url',
        'qx_type',
        'task_group_id',
        'word_style',
        'stay_time',
        'exposure_page_url',
        'pc_num_show'
    ];

    protected $configFormNames = [
        'weekend_choose',
        'is_big',
        'is_ua',
        'config_id'
    ];



    protected $addExtraFormNames = [
        't_s', // 任务页面，对应的信息
        'issued_json_value', // 计划模式下的发布数值
        'issued_value', // 固定模式下的发布数值
        'ua-set-json', //  UA设置
        'source_json', // 来源页面
        'time_zones', //  时段设置
        'click_set_data_info', //点击设置
        'issued_value',
        'area_channel_info',    //地区/渠道设置
        'exposure_page_url'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[] = [
            'title' => 'PC任务首页',
            'url'   => route('admin::pcTask.index')
        ];
    }

    public function index()
    {
        $this->breadcrumb[] = [
            'title' => 'PC任务列表',
            'url'   => ''
        ];
        $taskType = \request()->get('taskType', ConstantUtils::ADS_SERVICE);
        return view('admin.pcTask.index', [
            'platForm' => ConstantUtils::PC_PLATFORM,
            'taskType' => $taskType,
            'breadcrumb' => $this->breadcrumb
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return view
     */
    public function edit(Request $request, $id) {
        $this->breadcrumb[] = [
            'title' => '修改PC任务',
            'url'   => ''
        ];
        $taskType = (int)request()->get('taskType', ConstantUtils::ADS_SERVICE);
        $map = [
            'service_type' => ConstantUtils::ADS_SERVICE,
            'platform'     => ConstantUtils::PC_PLATFORM,
            'enable'       => 1
        ];
        // TaskGroupRepository::getTaskGroupList($map);
        $groupList = TaskGroupRepository::getTaskGroupList($map);
        //  获取taskData
        $taskData  = AreaTaskService::getTaskData($id);
        $groupMap  = [];
        foreach ($groupList as $row)
        {
            $groupMap[$row['id']] = $row['group_name'];
        }
        return view('admin.pcTask.add', [
            'platForm'   => ConstantUtils::PC_PLATFORM,
            'breadcrumb' => $this->breadcrumb,
            'taskType'   => $taskType,
            'taskData'   => $taskData,
            'id'         => $id,
            'groupList'  => $groupMap
        ]);
    }


    /**
     * @param TaskRequest $request
     * @return mixed
     */
    public function create(TaskRequest $request)
    {
        try {
            DB::beginTransaction();
            $postData = $request->only($this->addFormNames);
            $extraData = $request->only($this->addExtraFormNames);
            $configData = $request->only($this->configFormNames);
            AreaTaskService::saveTaskToDb($postData, $extraData, $configData);
            $idVal =  trim($postData['id']);
            DB::commit();
            $idVal ? $data = [
                'code' => 0,
                'msg'  => '更新成功!'
            ] : $data = [
                'code' => 0,
                'msg'  => '新增成功!'
            ];
        }
        catch (\Exception $ex)
        {
            //echo $ex->getMessage();die;
            DB::rollback();
            echo $ex->getTraceAsString();
            $data = [
                'code' => 1,
                'msg'  => $ex->getMessage()
            ];
        }
        return $data;
    }


    public function list()
    {
        $taskType = \request()->get('task_type', ConstantUtils::ADS_SERVICE);
        $platForm = \request()->get('task_category', ConstantUtils::PC_PLATFORM);
        $where = \request()->only($this->searchKeys);
        $perPage = \request()->get('perPage', 10);
        $taskGroupMap = TaskService::getTaskGroupList($taskType, $platForm);
        $res = AreaTaskRepository::getTaskPageData($perPage, $where);
        $issuedValueMap = AreaTaskService::getIssuedValueMap($res);
        $res = AreaTaskRepository::listTask($res, $taskGroupMap, $issuedValueMap);
        return $res;
    }

    /**
     * stop()方法 停止任务
     */
    public function stop()
    {
        $ids = \request()->get('ids');
        $res = AreaTaskRepository::stopTask($ids);
        if ($res) {
            return [
                'code' => 0,
                'msg'  => '停止任务成功!',
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '停止任务失败!',
            ];
        }
    }

    public function destroy()
    {
        $ids = \request()->get('ids');
        $res = AreaTaskRepository::delTask($ids);
        if ($res) {
            return [
                'code' => 0,
                'msg'  => '删除任务成功!',
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '删除任务失败!',
            ];
        }
    }


    public function add()
    {
        $this->breadcrumb[] = [
            'title' => '添加PC任务',
            'url'   => ''
        ];
        $taskType = (int)request()->get('taskType', ConstantUtils::ADS_SERVICE);
        $map = [
            'service_type' => $taskType,
            'platform'     => ConstantUtils::PC_PLATFORM,
            'enable'       => 1
        ];
        $groupList = TaskGroupRepository::getTaskGroupList($map);
        $groupMap = [];
        foreach ($groupList as $row)
        {
            $groupMap[$row['id']] = $row['group_name'];
        }

        return view('admin.pcTask.add', [
            'breadcrumb' => $this->breadcrumb,
            'platForm'   => ConstantUtils::PC_PLATFORM,
            'taskType'   => $taskType,
            'groupList'  => $groupMap
        ]);
    }

    /**
     * 获取渠道信息
     */
    public function getChannelInfo()
    {
        $date = request()->get('dates');
        $taskId = request()->get('taskId');
    }

    public function test()
    {
        return view('admin.pcTask.test');
    }

    /**
     * 获取地区信息和渠道信息
     */
    public function getAreaInfo()
    {
        $date = request()->get('dates');
        $taskId = request()->get('taskId');
        $key  = request()->get('cityKey');
        $tabIndex = request()->get('tab_index');
        $ratio = \request()->get('ratio', '100');
        $vars = TaskService::getAreaVarsForView($date, $key);
        $data = TaskService::getChannelVarsForView($date, $taskId);
        $vars = array_merge($vars, $data);
        return view('admin.pcTask.area', [
            'vars' => $vars,
            'date' => $date,
            'key'  => $key,
            'tab_index' => $tabIndex,
            'ratio' => $ratio
        ]);
    }
}
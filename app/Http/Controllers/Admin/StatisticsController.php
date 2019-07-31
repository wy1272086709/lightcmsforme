<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\StatisticsService;
use App\Utils\ConstantUtils;

class StatisticsController extends Controller
{

    /**
     * 统计首页
     */
    public function index()
    {
        return view('admin.statistics.index', [
            'taskType' => request('taskType', ConstantUtils::ADS_SERVICE)
        ]);
    }

    /**
     * @return array
     */
    public function list()
    {
        $taskType = request('taskType', ConstantUtils::FLOW_SERVICE);
        $date = request('dates', date('Y-m-d'));
        $perPage = request('limit', 50);
        $page = request('page', 1);
        // 如果是流量业务
        if (in_array($taskType, [ ConstantUtils::ADS_SERVICE]))
        {
            // ad service 和search service
            $total = StatisticsService::getAdsStatisticsTotal($date);
            $list  = StatisticsService::getAdsStatisticsRes($date, $perPage, $page);
            return [
                'data' => $list,
                'count'=> $total,
                'msg'  => '',
                'code' => 0
            ];
        }
        else if ($taskType == ConstantUtils::SEARCH_SERVICE)
        {
            $total = StatisticsService::getSearchCount($date);
            $list  = StatisticsService::getSearchPageList($date, $perPage, $page);
            return [
                'data' => $list,
                'count'=> $total,
                'msg'  => '',
                'code' => 0
            ];
        }
        else if ($taskType == ConstantUtils::FLOW_SERVICE) {
            $cntRes = StatisticsService::ippvStatisticsCount($date);
            $listRes = StatisticsService::ippvStatisticsRes($date, $perPage, $page);
            return [
                'data'  => $listRes,
                'count' => $cntRes,
                'msg'   => '',
                'code'  => 0,
            ];
        }
        else if ($taskType == ConstantUtils::HAO_123_SERVICE)
        {
            $list  = hao_123($date, $perPage, $page);
            $total = hao_123_total($date);
            return [
              'data' => $list,
              'count'=> $total,
              'msg'  => '',
              'code' => 0,
            ];
        }
    }


    /**
     * 小时流量详情
     */
    public function ipPvHourView()
    {
        $taskId = \request('id');
        $taskName = StatisticsService::getTaskName($taskId);
        $date = request('date');
        return view('admin.statistics.ip_pv_hour', [
            'taskName' => $taskName,
            'date'     => $date
        ]);
    }

    public function ipPvHourList()
    {
        $perPage = request('limit', 50);
        $page = request('page', 1);
        $taskId = request('id');
        $total = StatisticsService::countHourIppv($taskId);
        $list = StatisticsService::getHourIppvRes($taskId, $page, $perPage);
        return [
            'data' => $list,
            'count' => $total,
            'msg' => '',
            'code' => 0
        ];
    }
}
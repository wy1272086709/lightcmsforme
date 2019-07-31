<?php
namespace App\Http\Services;
use App\Model\Admin\AreaConfig;
use App\Model\Admin\AreaTask;
use App\Model\Admin\AreaTaskChannel;
use App\Repository\Admin\AreaTaskChannelRepository;
use App\Repository\Admin\AreaTaskMapRepository;
use App\Repository\Admin\AreaTaskRepository;
use App\Repository\Admin\ConfigPRepository;
use App\Repository\Admin\ModuleRepository;
use App\Repository\Admin\TaskSourceRepository;
use App\Repository\Admin\TaskTimeZoneSetRepository;
use App\Repository\Admin\TaskUaRepository;
use App\Repository\Admin\TaskUploadRepository;
use App\Repository\Admin\TaskUrlRepository;
use App\Repository\Admin\TaskWordRepository;
use App\Utils\ArrUtils;
use App\Utils\CommonUtils;
use App\Utils\ConstantUtils;
use App\Repository\Admin\TaskIssuedRepository;
use App\Utils\CurlUtils;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use App\Repository\Admin\AreaConfigRepository;

class AreaTaskService
{
    public static function getIssuedValueMap(\Illuminate\Contracts\Pagination\LengthAwarePaginator $data)
    {
        $items = $data->items();
        $arr = self::getIssuedValueArr($items);
        $fixedIssuedArr = $arr['fixed'];
        $planIssuedArr  = $arr['plan'];
        $fixedIssuedMap = $planIssuedMap = [];
        if ($fixedIssuedArr) {
            $fixedIssuedMap = TaskIssuedRepository::getIssuedValue($fixedIssuedArr, ConstantUtils::FIXED_ISSUE_MODE);
        }
        if ($planIssuedArr) {
            $planIssuedMap = TaskIssuedRepository::getIssuedValue($planIssuedArr, ConstantUtils::PLAN_ISSUE_MODE);
        }
        if ($fixedIssuedMap)
        {
            foreach ($fixedIssuedMap as $k=> $val)
            {
                $planIssuedMap[$k] = $val;
            }
        }
        return $planIssuedMap;
    }

    /**
     * 获取数值
     * @param $items array
     * @return array
     */
    private static function getIssuedValueArr($items)
    {
        $fixedIssuedTaskArr = [];
        $planIssuedTaskArr = [];
        foreach ($items as $row)
        {
            if ($row->issue_style == ConstantUtils::FIXED_ISSUE_MODE)
            {
                $fixedIssuedTaskArr[] = $row->id;
            }
            else if ($row->issue_style == ConstantUtils::PLAN_ISSUE_MODE)
            {
                $planIssuedTaskArr[] = $row->id;
            }
        }
        return [
            'fixed' => $fixedIssuedTaskArr,
            'plan'  => $planIssuedTaskArr
        ];
    }

    /**
     * 添加任务到DB 中.
     * @param $data
     * @param $extraData
     * @param $configData
     * @return boolean
     */
    public static function saveTaskToDb($data, $extraData, $configData)
    {
        $data = ArrUtils::filterEmpty($data);
        $style = isset($data['choice_type']) ? $data['choice_type']: '';
        $data['time_edTime'] = time();
        // $isPc = $data['qx_type'] == 0 ? true: false;
        // time_zone, max_issued
        $extraData  = ArrUtils::filterEmpty($extraData);
        $id = isset($data['id']) ? $data['id']: 0;
        $taskId = AreaTaskRepository::saveTask($data, $id);
        $methods = [ 'taskUrlDataToDb', 'issuedDataToDb', 'updateTaskIssueFields', 'sourceDataToDb', 'channelOrAreaDataToDb',
        'clickSetDataToDb', 'wordDataToDb' ];
        foreach ($methods as $method)
        {
            self::$method($taskId, $data, $extraData);
        }
        self::uaDataToDb($taskId, $configData, $extraData);
        $configDataExtra = self::updateConfigToXml($taskId, $data, $extraData);
        $configData = array_merge($configData, $configDataExtra);
        // 这里应该保存一个config_id, 当修改数据的时候,不然无法找到这个数据
        $configId = self::saveDataToConfig($configData);
        if ($configId) {
            self::setAreaTaskToRedis($taskId);
        } else {
            self::setChannelTaskToRedis($taskId);
        }
        self::publishTaskToRedis($taskId);
        return true;
    }

    /**
     * 任务页面对应的数据入库
     * @param $taskId
     * @param $data
     * @param $extraData
     * @return int
     */
    public static function taskUrlDataToDb($taskId, $data, $extraData)
    {
        $taskUrlStyle = isset($data['task_url_style']) ? $data['task_url_style']: '';
        // 文件上传的时候，为对应的路径信息
        $taskUrlContent = isset($extraData['t_s']) ? trim($extraData['t_s']): '';
        if (!$taskUrlContent) {
            return true;
        }
        if ($taskUrlStyle == ConstantUtils::TASK_URL_UPLOAD_STYLE && $taskUrlContent)
        {
            $configId = isset($data['config_id']) ? $data['config_id']:0;
            $configId && TaskUploadRepository::updateConfigData($data['config_id'], $taskUrlContent, ConstantUtils::TASK_URL_UPLOAD);
        }
        return TaskUrlRepository::saveTaskUrl($taskId, $taskUrlStyle, $taskUrlContent);
    }


    /**
     * 发布数据入库
     * @param $data
     * @param $extraData
     * @param $taskId
     * @return integer
     */
    public static function issuedDataToDb($taskId, $data, $extraData)
    {
        $issueStyle = isset($data['issue_style']) ? $data['issue_style']: '';
        // 计划模式下 json 字符串
        // 固定模式下的数据
        if ($issueStyle == ConstantUtils::FIXED_ISSUE_MODE)
        {
            $issueData = isset($extraData['issued_value']) ? $extraData['issued_value'] : 0;
        } else {
            $issueData  = isset($extraData['issued_json_value']) ? $extraData['issued_json_value']: '';
            $issueData ? $issueData = \json_decode($issueData, true): $issueData = [];
        }
        if (!$issueData)
        {
            return true;
        }
        // 固定模式 和计划模式
        $id = TaskIssuedRepository::saveIssuedValueToDb($taskId, $issueStyle, $issueData);
        return $id;
    }

    public static function updateTaskIssueFields($taskId)
    {
        TaskIssuedRepository::updateTaskIssueFields($taskId);
        return true;
    }

    /**
     * ua数据入库
     * @param $taskId
     * @param $data
     * @param $extraData
     * @return int
     */
    public static function uaDataToDb($taskId, $data, $extraData)
    {
        $uaStyle = isset($data['is_ua']) ? $data['is_ua']: '';
        $uaJsonData  = isset($extraData['ua-set-json']) ? $extraData['ua-set-json']: '';
        if (!$uaJsonData)
        {
            return true;
        }
        return TaskUaRepository::saveUaDataToDb($taskId, $uaStyle, $uaJsonData);
    }

    /**
     * 来源页面数据入库
     * @param $taskId
     * @param $data
     * @param $extraData
     * @return int
     */
    public static function sourceDataToDb($taskId, $data, $extraData)
    {
        $sourceJsonData = isset($extraData['source_json']) ? $extraData['source_json']: '';
        if (!$sourceJsonData)
        {
            return true;
        }
        $sourceStyle = isset($data['source_style']) ? $data['source_style']: '';
        if ($sourceStyle == ConstantUtils::UPLOAD_SOURCE || $sourceStyle == ConstantUtils::UPLOAD_RATIO_SOURCE)
        {
            $configId = isset($data['config_id']) ? $data['config_id']: 0;
            $uploadType = $sourceStyle == ConstantUtils::UPLOAD_SOURCE ? ConstantUtils::SOURCE_UPLOAD: ConstantUtils::SOURCE_RATIO_UPLOAD;
            $configId && TaskUploadRepository::updateConfigData($configId, $sourceJsonData, $uploadType);
        }
        return TaskSourceRepository::saveSourceData($taskId, $sourceStyle, $sourceJsonData);
    }

    /**
     * 地区渠道数据入库
     * @param $taskId
     * @param $data
     * @param $extraData
     * @return integer
     */
    public static function channelOrAreaDataToDb($taskId, $data, $extraData)
    {
        $aqType = isset($data['aq_type']) ? $data['aq_type']: '';
        $channelInfo = isset($extraData['area_channel_info']) ? $extraData['area_channel_info']: '';
        if (!$channelInfo)
        {
            return true;
        }
        $channelInfo = \json_decode($channelInfo, true);
        if ($aqType == ConstantUtils::AQ_TYPE_CITY)
        {
            return AreaTaskMapRepository::saveAreaTaskMapData($taskId, $channelInfo);
        }
        else if ($aqType == ConstantUtils::AQ_TYPE_CHANNEL)
        {
            return AreaTaskChannelRepository::saveAreaTaskChannelData($taskId, $channelInfo);
        }
    }

    /**
     * 时段数据入库
     * @param $taskId
     * @param $data
     * @param $extraData
     * @return integer
     */
    public static function timeZoneDataToDb($taskId, $data, $extraData)
    {
        $timeRangeStyle = isset($data['choice_type']) ? $data['choice_type']: '';
        // dump($timeRangeStyle);
        $timeZoneVal    = isset($extraData['time_zones']) ? $extraData['time_zones']: '';
        if (!$timeZoneVal)
        {
            return true;
        }
        return TaskTimeZoneSetRepository::saveTimeZoneData($taskId, $timeRangeStyle, $timeZoneVal);
    }

    /**
     * 点击设置数据入库
     * @param $taskId
     * @param $data
     * @param $extraData
     * @return integer
     */
    public static function clickSetDataToDb($taskId, $data, $extraData)
    {
        $clickSetData = isset($extraData['click_set_data_info']) ? trim($extraData['click_set_data_info']): '';
        if (!$clickSetData)
        {
            return false;
        }
        $clickSetData = \json_decode($clickSetData, true);
        return ConfigPRepository::saveTaskClickSetConfig($taskId, $clickSetData);
    }


    /**
     * 词库设置数据入库
     * @param $taskId
     * @param $data
     * @param $extraData
     * @return integer
     */
    public static function wordDataToDb($taskId, $data, $extraData)
    {
        $wordData = isset($extraData['word_set_json']) ? $extraData['word_set_json']: [];
        if (!$wordData)
        {
            return true;
        }
        $wordStyle   = isset($data['word_style']) ? $data['word_style']: '';
        if ($wordStyle == ConstantUtils::WORD_FILE_UPLOAD_STYLE)
        {
            $configId = isset($data['config_id']) ? $data['config_id']: 0;
            $configId && TaskUploadRepository::updateConfigData($configId, $wordData, ConstantUtils::WORD_UPLOAD);
        }
        // 词库设置. 0 为文本框导入, 1为文件导入
        return TaskWordRepository::saveTaskWord($taskId, $wordData);
    }


    /**
     * @param $configData
     * @return integer
     */
    public static function saveDataToConfig($configData)
    {
        return AreaConfigRepository::saveAreaConfig($configData);
    }


    /**
     * 根据任务ID获取对应的任务数据
     * @param $taskId
     * @return array
     */
    public static function getTaskData($taskId)
    {
        $taskData = AreaTaskRepository::getTaskRow($taskId);
        // 获取ua相关的数据
        $uaStyle  = isset($taskData['is_ua']) ? $taskData['is_ua']: '';
        $sourceStyle = isset($taskData['source_style']) ? $taskData['source_style']: '';
        $taskUrlStyle= isset($taskData['task_url_style']) ? $taskData['task_url_style']: '';
        $timeRangeStyle = isset($taskData['choice_type']) ? $taskData['choice_type']: '';
        $areaConfig = AreaConfigRepository::getOneAreaConfig($taskData['config_id']);
        $aqType = isset($taskData['aq_type']) ? $taskData['aq_type']: '';
        // 获取ua 相关的数据
        $uaData      = TaskUaRepository::getTaskUaData($taskId, $uaStyle);
        // 获取source相关的数据
        $sourceData  = TaskSourceRepository::getTaskSourceData($taskId, $sourceStyle);
        // 获取任务页面相关数据
        $taskUrlData = TaskUrlRepository::getTaskUrlData($taskId, $taskUrlStyle);
        // 获取地区/渠道相关的数据
        if ($aqType == ConstantUtils::AQ_TYPE_CHANNEL)
        {
            $areaChannelData = AreaTaskChannelRepository::getAreaTaskChannelRow($taskId);
        }
        else
        {
            $areaChannelData = AreaTaskMapRepository::getAreaTasMapRow($taskId);
        }
        // 发布数值的方式, 0为固定, 1为计划模式
        $issueStyle = isset($taskData['issue_style']) ? $taskData['issue_style']: '';
        $issueData  = TaskIssuedRepository::getTaskIssuedValueRes($taskId, $issueStyle);
        $timeZoneVal = $taskData['time_zone'];
        // 获取点击设置相关的数据
        $clickSetData = ConfigPRepository::getTaskClickSetData($taskId);
        $configIdVal = AreaTaskRepository::getConfigId($taskId);
        $uploadData = TaskUploadRepository::getUploadedData($taskId);
        $extraData = [
            'uaData'         => $uaData,
            'areaConfigData' => $areaConfig,
            'sourceData' => $sourceData,
            'taskUrlData'=> !empty($taskData['task_url']) ? $taskData['task_url']: $taskUrlData,
            'areaChannelData' => $areaChannelData,
            'timeZoneData'    => $timeZoneVal,
            'clickSetData'    => $clickSetData,
            'issueData'       => $issueData,
            'config_id'       => $configIdVal,
            'uploadInfo'      => $uploadData
        ];
        return array_merge($extraData, $taskData);
    }

    public static function setUidGroupToRedis()
    {
        // 如果不存在对应的值
        if (!Redis::hmget('_uid_group')) {
            $url = "http://hs.ffzip.com/api/__cid_2_gid.php";
            $curl = new CurlUtils();
            $curl_str = $curl->get($url);
            $cache_uid_group = \json_decode($curl_str, true);
            // 特殊处理 8300
            $cache_uid_group[8300] = 10;
            Redis::hmset('_uid_group', $cache_uid_group);
        }
    }

    /**
     * 存储地区数据到redis中
     * @param $taskId
     * @return array
     */
    public static function setAreaTaskToRedis($taskId)
    {
        $rows = AreaTaskMapRepository::getTaskMapData($taskId);
        if (!$rows)
        {
            return [];
        }
        $res = [];
        foreach ($rows as $row)
        {
            $row['g_id']['ratio'] = 100;
            if (isset($row['task_id']) && $row['task_id']>0 && !array_key_exists('uid_task_list', $res[$row['g_id']]))
            {
                $res[$row['g_id']]['uid_task_list'][$row['u_id']][] = $taskId;
            }
        }
        foreach ($res as $gid => $vm)
        {
            // mn_group_task
            Redis::hset('mn_group_task', $gid, \json_encode($vm, true));
        }
        //

    }

    /**
     * 存储渠道数据到redis中
     * @param $taskId
     * @return array
     */
    public static function setChannelTaskToRedis($taskId)
    {
        $rows = AreaTaskChannelRepository::getAreaTaskChannelRow($taskId);
        if (!$rows)
        {
            return [];
        }
        $res = [];
        foreach ($rows as $row)
        {
            if (isset($row['task_id']) && $row['task_id']>0 && !array_key_exists('uid_task_list', $res[$row['g_id']]))
            {
                $res[$row['g_id']]['uid_task_list'][$row['u_id']][] = $taskId;
            }
        }
        foreach ($res as $gid => $vm)
        {
            Redis::hset('mn_group_task', $gid, \json_encode($vm, true));
        }
        // $redis->hset('mn_group_task',$km,json_encode($vm));
    }






    public static function updateConfigToXml($taskId, $data, $extraData)
    {
        $yuan_file_path = config('path.yuan_file_path');
        $file_path      = config('path.file_path');
        $contens = self::getXmlConfigStr($taskId, $data, $extraData);
        $contens = preg_replace('/\srep="(.*?)"/', "", $contens);
        $contens = preg_replace("/\srep='(.*?)'/", "", $contens);
        $pos     = stripos($contens, ">");
        $contens = CommonUtils::strInsert($contens, $pos, " rep='$taskId'");
        $exname  = date('Ym').'/'.$taskId.'_'.time().'.xml';        //加密文件
        $yuan_exname = date('Ym').'/'.$taskId.'_'.time().'.xmls';  //源文件
        if(!is_dir($yuan_file_path.date('Ym'))) @mkdir($yuan_file_path.date('Ym'),0777,true);
        if(!is_dir($file_path.date('Ym'))) @mkdir($file_path.date('Ym'),0777,true);
        $filename = $file_path. $exname;
        $aes = new \App\Utils\Aes();
        $aesKey = config('path.cfg_api.aes_key');
        $encStr   = $aes->encode($aesKey, $contens);   //加密xml 并保存文件
        $hash     = sprintf('%08X', crc32($encStr));
        file_put_contents($filename, $encStr);    //保存加密文件
        file_put_contents($yuan_file_path.$yuan_exname, $contens);    //保存原文件
        return [
            'file_name' => $exname,
            'hash'      => $hash
        ];
    }


    /**
     * 在上传文件的情况下，这里对应的取数据的方式，要改一下的
     * @param $data
     * @param $taskId
     * @param $extraData
     * @return string
     */
    public static function getXmlConfigStr($taskId, $data, $extraData)
    {
        // 任务页面,来源页面，多个的情况下，都是取其中的一个
        $fields = [
          'ip_pv_value', 't_s', 'stay_time'
        ];
        $otherFields = [
            // 来源页面, 曝光页面, 点击设置对应的数据
          'source_json', 'exposure_page_url', 'click_set_data_info'
        ];
        $postData  = Arr::only($data, $fields);
        $otherData = Arr::only($extraData, $otherFields);
        // 任务页面数据
        $configId = isset($data['config_id']) ? $data['config_id']:0;
        $taskUrlStyle = isset($data['task_url_style']) ? $data['task_url_style']: '';
        $sStr = self::getTaskUrlForXml($taskId, $taskUrlStyle, $configId);
        $sourceStyle = isset($data['source_style']) ? $data['source_style']: '';
        $rfStr = self::getSourceForXml($taskId, $sourceStyle, $configId);
        $link  = isset($data['statistics_link']) ? trim($data['statistics_link']): '';
        $exposurePageUrl = isset($otherData['exposure_page_url']) ? trim($otherData['exposure_page_url']): '';
        $ipPvVal = isset($postData['ip_pv_value']) ? $postData['ip_pv_value']:'';
        $stayTimeVal = isset($postData['stay_time']) ? $postData['stay_time']: '';
        $str_t = '<t ip_pv="'. $ipPvVal .'" s="'. $sStr.'" firsttime="'. $stayTimeVal
            .'" rf="'. $rfStr .'" impurl="'. $exposurePageUrl .' " domain="'.$link. '" rep="'. $taskId .'">';
        $list  = isset($extraData['click_set_data_info']) ? \json_decode($extraData['click_set_data_info'], true): [];
        $newList = [];
        foreach ($list as $row)
        {
            $newList[$row['cid']] = $row;
        }
        $tree  = self::generateTree($newList);
        $treeStr = self::getTreeData($tree);
        return $str_t. "\n". $treeStr. "\n</t>";
    }

    /**
     * 来源对应的要生成的xml 是怎么样的格式，需要待定
     * @param $taskId
     * @param $sourceStyle
     * @param $configId
     * @return array
     */
    private static function getSourceForXml($taskId, $sourceStyle, $configId)
    {
        return TaskSourceRepository::getTaskRealSourceData($taskId, $sourceStyle, $configId);
    }

    /**
     * @param $taskId
     * @param $taskUrlStyle
     * @param $configId
     * @return array|mixed
     */
    private static function getTaskUrlForXml($taskId, $taskUrlStyle, $configId)
    {
        $taskData = AreaTaskRepository::getTaskRow($taskId);
        $taskUrlData = TaskUrlRepository::getRealTaskUrlData($taskId, $taskUrlStyle, $configId);
        if (is_array($taskUrlData) && $taskUrlData) {
            $randomKey = array_rand($taskUrlData, 1);
            $url = $taskUrlData[$randomKey];
        } else {
            $url = '';
        }
        return !empty($taskData['task_url']) ? $taskData['task_url']: $url;
    }

    /**
     * 获取程序执行的结果
     * @param $taskId
     * @return bool
     */
    public static function publishTaskToRedis($taskId)
    {
        //地区任务：area_task	、area_module、area_config：
        $taskRow = AreaTaskRepository::getTaskRow($taskId, 1);
        $groupId = isset($taskRow['task_group_id']) ? $taskRow['task_group_id']: 0;
        $configId = isset($taskRow['config_id']) ? $taskRow['config_id']: 0;
        $groupId ? $moduleObject = ModuleRepository::find($groupId): $moduleObject = [];
        $configId ? $configArr   = AreaConfigRepository::getOneAreaConfig($configId): $configArr = [];
        $taskId ? $configPArr = ConfigPRepository::getTaskClickSetData($taskId) : $configPArr = [];
        if ($moduleObject)
        {
            $moduleRes = [
              'name'    => $moduleObject->module_name,
              'dec_hash'=> $moduleObject->before_hash,
              'enc_hash'=> $moduleObject->after_hash,
              'ver'     => $moduleObject->version,
                'url'   => $moduleObject->file_name
            ];
        } else {
            $moduleRes = [];
        }
        if ($configArr)
        {
            $configRes = [
                'config'    => $configArr['file_name'],
                'conf_type' => $configArr['type'],
                'is_ua'     => $configArr['is_ua'],
                'is_hao'    => $configArr['is_hao'],
                'hao_pz'    => $configArr['hao_pz'],
                'is_big'    => $configArr['is_big'],
                'rf_list'   => $configArr['rf_list'],
                's_list'    => $configArr['s_list'],
                'gjc_list'  => $configArr['gjc_list'],
                'cfg_hash'  => $configArr['hash'],
                'we_choose' => $configArr['weekend_choose'],
            ];
        } else {
            $configRes = [];
        }
        // 这里少了time_start,time_end,task_category 字段，看下对于程序有什么影响.
        $taskRow ? $taskInfoArr = Arr::only($taskRow, ['ratio', 'level', 'aq_type', 'time_zone', 'time_edTime',
            'max_issued', 'max_return', 'time_issued', 'r_mnsc' ]): $taskInfoArr = [];
        // 通过分组获取对应的模块信息
        // 通过config_id 获取对应的配置信息
        // 获取对应的数据表中的其他信息, 然后存到redis 中去.
        $taskRes =  array_merge($moduleRes, $configRes, [ 'config_p' => $configPArr ], $taskInfoArr);
        $re = Redis::hset('mn_task_info', 'area_'. $taskId, \json_encode($taskRes, true));
        return $re;
    }



    //无限极数组拼接
    public static function getTreeData($tree) {
        static $str;
        foreach($tree as $t){
            $str .= "<p name1='{$t['time_interval']}' name2='{$t['odds']}' click_area='{$t['click_area']}'>\n";
            if(isset($t['son'])){
                self::getTreeData($t['son']);
            }
            $str .= "</p>\n";
        }
        return $str;
    }

    //无限极分类生成树状结构数组
    public static function generateTree($items) {
        $tree = array();
        foreach($items as $item){
            if(isset($items[$item['parent_id']])){
                $items[$item['parent_id']]['son'][] = &$items[$item['cid']];
            }else{
                $tree[] = &$items[$item['cid']];
            }
        }
        return $tree;
    }
}
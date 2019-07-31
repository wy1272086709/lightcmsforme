<?php
namespace App\Http\Services;

use App\Repository\Admin\AreaTaskChannelRepository;
use App\Repository\Admin\AreaTaskMapRepository;
use App\Repository\Admin\AreaTaskRepository;
use App\Repository\Admin\DayAreaNsmRepository;
use App\Repository\Admin\TaskGroupRepository;
use App\Utils\CurlUtils;

global $province_conf, $ad_server_conf, $ad_client_conf;
include_once realpath(dirname(base_path(), 2)).'/config/conf.php';

class TaskService
{

    /**
     * 获取时段设置对应的json字符串
     * @param $timeRangeType 1 表示时段分配, 2表示曲线分配
     * @param $isPc 1 表示PC, 0表示Mobile
     * @param $postData | array 传递过来的时段设置数据
     * @return string
     */
    public static function getTimeZone($timeRangeType, $isPc, $postData)
    {
        global $ad_server_conf;
        $zoneVal = isset($postData['time_zones']) ? $postData['time_zones']: [];
        if ($timeRangeType == 1)
        {
            return $zoneVal;
        }
        $quxian = (float)$zoneVal;
        if ($isPc)
        {
            if ((date('w') == 6) || (date('w') == 0))
            {
                $conf = $ad_server_conf['time_ratio']['area_task']['weekend'];    //周末曲线
            }
            else {
                $conf = $ad_server_conf['time_ratio']['area_task']['workday'];    //工作日曲线
            }
        }
        else
        {
            $conf = $ad_server_conf['time_ratio']['area_task']['mobile_work'];
        }

        $time  =  ['00', '01', '02', '03', '04', '05', '06','07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17','18', '19', '20', '21', '22', '23'];
        $i = 0;
        $timeZone = array();
        foreach ($conf as $k => $per) {
            $v = floatval((float)(rtrim($per, '%')) * $quxian) / 100;
            $timeZone[$time[$k]] = floor($v);
            $i += floor($v);
        }
        if($quxian-$i > 0){
            $diff = $quxian-$i;
            foreach ($timeZone as $h=>$num){
                if($diff == 0) break;
                $timeZone[$h] += 1;
                $diff--;
            }
        }
        foreach ($timeZone as $k=>$v){
            if(!$v) unset($timeZone[$k]);
            else $timeZone[$k]=(int)$v;
        }
        $timeZone = (!empty($timeZone))?\json_encode($timeZone):'';
        return $timeZone;
    }


    /**
     * 获取地区数据
     * @param $dates
     * @param $key
     * @return array
     */
    public static function getAreaData($dates, $key)
    {
        $Data = DayAreaNsmRepository::getDayAreaNsmData($dates, $key);
        $data = array();
        foreach ($Data as $v){
            $data[$v->province_id][$v->city_id] = $v->num_ip;
        }
        foreach ($data as $p => $c_array){
            if(count($c_array) == 1) $data[$p] = isset($c_array[0])?$c_array[0]:current($c_array);
            $data[$p][0] = array_sum($c_array);
            is_array($data[$p])&& ksort($data[$p]);
        }
        asort($data);
        return $data;
    }


    public static function getAreaVarsForView($dates, $key)
    {
        global $province_conf;
        $areaConf = self::loadAreaConf();
        $res = self::getAreaData($dates, $key);
        $provinceNameMap = [];
        $provinceRes = [];
        $hasCityDataRes = $pinyinMap = [];
        $pinyinRes = config('light.province_pinyin');
        foreach ($res as $k => $row)
        {
            if (!is_array($row))
            {
                $provinceRes[$k] = $row;
                $provinceNameMap[$k] = self::getProvinceName($k);
            }
            else
            {
                $hasCityDataRes[$k] = $row;
                $provinceName  = self::getProvinceName($k);
                $provinceNameMap[$k] = $provinceName;
                $pinyinMap[$k] = isset($pinyinRes[$provinceName]) ? $pinyinRes[$provinceName]: '';
            }
        }

        return [
            'provinceNameMap' => $provinceNameMap,
            'area_conf_json'  => $areaConf,
            'provinceRes'     => $provinceRes,
            'hasCityDataRes'  => $hasCityDataRes,
            'provincePinyinMap' => $pinyinMap
        ];
    }


    public static function getCityName($provinceId, $cityId)
    {
        $area_conf_json = self::loadAreaConf();
        return isset($area_conf_json[$provinceId][$cityId]) ? $area_conf_json[$provinceId][$cityId]: '';
    }

    public static function getProvinceName($k)
    {
        $proId = $k - 1;
        global $province_conf;
        $map = [
            0 => '国外',
            99=> '未知',
        ];
        $val = isset($province_conf[$proId])? $province_conf[$proId]: '';
        return isset($map[$k])?$map[$k]:$val;
    }

    /**
     * 获取province_id
     * @param $taskId
     * @return array
     */
    public static function getProvinceId($taskId)
    {
        $taskData = AreaTaskMapRepository::getTaskMapData($taskId);
        $provinceIdMap = [];
        foreach ($taskData as $k => $row)
        {
            if ($row['city_id'] == 0)
            {
                $provinceIdMap[$row['province_id']] = 1;
            }
        }
        return array_keys($provinceIdMap);
    }

    /**
     * 获取城市ID
     * @param $taskId
     * @return array
     */
    public static function getCityId($taskId)
    {
        $taskData = AreaTaskMapRepository::getTaskMapData($taskId);
        $res =  $taskData ? array_column($taskData, 'city_id'): [];
        return $res ? array_unique($res): [];
    }

    public static function loadMainConf()
    {

    }


    public static function loadAreaConf()
    {
        include_once realpath(dirname(base_path())).'/lib/area_conf.php';
        return \json_decode($area_conf_json, true);
    }

    public static function getChannelData($date)
    {
        $curlRes = self::getChannelSourceData($date);
        $qdArr   = $curlRes['gpcnum'];
        $arrOne = [];
        $arrTwo = [];
        //没有子渠道的放上面
        if(count($qdArr)>0){
            foreach($qdArr as $ko=>$vo) {
                //如果组渠道没有子渠道-循环插入组渠道
                if(gettype($vo)=='string' || gettype($vo)=='integer'){
                    $arrOne[$ko] = $vo;
                }
                //如果组渠道有子渠道-同时循环插入子渠道
                if(gettype($vo)=='array') {
                    $arrTwo[$ko] = $vo;
                }
            }
        }
        //没有子渠道的添加到分组渠道数组
        foreach($arrOne as $kr=>$vr){
            $arrTwo[$kr] = $vr;
        }
        asort($arrTwo);//没有子渠道的放上面
        return $arrTwo;
    }

    public static function getChannelVarsForView($date, $taskId = '')
    {
        $data = self::getChannelData($date);
        $groupRes = [];
        $childGroupRes = [];
        foreach ($data as $k => $row)
        {
            if (!is_array($row))
            {
                $groupRes[$k] = $row;
            }
            else
            {
                $childGroupRes[$k] = $row;
            }
        }
        // 分组名称
        $groupName = self::getGroupNameArr($date);
        //获取对应的g_id 和u_id 数组
        if ($taskId)
        {
            $gIdArr = self::getChannelGids($taskId);
            $uIdArr = self::getChannelUids($taskId);
        }
        // 渠道选用次数
        $channelUseCount = self::getCountChannel();
        $vars = [
            'firstLevelChannelRes' => $groupRes,
            'TwoLevelChannelRes'   => $childGroupRes,
            'groupNameMap'    => $groupName,
            'channelUseCount' => $channelUseCount,
        ];
        $taskId && $vars['taskId'] = $taskId;
        isset($gIdArr) && $vars['gIds'] = $gIdArr;
        isset($uIdArr) && $vars['uIds'] = $uIdArr;
        return $vars;
    }

    /**
     * 获取同分组已选渠道
     * @param $taskId
     * @param $groupId
     */
    public static function getSameGroupChannels($taskId, $groupId)
    {


    }

    public static function getChannelSourceData($date)
    {
        //渠道接口：
        $url = "http://simulation.faafox.com/api/cuid_pcnum.php?type=gid&data=nsm&date=$date";
        $curlStr = CurlUtils::get($url);
        $curlRes = json_decode($curlStr,true);
        $json = json_decode('{"gname":{"2":"\u65b0\u7ec4_1005","6":"P5_1001X","7":"N17x6408","9":"\u5206\u7ec41703IE-HTS","10":"\u5206\u7ec4 SouGou 5k","11":"NS_6501","12":"\u7279-8100-1011","13":"tmp_CMD-6x6","15":"\u5206\u7ec4_82\/8000","16":"\u65b0\u7ec41006","17":"NG_1012","20":"NG_1014","21":"\u5206\u7ec4K_0202","22":"NS_6000","23":"\u65b0\u7ec4_660\u7cfb","25":"N19x1018","28":"640*-\u7ec4","29":"NS_6301","30":"\u7a7a\u89c4\u5219\u7528","31":"\u6d4b-N86X4","32":"N_1019","33":"N_1020","34":"1905N_1021","35":"1905N_1022","36":"1905N_1024","37":"N1905_1025","38":"N1905_1026","39":"N1906_1027"},"gpcnum":{"13":268073,"16":{"100620":10367,"100623":8825,"0":224630,"100622":9386,"100625":6084,"100616":18598,"100619":11325,"100611":18702,"100615":18739,"100624":9615,"100613":19004,"100621":11221,"100618":12021,"100614":18837,"100610":19100,"100617":12913,"100612":19128},"28":{"640530":4011,"640524":1318,"640518":2882,"640527":1567,"640528":1354,"640516":2667,"640523":1475,"0":47318,"640521":1949,"640519":2819,"640510":2799,"640520":2665,"640526":1239,"640522":1642,"640515":2690,"640529":1618,"640512":2691,"640514":2684,"640525":1219,"640517":2649,"640513":2623,"640511":2742},"6":{"0":81905,"100116":2545,"100110":16858,"100112":16568,"100113":12675,"100115":7429,"100111":16574,"100114":9048},"34":{"102112":23148,"102110":19939,"102113":20624,"102111":20574,"0":84285},"25":{"101817":17109,"101825":20261,"101822":19010,"101818":17626,"101810":20924,"101816":16493,"101824":20013,"101820":17674,"101813":17200,"101827":19149,"101811":19310,"101821":18545,"101826":20864,"101823":19262,"101814":16384,"101815":16413,"101828":5799,"101812":16813,"0":336860,"101819":17362},"21":{"660513":1611,"0":11754,"660512":2330,"660510":3013,"660511":3026,"660514":1638},"2":7706,"38":{"102612":23058,"102622":25546,"102624":28210,"102610":30632,"102621":24876,"102616":23401,"102611":26473,"102615":23875,"102619":24487,"102613":23277,"102623":25974,"102617":24229,"102618":24969,"102620":25309,"102614":23944,"102625":6314,"0":384574},"12":{"0":23852,"101110":16131,"101111":7627},"9":1345,"7":{"0":107606,"640833":2887,"640841":1834,"640825":2899,"640842":1686,"640831":2939,"640821":2924,"640811":3123,"640845":2001,"640846":2859,"640818":2895,"640836":2079,"640813":3037,"640844":1536,"640826":2959,"640832":2896,"640848":2569,"640828":2967,"640839":1784,"640843":1687,"640835":2388,"640823":2932,"640812":3132,"640847":1935,"640827":2895,"640824":2835,"640829":2853,"640814":2881,"640815":2931,"640849":5737,"640840":1537,"640820":3070,"640819":3055,"640834":2895,"640837":1919,"640810":2987,"640822":2934,"640838":1708,"640830":3107,"640817":3061,"640816":3120},"36":{"102415":20954,"102410":21793,"102417":22902,"102413":22071,"102418":14449,"102411":20916,"102416":22057,"102414":22581,"102412":22874,"0":190597},"23":4808,"17":16452,"32":{"0":44542,"101911":16681,"101912":9624,"101910":18236},"15":1069,"22":3799,"10":{"830013":3204,"830010":3425,"830022":2515,"830024":1058,"830018":1661,"0":34564,"830015":1938,"830017":1871,"830023":2057,"830020":1944,"830012":3261,"830016":1921,"830021":2044,"830014":2350,"830019":1906,"830011":3222},"33":{"102015":12116,"102016":8956,"102014":14302,"102012":13093,"102011":14587,"102010":16070,"102013":13527,"0":92651},"11":2437,"20":{"101411":10749,"101414":13200,"101413":15265,"101412":8925,"101415":6888,"101410":11573,"0":66600},"29":3016,"39":{"102710":18395,"0":18395},"35":{"102210":3001,"0":3001},"37":5353,"None":4,"31":1},"sn2nsm":{"13":[0],"0":["Error: ..\/..\/data\/_simulation_notin_nsm_19-07-09.txt not found!"]}}', true);
        return $json;
    }

    public static function getGroupNameArr($date)
    {
        $data = self::getChannelSourceData($date);
        return isset($data['gname'])? $data['gname']: [];
    }

    /**
     * 获取对应的渠道g_id 数据
     * @param $taskId
     * @return array
     */
    public static function getChannelGids($taskId)
    {
        $res = AreaTaskChannelRepository::getChannelData($taskId);
        $gIds = [];
        foreach ($res as $row)
        {
            if ($row['u_id'] == 0)
            {
                $gIds[$row['g_id']] = 1;
            }
        }
        return array_keys($gIds);
    }

    /**
     * 获取对应的渠道u_id 数据
     * @param $taskId
     * @return array
     */
    public static function getChannelUids($taskId)
    {
        $res  = AreaTaskChannelRepository::getChannelData($taskId);
        $uIds = $res ? array_column($res, 'u_id'): [];
        return $uIds ? array_unique($uIds): [];
    }

    /**
     * 渠道被选用次数
     * @return array
     */
    public static function getCountChannel()
    {
        $taskIds = AreaTaskRepository::getEnableTaskIds();
        $data = AreaTaskChannelRepository::getAreaTaskChannelData($taskIds);
        $arr = array();
        foreach ($data as $v){
            if( $v->u_id > 0 ){    //子渠道
                if(!array_key_exists($v->u_id, $arr)) $arr[$v->u_id]=1;
                else $arr[$v->u_id]+=1;
            }else{   //主渠道
                if(!array_key_exists($v->g_id, $arr)) $arr[$v->g_id]=1;
                else $arr[$v->g_id]+=1;
            }
        }
        return $arr;
    }

    public static function getGroupName($date, $k)
    {
        $gnameArr = self::getGroupNameArr($date);
        return $gnameArr[$k]!=''? $gnameArr[$k]: '小组_'.$k;
    }

    /**
     * 获取任务分组列表
     * @param $taskType
     * @param $platForm
     * @return array
     */
    public static function getTaskGroupList($taskType, $platForm)
    {
        $map = [
            'service_type' => $taskType,
            'platform'     => $platForm,
            'enable'       => 1
        ];
        // 获取任务Map
        $taskGroupList = TaskGroupRepository::getTaskGroupList($map);
        $taskGroupMap = [];
        foreach ($taskGroupList as $item) {
            $taskGroupMap[$item['id']] = $item['group_name'];
        }
        return $taskGroupMap;
    }

}
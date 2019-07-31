<?php

use Illuminate\Database\Eloquent\Model;
use App\Foundation\Tire;
use Illuminate\Support\Facades\Cache;
use App\Model\Admin\SensitiveWord;
use Illuminate\Support\Facades\DB;

function parseEntityFieldParams($params)
{
    $items = explode("\n", $params);
    return array_map(function ($item) {
        return explode("=", $item);
    }, $items);
}

function isChecked($value, $options)
{
    return in_array($value, explode(',', $options), true);
}

function xssFilter(Model $data)
{
    $attributes = $data->getAttributes();
    foreach ($attributes as &$v) {
        if (is_string($v)) {
            $v = htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8');
        }
    }
    $data->setRawAttributes($attributes);
}

function initTire()
{
    return Cache::rememberForever('sensitive_words_tire', function () {
        $tires = [];

        foreach (['noun', 'verb', 'exclusive'] as $v) {
            $words = SensitiveWord::query()->select($v)->where($v, '<>', '')->get();

            $tire = new Tire();
            foreach ($words as $k) {
                $tire->add($k->$v);
            }
            $tires[$v] = $tire;
        }

        return $tires;
    });
}

function initTireSingle()
{
    return Cache::rememberForever('sensitive_words_tire_single', function () {
        $types = SensitiveWord::query()->select('type')->groupBy('type')->get();
        $tire = new Tire();
        foreach ($types as $type) {
            $words = SensitiveWord::query()->where('type', $type->type)->get();
            $nouns = [];
            $verbs = [];
            $exclusives = [];
            foreach ($words as $word) {
                if ($word->noun !== '') {
                    $nouns[] = $word->noun;
                } elseif ($word->verb !== '') {
                    $verbs[] = $word->verb;
                } elseif ($word->exclusive !== '') {
                    $exclusives[] = $word->exclusive;
                }
            }

            foreach ($exclusives as $k) {
                $tire->add($k);
            }
            foreach ($verbs as $vk) {
                foreach ($nouns as $nk) {
                    $tire->add($vk . $nk);
                }
            }
        }

        return $tire;
    });
}

function mapTypeToVerbOfSensitiveWords()
{
    return Cache::rememberForever('sensitive_verb_words', function () {
        $words = SensitiveWord::query()->select('verb', 'type')->where('verb', '<>', '')->get();

        $data = [];
        foreach ($words as $word) {
            $data[$word->type <> '' ? $word->type : 'others'][] = $word->verb;
        }

        return $data;
    });
}

/**
 * 敏感词检查
 *
 * @param string $text 待检查文本
 * @param string $type 名词、动词的检测方法。默认为 join 。join：名词和动词相连组合在一起视为违规 all：名词和动词只要同时出现即为违规
 * @param null $mode 检查模式。仅 $type 为 all 时有效。默认名词、动词、专用词都检查，显示可指定为 noun verb exclusive
 * @return array
 */
function checkSensitiveWords(string $text, $type = 'join', $mode = null)
{
    if (!is_null($mode) && !in_array($mode, ['noun', 'verb', 'exclusive'])) {
        throw new \InvalidArgumentException('mode参数无效，只能为null值、noun、exclusive');
    }

    if ($type === 'join') {
        $tire = initTireSingle();
        $result = $tire->seek($text);
        return $result;
    }

    $tires = initTire();
    if (!is_null($mode)) {
        return $tires[$mode]->seek($text);
    }

    $result = [];
    $return = [];
    foreach ($tires as $k => $tire) {
        $result[$k] = $tire->seek($text);
    }
    if (!empty($result['noun']) && !empty($result['verb'])) {
        $data = mapTypeToVerbOfSensitiveWords();
        foreach ($result['noun'] as $noun) {
            $type = Cache::rememberForever('sensitive_words_noun_type:' . $noun, function () use ($noun) {
                return SensitiveWord::query()->where('noun', $noun)->value('type');
            });
            $type = $type ? $type : 'others';
            $verbs = array_intersect($data[$type], $result['verb']);
            if (!empty($verbs)) {
                array_push($verbs, $noun);
                $return[] = implode(' ', $verbs);
            }
        }
    }
    return array_merge($return, $result['exclusive']);
}

function get_today_return_log($searchTime){
    $_root_dir = config('path.root_path');
    require_once($_root_dir.'/entry/lib/_nsm_issued.php');
    $cache_path = config('path.cache_path');
    // Use Cache
    /*
     * 2018-10-14_nsm_issuedx.json
     * 2018-10-14_nsm_returnx.json
     */
    $_cache_issued_file = $cache_path.$searchTime.'_nsm_issuedx.json';
    $_cache_return_file = $cache_path.$searchTime.'_nsm_returnx.json';

    if( file_exists($_cache_issued_file) && file_exists($_cache_return_file) ){
        $response = array();
        $_issued_content = \json_decode(file_get_contents($_cache_issued_file),true);
        $_return_content = \json_decode(file_get_contents($_cache_return_file),true);
        if( array_key_exists('sum',$_issued_content) ){
            $response = $_issued_content['sum'];
            foreach($_issued_content['sum'] as $_tid=>$_tinfo){
                // Insert: taskID
                $response[$_tid]['taskID'] = $_tid;
                $response[$_tid]['date']   = $searchTime;
                $response[$_tid]['IP']     = _nsm_issued($_tid,'sum','ip');
                $response[$_tid]['PV']     = _nsm_issued($_tid,'sum','pv');
                // Insert: succ_ip succ_pv
                if( array_key_exists($_tid,$_return_content) ){
                    $response[$_tid]['succ_ip'] = $_return_content[$_tid]['ip'];
                    $response[$_tid]['succ_pv'] = $_return_content[$_tid]['pv'];
                }
            }
        }
        ksort($response);
        return $response;
    }else{
        echo 	$_cache_issued_file.' not found!';
    }
    $file_path = config('path.file_path');
    $db =
    $month = date('ym', strtotime($searchTime));

    $sql   = "select '".$searchTime."' as date, taskID, count(IP) as PV , count(DISTINCT IP) as IP , taskType from nxm_hour_" . $month . " where left(date,10)='" . $searchTime . "' group by taskID";  //task_ippv

    $_key_cache_issued = '_nsm_issued_ippv';
    $query_data = get_cache($_key_cache_issued);

    if( $query_data == false )
    {
        $query_data  = DB::select($sql);
        foreach ($query_data as $k=>$row)
        {
            $newRow = (array) $row;
            $query_data[$k] = $newRow;
        }
        //补差一个小时的数据
        $result = get_now_nsm_hour_data();
        foreach ( $query_data as $_key=>$_value ){
            if( isset($result[$_value['taskID']]) ){
                if( $result[$_value['taskID']]['pv']>0 ){
                    $query_data[$_key]['PV'] += $result[$_value['taskID']]['pv'];
                }
            }
        }

        set_cache($_key_cache_issued,$query_data,600);
    }

    $riqi = date("ymd", strtotime($searchTime));
    $file_name = $file_path . "_tj_" . $riqi . ".txt";
    if ( file_exists($file_name) ) {

        $_today_ippv = count_nsm_ippv($file_name);

        if( count($_today_ippv) >0 ){

            foreach ( $query_data as $k => &$v ){
                $task_id = $v['taskID'];
                $v['succ_pv'] = $_today_ippv[$task_id]['pv'];
                $v['succ_ip'] = $_today_ippv[$task_id]['ip'];
            }
            return $query_data;
        }
    }else{
        echo $file_name.'file not found!';
    }
    return array();
}

function get_now_nsm_hour_data(){
    //弥补当前小时的数据
    $date = date('y-m-d_H', time());
    $file_name = str_replace('entry','log',$_SERVER['DOCUMENT_ROOT']).'/_nsm_log/'. $date .'_nsm_hour.txt';
    $array = [];
    if (file_exists($file_name)) {
        $fhand = fopen($file_name, 'r');
        $str_no = array("\n", "\r");
        while (!feof($fhand)) {
            $line = fgets($fhand); //读取一行
            $line = str_replace($str_no, '', $line);
            if (empty($line)) continue;
            $arr_data = \json_decode($line, true);
            $array[$arr_data['taskID']][] = $arr_data['IP'];
        }
        fclose($fhand);
    }
    $result = [];
    foreach ($array as $taskID=>$value){
        $result[$taskID]['pv'] = count($value);
    }
    return $result;
}


function count_nsm_ippv($file_name){
    $response   = array();

    $_cache_key = '_nsm_tj_'.basename($file_name,".txt").'_ippv';
    $response  = get_cache($_cache_key,0);
    //if( $response != false ) print_r($response);exit();

    if( file_exists($file_name) && $response==false ) {
        $log_res = array();
        //= analysis file content
        $fhand = fopen($file_name, 'r');
        $str_no = array("\n", "\r");
        while (!feof($fhand))
        {
            $line = fgets($fhand); //读取一行
            $line = str_replace($str_no, '', $line);
            if (empty($line)) continue;
            $arr_data = \json_decode($line, true);
            if(empty($arr_data) || !array_key_exists('ID', $arr_data)) continue;  //数据ID键缺失
            if(!array_key_exists($arr_data['ID'], $log_res)) $log_res[$arr_data['ID']] = array();
            if(!array_key_exists($arr_data['IP'],$log_res[$arr_data['ID']])) $log_res[$arr_data['ID']][$arr_data['IP']] = 0;
            $log_res[$arr_data['ID']][$arr_data['IP']] += 1;
        }
        fclose($fhand);
        //= END
        //- if insert
        if( count($log_res) > 0 ){
            //-- sum Task:ip/pv
            foreach($log_res as $tid=>$tipv){
                $response[$tid] = array(
                    'ip' => count($tipv),
                    'pv' => array_sum($tipv)
                );
            }
            unset($log_res);
            //--
        }
        //-
        // set cache
        set_cache($_cache_key,$response,1200);
    }
    return $response;
}


function set_cache($key,$value,$timeout=0){
    $cache_path = str_replace('entry','cache',$_SERVER['DOCUMENT_ROOT']).'/_cache_'.$key;
    //	cache content
    $cache_content = base64_encode(serialize($value));

    //	cache file
    $cache_file_x = $cache_path."x";
    $cache_file_y = $cache_path."y";

    //  加入缓存时间
    if( $timeout > 0 ) $cache_content.= "\n".$timeout;

    //	cache time
    if( file_exists($cache_file_x) )	$cache_x_time = filemtime($cache_file_x);
    if( file_exists($cache_file_y) )	$cache_y_time = filemtime($cache_file_y);
    //		write cache
    if( !file_exists($cache_file_x) )		file_put_contents($cache_file_x,$cache_content,LOCK_EX);
    if( !file_exists($cache_file_y) )		file_put_contents($cache_file_y,$cache_content,LOCK_EX);

    //		update cache
    if( $cache_x_time>$cache_y_time ){
        #echo "write y [".$cache_file_y."]<br/>\n";
        file_put_contents($cache_file_y,$cache_content,LOCK_EX);
    }
    else{
        #echo "write x [".$cache_file_x."]<br/>\n";
        file_put_contents($cache_file_x,$cache_content,LOCK_EX);
    }
}

function get_cache($key,$model=0){
    //	cache path
    $cache_path = str_replace('entry','cache',$_SERVER['DOCUMENT_ROOT']).'/_cache_'.$key;
    //	cache file
    $cache_file_x = $cache_path."x";
    $cache_file_y = $cache_path."y";
    if( !file_exists($cache_file_x) && !file_exists($cache_file_y) ) return false;

    //	cache time
    $cache_x_time = file_exists($cache_file_x)?filemtime($cache_file_x):0;
    $cache_y_time = file_exists($cache_file_y)?filemtime($cache_file_y):0;
    //
    if( $cache_x_time>$cache_y_time ){
        $cache_file  = $cache_file_x;
        $update_time = $cache_x_time;
    }
    else{
        $cache_file = $cache_file_y;
        $update_time = $cache_y_time;
    }
    #echo "use [".$cache_file."]<br/>\n";
    $cache_arrays = explode("\n",file_get_contents($cache_file));

    if( count($cache_arrays) == 2 && 0 === $model && time()-$update_time > $cache_arrays[1] ){
        return false;
    }
    else return unserialize(base64_decode($cache_arrays[0]));
}

function bili($int_1, $int2=1, $split_1=80, $split_2=60, $show=0){
    $int2    = $int2<1?1:$int2;
    $out_num = $int_1/$int2;
    $_format = '%.3f%%';
    // 设定显示原始数值
    $_show = '';
    $_pnum = '';
    if( $show == 1 ) $_pnum = $int_1.' | ';
    if( $show == 2 ) $_pnum = $int2.' | ';
    if( $show > 0 && $out_num == 0 ) return '0%';
    // if( $out_num >= 1 ) $_format = '%d%%';

    $color_arr = array(
        'green'  => array('color'=>'green','bgcolor' => '#457c61'),
        'orange' => array('color'=>'orange','bgcolor' => '#847432'),
        'red'    => array('color'=>'red','bgcolor' => '#ad5c4a'),
    );
    if( $out_num >= $split_1/100 ) $color = 'green';
    elseif( $out_num >= $split_2/100 ) $color = 'orange';
    else $color = 'red';

    $proportion = sprintf($_format,$out_num*100);
    $style_width= $out_num*100;
    if( $style_width > 100 ) $style_width = 100;

    if( $show == 4 ) return "<span style=\"width:{$style_width}%;float:left;background-color:{$color_arr[$color]['bgcolor']}\" title='$int_1'>".$proportion."</span>".(($style_width>20 && $style_width<70)?$int_1:'');

    return $_pnum."<span class='{$color_arr[$color]['color']}'>".$proportion."</span>";
}

function t_now($timeZone, $time_edTime){
    $count = 0;
    $date_edTime = date('Y-m-d', $time_edTime);
    $hour_edTime = date('H', $time_edTime);
    foreach ($timeZone as $_hour=>$num){
        if($_hour > date('H')) continue;
        if($date_edTime==date('Y-m-d') && $_hour<$hour_edTime) continue; //今天开启的任务之前的量不计算在内
        $count += $num;
    }
    return $count;
}


/**
 * @name hao123实时数据读取
 * @param $searchTime  2018-02-13
 * @param $perPage
 * @param $page
 * @return array
 */
function hao_123($searchTime, $perPage, $page){
    $_response = [];
    $offset = ((int)$page -1) * $perPage;
    if( $searchTime != date("Y-m-d") ){
        //--------领取
        $sql = "select * from hao123_active_log where `date`='$searchTime' limit $offset,$perPage";
        $active_query  = DB::select($sql);

        //--------返回
        $sql = "select * from hao123_return_log where `date`='$searchTime'";
        $return_query  = DB::select($sql);
        $active_query  = array_map(function ($row){
            return (array) $row;
        }, $active_query);

        $return_query = array_column($return_query,NULL,'taskID');
        $return_query = array_map(function($row){
            return (array)$row;
        }, $return_query);
        $result  = [];
        $taskIds = array_column($active_query, 'taskID');
        $taskIds ? $taskNameRes = \App\Repository\Admin\AreaTaskRepository::getTaskName($taskIds): $taskNameRes = [];
        foreach ($active_query as $value){
            $item = array( 'taskID' => $value['taskID'] );
            $taskID = $value['taskID'];
            $item['ip']   = $value['ip'];
            $item['task_name'] = isset($taskNameRes[$taskID]) ? $taskNameRes[$taskID]: '';
            $item['r_ip'] = $return_query[$taskID]['ip'];
            $item['pv']   = $value['pv'];
            $item['r_pv'] = $return_query[$taskID]['pv'];
            $item['click_num']     = $value['click_num'];
            $item['r_click_num']   = $return_query[$taskID]['click_num'];
            $item['r_clickresult'] = $return_query[$taskID]['click_result'];
            $ip = $item['ip'] . " / ". $item['r_ip'] . ' ( ' .round($item['r_ip']/$item['ip']*100, 2)."% )";
            $item['IP'] = $ip;
            $PV = $item['pv'] . " / ". $item['r_pv'] . ' ( ' .round($item['r_pv']/$item['pv']*100, 2)."% )";
            $item['PV'] = $PV;
            $ip_pv   = round($item['ip'] / $item['pv'] * 100, 2).'%';      //领取IP PV比
            $r_ip_pv = round($item['r_ip'] / $item['r_pv'] * 100, 2).'%';  //返回IP PV比
            $ip_click_num = round($item['r_ip'] / $item['r_click_num'], 2);  //实际IP/实际点击次数
            $item['ip_pv']   = $ip_pv;
            $item['r_ip_pv'] = $r_ip_pv;
            $item['ip_click_num'] = $ip_click_num;
            $result[] = $item;
        }
        return $result;
    }
    else{
        $active_data = hao123_active_data();
        $return_data = hao123_return_data();

        foreach ($active_data as $tid=>$value){
            $_response[$tid] = array_merge($active_data[$tid], $return_data[$tid]);
        }
    }
    return $_response;
}

function hao_123_total($searchTime){
    $_response = [];
    if( $searchTime != date("Y-m-d") ){
        //--------领取
        $sql = "select count(*) as cnt from hao123_active_log where `date`='$searchTime'";
        $active_query  = DB::select($sql);
        $cntObj = current($active_query);
        return ((array)$cntObj)['cnt'];
    }
    else{
        $active_data = hao123_active_data();
        $return_data = hao123_return_data();

        foreach ($active_data as $tid=>$value){
            $_response[$tid] = array_merge($active_data[$tid], $return_data[$tid]);
        }
    }
    return $_response;
}


function hao123_active_data()
{
    $_data_dir = config('path.data_dir');
    $cache_path = config('path.cache_path');
    $dateTime = date('ymd');
    $active_temp_file = $_data_dir.'/hao_active_log/'. $dateTime. '_hao123_active.temp';
    if(strpos($_SERVER['SERVER_ADDR'], "192.168.") !== false) $active_temp_file = $cache_path. $dateTime. '_hao123_active.temp';  // mmp外网和内外的路径读写权限不一样
    $diff_hours = file_diff_hour($active_temp_file);
    //echo implode(",", $diff_hours);
    foreach ($diff_hours as $_hour){
        $file_name = $_data_dir.'/hao_active_log/'. $dateTime.$_hour .'.txt';
        $cnt_res_tmp = array();
        if( file_exists($file_name) ) {
            $fhand = fopen($file_name, 'r');
            $str_no = array("\n", "\r");
            while (!feof($fhand))
            {
                $line = fgets($fhand); //读取一行
                $line = str_replace($str_no, '', $line);
                $arr_data = json_decode($line, true);
                if( empty($arr_data) ) continue;
                $ID = $arr_data['id'];
                $taskCnt = $arr_data['taskCnt'];
                if(!array_key_exists($ID, $cnt_res_tmp)) $cnt_res_tmp[$ID] =0;
                $cnt_res_tmp[$ID] +=$taskCnt;
            }
            fclose($fhand);
            $temp_content = unserialize( file_get_contents($active_temp_file) );
            if(empty($temp_content)) $temp_content=[];
            $temp_content[$_hour]=$cnt_res_tmp;
            $res = file_put_contents($active_temp_file, serialize($temp_content));
        }
    }

    $cnt_result = unserialize( file_get_contents($active_temp_file) );
    $cnt_res = [];
    foreach ($cnt_result as $id_pv){
        foreach ($id_pv as $taskID=>$pv){
            if(!array_key_exists($taskID, $cnt_res)) $cnt_res[$taskID]=0;
            $cnt_res[$taskID]+=$pv;
        }
    }
    $_response = [];
    $date = date('Y-m-d');
    $file_name = $cache_path. $date .'_nsm_issuedx.json';
    $file_comtent = \json_decode(file_get_contents($file_name), true);
    $file_comtent = $file_comtent['sum'];
    if( count($cnt_res)>0 ){
        foreach($cnt_res as $tid=>$num){
            $ip = $file_comtent[$tid]['ip']?:0;
            $PV = $file_comtent[$tid]['pv']?:0;
            $_response[$tid]['ip'] = $ip;
            $_response[$tid]['pv'] = $PV;
            $_response[$tid]['click_num'] = $num;
        }
        unset($cnt_res);
    }
    return $_response;
}


function hao123_return_data()
{
    global $_data_dir,$cache_path;
    $dateTime = date('Y-m-d');
    $_cache_return_file = $cache_path.$dateTime.'_nsm_returnx.json';
    $_return_content = \json_decode(file_get_contents($_cache_return_file),true);
    $file_name = $_data_dir."/_hao_tj/_hao_".date('ymd').'.txt';

    $click_res = array();      //clicknum字段
    $click_result = array();   //clickresult字段
    $_response = [];
    if( file_exists($file_name) ){
        $fhand = fopen($file_name, 'r');
        $str_no = array("\n", "\r");
        while (!feof($fhand))
        {
            $line = fgets($fhand); //读取一行
            $line = str_replace($str_no, '', $line);
            $arr_data = \json_decode($line, true);
            if( empty($arr_data) ) continue;
            $ID = $arr_data['ID'];
            $result = $arr_data['args']['result'];
            if( !array_key_exists($ID, $click_res) ) $click_res[$ID]=0;
            if(!array_key_exists($ID, $click_result)) $click_result[$ID]=0;
            foreach ($result as $value){
                if($value['clicknum']>0) $click_res[$ID]+=1;
                if($value['clickresult']>0) $click_result[$ID]+=1;
            }
        }
        fclose($fhand);
    }

    if( count($click_res)>0 ){
        foreach ($click_res as $tid=>$num){
            $ip = $_return_content[$tid]['ip']?:0;
            $PV = $_return_content[$tid]['pv']?:0;
            $_response[$tid]['r_ip'] = $ip;
            $_response[$tid]['r_pv'] = $PV;
            $_response[$tid]['r_click_num'] = $num;
            $_response[$tid]['r_clickresult'] = $click_result[$tid];
        }
        unset($click_res);
    }
    return $_response;
}

/**
 * @param $file_name
 * @return array
 */
function file_diff_hour($file_name){
    $file_content = unserialize( file_get_contents($file_name) );
    $_now_hours = [];
    for($i=0;$i<24;$i++){
        if( $i>intval(date("H")) ) break;
        if($i<10) $i="0".$i;
        $_now_hours[] = "$i";
    }
    if( count($file_content)>0 ){
        $_file_hours = array_keys($file_content);
        unset($_file_hours[date('H')]);
        $diff_hours = array_diff($_now_hours, $_file_hours);
        if (count($diff_hours)>0) return $diff_hours;
    }
    return $_now_hours;
}
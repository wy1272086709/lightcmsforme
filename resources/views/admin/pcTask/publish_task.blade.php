<style type="text/css">
    .layui-form-label {
        width: 85px;
    }
</style>
<form class="layui-form">
    <span class="margin-span">@if(isset($taskData['id'])) 修改 @else 新建 @endif @if($taskType == 1)广告 @elseif($taskType==2)搜索 @elseif($taskType==3) 流量@elseif($taskType==4)hao123 @endif任务</span>
    <button id="saveBtn" class="layui-btn layui-btn-normal" lay-submit lay-filter="saveTask" data-tasktype="{{$taskType}}">保存任务</button>
    <label id="returnAdsBtn" class="layui-btn layui-btn-primary" lay-event="goList" data-tasktype="{{$taskType}}">返回@if($taskType == 1)广告 @elseif($taskType==2)搜索 @elseif($taskType==3) 流量@elseif($taskType==4)hao123 @endif 任务列表</label>
    <hr/>
    <span class="margin-span">基本配置</span>
    <hr/>
    <input type="hidden" name="id" value="{{isset($taskData['id'])? $taskData['id']: 0}}" />
    <input type="hidden" name="config_id" value="{{isset($taskData['config_id'])? $taskData['config_id']: 0}}" />
    <div class="layui-form-item">
        <label class="layui-form-label">任务名称</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="task_name" id="task_name" class="layui-input" placeholder="请输入任务名称" autocomplete="off" value="{{isset($taskData['task_name'])? $taskData['task_name']:''}}">
        </div>
        <input type="hidden" name="is_big" id="is_big" value="{{isset($taskData['areaConfigData']['is_big'])? $taskData['areaConfigData']['is_big']:0}}" />
        <input type="hidden" name="r_mnsc" id="r_mnsc" value="{{isset($taskData['r_mnsc'])? $taskData['r_mnsc']:1}}" />
        <div class="layui-input-inline">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="task_name_set">高级设置</label>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">任务页面</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="task_url" id="task_url" class="layui-input" placeholder="请输入任务页面URL" autocomplete="off" value="{{isset($taskData['task_url']) ? $taskData['task_url']: ''}}">
        </div>
        <div class="layui-input-inline layui-hide" id="task_url_set_info" style="
                            display: inline-block;
                            heigth: 38px;
                            height: 38px;
                            margin-left: 3px;
                            line-height: 38px;
                        "></div>
        <input type="hidden" name="t_s" id="t_s" value="{{isset($taskData['uploadInfo'][\App\Utils\ConstantUtils::TASK_URL_UPLOAD])?$taskData['uploadInfo'][\App\Utils\ConstantUtils::TASK_URL_UPLOAD]:''}}" />
        <input type="hidden" name="task_url_style" id="task_url_style" value="{{isset($taskData['task_url_style'])? $taskData['task_url_style']: ''}}" />
        <div class="layui-input-inline">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="task_url_set">批量</label>
        </div>
    </div>
    @if($taskType!=4)
    <div class="layui-form-item">
        <label class="layui-form-label">发布数值</label>
        <div class="layui-input-inline input-style @if(isset($taskData['issueData']) && isset($taskData['issue_style']) && $taskData['issue_style'] == 1) layui-hide @endif">
            <input type="text" name="issued_value" id="issued_value" class="layui-input" placeholder="请输入发布数值" autocomplete="off" @if(isset($taskData['issue_style'])&& $taskData['issue_style']==0) value="{{$taskData['issueData']}}" @else value="" @endif>
        </div>
        <div class="layui-input-inline @if(!isset($taskData['issueData'])||empty($taskData['issue_style'])) layui-hide @endif" id="issued_set_info" style="
                            display: inline-block;
                            height: 38px;
                            margin-left: 3px;
                            line-height: 38px;
                        ">已设置时间段内的计划IP量</div>
        <!-- 发布数值对应的发布方式 -->
        <input type="hidden" name="issue_style" id="issue_style" value="{{isset($taskData['issue_style'])? $taskData['issue_style']:'0'}}" />
        <input type="hidden" name="issued_json_value" id="issued_json_value" @if(isset($taskData['issueData'])) value="{{$taskData['issueData']}}" @else value="" @endif/>
        <div class="layui-input-inline">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="issued_set_btn">高级设置</label>
        </div>
    </div>
    @endif
    <!-- hao123 没有IP/PV 比-->
    @if($taskType !=4)
        <div class="layui-form-item">
            <label class="layui-form-label">IP/PV比</label>
            <div class="layui-input-inline input-style">
                <input type="text" name="ip_pv_value" id="ip_pv_value" class="layui-input" placeholder="请输入IP/PV比" autocomplete="off" value="{{isset($taskData['ip_pv_value'])? $taskData['ip_pv_value']: ''}}">
            </div>
            <span class="layui-word-aux">
                            常用：1:1.2   1:1.5   XXX
                        </span>
        </div>
    @endif
    @if($taskType!=4)
    <div class="layui-form-item">
        <label class="layui-form-label">停留时长</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="stay_time" id="stay_time" class="layui-input" placeholder="请输入停留时长" autocomplete="off" value="{{isset($taskData['stay_time'])? $taskData['stay_time']: ''}}" />
        </div>
        <span class="layui-word-aux">ms 常用：XXX   XXX   XXX</span>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">优先级</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="level" id="level" class="layui-input" placeholder="请输入优先级" autocomplete="off" value="{{isset($taskData['level'])? $taskData['level']: ''}}" />
        </div>
        <span class="layui-word-aux">ms 常用：50   100   120</span>
    </div>
    @else
    <div class="layui-form-item">
        <label class="layui-form-label">IP数值</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="level" id="level" class="layui-input" placeholder="请输入IP数值" autocomplete="off"
                   value="{{isset($taskData['level'])? $taskData['level']: ''}}" />
        </div>
        <span class="layui-word-aux"></span>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">搜索数值</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="level" id="level" class="layui-input" placeholder="请输入搜索数值" autocomplete="off"
                   value="{{isset($taskData['level'])? $taskData['level']: ''}}" />
        </div>
        <span class="layui-word-aux"></span>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">搜索机器比例</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="level" id="level" class="layui-input" placeholder="搜索机器比例" autocomplete="off"
                   value="{{isset($taskData['level'])? $taskData['level']: ''}}" />
        </div>
        <span class="layui-word-aux"></span>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">机器点击比例</label>
        <div class="layui-input-inline input-style">
            <input type="text" name="level" id="level" class="layui-input" placeholder="机器点击比例" autocomplete="off"
                   value="{{isset($taskData['level'])? $taskData['level']: ''}}" />
        </div>
        <span class="layui-word-aux"></span>
    </div>
    @endif
    <div class="layui-form-item">
        <label class="layui-form-label">周末数据浮动</label>
        <div class="layui-input-inline input-style">
            <input type="radio" name="weekend_choose" value="1" title="开启" @if(isset($taskData['areaConfigData']['weekend_choose']) && $taskData['areaConfigData']['weekend_choose'] == 1 ) checked
            @elseif(!isset($id)) checked @endif>
            <input type="radio" name="weekend_choose" value="0" title="关闭" @if(isset($taskData['areaConfigData']['weekend_choose']) && $taskData['areaConfigData']['weekend_choose'] == 0 ) checked @endif>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">任务分组</label>
        <div class="layui-input-inline input-style">
            <select class="layui-form-select" name="task_group_id">
                <option value="">请选择任务分组</option>
                @foreach($groupList as  $idVal => $row)
                    <option value="{{$idVal}}" @if(isset($taskData['task_group_id']) && $idVal == $taskData['task_group_id']) selected
                            @endif>{{$row}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">是否启用</label>
        <div class="layui-input-inline input-style">
            <input type="radio" name="enable" value="1" title="开启" @if(isset($taskData['enable']) && $taskData['enable'] == 1 ) checked
                   @elseif(!isset($id)) checked @endif>
            <input type="radio" name="enable" value="0" title="关闭" @if(isset($taskData['enable']) && $taskData['enable'] == 0 ) checked @endif>
        </div>
    </div>
    <p><span class="margin-span">高级配置</span></p>
    <hr/>


    <div class="layui-row">
        <label class=" layui-col-md1 css-bold-font">统计链接</label>
        @if(isset($taskData['id']))
            @if(!empty($taskData['statistics_code'] && !empty($taskData['statistics_link'])))
                <span class="layui-col-md2" id="statistics_info">已填写统计代码和统计链接</span>
                @elseif(!empty($taskData['statistics_code']))
                <span class="layui-col-md2" id="statistics_info">已填写统计代码</span>
                @elseif(!empty($taskData['statistics_link']))
                <span class="layui-col-md2" id="statistics_info">已填写统计链接</span>
                @else
                <span  class="layui-col-md2" id="statistics_info">暂未设置</span>
            @endif
            @else
            <span class="layui-col-md2" id="statistics_info">暂未设置</span>
        @endif

        <!-- 统计代码和统计链接对应的隐藏域 -->
        <input type="hidden" name="statistics_code" value="{{isset($taskData['statistics_code'])? $taskData['statistics_code']: ''}}" />
        <input type="hidden" name="statistics_link" value="{{isset($taskData['statistics_link'])? $taskData['statistics_link']: ''}}" />
        <div class="layui-input-inline layui-col-md1">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="statistics-btn">设置</label>
        </div>

        <!-- 在修改的页面的时候,要给这两个变量赋值 -->
        <input type="hidden" id="is_ua" name="is_ua" value="{{isset($taskData['is_ua'])?$taskData['is_ua']: '1'}}" />
        <input type="hidden" id="ua-set-json" name="ua-set-json" value="{{isset($taskData['uaData'])? $taskData['uaData']: ''}}" />
        @if($taskType!=4)
            <label class=" css-bold-font layui-col-md1">UA设置</label>
            <span class="layui-col-md2" id="ua-set-span-info">@if(!empty($taskData['uaData'])) UA已设置 @else 暂未设置 @endif</span>
            <div class="layui-input-inline layui-col-md3">
                <label class="layui-btn layui-btn-normal layui-btn-sm" id="ua-set-btn">设置</label>
            </div>
        @endif
    </div>


    <div class="layui-row margin-row">
        @if($taskType!=2&&$taskType!=4)
        <label class="layui-col-md1 css-bold-font">来源页面</label>
        <span class="layui-col-md2" id="sourceSetText">@if(!empty($taskData['sourceData'])) 来源已设置 @else 暂未设置 @endif</span>
        <input type="hidden" name="source_style" id="source_style" value="{{isset($taskData['source_style']) ? $taskData['source_style']: ''}}"/>
        <input type="hidden" name="source_json" id="source_json" value="{{isset($taskData['sourceData']) ? $taskData['sourceData']: ''}}" />
        <div class="layui-input-inline layui-col-md1">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="source-btn">设置</label>
        </div>
        @endif
        <label class="css-bold-font layui-col-md1">地区/渠道设置</label>
        <span class="layui-col-md2" id="areaChannelInfo">
            @if(!empty($taskData['areaChannelData']))
                地区/渠道信息已设置
            @else
                地区/渠道信息暂未设置
            @endif
        </span>
        <!-- 渠道或者地区勾选的值 -->
        <input type="hidden" name="area_channel_info" id="area_channel_info" value="{{isset($taskData['areaChannelData']) ? \json_encode($taskData['areaChannelData'], true): ''}}" />
        <!-- 地区或者渠道勾选的总数 -->
        <input type="hidden" name="pc_num_show" id="pc_num_show" value="{{isset($taskData['pc_num_show'])? $taskData['pc_num_show']:0}}"/>
        <!-- 用来区分隐藏域中的值是渠道还是地区的0为地区,1为渠道 -->
        <input type="hidden" name="aq_type" id="aq_type" value="{{isset($taskData['aq_type'])? $taskData['aq_type']: ''}}" />
            <input type="hidden" name="qx_type" id="qx_type_val" value="{{isset($taskData['qx_type'])? $taskData['qx_type']: '0'}}" />
        <div class="layui-input-inline layui-col-md3">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="dq-qd-set-btn">设置</label>
        </div>
    </div>
    <div class="layui-row margin-row">
        @if($taskType!=2 && $taskType!=4)
        <label class="layui-col-md1 css-bold-font">曝光页面</label>
        <span class="layui-col-md2" id="exposurePage">
            @if(!empty($taskData['exposure_page_url']))
                {{$taskData['exposure_page_url']}}
            @else
                暂未设置
            @endif
        </span>
        <input type="hidden" name="exposure_page_url" value="{{isset($taskData['exposure_page_url']) ? $taskData['exposure_page_url']: ''}}"
               id="exposure_page_url" />
        <div class="layui-input-inline layui-col-md1">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="exposure-page-set">设置</label>
        </div>
        @endif
        <label class="css-bold-font layui-col-md1">时段设置</label>
        <span class="layui-col-md2" id="timeRange">@if(!empty($taskData['timeZoneData'])) 时段信息已设置 @else 暂未设置 @endif</span>
        <input type="hidden" name="choice_type" id="choice_type" value="{{isset($taskData['choice_type'])? $taskData['choice_type']:''}}" />
        <input type="hidden" name="time_zone" id="time_zone" value="{{isset($taskData['timeZoneData'])? $taskData['timeZoneData']: ''}}" />
        <div class="layui-input-inline layui-col-md3">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="time-range-set">设置</label>
        </div>
    </div>

    @if($taskType!=2 &&$taskType!=4)
    <div class="layui-row margin-row">
        <label class="layui-col-md1 css-bold-font layui-col-md-offset4">点击设置</label>
        <span class="layui-col-md2" id="click-set-span-info">
            @if(!empty($taskData['clickSetData']))
                点击设置已经设置
            @else
                随机/模式
            @endif
        </span>
        <input type="hidden" name="click_set_data_info" id="click_set_data_info" value="{{!empty($taskData['clickSetData'])? \json_encode($taskData['clickSetData'], true): ''}}" />
        <div class="layui-input-inline layui-col-md3">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="click-set">设置</label>
        </div>
    </div>
    @endif

    <div class="layui-row margin-row">
        @if($taskType==2)
        <label class=" css-bold-font layui-col-md1">词库设置</label>
        <span class="layui-col-md2" id="word-set-span-info">暂未设置</span>
        <input type="hidden" name="word_set_style" id="word_set_style" value="{{isset($taskData['word_style'])? $taskData['word_style']: ''}}"/>
        <input type="hidden" name="word_set_json" id="word_set_info" value="{{isset($taskData['word_content']) ? \json_encode($taskData['word_content'], true): ''}}" />
        <div class="layui-input-inline layui-col-md1">
            <label class="layui-btn layui-btn-normal layui-btn-sm" id="word-set-btn">设置</label>
        </div>
        @endif
    </div>
</form>
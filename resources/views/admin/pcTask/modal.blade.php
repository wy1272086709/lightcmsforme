<style type="text/css">
    .float-li>li{
        float:left;
        list-style-type: none;
    }
</style>

<!-- 发布数值对应的模态框 -->
<script type="text/html" id="issued-modal">
    <div class="layui-tab layui-tab-brief" lay-filter="layui-issued-mode-filter">
        <ul class="layui-tab-title layui-form">
            <li class="layui-this" lay-id="issued_fix_mode">
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-block">
                        <input type="radio" name="issued_mode" value="0" title="固定模式(设置每天一样的IP流量数)">
                    </div>
                </div>
            </li>
            <li lay-id="issued_plan_mode">
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-block">
                        <input type="radio" name="issued_mode" value="1" title="计划模式(设置每天不同的IP流量数)">
                    </div>
                </div>
            </li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-form-item">
                    <label class="layui-form-label">计划IP量: </label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" name="schedule_number">
                    </div>
                    <label class="layui-form-label" style="padding: 0px 0px;text-align:left;">天</label>
                </div>
            </div>
            <div class="layui-tab-item">
                <div class="layui-fluid">
                    <div class="layui-row">
                        <label class="layui-form-label layui-col-md1.2">开始时间: </label>
                        <div class="layui-input-inline layui-col-md2">
                            <input type="text" class="layui-input" name="schedule_start_date" id="schedule_start_date"/>
                        </div>
                        <label class="layui-form-label layui-col-md1.2">结束时间: </label>
                        <div class="layui-input-inline layui-col-md2">
                            <input type="text" class="layui-input" name="schedule_end_date" id="schedule_end_date"/>
                        </div>
                        <label class="layui-form-label layui-col-md1.3">计划日IP量: </label>
                        <div class="layui-input-inline layui-col-md2">
                            <input type="text" class="layui-input" name="schedule_number" id="schedule_number"/>
                        </div>
                        <label class="layui-form-label layui-col-md1" style="text-align:left;">IP</label>
                        <div class="layui-input-inline layui-col-md1">
                            <button class="layui-btn layui-btn-normal" lay-event="submit" type="submit" id="addScheduleBtn">添加</button>
                        </div>
                    </div>
                </div>
                <table class="layui-table">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>开始时间</th>
                        <th>结束时间</th>
                        <th>天数</th>
                        <th>计划IP量</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody id="layui-schedule-tbody" class="layui-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</script>

<!-- 任务名称对应的模态框 -->
<script type="text/html" id="task-name-modal">
<div>
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">任务名称</label>
            <div class="layui-input-inline">
                <input type="text" name="s_task_name" id="s_task_name" class="layui-input" />
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">是否高耗时</label>
            <div class="layui-input-inline">
                <input type="radio" name="is_big_radio" value="1" title="是">
                <input type="radio" name="is_big_radio" value="0" title="否">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">页面发布次数</label>
            <div class="layui-input-inline">
                <input type="text" name="r_mnsc" id="r_mnsc"  class="layui-input"  value="" placeholder="1"/>
            </div>
        </div>
    </form>
</div>
</script>

<!-- 来源页面对应的模态框 -->
<script type="text/html" id="source-url-modal">
<div class="">
    <div class="layui-tab layui-tab-brief" lay-filter="source_set_filter">
        <ul class="layui-tab-title layui-form">
            <li class="layui-this layui-form-item" lay-id="custom_set_source">
                <div class="layui-input-block">
                    <input type="radio" name="source-url-mode"  value="0" title="逐条自定义来源" checked>
                </div>
            </li>
            <li class="layui-form-item" lay-id="batch_set_source">
                <div class="layui-input-block">
                    <input type="radio" name="source-url-mode" value="1" title="批量设置来源">
                </div>
            </li>
            <li class="layui-form-item" lay-id="source_export">
                <div class="layui-input-block">
                    <input type="radio" name="source-url-mode" value="2" title="来源文件导入(不含比例)">
                </div>
            </li>
            <li class="layui-form-item" lay-id="source_ratio_export">
                <div class="layui-input-block">
                    <input type="radio" name="source-url-mode" value="3" title="来源文件导入(含比例)">
                </div>
            </li>
        </ul>
        <hr/>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="source_site_url" class="layui-input" placeholder="请输入来源网址"/>
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" id="source_ratio" class="layui-input" placeholder="访问比例"/>
                    </div>
                    <div style="float:left;line-height: 38px;height: 38px;">
                        <label class="">%访问</label>
                    </div>
                    <div class="layui-input-inline" style="margin-left: 20px;width: auto;">
                        <label class="layui-btn layui-btn-normal layui-btn-sm" id="source_btn_add">添加</label>
                    </div>
                </div>
                <table class="layui-table">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>来源内容</th>
                        <th>访问比例</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody class="layui-table-body" id="source-layui-body"></tbody>
                </table>
            </div>
            <div class="layui-tab-item">
                <div class="layui-input-block" style="margin-left: auto;">
                    <textarea class="layui-textarea" id="batch_source_url"></textarea>
                </div>
                <p>
                    说明:清空网址
                    来源网址复制到上面文本框中，一个网址一行，用回车换行分隔，最多500条，超过部分自动删除；
                    每条网址长度不能超过512字节；
                    所有网址来源的几率相同，如想增加某网址概率，增加条数即可；
                </p>
            </div>
            <div class="layui-tab-item">
                <div class="layui-form-item">
                    <label class="layui-form-label">导入来源文件</label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="source_file" id="source_file" value=""
                               @if(isset($id)) lay-verify="required" @endif;/>
                        <button type="button" class="layui-btn" id="uploadSource">
                            <i class="layui-icon">&#xe67c;</i>上传文件
                        </button>
                        <label id="label_file_name" class="layui-form-label"></label>
                    </div>
                </div>
            </div>
            <div class="layui-tab-item">
                <div class="layui-form-item">
                    <label class="layui-form-label">导入来源文件</label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="source_ratio_file" id="source_ratio_file" value=""
                               @if(isset($id)) lay-verify="required" @endif />
                        <button type="button" class="layui-btn" id="uploadSourceRatio">
                            <i class="layui-icon">&#xe67c;</i>上传文件
                        </button>
                        <label id="label_ratio_file_name" class="layui-form-label"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</script>

<!-- 时段设置 -->
<script type="text/html" id="time-range-modal">
<div class="layui-form">
    <div class="layui-tab layui-tab-brief" lay-filter="time_range_set_filter">
        <ul class="layui-tab-title">
                <li class="layui-this" lay-id="quxian_li">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width: auto;"></label>
                        <div class="layui-input-inline">
                            <input type="radio" name="time_range_mode"  value="0" title="曲线分配">
                        </div>
                    </div>
                </li>
                <li lay-id="time_range_li">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width: auto;"></label>
                        <div class="layui-input-inline">
                            <input type="radio" name="time_range_mode"  value="1" title="时段分配">
                        </div>
                    </div>
                </li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-form">
                    <label class="layui-form-label" style="width: auto;">曲线分配</label>
                    <div class="layui-input-inline">
                        <input type="radio" name="qx_type_ele" class="layui-input" title="PC曲线" value="0">
                    </div>
                    <div class="layui-input-inline">
                        <input type="radio" name="qx_type_ele" class="layui-input" title="移动端曲线" value="1">
                    </div>
                </div>
            </div>
            <div class="layui-tab-item float-li">
                @foreach(range('00', '23', 1) as $i)
                    <li>
                        <div class="layui-form">
                            <label class="layui-form-label" style="width: auto;">@if($i<10) {{'0'.$i}} @else {{ $i }} @endif</label>
                            <div class="layui-input-inline" style="width: 80px;">
                                <input type="text" name="dateZones[{{$i<10?'0'.$i:$i}}]" class="layui-input date-zones" lay-verify="number">
                            </div>
                        </div>
                    </li>
                @endforeach
            </div>
        </div>
    </div>
</div>
</script>

<!-- 任务页面--批量导入 -->
<script id="task-url-modal" type="text/html">
    <div class="layui-form">
        <div class="layui-tab layui-tab-brief" lay-filter="task_url_set_filter">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="task_url_li_0">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width: auto;"></label>
                        <div class="layui-input-inline">
                            <input type="radio" name="task_url_input"  value="0" title="内容批量导入">
                        </div>
                    </div>
                </li>
                <li lay-id="task_url_li_1">
                    <div class="layui-form-item">
                        <label class="layui-form-label" style="width: auto;"></label>
                        <div class="layui-input-inline">
                            <input type="radio" name="task_url_input"  value="1" title="文件导入">
                        </div>
                    </div>
                </li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form-item margin-block" style="border-top: 1px solid black;">
                        <p class="margin-block">批量导入</p>
                        <div class="layui-input-block margin-block">
                            <textarea name="textarea_t_s" id="textarea_t_s" class="layui-textarea" style="min-height: 300px;"></textarea>
                        </div>
                        <p>
                            说明:清空网址
                            将网址复制到上面文本框中，一个网址一行，用回车换行分隔，最多1000条，超过部分自动删除；
                            每条网址长度不能超过1024字节
                        </p>
                    </div>
                </div>
                <div class="layui-tab-item">
                    <div class="layui-form-item">
                        <label class="layui-form-label">导入文件</label>
                        <div class="layui-input-inline">
                            <input type="hidden" name="task_url_file" id="task_url_file" value="{{isset($taskData['uploadInfo'][\App\Utils\ConstantUtils::TASK_URL_UPLOAD])?$taskData['uploadInfo'][\App\Utils\ConstantUtils::TASK_URL_UPLOAD]:''}}" @if(isset($id)) lay-verify="required" @endif />
                                <button type="button" class="layui-btn" id="uploadUrlFile">
                                    <i class="layui-icon">&#xe67c;</i>上传文件
                                </button>
                            <label id="label_file_name" class="layui-form-label"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>

<!-- 统计链接 -->
<script id="statistics-modal" type="text/html">
<div class="">
    <div class="layui-tab layui-tab-brief">
        <ul class="layui-tab-title">
            <li class="layui-this">
                <span>统计代码</span>
            </li>
            <li>
                <span>统计链接</span>
            </li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <textarea class="layui-textarea" id="statistics_code"></textarea>
                <p>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p>
                <hr/>
            </div>
            <div class="layui-tab-item">
                <textarea class="layui-textarea" id="statistics_link"></textarea>
                <p>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p>
                <hr/>
            </div>
        </div>
    </div>
</div>
</script>

<!-- 曝光页面 -->
<script type="text/html" id="exposure-page-modal">
<div class="">
    <div class="layui-form">
        <label class="layui-form-label" style="width: auto;">曝光页面</label>
        <div class="layui-input-block">
            <input type="text" name="exposure_page_mode"  class="layui-input" />
        </div>
    </div>
</div>
</script>

<!-- UA 设置 -->
<script type="text/html" id="ua-set-modal">
<div class="">
    <div class="layui-tab layui-tab-brief" lay-filter="ua-set-filter">
        <ul class="layui-tab-title layui-form">
            <li class="layui-this layui-form-item" lay-id="auto_ua">
                <label class="layui-form-label" style="width: auto;"></label>
                <div class="layui-input-inline">
                    <input type="radio" name="ua_mode"  value="0" title="自动UA标识">
                </div>
            </li>
            <li class="layui-form-item" lay-id="custom_ua">
                <label class="layui-form-label" style="width: auto;"></label>
                <div class="layui-input-inline">
                    <input type="radio" name="ua_mode"  value="1" title="自定义UA标识">
                </div>
            </li>
            <li class="layui-form-item" lay-id="batch_export_ua">
                <label class="layui-form-label" style="width: auto;"></label>
                <div class="layui-input-inline">
                    <input type="radio" name="ua_mode"  value="2" title="批量导入UA标识">
                </div>
            </li>
            <li class="layui-form-item" lay-id="upload_file_ua">
                <label class="layui-form-label" style="width: auto;"></label>
                <div class="layui-input-inline">
                    <input type="radio" name="ua_mode"  value="3" title="文件上传设置UA标识">
                </div>
            </li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <p>
                    User Agent 中文名为用户代理，简称 UA，它是一个特殊字符串头，使得服务器能够识别客户使用的操作系统及版本、CPU 类型、浏览器及版本、浏览器渲染引擎、浏览器语言、浏览器插件等。
                    默认的自动UA标识是根据CNZZ公布的中国互联网的分析报告，做成一万多条UA，并且根据占比进行予分配，符合绝大多数的场景，一般用户请选择自动UA，无需设置。
                </p>
            </div>
            <div class="layui-tab-item">
                <br/>
                <div class="layui-form" style="float:left;">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-inline" style="width: 150px;">
                        <select name="isPc" id="ua-isPc">
                            <option value="1">PC</option>
                            <option value="2">移动端</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form" style="float:left;">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-inline" style="width: auto;">
                        <select name="browser" id="ua-browser">
                            <option value="1">IE浏览器</option>
                            <option value="2">火狐浏览器</option>
                            <option value="3">Chrome浏览器</option>
                            <option value="4">Opera浏览器</option>
                            <option value="5">Safari浏览器</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form" style="float:left;">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-inline" style="width: auto;">
                        <input type="text" class="layui-input" placeholder="请输入访问比例" id="ua_visit_radio" />
                    </div>
                </div>
                <label>%访问</label>
                <button class="layui-btn layui-btn-normal" id="addUaBtn" lay-event="addUa">添加</button>
                <table class="layui-table" lay-filter="ua_table" id="ua_table">
                    <thead>
                        <tr>
                            <th lay-data="{field:'no', width:80}">序号</th>
                            <th lay-data="{field:'device_type', width:80}">设备类型</th>
                            <th lay-data="{field:'browser', width:80}">浏览器</th>
                            <th lay-data="{field:'visit_radio', width:80}">访问比例</th>
                            <th lay-data="{width:80}">操作</th>
                        </tr>
                    </thead>
                    <tbody class="layui-table-body" id="layui-ua-body"></tbody>
                </table>
            </div>
            <div class="layui-tab-item">
                <div class="layui-form-item">
                    <div class="layui-input-block" style="margin-left:auto;">
                        <textarea class="layui-textarea" id="multi_ua_textarea"></textarea>
                    </div>
                    <span>清空网址</span>
                </div>
                <p>
                    说明:清空网址
                    UA标识复制到上面的文本框中。分辨率写在UA后面，用“||”符号分隔，分辨率的宽和高用半角“,”分隔，不填写或者格式错误，则按默认随机分辨率；
                    一个UA一行，用回车换行分隔，最多100条，超过部分自动删除，每条UA加分辨率长度不能超过512字节；
                    所有UA的记录相同，如果需要增加某UA的概率，增加条数即可。
                </p>
            </div>
            <div class="layui-tab-item">
                <div class="layui-form-item">
                    <label class="layui-form-label">导入ua文件</label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="ua_file" id="ua_file" value="{{isset($taskData['uaData']) && $taskData['is_ua']==3?$taskData['uaData']: ''}}" @if(isset($id)) lay-verify="required" @endif;/>
                            <button type="button" class="layui-btn" id="uploadUa">
                                <i class="layui-icon">&#xe67c;</i>上传文件
                            </button>
                        <label id="label_file_name" class="layui-form-label">{{isset($taskData['uaData'])&& $taskData['is_ua']==3?$taskData['uaData']:''}}</label>
                    </div>
                </div>
                <p>
                   xxxxx
                </p>
            </div>
        </div>
    </div>
    <hr/>
</div>
</script>

<!-- 点击设置 -->
<script type="text/html" id="clickSetModal">
<div class="" id="clickSetDiv">
    <hr/>
    <div class="layui-card-body layui-form">
        <div class="layui-form-item clickSetItem level1" data-pid="0">
            <div class="layui-inline">
                <label class="layui-form-label" style="width: 120px;"><span class="clickPrefix">&nbsp;&nbsp;</span><span class="clickSetNo">1</span>
                    点击间隔时间</label>
                <div class="layui-input-inline" style="width: 170px;">
                    <input type="text" class="layui-input interval-time" name="tree[1][time_inter]" placeholder="请输入数字或者数字,数字" />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label" style="width:auto;">点击区域</label>
                <div class="layui-input-inline" style="width: 120px;">
                    <input type="text" class="layui-input click-area" name="tree[1][click_area]" placeholder="请输入点击区域" />
                </div>
            </div>

            <div class="layui-inline">
                <label class="layui-form-label" style="width:auto;"></label>
                <div class="layui-input-inline">
                <select style="width:120px;margin-top: -10px;" name="tree[1][type]" class="tag">
                    <option value="5">5-当前页面</option>
                    <option value="3">3-新标签</option>
                </select>
                </div>
            </div>

            <div class="layui-inline">
                <label class="layui-form-label" style="width: auto;">点击几率</label>
                <div class="layui-input-inline" style="width:130px;">
                    <input type="text" class="layui-input  click-odds" style="width:120px;" name="tree[1][odds]" placeholder="请输入点击几率" />
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn layui-btn-primary addSiblingDir">添加同级目录</button>
                <button class="layui-btn layui-btn-primary addSubDir">添加子目录</button>
            </div>
        </div>
    </div>
</div>
</script>

<!-- 词库设置 -->
<script id="word-set-modal" type="text/html">
    <div class="">
        <div class="layui-tab layui-tab-brief" lay-filter="word-set-filter">
            <ul class="layui-tab-title layui-form">
                <li class="layui-this layui-form-item" lay-id="batch_ua_import">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-inline">
                        <input type="radio" name="word_mode"  value="0" title="批量导入">
                    </div>
                </li>
                <li class="layui-form-item" lay-id="custom_word_set">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-inline">
                        <input type="radio" name="word_mode"  value="1" title="文件上传">
                    </div>
                </li>
                <li class="layui-form-item" lay-id="out_link_set">
                    <label class="layui-form-label" style="width: auto;"></label>
                    <div class="layui-input-inline">
                        <input type="radio" name="word_mode"  value="1" title="外部链接填写">
                    </div>
                </li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <textarea class="layui-textarea" id="word-set-textarea"></textarea>
                    <p>XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</p>
                    <hr/>
                </div>
                <div class="layui-tab-item">
                    <div class="layui-form-item">
                        <label class="layui-form-label">导入文件</label>
                        <div class="layui-input-inline">
                            <input type="hidden" name="word_file" id="word_file" value="{{isset($module['file_name'])?$module['file_name']: ''}}" @if(isset($id)) lay-verify="required" @endif;/>
                            @if(!isset($id))
                                <button type="button" class="layui-btn" id="uploadWord">
                                    <i class="layui-icon">&#xe67c;</i>上传词库文件
                                </button>
                            @endif
                            <label id="label_file_name" class="layui-form-label">{{isset($module['file_name'])?$module['file_name']:''}}</label>
                        </div>
                    </div>
                    <p>
                        xxxxx
                    </p>
                </div>
                <!-- -->
                <div class="layui-tab-item">
                    xxxx
                </div>
            </div>
        </div>
    </div>
</script>
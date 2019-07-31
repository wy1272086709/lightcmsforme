@extends('admin.base')
@section('content')
    <!-- 面包屑组件 -->
    @include('admin.breadcrumb')
    <div class="layui-card">
        <div class="layui-form layui-card-header light-search">
            <form>
                <input type="hidden" name="action" value="search">
                <div class="layui-inline">
                    <label class="layui-form-label">业务类型</label>
                    <div class="layui-input-inline">
                        <select class="" name="taskType" id="taskType">
                            <option value="1" @if(request()->get('taskType') == '1') selected @endif>广告业务</option>
                            <option value="2" @if(request()->get('taskType') == '2') selected @endif>搜索业务</option>
                            <option value="3" @if(request()->get('taskType') == '3') selected @endif>流量业务</option>
                            <option value="4" @if(request()->get('taskType') == '4') selected @endif>hao123业务</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">日期选择</label>
                    <div class="layui-input-inline">
                        <input type="text" name="dates" autocomplete="off" id="search-date" class="layui-input" value="{{ request()->get('dates') }}">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-list" lay-filter="form-search" id="submitBtn">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table class="layui-table" lay-data="{url:'{{ route('admin::statistics.list') }}?{{ request()->getQueryString() }}', page:true, limit:50, id:'test', toolbar:'#toolbar'}"
                   lay-filter="test">
                <thead>
                @if($taskType == \App\Utils\ConstantUtils::FLOW_SERVICE)
                    <tr>
                        <th lay-data="{field:'date'}">日期</th>
                        <th lay-data="{field:'taskID', width:80, sort: true}">任务ID</th>
                        <th lay-data="{field:'task_name'}">任务名称</th>
                        <th lay-data="{field:'t_now'}">当前发布量</th>
                        <th lay-data="{field:'t_today'}">发布数值</th>
                        <th lay-data="{field:'IP'}">领取IP</th>
                        <th lay-data="{field:'PV'}">领取PV</th>
                        <th lay-data="{field:'succ_ip'}">执行IP</th>
                        <th lay-data="{field:'succ_pv'}">执行PV</th>
                        <th lay-data="{field:'per'}">成功百分比</th>
                        <th lay-data="{field:'loginip',templet:'#ippv_hour_view'}">小时详情</th>
                    </tr>
                    @elseif($taskType == \App\Utils\ConstantUtils::SEARCH_SERVICE || $taskType == \App\Utils\ConstantUtils::ADS_SERVICE)
                    <tr>
                        <th lay-data="{field:'date'}">time</th>
                        <th lay-data="{field:'task_id', width:80, sort: true}">task_id</th>
                        <th lay-data="{field:'task_name'}">task_name</th>
                        <th lay-data="{field:'url'}">url</th>
                        <th lay-data="{field:'plan_count'}">plan_count</th>
                        <th lay-data="{field:'exec_count'}">exec_count</th>
                        <th lay-data="{field:'succ_count'}">succ_count</th>
                        <th lay-data="{field:'search_scnt'}">search_scnt</th>
                        <th lay-data="{field:'bs_scnt'}">bs_scnt</th>
                        <th lay-data="{field:'bs_scnt2'}">bs_scnt2</th>
                        <th lay-data="{field:'bs_ccnt'}">bs_ccnt</th>
                    </tr>
                    @elseif($taskType == \App\Utils\ConstantUtils::HAO_123_SERVICE)
                    <tr>
                        <th lay-data="{field:'taskID', width:80, sort: true}">任务ID</th>
                        <th lay-data="{field:'task_name'}">任务名称</th>
                        <th lay-data="{field:'IP'}">IP(领取/返回)</th>
                        <th lay-data="{field:'PV'}">PV(领取/返回)</th>
                        <th lay-data="{field:'ip_pv'}">IP/PV(领取)</th>
                        <th lay-data="{field:'r_ip_pv'}">IP/PV(返回)</th>
                        <th lay-data="{field:'r_click_num'}">点击数(领取\返回)</th>
                        <th lay-data="{field:'r_clickresult'}">点击结果(点击结果/点击数)</th>
                        <th lay-data="{field:'ip_click_num'}">实际IP/实际点击次数</th>
                        <th lay-data="{field:'loginip',templet:'#hao123_hour_view', width:200}">操作</th>
                    </tr>
                @endif
                </thead>
            </table>
        </div>
    </div>

@endsection
<script type="text/html" id="ippv_hour_view">
    <a href="<% d.ippvViewUrl %>" class="layui-table-link" title="查看详情"><i class="layui-icon layui-icon-list"></i>查看详情</a>
</script>

<script type="text/html" id="hao123_hour_view">
    <a href="<% d.ippvViewUrl %>" class="layui-table-link" title="查看详情"><i class="layui-icon layui-icon-list"></i>查看详情</a>
    <a href="<% d.ippvViewUrl %>" class="layui-table-link" title="查看详情"><i class="layui-icon layui-icon-list"></i>查看详情</a>
</script>

@section('js')
<script type="text/javascript">
    layConfig();
    $(function () {
        fireDateEvts();
    });

    function layConfig() {
        var laytpl = layui.laytpl;
        laytpl.config({
            open: '<%',
            close: '%>'
        });
    }

    function fireDateEvts()
    {
        $('#search-date').on('click', function (e) {
            renderDateEle('search-date');
        });
    }
    function renderDateEle(id)
    {
        var laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            elem: '#' + id //指定元素
        });
    }
</script>
@endsection
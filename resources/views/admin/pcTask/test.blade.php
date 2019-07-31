@extends('admin.base')
@section('content')
    @include('admin.breadcrumb')
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
                <option value="1">火狐浏览器</option>
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
            <th lay-data="{type:'checkbox', width:80}"></th>
            <th lay-data="{field:'device_type', width:80}">设备类型</th>
            <th lay-data="{field:'browser', width:80}">浏览器</th>
            <th lay-data="{field:'visit_radio', width:80}">访问比例</th>
        </tr>
        </thead>
        <tbody id="ua_set_body" class="layui-table-body"></tbody>
    </table>
@endsection
<!-- 模板引擎 -->
<script type="text/html" id="ua_tr">
    <tr>
        <td><% d.device_type %></td>
        <td><% d.browser %></td>
        <td><% d.visit_ratio %></td>
        <td><label class="layui-btn layui-btn-danger layui-btn-sm ua-tr-del">删除</label></td>
    </tr>
</script>

@section('js')
<script>
    $(function () {
        laytplConfig();
    })
    $(document).on('click', '#addUaBtn', function () {
        console.log('addUaBtn click here!');
        /*var objList = table.cache['ua_table'];
        objList.push({
            device_type: 'PC',
            browser: 'Chrome',
            visit_radio: '0.5'
        });
        console.log(objList);
        objList.push({
            device_type: '',
            browser: '',
            visit_radio: ''
        });
        initTable(objList);
        /*
        table.reload('ua_table', {
            data: objList
        });*/

        // 访问比例
        var visitRadio = $('#ua_visit_radio').val();
        // 浏览器
        var browserTextEle = $('#ua-browser').find('option:selected');
        var browser = browserTextEle.text();
        // 设备类型
        var deviceTypeEle = $('#ua-isPc').find('option:selected')
        var deviceType = deviceTypeEle.text();

        var data = {
            device_type: deviceType,
            browser: browser,
            visit_radio: visitRadio
        };

        var tpl = layui.laytpl;
        var uaRowHtml = $('#ua_tr').html();
        tpl(uaRowHtml).render(data, function(html){
            var table = layui.table;
            $("#ua_set_body").append($(html));
        });
    });

    function laytplConfig()
    {
        var tpl = layui.laytpl;
        tpl.config({
            open: '<%',
            close: '%>'
        });
    }
</script>
@endsection
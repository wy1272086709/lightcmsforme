@extends('admin.base')
@section('css')
<style type="text/css">
    .layui-table-cell {
        display: inline-block;
        height: auto;
    }
</style>
@endsection

@section('content')
    @include('admin.breadcrumb')
    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li @if($taskType == \App\Utils\ConstantUtils::ADS_SERVICE) class="layui-this" @endif>广告业务</li>
            <li @if($taskType == \App\Utils\ConstantUtils::SEARCH_SERVICE) class="layui-this" @endif>搜索业务</li>
            <li @if($taskType == \App\Utils\ConstantUtils::FLOW_SERVICE) class="layui-this" @endif>流量业务</li>
            @if($platForm == \App\Utils\ConstantUtils::PC_PLATFORM)
            <li @if($taskType == \App\Utils\ConstantUtils::HAO_123_SERVICE) class="layui-this" @endif>hao123</li>
            @endif
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item @if($taskType == \App\Utils\ConstantUtils::ADS_SERVICE) layui-show @endif">
                @if($taskType == \App\Utils\ConstantUtils::ADS_SERVICE)
                    @include('admin.pcTask.task_type_list', [ 'taskType' => $taskType, 'platForm' => $platForm ])
                @endif
            </div>
            <div class="layui-tab-item @if($taskType == \App\Utils\ConstantUtils::SEARCH_SERVICE) layui-show @endif">
                @if($taskType == \App\Utils\ConstantUtils::SEARCH_SERVICE)
                    @include('admin.pcTask.task_type_list', [ 'taskType' => $taskType, 'platForm' => $platForm ])
                @endif
            </div>
            <div class="layui-tab-item @if($taskType == \App\Utils\ConstantUtils::FLOW_SERVICE) layui-show @endif">
                @if($taskType == \App\Utils\ConstantUtils::FLOW_SERVICE)
                    @include('admin.pcTask.task_type_list', [ 'taskType' => $taskType, 'platForm' => $platForm ])
                @endif
            </div>
            @if($platForm == \App\Utils\ConstantUtils::PC_PLATFORM)
            <div class="layui-tab-item @if($taskType == \App\Utils\ConstantUtils::HAO_123_SERVICE) layui-show @endif">
                @if($taskType == \App\Utils\ConstantUtils::HAO_123_SERVICE)
                    @include('admin.pcTask.task_type_list', [ 'taskType' => $taskType, 'platForm' => $platForm ])
                @endif
            </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        var element = layui.element;
        var form = layui.form;
        var table = layui.table;
        element.on('tab(docDemoTabBrief)', function (data) {
            // 获取当前tab 所在下标
            var index = data.index;
            window.location.href = getLocationHref(index);
        });

        function getType() {
            var href = window.location.pathname;
            var pathNameArr = href.split('/');
            return pathNameArr[2];
        }

        function getLocationHref(index) {
            var href = "";
            var type = getType();
            console.log('type:', type);
            switch (index) {
                case 0:
                    if (type == 'pcTask') {
                        href = "{{route('admin::pcTask.index', [ 'taskType' => \App\Utils\ConstantUtils::ADS_SERVICE ])}}";
                    } else {
                        href = "{{route('admin::mobileTask.index', [ 'taskType' => \App\Utils\ConstantUtils::ADS_SERVICE ])}}";
                    }
                    break;
                case 1:
                    if (type == 'pcTask') {
                        href = "{{route('admin::pcTask.index', [ 'taskType' => \App\Utils\ConstantUtils::SEARCH_SERVICE ])}}";
                    } else {
                        href = "{{route('admin::mobileTask.index', [ 'taskType' => \App\Utils\ConstantUtils::SEARCH_SERVICE ])}}";
                    }
                    break;
                case 2:
                    if (type == 'pcTask') {
                        href = "{{route('admin::pcTask.index', [ 'taskType' => \App\Utils\ConstantUtils::FLOW_SERVICE ])}}";
                    } else {
                        href = "{{route('admin::mobileTask.index', [ 'taskType' => \App\Utils\ConstantUtils::FLOW_SERVICE ])}}";
                    }
                    break;
                case 3:
                    if (type == 'pcTask') {
                        href = "{{route('admin::pcTask.index', [ 'taskType' => \App\Utils\ConstantUtils::HAO_123_SERVICE ])}}";
                    } else {
                        href = "{{route('admin::mobileTask.index', [ 'taskType' => \App\Utils\ConstantUtils::HAO_123_SERVICE ])}}";
                    }
                    break;
            }
            return href;
        }

        //监听提交
        form.on('submit(taskGroup)', function(data){
            window.form_submit = $('#submitBtn');
            form_submit.prop('disabled', true);
            var idVal = $('#id').val();
            var url = idVal?"{{route('admin::pcTask.update')}}": "{{route('admin::pcTask.create')}}";
            $.ajax({
                url: url,
                data: data.field,
                success: function (result) {
                    if (result.code !== 0) {
                        form_submit.prop('disabled', false);
                        layer.msg(result.msg, {shift: 6});
                        return false;
                    }
                    layer.msg(result.msg, {icon: 1, time: 1000}, function () {
                        window.location.href = "{{route('admin::pcTask.index')}}"
                    });
                }
            });
            return false;
        });


        function getCheckedData() {
            var checkStatusData = table.checkStatus('test'); //idTest 即为基础参数 id 对应的值
            return checkStatusData;
        }

        function getIds(checkedData) {
            var ids = [];
            var len = checkedData.data.length;
            for (var m=0;m<len;m++) {
                var row = checkedData.data[m];
                ids.push(row.id);
            }
            return ids;
        }

        // 这里是停止的操作
        function ajaxStopTask(ids, urlSegment) {
            var url = "{{route('admin::pcTask.stop')}}";
            $.post(url, {
                ids: ids
            }, function (data) {
                var str = { 'task_type':"{{$taskType}}", 'task_category': "{{$platForm}}" };
                if (parseInt(data.code) === 0) {
                    // 表格重新加载.
                    layer.msg('停止成功', {
                            icon:1,timeout: 1000
                        }, function () {
                            // 设定异步数据接口的额外参数
                            table.reload('test', {
                                url: "{{route('admin::pcTask.list')}}",
                                where: str
                            });
                        }
                    );
                }
            }, 'json')
        }


        function ajaxDelTask(ids, urlSegment) {
            if (urlSegment == 'pcTask') {
                var url = "{{route('admin::pcTask.delete')}}";
            }
            $.post(url, {
                ids: ids
            }, function (data) {
                if (parseInt(data.code) === 0) {
                    // 表格重新加载.
                    layer.msg('删除成功', {
                            icon:1,timeout: 1000
                        }, function () {
                            // 设定异步数据接口的额外参数
                            table.reload('test', {
                                url: "{{route('admin::pcTask.list')}}",
                                where: {}
                            });
                        }
                    );
                }
            }, 'json');
        }

        table.on('toolbar(test)', function (data) {
            var evt = data.event;
            var checkedData = getCheckedData();
            var ids = getIds(checkedData);
            var category = checkedData.data[0].task_category;
            if (category == 0) {
                var urlSegment = 'pcTask';
            }  else {
                var urlSegment = 'mobileTask';
            }
            switch (evt) {
                case 'stop':
                    if (checkedData.data.length == 0) {
                        layer.alert('请先勾选一项后再进行操作!');
                        return;
                    }
                    ajaxStopTask(ids, urlSegment);
                    break;
                case 'delete':
                    if (checkedData.data.length == 0) {
                        layer.alert('请先勾选一项后再进行操作!');
                        return;
                    }
                    ajaxDelTask(ids, urlSegment);
                    break;
                default:
                    break;
            }
        });

        table.on('tool(test)', function (obj) {
            var evt = obj.event;
            var d = obj.data;
            var idStr = [ d.id ];
            switch (evt) {
                case 'stop':
                    ajaxStopTask(idStr);
                    break;
                case 'delete':
                    ajaxDelTask(idStr);
                    break;
                case 'edit':
                    if (d.task_category == 0) {
                        href = "/admin/pcTask/"+d.id+"/edit";
                    } else {
                        href = "/admin/mobileTask/"+d.id+"/edit";
                    }
                    window.location.href= href;
                    break;
                default:
                    break;
            }
        });
    </script>
@endsection

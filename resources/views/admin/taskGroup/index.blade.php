@extends('admin.base')
@section('css')
    <style type="text/css">
        .layui-table-cell {
            font-size:14px;
            padding:0 5px;
            height:auto;
            overflow:visible;
            text-overflow:inherit;
            white-space:normal;
            word-break: break-all;
        }
    </style>
@endsection

@section('content')

@include('admin.breadcrumb')
    <div>
        <table class="layui-table" lay-data="{url:'/admin/taskGroup/list', page:true, limit:50, id:'test', toolbar:'#toolbar'}"
               lay-filter="test">
            <thead>
                <tr>
                    <th lay-data="{checkbox:true}"></th>
                    <th lay-data="{field:'id'}">序号</th>
                    <th lay-data="{field:'group_name'}">分组名称</th>
                    <th lay-data="{field:'enable_text'}">启用与否</th>
                    <th lay-data="{field:'platform'}">平台</th>
                    <th lay-data="{field:'service_name'}">所属类型</th>
                    <th lay-data="{field:'module_name'}">所属模块</th>
                    <th lay-data="{field:'description'}">描述</th>
                    <th lay-data="{width:230, templet:'#tool'}">操作</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

<script type="text/html" id="toolbar">
    <div>
        <a href='{{ route('admin::taskGroup.add') }}'><i class='layui-icon layui-icon-add-1'></i>新增任务分组</a>
        <i class='layui-icon layui-icon-delete' lay-event='delete' style="margin-left: 30px;"></i><span lay-event="delete">删除任务分组</span>
    </div>
</script>

<script type="text/html" id="tool">
    <div>
        <button type="button" class="layui-btn layui-btn-normal" lay-event="edit">修改</button>
        <button type="button" class="layui-btn layui-btn-danger" lay-event="delete">删除</button>
    </div>
</script>
@section('js')

<script>
    var table = layui.table;
    var selectIds = [];
    table.on('checkbox(test)', function (obj) {
        if (obj.checked) {
            selectIds.push(obj.data.id);
        } else {
            // 如果取消，将这个元素删除.
            var index = selectIds.indexOf(obj.data.id);
            if (index!== -1) {
                // 将这个元素删除
                selectIds.splice(index, 1);
            }
        }
    });


    table.on('tool(test)', function (obj) {
        console.log('obj', obj);
        switch (obj.event)
        {
            case 'edit':
                editTaskGroup(obj.data);
                break;
            case 'delete':
                delOneTaskGroup(obj.data)
                break;
        }
    });

    table.on('toolbar(test)', function (obj) {
        console.log('obj', obj);
        switch (obj.event) {
            case 'delete':
                delTaskGroup(obj);
                break;
        }
    });

    function editTaskGroup(row) {
        var url = '/admin/taskGroup/' + row.id+"/edit";
        window.location.href = url;
    }

    function delOneTaskGroup(row) {
        layer.confirm('确定要删除任务分组' + row.group_name+"?", {icon: 3, title:'提示'}, function(index){
            var ids = [ row.id ];
            ajaxDelTaskGroup(ids);
            layer.close(index);
        });
    }

    function delTaskGroup() {
        layer.confirm('确定要删除勾选的任务分组?', {icon: 3, title:'提示'}, function(index){
            if (selectIds.length === 0) {
                layer.alert('请先勾选一行数据后再进行删除操作!', {icon: 1});
                return;
            }
            ajaxDelTaskGroup(selectIds);
            layer.close(index);
        });
    }
    /**
     * 删除模块ID
     * @param ids
     */
    function ajaxDelTaskGroup(ids) {
        $.post('/admin/taskGroup/delete', {
            ids:ids
        }, function (data) {
            if (data.code == 0) {
                layer.msg('删除成功', {icon:1, time: 1000}, function () {
                    selectIds = [];
                    table.reload('test', {
                        where:{},
                        url: '/admin/taskGroup/list'
                    });
                });
            } else {
                layer.alert(data.msg);
            }
        }, 'json');
    }
</script>
@endsection

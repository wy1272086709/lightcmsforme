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
<div class="layui-card">
    <div class="layui-form layui-card-header light-search">
        <form>

            <div class="layui-inline">
                <label class="layui-form-label">模块名称</label>
                <div class="layui-input-inline">
                    <input type="text"  name="module_name" autocomplete="off" class="layui-input" value="{{ request()->get('module_name') }}" />
                </div>
            </div>

            <div class="layui-inline">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <select name="enable" lay-verify="required">
                        <option value="-1" @if(request()->get('enable') == '-1') selected @endif;>全部</option>
                        <option value="1" @if(request()->get('enable') == '1') selected @endif;>启用</option>
                        <option value="0" @if(request()->get('enable') == '0') selected @endif;>未启用</option>
                    </select>
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
        <table class="layui-table" lay-data="{url:'{{ route('admin::module.list') }}?{{ request()->getQueryString() }}', page:true, perPage:50, id:'test', toolbar:'#toolbar'}"
               lay-filter="test">
            <thead>
            <tr>
                <th lay-data="{checkbox:true}">序号</th>
                <th lay-data="{field:'module_name'}">模块名称</th>
                <th lay-data="{field:'file_name'}">模块文件</th>
                <th lay-data="{field:'version'}">版本号</th>
                <th lay-data="{field:'before_hash'}">hash前</th>
                <th lay-data="{field:'after_hash'}">hash后</th>
                <th lay-data="{field:'description'}">备注</th>
                <th lay-data="{field:'enableText'}">启用与否</th>
                <th lay-data="{width:230, templet:'#tool'}">操作</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

<script type="text/html" id="toolbar">
    <div>
        <a href='{{ route('admin::module.add') }}'><i class='layui-icon layui-icon-add-1'></i>新增模块</a>
        <i class='layui-icon layui-icon-delete' lay-event='delete' style="margin-left: 30px;"></i><span lay-event="delete">删除模块</span>
    </div>
</script>

<script type="text/html" id="tool">
    <div>
        <button type="button" class="layui-btn layui-btn-normal" lay-event="edit">修改</button>
        <button type="button" class="layui-btn layui-btn-warm" lay-event="stop">停止</button>
        <button type="button" class="layui-btn layui-btn-danger" lay-event="delete">删除</button>
    </div>
</script>

@section('js')
    <script type="text/javascript">
        var table = layui.table;
        table.on('tool(test)', function (obj) {
            console.log('obj', obj);
            switch (obj.event)
            {
                case 'edit':
                    editModule(obj.data);
                    break;
                case 'stop':
                    stopModule(obj.data)
                    break;
                case 'delete':
                    delOneModule(obj.data)
                    break;
            }
        });

        table.on('toolbar(test)', function (obj) {
           console.log('obj', obj);
            switch (obj.event) {
                case 'delete':
                    delModule(obj);
                    break;
            }
        });

        function editModule(row) {
            var url = '/admin/module/' + row.id+"/edit";
            window.location.href = url;
        }

        function stopModule(row) {
            var enableUrl = '/admin/module/' + row.id+'/enable';
            $.post(enableUrl, {
                   enable: 0
               }, function(data){
                if (data.code == 0) {
                    layer.msg('停止成功!', { icon:1, time: 1000 }, function () {
                        selectIds = [];
                        table.reload('test', {
                            url: '/admin/module/list'
                            ,where: {} //设定异步数据接口的额外参数
                        });
                    }, 1)
                }
            }, 'json');
        }

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


        function delModule() {
            layer.confirm('确定要删除勾选的模块?', {icon: 3, title:'提示'}, function(index){
                if (selectIds.length === 0) {
                    layer.alert('请先勾选一行数据后再进行删除操作!', {icon: 1});
                    return;
                }
                ajaxDelModule(selectIds);
                layer.close(index);
            });
        }


        function delOneModule(row) {
            layer.confirm('确定要删除模块' + row.module_name+"?", {icon: 3, title:'提示'}, function(index){
                var ids = [ row.id ];
                ajaxDelModule(ids);
                layer.close(index);
            });
        }

        /**
         * 删除模块ID
         * @param ids
         */
        function ajaxDelModule(ids) {
            $.post('/admin/module/delete', {
                ids:ids
            }, function (data) {
                if (data.code == 0) {
                    layer.msg('删除成功', {icon:1, time: 1000}, function () {
                        selectIds = [];
                        table.reload('test', {
                            where:{},
                            url: '/admin/module/list'
                        });
                    });
                } else {
                    layer.alert(data.msg);
                }
            }, 'json');
        }
    </script>
@endsection
@extends('admin.base')

@section('content')
    @include('admin.breadcrumb')

    <div class="layui-card">
        <div class="layui-form layui-card-header light-search">
            <form>
                <input type="hidden" name="action" value="search">
                <div class="layui-inline">
                    <label class="layui-form-label">用户ID</label>
                    <div class="layui-input-inline">
                        <input type="text" name="aid" autocomplete="off" class="layui-input" value="{{ request()->get('aid') }}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-inline">
                        <input type="text" name="username" autocomplete="off" class="layui-input" value="{{ request()->get('name') }}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">创建日期</label>
                    <div class="layui-input-inline">
                        <input type="text" name="pubdate" class="layui-input" id="pubdate" value="{{ request()->get('pubdate') }}">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">用户类型</label>
                    <div class="layui-input-inline">
                        <select name="typeid" lay-verify="">
                            <option value="-1" @if(!request()->get('typeid'))  selected @endif;>全部</option>
                            <option value="1" @if((int)request()->get('typeid') ===1)  selected @endif;>总管理员</option>
                            <option value="0" @if((int)request()->get('typeid') ===0 && request()->get('typeid')) selected @endif;>普通管理员</option>
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
            <table class="layui-table" lay-data="{url:'{{ route('admin::userManage.list') }}?{{ request()->getQueryString() }}', page:true, limit:50, id:'test', toolbar:'#toolbar'}"
                   lay-filter="test">
                <thead>
                <tr>
                    <th lay-data="{checkbox:true}"></th>
                    <th lay-data="{field:'aid', width:80, sort: true}">ID</th>
                    <th lay-data="{field:'username'}">用户名</th>
                    <th lay-data="{field:'statusText'}">启用状态</th>
                    <th lay-data="{field:'realname'}">真实姓名</th>
                    <th lay-data="{field:'typename'}">账号类型</th>
                    <th lay-data="{field:'pubdate'}">账号生成时间</th>
                    <th lay-data="{field:'logintime'}">最后登录后台时间</th>
                    <th lay-data="{field:'loginip'}">登录IP</th>
                    <th lay-data="{width:200, templet:'#action'}">操作</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

<script type="text/html" id="action">
    <a href="<% d.editUrl %>" class="layui-table-link" title="编辑管理员"><i class="layui-icon layui-icon-edit"></i></a>
    <!--
    <a href="<% d.roleUrl %>" class="layui-table-link" style="margin-left: 10px" title="分配角色"><i class="layui-icon layui-icon-auz"></i></a>
    -->
</script>

<script type="text/html" id="aid-checkbox">
    <input type="checkbox" value="<% d.aid %>" name="aid[]" lay-skin="primary" />
</script>

<script type="text/html" id="toolbar">
    <div>
        <a href='{{ route('admin::userManage.add') }}'><i class='layui-icon layui-icon-add-1'></i>新增管理员</a>
        <i class='layui-icon layui-icon-delete' lay-event='delete' style="margin-left: 30px;"></i><span lay-event="delete">删除管理员</span>
    </div>
</script>

@section('js')
    <script>
        var laytpl = layui.laytpl;
        laytpl.config({
            open: '<%',
            close: '%>'
        });

        var laydate = layui.laydate;
        laydate.render({
            elem: '#created_at',
            range: '~'
        });

        var table = layui.table;
        var selectAids = [];
        table.on('toolbar(test)', function (obj) {
           if (obj.event == 'delete') {
               layer.confirm('确定要删除吗?', {icon: 3, title:'提示'}, function(index){
                   if (selectAids.length === 0) {
                        layer.alert('请先勾选一行数据后再进行删除操作!', {icon: 1});
                        return;
                   }
                   ajaxDel(selectAids)
                   layer.close(index);
               });
           }
        });


        table.on('checkbox(test)', function (obj) {
           if (obj.checked) {
               selectAids.push(obj.data.aid);
           } else {
               // 如果取消，将这个元素删除.
               var index = selectAids.indexOf(obj.data.aid);
               if (index!== -1) {
                   // 将这个元素删除
                   selectAids.splice(index, 1);
               }
           }
        });


        function ajaxDel(ids) {
            $.post('/admin/userManage/delete', {
                ids: ids
            }, function (data) {
                if (parseInt(data.code) === 0) {
                    // 表格重新加载.
                    layer.msg('删除成功', {
                        icon:1,timeout: 1000
                    }, function () {
                            table.reload('test', {
                                url: '/admin/userManage/list'
                                ,where: {} //设定异步数据接口的额外参数
                            });
                        }
                    );
                }
            }, 'json')

        }
    </script>
@endsection
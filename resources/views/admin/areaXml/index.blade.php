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
    <div class="layui-form">
        <form>
        <div class="layui-form-item">
            <label class="layui-form-label">任务ID:</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="taskId" />
            </div>
            <button class="layui-btn layuiadmin-btn-list" lay-filter="form-search" id="submitBtn">
                <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
            </button>
        </div>
        </form>
    </div>

    <table class="layui-table" lay-data="{url:'{{ route('admin::areaXml.list') }}?{{ request()->getQueryString() }}', page:true, limit:50, id:'test'}"
           lay-filter="test">
        <thead>
        <tr>
            <th lay-data="{checkbox:true}"></th>
            <th lay-data="{field:'no', width:80, sort: true}">ID</th>
            <th lay-data="{field:'taskId'}">任务ID</th>
            <th lay-data="{field:'taskName'}">任务名称</th>
            <th lay-data="{field:'mtime'}">修改时间</th>
            <th lay-data="{field:'cfg_hash'}">cfg_hash</th>
            <th lay-data="{field:'content'}">XML</th>
            <th lay-data="{width:200, templet:'#action'}">操作</th>
        </tr>
        </thead>
    </table>
@endsection
<script type="text/html" id="action">

</script>
@section('js')
<script type="text/javascript">



</script>
@endsection



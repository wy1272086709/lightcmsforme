@extends('admin.base')
@section('css')
<style type="text/css">
    .css-input-width{
        width: 300px;
    }
</style>
@endsection
@section('content')
    <div class="layui-card">

        @include('admin.breadcrumb')
        <div class="layui-card-body">
            <form class="layui-form" action="@if(!isset($id)) {{ route('admin::taskGroup.create') }} @else {{ route('admin::taskGroup.create') }} @endif" method="post">
                <div class="layui-form-item">
                    <label class="layui-form-label">分组名称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="group_name" required  @if(isset($id)) readonly @endif
                        lay-verify="required" placeholder="请输入分组名称" autocomplete="off" class="layui-input css-input-width"
                               value="@if(isset($id)){{$taskGroup->group_name}}@endif" />
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">启用</label>
                    <div class="layui-input-block">
                        <input type="radio" name="enable" value="1" title="是" @if(isset($taskGroup) && (int)$taskGroup->enable === 1)checked @endif>
                        <input type="radio" name="enable" value="0" title="否" @if(isset($taskGroup) && (int)$taskGroup->enable === 0)checked @endif>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">平台</label>
                    <div class="layui-input-block">
                        <input type="radio" name="platform" value="0" title="PC" @if(isset($taskGroup) && (int)$taskGroup->platform === 0)  checked @endif>
                        <input type="radio" name="platform" value="1" title="移动端" @if(isset($taskGroup) && (int)$taskGroup->platform === 1) checked @endif>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属类型</label>
                    <div class="layui-input-inline">
                        <select name="service_type" lay-verify="required" required>
                            <option value="1" @if(isset($taskGroup) && (int)$taskGroup->service_type === 1)  selected @endif>广告业务</option>
                            <option value="2" @if(isset($taskGroup) && (int)$taskGroup->service_type === 2) selected @endif>搜索业务</option>
                            <option value="3" @if(isset($taskGroup) && (int)$taskGroup->service_type === 3)  selected @endif>流量业务</option>
                            <option value="4" @if(isset($taskGroup) && (int)$taskGroup->service_type === 4) selected @endif>hao123业务</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">所属模块</label>
                    <div class="layui-input-inline css-input-width">
                        <select name="module_id" lay-verify="required" required>
                            @foreach($moduleNameMap as $k =>$moduleName)
                            <option value="{{$k}}" @if(isset($taskGroup) && (int)$taskGroup->module_id === $k) selected @endif>{{$moduleName}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">备注</label>
                    <div class="layui-input-inline">
                        <textarea name="description" class="layui-textarea css-input-width">@if(isset($taskGroup)){{$taskGroup->description}} @endif</textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="hidden" id="id" name="id" value="{{isset($id)?$id:''}}" class="css-input-width"/>
                        <button class="layui-btn" lay-submit lay-filter="taskGroup" id="submitBtn">提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var form = layui.form;

        //监听提交
        form.on('submit(taskGroup)', function(data){
            window.form_submit = $('#submitBtn');
            form_submit.prop('disabled', true);
            var idVal = $('#id').val();
            console.log(data.field);
            var url = idVal?"{{route('admin::taskGroup.update')}}": "{{route('admin::taskGroup.create')}}";
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
                        window.location.href = "{{route('admin::taskGroup.index')}}"
                    });
                }
            });

            return false;
        });
    </script>
@endsection
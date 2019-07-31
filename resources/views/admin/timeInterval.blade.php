@extends('admin.base')
@section('content')
    @include('admin.breadcrumb')
    <form class="layui-form" lay-filter="test"> <!-- 提示：如果你不想用form，你可以换成div等任何一个普通元素 -->
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: 100px;">间隔时间管理:</label>
            <div class="layui-input-inline">
                <input type="text" lay-verify="intrule" name="mn_interval_time" placeholder="请输入" autocomplete="off"
                       class="layui-input" value="{{$intervalTime}}" />
            </div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="*">提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
    @endsection
@section('js')
    <script type="text/javascript">
        var form = layui.form;
        form.verify({
            intrule: function (value, item) {
                var flag = typeof parseInt(value) === 'number' && value%1 === 0;
                if (!flag) {
                    return '间隔时间只能为整数!';
                }
            }
        });

        form.on('submit(*)', function (data) {
            var formData = data.field;
            setIntervalTime(formData);
            return false;
        });

        function setIntervalTime(json) {
            var url = "{{ route('admin::intervalTime.store') }}";
            $.post(url, json, function (data) {
                console.log('data:', data);
                if (parseInt(data.code) === 0) {
                    layer.msg('间隔时间设置成功!');
                } else {
                    layer.alert('间隔时间设置失败!');
                }
            }, 'json');
        }
    </script>
@endsection
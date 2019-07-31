@extends('admin.base')
@section('css')
<style type="text/css">
    .css-module-1 {
        width: 300px;
    }
    .required-span {
        color: red;
    }
</style>
@endsection
@section('content')
    @include('admin.breadcrumb')
    <form class="layui-form"> <!-- 提示：如果你不想用form，你可以换成div等任何一个普通元素 -->
        <div class="layui-form-item">
            <label class="layui-form-label" class="required-span">模块名称<span class="required-span">*</span></label>
            <div class="layui-input-inline">
                <input type="text"  name="module_name" required  lay-verify="required" @if(isset($module)) readonly @endif; placeholder="请输入模块名称" autocomplete="off" class="layui-input css-module-1" value="{{isset($module['module_name'])?$module['module_name']:''}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">模块文件<span class="required-span">*</span></label>
            <div class="layui-input-inline">
                <input type="hidden" name="file_name" id="file_name" value="{{isset($module['file_name'])?$module['file_name']: ''}}" @if(isset($id)) lay-verify="required" @endif;/>
                @if(!isset($id))
                    <button type="button" class="layui-btn" id="test1">
                        <i class="layui-icon">&#xe67c;</i>上传文件
                    </button>
                @endif
                <label id="label_file_name" class="layui-form-label">{{isset($module['file_name'])?$module['file_name']:''}}</label>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" class="required-span">版本号<span class="required-span">*</span></label>
            <div class="layui-input-inline">
                <input type="text" name="version"  required  lay-verify="required|isVersion" placeholder="请输入版本号" autocomplete="off" class="layui-input css-module-1" value="{{isset($module['version'])?$module['version']:''}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" class="required-span">hash前<span class="required-span">*</span></label>
            <div class="layui-input-inline">
                <input type="text"  name="before_hash" placeholder="请输入hash前" autocomplete="off" class="layui-input css-module-1" value="{{isset($module['before_hash'])?$module['before_hash']:''}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" class="required-span">hash后<span class="required-span">*</span></label>
            <div class="layui-input-inline">
                <input type="text"  name="after_hash" placeholder="请输入hash后" autocomplete="off" class="layui-input css-module-1" value="{{isset($module['after_hash'])?$module['after_hash']:''}}">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" class="required-span">备注</label>
            <div class="layui-input-inline">
                <textarea name="description"  placeholder="请输入备注" class="layui-textarea css-module-1">{{isset($module['description'])? $module['description']: ''}}</textarea>
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <div class="layui-form-item" pane>
                <label class="layui-form-label">启用</label>
                <div class="layui-input-block">
                    <input type="radio" name="enable" value="1" title="启用" @if(isset($module) && $module['enable'] == 1) checked @endif;>
                    <input type="radio" name="enable" value="0" title="未启用" @if(isset($module) && $module['enable'] == 0) checked @endif;>
                </div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" id="id" name="id" value="{{isset($id)?$id:''}}" />
                <input type="hidden" id="type" name="type" value="4" />
                <button class="layui-btn" lay-submit id="submitBtn" lay-filter="*">提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
        <!-- 更多表单结构排版请移步文档左侧【页面元素-表单】一项阅览 -->
    </form>
@endsection
@section('js')
<script type="text/javascript">
    layui.use('upload', function(){
        var upload = layui.upload;
        if (!$('#id').val())
        {

        }
        //执行实例
        var uploadInst = upload.render({
            elem: '#test1',
            accept: "file",
            field: 'file1',
            acceptMine: 'image/jpg,image/png',
            url: "/admin/upload",
            done: function(res){
                //上传完毕回调
                $('#label_file_name').text(res.filepath);
                $('#file_name').val(res.filepath);
                console.log('upload success!')
            },
            error: function(){
                //请求异常回调
            }
        });
    });

    var form = layui.form;
    form.verify({
        isVersion: function (value, item) {
            var preg = /^(\d{1,}\.){1,}\d$/;
            if (!value)
            {
                return '版本号必须!';
            }
            if (!preg.test(value))
            {
                return '版本号中只能包含数字和逗号!如1.0.1';
            }
        }
    });

    var table = layui.table;

    //监听提交
    form.on('submit(*)', function(data){
        console.log('data', data);
        window.form_submit = $('#submitBtn');
        form_submit.prop('disabled', true);
        var idVal = $('#id').val();
        var url = idVal?"{{route('admin::module.update')}}": "{{route('admin::module.create')}}";
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
                    window.location.href = "{{route('admin::module.index')}}"
                });
            }
        });

        return false;
    });
</script>
@endsection
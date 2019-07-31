@extends('admin.base')
@section('css')
    <style type="text/css">
        .margin-span{
            margin-right: 40px;
            font-weight: bold;
        }
        .input-style{
            width: 500px;
        }
        .css-bold-font {
            font-weight: bold;
        }
        .margin-row {
            margin-top: 30px;
        }
        .fix-modal-label {
            width: 86px;
            padding: 9px 10px;
        }
        .margin-block {
            margin-left:20px;
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
                    @include('admin.pcTask.publish_task', [ 'taskType' => $taskType ])
                @endif
            </div>
            <div class="layui-tab-item @if($taskType == \App\Utils\ConstantUtils::SEARCH_SERVICE) layui-show @endif">
                @if($taskType == \App\Utils\ConstantUtils::SEARCH_SERVICE)
                    @include('admin.pcTask.publish_task', [ 'taskType' => $taskType ])
                @endif
            </div>
            <div class="layui-tab-item @if($taskType == \App\Utils\ConstantUtils::FLOW_SERVICE) layui-show @endif">
                @if($taskType == \App\Utils\ConstantUtils::FLOW_SERVICE)
                    @include('admin.pcTask.publish_task', [ 'taskType' => $taskType ])
                @endif
            </div>
            @if($platForm == \App\Utils\ConstantUtils::PC_PLATFORM)
            <div class="layui-tab-item @if($taskType == \App\Utils\ConstantUtils::HAO_123_SERVICE) layui-show @endif">
                @if($taskType == \App\Utils\ConstantUtils::HAO_123_SERVICE)
                    @include('admin.pcTask.publish_task', [ 'taskType' => $taskType ])
                @endif
            </div>
            @endif
        </div>
    </div>
@endsection

@include('admin.pcTask.modal')

@section('js')
    <script type="text/javascript">
        var element = layui.element;
        element.on('tab(docDemoTabBrief)', function (data) {
            // 获取当前tab 所在下标
            var index = data.index;
            window.location.href = getLocationHref(index);
        });

        function getLocationHref(index) {
            var href = "";
            switch (index) {
                case 0:
                    href = "{{route('admin::mobileTask.add', [ 'taskType' => \App\Utils\ConstantUtils::ADS_SERVICE ])}}";
                    break;
                case 1:
                    href = "{{route('admin::mobileTask.add', [ 'taskType' => \App\Utils\ConstantUtils::SEARCH_SERVICE ])}}";
                    break;
                case 2:
                    href = "{{route('admin::mobileTask.add', [ 'taskType' => \App\Utils\ConstantUtils::FLOW_SERVICE ])}}";
                    break;
                case 3:
                    href = "{{route('admin::mobileTask.add', [ 'taskType' => \App\Utils\ConstantUtils::HAO_123_SERVICE ])}}";
                    break;
            }
            return href;
        }
    </script>
    <script type="text/javascript" src="{{'/public/admin/js/task.js'}}"></script>
@endsection

@include('admin.pcTask.task_templet')
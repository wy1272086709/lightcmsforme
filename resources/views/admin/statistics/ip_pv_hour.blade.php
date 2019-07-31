@extends('admin.base')
@section('content')
    <!-- 面包屑组件 -->
    @include('admin.breadcrumb')
    <h3>{{$taskName}}的({{$date}})日每小时数据：</h3>
    <table class="layui-table" lay-data="{height:600, url:'{{route('admin::statistics.ipPvHourList')}}?id={{request('id')}}', page:true, id:'test'}" lay-filter="test">
        <thead>
        <tr>
            <th lay-data="{field:'date', width:110}">日期</th>
            @for($i = 0;$i<24;$i++)
                <th lay-data="{field:'stat_val_{{$i}}'}">@if($i<10) {{'0'.$i}} @else {{$i}} @endif</th>
            @endfor
        </tr>
        </thead>
    </table>
@endsection

@section('js')
<script type="text/javascript">

</script>
@endsection
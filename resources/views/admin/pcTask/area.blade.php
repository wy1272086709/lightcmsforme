<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@isset($breadcrumb){{ last($breadcrumb)['title'] }}@endisset - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="/public/vendor/layui-v2.4.5/css/layui.css" media="all">
    <link rel="stylesheet" href="/public/admin/css/lightCMSAdmin.css" media="all">
    <style type="text/css">
        .pinyin ul, .pinyin li {
            display: inline-block;
            list-style-type: none;
            font-weight: bold;
        }
        .pinyin>li {
            margin-right: 20px;
        }
        .common-use-block>li{
            float:left;
        }
        .total_span {
            color: #666600;
        }
        .float_li {
            float: left;
        }
        .clear_li {
            clear: both;
        }
    </style>
</head>
<div class="layui-body" style="left:10px;">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;" class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title layui-form">
            <li class="@if($tab_index == 0) layui-this @endif" lay-id="area_tab_li">
                <div class="layui-input-block">
                    <input type="radio" name="area_channel_radio" value="0" title="地区">
                </div>
            </li>
            <li class="@if($tab_index == 1) layui-this @endif" lay-id="channel_tab_li">
                <div class="layui-input-block">
                    <input type="radio" name="area_channel_radio" value="1" title="渠道">
                </div>
            </li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item @if($tab_index == 0) layui-show @endif">
                @include('admin.pcTask.common_form', [ 'tab_index' => 0 ])
                <ul class="pinyin">
                    @foreach(range('A', 'Z', 1) as $i)
                        <li><a href="#">{{$i}}</a></li>
                    @endforeach
                </ul>
                <div class="common-use-block">
                    @php $c = 0; @endphp
                    @foreach($vars['provinceRes'] as $k => $v)
                        @php $f = $c++; @endphp
                        <li class="layui-form float_li @if($f%4==0) clear_li @endif">
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: auto;"></label>
                                <div class="layui-input-inline">
                                    <input type="checkbox" name="area_group_ids[{{$k}}]" lay-filter="area_group_filter" value="{{$v}}" id="g_{{$k}}" title="{{$vars['provinceNameMap'][$k]}} 台数: {{$v}}" lay-skin="primary"/>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </div>
                <hr/>
                <div class="css-dq">
                    @foreach($vars['hasCityDataRes'] as $k=> $row)
                    <h4 class="clear_li">{{$vars['provincePinyinMap'][$k]}}</h4>
                    <li class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: auto;"></label>
                            <div class="layui-input-inline">
                                <input type="checkbox" name="area_group_ids[{{$k}}]" id="pro_{{$k}}" class="province-chk" lay-filter="province-chk-filter" title="{{$vars['provinceNameMap'][$k]}} {{$row[0]}}" value="{{$row[0]}}" lay-skin="primary"/>
                            </div>
                        </div>
                    </li>
                    <ul>
                        @php $x=0; @endphp
                        @foreach($row as $cityId => $val)
                            @if($cityId !=0)
                                @php $j=$x++; @endphp
                                <li class="layui-form area_li_{{$k}} float_li @if($j%4==0) clear_li @endif" style="margin-left: 20px;" lay-filter="child-area-filter">
                                    <div class="layui-form-item">
                                        <label class="layui-form-label" style="width: auto;"></label>
                                        <div class="layui-input-inline">
                                            <input type="checkbox" name="area_group_ids[{{$k}}][{{$cityId}}]" lay-filter="city-area-filter" id="area_{{$k}}_{{$cityId}}" class="city-chk-{{$k}}" title="{{$vars['area_conf_json'][$k][$cityId]}} {{$val}}" lay-skin="primary" value="{{$val}}" />
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    @endforeach
                </div>
            </div>
            <div class="layui-tab-item @if($tab_index == 1) layui-show @endif">
                @include('admin.pcTask.common_form', [ 'tab_index' => 1, 'ratio' => $ratio ])
                <ul class="pinyin">
                    @foreach(range('A', 'Z', 1) as $i)
                        <li><a href="#">{{$i}}</a></li>
                    @endforeach
                </ul>
                <div class="common-use-block">
                    @php $i=0; @endphp
                    @foreach($vars['firstLevelChannelRes'] as $k => $v)
                        @php $j = $i++; @endphp
                        <li class="layui-form float_li @if($j%4==0) clear_li @endif">
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: auto;"></label>
                                <div class="layui-input-inline">
                                    <input type="checkbox" name="channel_group_ids[{{$k}}]" value="{{$v}}" lay-filter="channel-group-common-filter" id="g_{{$k}}" title="{{isset($vars['groupNameMap'][$k]) && $vars['groupNameMap'][$k]!=''?$vars['groupNameMap'][$k]:'分组_'.$k}} 台数: {{$v}} {{isset($vars['channelUseCount'][$k])?$vars['channelUseCount'][$k]:''}}" lay-skin="primary"/>
                                </div>
                            </div>
                        </li>
                    @endforeach

                </div>
                <div class="css-qd">
                    @foreach($vars['TwoLevelChannelRes'] as $k=> $row)
                        <li class="layui-form">
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: auto;"></label>
                                <div class="layui-input-inline">
                                    <input type="checkbox" name="channel_group_ids[{{$k}}]" id="channel_g_{{$k}}" value="{{is_array($row)?$row[0]:$row}}" class="channel-group-chk" lay-filter="channel-group-chk-filter" title="{{isset($vars['groupNameMap'][$k])?$vars['groupNameMap'][$k]:''}} 台数: {{is_array($row)?$row[0]:$row}} {{isset($vars['channelUseCount'][$k])?$vars['channelUseCount'][$k]:''}} " lay-skin="primary"/>
                                </div>
                            </div>
                        </li>
                        <ul>
                            @php $m=0; @endphp
                            @foreach($row as $cityId => $val)
                                @if($cityId !=0)
                                    @php $h = $m++; @endphp
                                    <li class="layui-form channel_li_{{$k}} float_li @if($h%4==0) clear_li @endif" style="margin-left: 20px;" lay-filter="channel-child-chk-filter">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label" style="width: auto;"></label>
                                            <div class="layui-input-inline">
                                                <input type="checkbox" name="channel_group_ids[{{$k}}][{{$cityId}}]" id="area_{{$k}}_{{$cityId}}" value="{{$val}}" class="child-channel-{{$k}}" title="{{isset($cityId)?$cityId:''}} 台数: {{$val}} {{isset($vars['channelUseCount'][$k])?$vars['channelUseCount'][$k]:''}}" lay-skin="primary"/>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/public/vendor/layui-v2.4.5/layui.all.js"></script>
<script src="/public/admin/js/admin.js"></script>
<script>
$(function () {
    showDateEvt();
    selectEvts();
});

function showDateEvt()
{
    $('#dates').on('click', function () {
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            //执行一个laydate实例
            laydate.render({
                elem: '#dates',
                //直接显示
                show: true
            });
        });
    });
}


/**
 * 全选，全不选，反选的事件监听
 */
function selectEvts()
{
    var form = layui.form;
    var element = layui.element;
    element.on('tab(docDemoTabBrief)', function(data){
        var index = data.index; //得到当前Tab的所在下标
        console.log('index00000', index);
        $('#tab_index').val(index);
        var radioEle = $('input[name="area_channel_radio"][value="'+index+'"]');
        radioEle.prop('checked', true);
        $('input[name="area_channel_radio"][value!="'+index+'"]').prop('checked', false);
        layui.form.render('radio');
    });

    //地区信息里面,复选项框选中,子选项框也选中
    form.on('checkbox(province-chk-filter)', function (data) {
        console.log('go here province filter');
        // 当前元素选中
        var idStr = data.elem.id;
        var id = idStr.split('_')[1];
        if (data.elem.checked) {
            $('.city-chk-'+id).prop('checked', true);
            $('.area_li_'+id).addClass('layui-hide');
        } else {
            $('.city-chk-'+id).prop('checked', false);
            $('.area_li_'+id).removeClass('layui-hide');
        }
        form.render();
    });




    //渠道信息里面，复选项框选中,子选项框也选中
    form.on('checkbox(channel-group-chk-filter)', function (data) {
        // 当前元素选中
        var idStr = data.elem.id;
        var id = idStr.split('channel_g_')[1];
        var childChannelSelector = 'channel_li_'+id;
        if (data.elem.checked) {
            $('.child-channel-'+id).prop('checked', true);
            $('.'+childChannelSelector).addClass('layui-hide');
        } else {
            $('.child-channel-'+id).prop('checked', false);
            $('.'+childChannelSelector).removeClass('layui-hide');
        }
        form.render();
    });


    //全选
    form.on('radio(all-select)', function (data) {
        console.log('all select');
        if (data.value=='1') {
            $('input[type="checkbox"]').prop('checked', true);
            form.render();
        }
    });

    //全不选
    form.on('radio(all-no-select)', function (data) {
        if (data.value == '2') {
            console.log('all no select');
            $('input[type="checkbox"]').prop('checked', false);
            form.render();
        }
    });

    //反选
    form.on('radio(all-opposite-select)', function (data) {
        if (data.value == '3') {
            console.log('all opposite select');
            var checkedIds = [];
            var checkEles  = $('input:checkbox:checked');
            checkEles.each(function(){
                var idStr = $(this).attr('id');
                checkedIds.push(idStr);
            });
            var idSelectorArr = checkedIds.map(function (id) {
                return '#' + id;
            });
            // 将选中的元素取消选中
            $(idSelectorArr.join(',')).prop('checked', false);
            // 未选中的元素选中.
            $('input:checkbox').not(idSelectorArr.join(',')).prop('checked', true);
        }
        form.render();
    });
}
</script>

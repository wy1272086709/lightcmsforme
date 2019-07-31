<form class="layui-form" action="">
    <div class="layui-form-item @if($tab_index == 1) layui-show @endif">
        <label class="layui-form-label" style="">已选<span id="selectText"></span></label>
        <div class="layui-input-inline" style="width: 260px;">
            <input type="radio" name="xz" title="全选" value="1" lay-filter="all-select">
            <input type="radio" name="xz" title="全不选" value="2" lay-filter="all-no-select">
            <input type="radio" name="xz" title="反选" value="3" lay-filter="all-opposite-select">
        </div>

        <!-- 发布分组比例 -->
        @if($tab_index == 1)
            <div class="layui-input-inline" style="width: 160px;">
                <input type="text" name="ratio" id="ratio"  value="{{$ratio}}" class="layui-input" placeholder="请输入发布分组比例" />
            </div>
        @endif

        <div class="layui-input-inline">
            <input type="text" class="layui-input" placeholder="请输入日期" name="dates" id="dates" value="{{$date?$date:date('Y-m-d')}}"/>
        </div>

        <div class="layui-input-inline" id="layui-search-key">
            @if($tab_index == 0)
                <input type="text" class="layui-input" placeholder="按城市名进行搜索" id="cityKey" name="cityKey"    value="{{$key?$key:''}}"/>
            @endif
        </div>
        <button class="layui-btn layui-btn-normal" type="submit">搜索</button>
        <input type="hidden" name="tab_index" id="tab_index" value="{{$tab_index}}" />
    </div>
</form>

<div class="layui-card">
    <div class="layui-form layui-card-header light-search">
        <form>
            <div class="layui-inline">
                <label class="layui-form-label">任务名称</label>
                <div class="layui-input-inline">
                    <input type="text"  name="task_name" autocomplete="off" class="layui-input" value="{{ request()->get('task_name') }}" />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">是否启用</label>
                <div class="layui-input-block">
                    <select name="enable" lay-verify="required">
                        <option value="-1" @if(request()->get('enable') == '-1') selected @endif;>全部</option>
                        <option value="1" @if(request()->get('enable') == '1') selected @endif;>启用</option>
                        <option value="0" @if(request()->get('enable') == '0') selected @endif;>未启用</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="task_type" value="{{$taskType}}" />
            <input type="hidden" name="task_category" value="{{$platForm}}" />
            <div class="layui-inline">
                <button class="layui-btn layuiadmin-btn-list" lay-filter="form-search" id="submitBtn">
                    <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="layui-card-body">
        <table class="layui-table" lay-data="{url:'{{ route('admin::pcTask.list') }}?{{ request()->getQueryString().'&task_category='.$platForm.'&task_type='.$taskType }}', page:true, perPage:50, id:'test', toolbar:'#toolbar'}"
               lay-filter="test">
            <thead>
            <tr>
                <th lay-data="{checkbox:true}">序号</th>
                <th lay-data="{field:'id'}">任务ID</th>
                <th lay-data="{field:'task_name'}">名称</th>
                <th lay-data="{field:'status_text'}">启用状态</th>
                <th lay-data="{field:'level'}">优先级</th>
                <th lay-data="{field:'issue_value'}">发布量级</th>
                <th lay-data="{field:'task_group_name'}">任务分组</th>
                <th lay-data="{width:190, field:'create_time'}">创建时间</th>
                <th lay-data="{width:190, field:'stop_time'}">结束时间</th>
                <th lay-data="{width:190, field:'update_time'}">更新时间</th>
                <th lay-data="{width:250, templet:'#tool'}">操作</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="toolbar" type="text/html">
    <div class="layui-form-item">
        <a href='{{ $platForm == \App\Utils\ConstantUtils::PC_PLATFORM ? route('admin::pcTask.add', [ 'taskType' => $taskType ]): route('admin::mobileTask.add', [ 'taskType' => $taskType ]) }}'><i class='layui-icon layui-icon-add-1'></i>新增任务</a>
        <i class='layui-icon layui-icon-pause' style="margin-left: 30px;"></i><span lay-event="stop">停止任务</span>
        <i class='layui-icon layui-icon-delete' style="margin-left: 30px;"></i><span lay-event="delete">删除任务</span>
    </div>
</script>

<script id="tool" type="text/html">
    <div class="layui-form-item">
        <button type="button" class="layui-btn layui-btn-normal" lay-event="edit">修改</button>
        <button type="button" class="layui-btn layui-btn-danger" lay-event="delete">删除</button>
        <button type="button" class="layui-btn layui-btn-warm"   lay-event="stop">停止</button>
    </div>
</script>

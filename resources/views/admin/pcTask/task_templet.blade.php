<!-- 添加ua头模板引擎 -->
<script type="text/html" id="ua_tr">
    <tr>
        <td><% d.no %></td>
        <td><% d.device_type %></td>
        <td><% d.browser %></td>
        <td><% d.visit_ratio %></td>
        <td>
            <label class="layui-btn layui-btn-danger layui-btn-sm ua-tr-del" lay-event="ua-del-evt">删除</label>
            <label class="layui-btn layui-btn-normal layui-btn-sm ua-tr-mod" lay-event="ua-mod-evt">修改</label>
        </td>
    </tr>
</script>

<!-- 添加ua头模板引擎 -->
<script type="text/html" id="multi_ua_set">
    <%# var i = 0; %>
    <%# layui.each(d.list, function(index,item) { %>
    <tr>
        <td><% i++ %></td>
        <td><% item.device_type %></td>
        <td><% item.browser %></td>
        <td><% item.visit_ratio %></td>
        <td>
            <label class="layui-btn layui-btn-danger layui-btn-sm ua-tr-del" lay-event="ua-del-evt">删除</label>
            <label class="layui-btn layui-btn-normal layui-btn-sm ua-tr-mod" lay-event="ua-mod-evt">修改</label>
        </td>
    </tr>
    <%# }); %>
</script>

<!-- 添加来源时候的模板引擎 -->
<script type="text/html" id="source_url_tr">
    <tr>
        <td><% d.no %></td>
        <td><% d.source_content %></td>
        <td><% d.visit_ratio %></td>
        <td>
            <label class="layui-btn layui-btn-normal layui-btn-sm source-tr-mod">修改</label>
            <label class="layui-btn layui-btn-danger layui-btn-sm source-tr-del">删除</label>
        </td>
    </tr>
</script>

<script type="text/html" id="source_url_body_data_templet">
    <%# var i=0; %>
    <%# layui.each(d.list, function(index, item){ %>
    <tr>
        <td><% i++ %></td>
        <td><% item.source_content %></td>
        <td><% item.visit_ratio %></td>
        <td>
            <label class="layui-btn layui-btn-normal layui-btn-sm source-tr-mod">修改</label>
            <label class="layui-btn layui-btn-danger layui-btn-sm source-tr-del">删除</label>
        </td>
    </tr>
    <%# }); %>
</script>

<!-- 点击设置相关的模板,添加同级模板 -->
<script type="text/html" id="click_add_dir_templet">
    <div class="layui-form-item clickSetItem level<% d.dir_level %>"  data-pid="<% d.pid %>">
        <div class="layui-inline">
            <label class="layui-form-label" style="width: 120px;"><span class="clickPrefix"><% d.prefix %></span>
                <span class="clickSetNo"><% d.no %></span>点击间隔时间</label>
            <div class="layui-input-inline" style="width: 170px;">
                <input type="text" class="layui-input interval-time" name="tree[<% d.no %>][time_inter]" placeholder="请输入数字或者数字,数字">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label" style="width:auto;">点击区域</label>
            <div class="layui-input-inline" style="width: 120px;">
                <input type="text" class="layui-input click-area" name="tree[<% d.no %>][click_area]" placeholder="请输入点击区域">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label" style="width:auto;"></label>
            <div class="layui-input-inline">
                <select style="width:120px;margin-top: -10px;" name="tree[<% d.no %>][type]" class="tag">
                    <option value="5">5-当前页面</option>
                    <option value="3">3-新标签</option>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label" style="width: auto;">点击几率</label>
            <div class="layui-input-inline" style="width:130px;">
                <input type="text" class="layui-input click-odds" style="width:120px;" name="tree[<% d.no %>][odds]" placeholder="请输入点击几率">
            </div>
        </div>
        <div class="layui-inline">
            <button class="layui-btn layui-btn-primary addSiblingDir">添加同级目录</button>
            <button class="layui-btn layui-btn-primary addSubDir">添加子目录</button>
            <button class="layui-btn layui-btn-danger layui-btn-sm source-dir-del">删除</button>
        </div>
    </div>
</script>

<!-- 点击设置对应的模板 -->
<script type="text/html" id="clickSetModalData">
    <%# layui.each(d.list, function(index, item){  %>
    <div class="layui-form-item clickSetItem level<% item.dir_level %>"  data-pid="<% item.pid %>">
        <div class="layui-inline">
            <label class="layui-form-label" style="width: 120px;"><span class="clickPrefix"><% item.prefix %></span>
                <span class="clickSetNo"><% item.cid %></span>点击间隔时间</label>
            <div class="layui-input-inline" style="width: 170px;">
                <input type="text" class="layui-input interval-time" value="<% item.time_interval %>" name="tree[<% item.cid %>][time_inter]" placeholder="请输入数字或者数字,数字">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label" style="width:auto;">点击区域</label>
            <div class="layui-input-inline" style="width: 120px;">
                <input type="text" class="layui-input click-area" value="<% item.click_area %>" name="tree[<% item.cid %>][click_area]" placeholder="请输入点击区域">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label" style="width:auto;"></label>
            <div class="layui-input-inline">
                <select style="width:120px;margin-top: -10px;" name="tree[<% item.cid %>][type]" class="tag">
                    <option value="5">5-当前页面</option>
                    <option value="3">3-新标签</option>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label" style="width: auto;">点击几率</label>
            <div class="layui-input-inline" style="width:130px;">
                <input type="text" class="layui-input click-odds" value="<% item.odds %>" style="width:120px;" name="tree[<% item.cid %>][odds]" placeholder="请输入点击几率">
            </div>
        </div>
        <div class="layui-inline">
            <button class="layui-btn layui-btn-primary addSiblingDir">添加同级目录</button>
            <button class="layui-btn layui-btn-primary addSubDir">添加子目录</button>
            <button class="layui-btn layui-btn-danger layui-btn-sm source-dir-del">删除</button>
        </div>
    </div>
    <%# }); %>
</script>

<!--  发布数值对应的模板引擎-->
<script type="text/html" id="issued_number_templet">
    <tr>
        <td><% d.no %></td>
        <td><% d.start_issued_date %></td>
        <td><% d.end_issued_date %></td>
        <td><% d.totalDays %></td>
        <td><% d.schedule_number %></td>
        <td>计划中</td>
        <td>
            <label class="layui-btn layui-btn-danger layui-btn-sm issued-tr-del">删除</label>
            <label class="layui-btn layui-btn-normal layui-btn-sm issued-tr-mod">修改</label>
        </td>
    </tr>
</script>

<!-- 发布数值批量对应的模板引擎 -->
<script type="text/html" id="issued_number_table_body_templet">
    <%# var i=0; %>
    <%# layui.each(d.list, function(index, item){  %>
    <tr>
        <td><% i++ %></td>
        <td><% item.start_issued_date %></td>
        <td><% item.end_issued_date %></td>
        <td><% item.totalDays %></td>
        <td><% item.schedule_number %></td>
        <td>计划中</td>
        <td>
            <label class="layui-btn layui-btn-danger layui-btn-sm issued-tr-del">删除</label>
            <label class="layui-btn layui-btn-normal layui-btn-sm issued-tr-mod">修改</label>
        </td>
    </tr>
    <%# }); %>
</script>

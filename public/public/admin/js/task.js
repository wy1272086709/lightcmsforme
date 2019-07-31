$(function () {
    laytplConfig();
    layuiForm();
    btnEvents();
    wordSetFunc();
});

function btnEvents()
{
    var form = layui.form;
    $('#returnAdsBtn').on('click', function () {
        var taskTypeVal = $(this).attr('data-tasktype');
        var href = window.location.pathname;
        var hrefArr = href.split('/');
        var secondEleValue = hrefArr[2];
        if (secondEleValue == 'mobileTask') {
            window.location.href = '/admin/mobileTask/index?taskType=' + taskTypeVal;
        } else if (secondEleValue == 'pcTask') {
            window.location.href = '/admin/pcTask/index?taskType=' + taskTypeVal;
        }
    });
    form.on('submit(saveTask)', function (data) {
        window.form_submit = $('#saveBtn');
        form_submit.prop('disabled', true);
        var idVal = $('#id').val();
        var href = window.location.pathname;
        var hrefArr = href.split('/');
        var secondEleValue = hrefArr[2];
        var url = '/admin/pcTask/create';
        var searchStr = window.location.search;
        var taskTypeValArr = searchStr.split('taskType=');
        if (taskTypeValArr[1] != undefined) {
            data.field.task_type = taskTypeValArr[1];
        } else {
            var taskTypeVal = form_submit.data('tasktype');
            data.field.task_type = taskTypeVal;
        }
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
                    window.location.href = "/admin/"+ secondEleValue+ '/index?taskType='+ data.field.task_type
                });
            }
        });

        return false;
    });


}

function delUaTableRow()
{
    $('.layui-layer-page').on('click', '.ua-tr-del', function (e) {
        console.log($(this));
        $(this).parents('tr').remove();
    });
}

function editUaTableRow() {
    $('.layui-layer-page').on('click', '.ua-tr-mod', function (e) {
        var t = $(this).text();
        var textVal = $(this).text();
        var tr = $(this).parents('tr');
        // 设备类型
        var serviceTypeEle = tr.find('td:eq(1)');
        // 浏览器
        var browserEle = tr.find('td:eq(2)');
        // 访问比例
        var visitRatioEle = tr.find('td:eq(3)');
        if (t == '修改') {
            var selectVar1 = serviceTypeEle.text() == 'PC' ? ' selected' : '';
            var selectVar2 = serviceTypeEle.text() == '移动端' ? ' selected' : '';
            var serviceTypeOptions = "<select name='service_type' class='layui-select'>" +
                "<option value='1'" + selectVar1 + ">PC</option>" +
                "<option value='2'" + selectVar2 + ">移动端</option>" +
                "</select>";
            var browserArr = {
                1: 'IE浏览器',
                2: '火狐浏览器',
                3: 'Chrome浏览器',
                4: 'Opera浏览器',
                5: 'Safari浏览器'
            };
            // 浏览器类型
            var browserOptions = "<select name='browser_type' class='layui-select'>";
            for (var m = 1;m<=5;m++) {
                var selectedText = '';
                if (browserArr[m] == browserEle.text()) {
                    selectedText = 'selected'
                }
                browserOptions+="<option  value='"+m+"' "+selectedText+">"+browserArr[m]+"</option>";
            }
            browserOptions+="</select>";
            // 访问比例
            var visitRatioText = "<input  class='layui-input' name='visit_ratio' value='" + visitRatioEle.text() + "'>";
            serviceTypeEle.html(serviceTypeOptions);
            browserEle.html(browserOptions);
            visitRatioEle.html(visitRatioText);
            $(this).text('保存');
        } else if (t == '保存') {
            var serviceTypeEleVal = tr.find('select[name="service_type"]').find('option:selected').text();
            var visitRatioEleVal = tr.find('input[name="visit_ratio"]').val();
            var browserEleVal = tr.find('td:eq(2)').find('option:selected').text();
            console.log('broser', browserEleVal);
            serviceTypeEle.text(serviceTypeEleVal);
            browserEle.text(browserEleVal);
            visitRatioEle.text(visitRatioEleVal);
            $(this).text('修改');
        }
    });
}

function delSourceTableRow() {
    // source-tr-del
    $('.layui-layer-page').on('click', '.source-tr-del', function (e) {
        console.log($(this));
        $(this).parents('tr').remove();
    });
}

function editSourceTableRow() {
    $('.layui-layer-page').on('click', '.source-tr-mod', function (e) {
        var textVal = $(this).text();
        var tr = $(this).parents('tr');
        var sourceContentEle = tr.find('td:eq(1)');
        var visitRatioEle = tr.find('td:eq(2)');
        if ( textVal == '修改') {
            // 来源内容
            var content = sourceContentEle.html();
            // 访问比例
            var visitRatio = visitRatioEle.html();
            // 来源内容
            sourceContentEle.html("<input type='text' class='layui-text' name='source_content' value='" + content + "' />");
            // 访问比例
            visitRatioEle.html("<input type='text' class='layui-text' name='visit_ratio' value='" + visitRatio + "' />");
            $(this).text('保存');
        } else if (textVal == '保存') {
            var sourceContentVal = tr.find('input[name="source_content"]').val();
            var ratioVal = tr.find('input[name="visit_ratio"]').val();
            sourceContentEle.text(sourceContentVal);
            visitRatioEle.text(ratioVal);
            $(this).text('修改');
        }
    });
}

function layuiForm()
{
    layui.use('form', function(){
        var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
        form.render('radio');
        form.render('select');
        form.render('checkbox');
        taskNameEvent();
        taskUrlSetEvent();
        issuedSetEvent();
        statisticsUrlSetEvent();
        sourceSetEvent();
        channelSetEvent();
        timeRangeSetEvent();
        clickSetEvent();
        uaSetEvent();
        exposurePageSetEvent();
    });
}

function exposurePageSetEvent () {
    $('#exposure-page-set').on('click', function (e) {
        layer.open({
            type: 1,
            title: '曝光页面',
            btn: ['确定', '取消'],
            area: [ '500px', '150px' ],
            content: $('#exposure-page-modal').html(),
            cancel: function (index, layero) {
                layer.close(index);
                return false;
            },
            success: function(layero, index) {
                var ele = $('input[name="exposure_page_mode"]');
                var textVal = $.trim($('#exposurePage').text());
                if (textVal && textVal != '暂未设置')
                {
                    ele.val(textVal);
                }
            },
            yes: function (index, layero) {
                var sVal = $.trim($('input[name="exposure_page_mode"]').val());
                if (!sVal) {
                    layer.alert('请先填写曝光页面!');
                    return false;
                }
                $('#exposurePage').text(sVal);
                $('#exposure_page_url').val(sVal);
                layer.close(index);
            },
            btn2: function (index, layero) {

                console.log('btn2');
            }
        });
    });
}

// 任务名称设置
function taskNameEvent()
{
    $('#task_name_set').on('click', function () {
        layer.open({
            type: 1,
            title: '任务名称',
            btn: ['确定', '取消'],
            area: [ '500px', '300px' ],
            content: $('#task-name-modal').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响,
            cancel: function (index, layero) {
                layer.close(index);
                return false;
            },
            yes: function (index, layero) {
                // 点击确定了后，需要将对应的值写到隐藏域中去.
                var taskNameVal = $('#s_task_name').val();
                var isBig = $('input[name="is_big_radio"]:checked').val();
                var rMnsc = $('#r_mnsc').val();
                $('#task_name').val(taskNameVal);
                $('#is_big').val(isBig);
                $('#task_r_mnsc').val(rMnsc);
                layer.close(index);
            },
            btn2: function (index, layero) {

                console.log('btn2');
            },
            success: function () {
                var taskNameVal = $.trim($('#task_name').val());
                if (taskNameVal!='') {
                    $('#s_task_name').val(taskNameVal);
                }
                var taskIsBig = $.trim($('#is_big').val());
                if (taskIsBig!='') {
                    // 否则这样子选中
                    $('input[name="is_big_radio"][value!="'+taskIsBig+'"]').removeAttr('checked');
                    $('input[name="is_big_radio"][value="'+taskIsBig+'"]').attr('checked', 'checked');
                } else {
                    // 0 值选中
                    $('input[name="is_big_radio"][value="0"]').attr('checked', 'checked');
                }
                var taskRMnsc = $.trim($('#task_r_mnsc').val());
                if (taskRMnsc!='') {
                    $('#r_mnsc').val(taskRMnsc);
                }
                var f = layui.form;
                f.render('radio');
            }
        });
    });
}


<!-- 发布数值，添加按钮，对应的事件监听 -->
function addIssueBtnEvts(selector)
{
    var i = 0;
    $(selector).on('click', '#addScheduleBtn', function () {
        var tpl = layui.laytpl;
        var startEle = $('#schedule_start_date');
        var endEle = $('#schedule_end_date');
        var json = {
            start_issued_date: startEle.val(), // 开始日期
            end_issued_date: endEle.val(), // 结束日期
            schedule_number: $('#schedule_number').val(), // 发布数值
            no: i++,
            totalDays:countDays(endEle.val(), startEle.val())
        };
        tpl($('#issued_number_templet').html()).render(json, function (html) {
            // layui 计划任务
            $('#layui-schedule-tbody').append(html);
        });
    });
}

function editIssueBtnEvts(selector) {
    $(selector).on('click', '.issued-tr-mod', function () {
        var textVal = $(this).text();
        var tr = $(this).parents('tr');
        var startDateEle = tr.find('td:eq(1)');
        var endDateEle = tr.find('td:eq(2)');
        var planIpCountEle = tr.find('td:eq(4)');
        if ( textVal == '修改') {
            // 计划IP量
            var content = planIpCountEle.html();
            planIpCountEle.html("<input type='text' class='layui-text'  name='plan_ip_count' value='" + content + "' />");
            // 访问比例
            startDateEle.html("<input type='text' class='layui-text' id='ip_start_date' name='ip_start_date' value='" + startDateEle.text() + "' />");
            endDateEle.html("<input type='text' class='layui-text' id='ip_end_date' name='ip_end_date' value='" + endDateEle.text() + "' />");
            var laydate = layui.laydate;
            laydate.render({
                elem: '#ip_start_date', //指定元素
                type: 'date',
            });
            laydate.render({
                elem: '#ip_end_date', //指定元素
                type: 'date'
            });
            $(this).text('保存');
        } else if (textVal == '保存') {
            var planIpCountVal = tr.find('input[name="plan_ip_count"]').val();
            var startDateVal = tr.find('#ip_start_date').val();
            var endDateVal = tr.find('#ip_end_date').val();
            var countDayVal = countDays(endDateVal, startDateVal);
            planIpCountEle.text(planIpCountVal);
            startDateEle.text(startDateVal);
            endDateEle.text(endDateVal);
            // 总共多少天
            var countDaysEle = tr.find('td:eq(3)');
            countDaysEle.text(countDayVal);
            $(this).text('修改');
        }
    });
}

function delIssuedRowEvents(selector) {
    $(selector).on('click', '.issued-tr-del', function () {
        $(this).parents('tr').remove();
    });
}


// 计算日期间隔天数.
function countDays(endDate, startDate)
{
    var date1 = new Date(startDate);
    var date2 = new Date(endDate);
    return parseInt((date2 - date1)/3600/1000/24) + 1;
}

// 发布数值
function issuedSetEvent() {
    $('#issued_set_btn').on('click', function (e) {
        layer.open({
            type: 1,
            title: '发布数值',
            btn: ['确定', '取消'],
            area: [ '1100px', '300px' ],
            content: $('#issued-modal').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响,
            cancel: function (layero, index) {
                layer.close(layero);
                return false;
            },
            success: function(layero, index) {
                var f = layui.form;
                var laydate = layui.laydate;
                f.render('radio');
                laydate.render({
                    elem: '#schedule_start_date', //指定元素,
                    type: 'date',
                });
                laydate.render({
                    elem: '#schedule_end_date', //指定元素
                    type: 'date'
                });
                tabChangeRadio('layui-issued-mode-filter', 'issued_mode');
                addIssueBtnEvts('#layui-layer'+index);
                delIssuedRowEvents('#layui-layer'+index);
                editIssueBtnEvts('#layui-layer'+index);

                // 这里需要将设置的对应的发行量的值，保存起来。
                // 这里需要将对应的隐藏域里面的值，设置到模态框上.
                var style = $('#issue_style').val();
                if (style!='') {
                    var styleMap =[ 'issued_fix_mode', 'issued_plan_mode' ];
                    var element = layui.element;
                    element.tabChange('layui-issued-mode-filter', styleMap[style]);
                }
                if (style == '0') {
                    // 设置对应的数值
                    $('#layui-layer' + index).find('input[name="schedule_number"]').val($('#issued_value').val());
                } else {
                    var jsonStr  = $('#issued_json_value').val();
                    var tpl = layui.laytpl;
                    if (jsonStr) {
                        var jsonData = JSON.parse(jsonStr);
                        tpl($('#issued_number_table_body_templet').html()).render({list: jsonData}, function (html) {
                            // layui 计划任务
                            $('#layui-layer' + index).find('#layui-schedule-tbody').html(html);
                        });
                    }
                }
            },
            yes: function (index, layero) {
                // 点击确定了后,判断发行量对应的模式,然后将对应的值设置到 隐藏域，和基本的input 框中.
                var thisLiEle = $('#layui-layer'+index).find('li.layui-this');
                var style = thisLiEle.index();
                if (style =='0') {
                    var v = $.trim($('input[name="schedule_number"]').val());
                    if (v!='')
                    {
                        $('#issued_set_info').addClass('layui-hide');
                        $('#issued_value').parent().removeClass('layui-hide');
                        $('#issued_value').val(v);
                    }
                    else {
                        layer.alert('请填写任务量!');
                        return;
                    }
                }
                else {
                    // 获取表格里面的数据
                    var data = getTableBodyData('layui-schedule-tbody', ['no', 'start_issued_date', 'end_issued_date', 'totalDays', 'schedule_number']);
                    if (data.length==0) {
                        layer.alert('请添加一段时间内的计划IP量!');
                        return;
                    }
                    $('#issued_value').parent().addClass('layui-hide');
                    $('#issued_set_info').removeClass('layui-hide').text('已设置时间段内的计划IP量');
                    $('#issued_json_value').val(JSON.stringify(data));
                }
                $('#issue_style').val(style);
                layer.close(index);
            },
            btn2: function (layero, index) {

            }
        });
    })
}

// 来源页面
function sourceSetEvent() {
    var tpl = layui.laytpl;
    $('#source-btn').on('click', function (e) {
        layer.open({
            type: 1,
            title: '来源页面',
            btn: ['确定', '取消'],
            area: ['auto', '400px'],
            content: $('#source-url-modal').html(),
            cancel: function (index, layero) {

            },
            // 需要更新来源页面对应的文本到页面上
            // 需要保存对应的来源信息，以及勾选的来源类型到隐藏域中.
            yes: function (index, layero) {
                // 需要校验信息是否填写完整。
                var thisLiEle = $('#layui-layer'+index).find('li.layui-this');
                var style = thisLiEle.index();
                if (style == '0') {
                    var sourceBodyVal = $.trim($('#source-layui-body').html());
                    if (!sourceBodyVal)  {
                        layer.alert('请逐条设置来源后再提交');
                        return;
                    }
                    // 来源信息设置
                    var sourceData = getTableBodyData('source-layui-body', ['no', 'source_content', 'visit_ratio']);
                    // 将source_type 转变为整形, no 字段去掉
                    var sourceNewData = [];
                    var len = sourceData.length;
                    var sum = 0;
                    for (var f = 0;f<len;f++)
                    {
                        var row = sourceData[f];
                        var ratioVal = row.visit_ratio;
                        sum+=parseInt(ratioVal.substr(0, ratioVal.length - 1));
                        sourceNewData.push(row);
                    }
                    if (sum!=100) {
                        layer.alert('来源比率之和需要为100%');
                        return;
                    }
                    $('#source_json').val(JSON.stringify(sourceNewData));
                }else if (style == '1') {
                    var batchSourceVal = $.trim($('#batch_source_url').val());
                    if (!batchSourceVal) {
                        layer.alert('批量来源未填写');
                        return;
                    }
                    $('#source_json').val(batchSourceVal);
                }else if (style == '2' || style == '3') {
                    // 这里要验证文件是否有上传.
                    if (style == '2') {
                        var v = $.trim($('#label_file_name').text());
                    } else {
                        var v = $.trim($('#label_ratio_file_name').text());
                    }
                    if (!v) {
                        layer.alert('请上传来源文件!');
                        return;
                    }
                    $('#source_json').val(v);
                }
                $('#sourceSetText').text('已设置来源页面信息');
                // 来源方式设置source_style
                $('#source_style').val(style);
                layer.close(index);
            },
            btn2: function (index, layero) {

            },
            success: function (layero, index) {
                var form = layui.form;
                form.render('radio');
                addSourceRowEvt();
                editSourceTableRow();
                delSourceTableRow();
                uploadSource();
                uploadSourceRatio();
                tabChangeRadio('source_set_filter', 'source-url-mode');
                // 这里需要将来源页面对应的隐藏域里面的数据，赋值到表单上。
                // 同时还要切换到指定的tab 页上.
                var styleVal = $('#source_style').val();
                var sourceStyleMap = [
                    'custom_set_source',
                    'batch_set_source',
                    'source_export',
                    'source_ratio_export'
                ];
                if (styleVal != '') {
                    var element = layui.element;
                    element.tabChange('source_set_filter', sourceStyleMap[styleVal]);
                }
                // 来源方式
                if (styleVal == '0') {
                    var bodyData = $.trim($('#source_json').val());
                    if (bodyData) {
                        var d = JSON.parse(bodyData);
                        tpl($('#source_url_body_data_templet').html()).render({list: d}, function (html) {
                            $('#source-layui-body').html(html);
                        });
                    }
                } else if (styleVal == '1') {
                    var v = $('#source_json').val();
                    $('#batch_source_url').val(v);
                } else if (styleVal == '2') {
                    // 来源值
                    var sourceVal = $.trim($('#source_json').val());
                    if (sourceVal) {
                        $('#label_file_name').text(sourceVal);
                    }
                } else if (styleVal == '3') {
                    // 来源值设置
                    // 来源值
                    var sourceVal = $.trim($('#source_json').val());
                    if (sourceVal) {
                        $('#label_ratio_file_name').text(sourceVal);
                    }
                }
            }
        });
    });
}

function getSourceTypeVal(text) {
    var type = 0;
    if (text == '自动随机来源') {
        type = 1;
    } else if (text == '自定义网址') {
        type = 2;
    } else if (text == '淘宝商品搜索词') {
        type = 3;
    }
    return type;
}

// 点击设置
function clickSetEvent() {
    $('#click-set').on('click', function (e) {
        layer.open({
            type: 1,
            title: '点击设置',
            btn: ['确定', '取消'],
            area: ['auto', '400px'],
            content: $('#clickSetModal').html(),
            cancel: function (index, layero) {

            },
            success: function(index, layero) {
                var form = layui.form;
                form.render('select');
                dirAboutEvents('#layui-layer'+layero);
                delDirEvents('#layui-layer'+layero);
                var jsonData = $('#click_set_data_info').val();
                // 将对应的json 数据，赋值到模态框中
                if (jsonData!='') {
                    var data = JSON.parse(jsonData);
                    var len = data.length;
                    $('.interval-time').eq(0).val(data[0].time_interval);
                    $('.click-area').eq(0).val(data[0].click_area);
                    $('.click-odds').eq(0).val(data[0].odds);
                    $('.tag').eq(0).val(data[0].type);

                    for (var m =1;m<len;m++) {
                        data[m].prefix = str_repeat(data[m].cid %10);
                        data[m].dir_level = parseInt(data[m].cid) %10+1;
                        data[m].pid = parseInt(data[m].cid /10);
                    }
                    var newData = data.slice(1);
                    var tpl = layui.laytpl;
                    tpl($('#clickSetModalData').html()).render({ list:newData }, function (html) {
                        $('#clickSetDiv').find('.layui-form').append(html);
                        var eles = $('.tag').slice(1);
                        eles.each(function (index, item) {
                           $(item).find('option[value="'+newData[index]['type']+'"]').attr('selected', 'selected');
                        });
                        layui.form.render('select');
                    });
                }
            },
            yes: function (index, layero) {
                var data = getClickSetData();
                var clickData = JSON.stringify(data);
                // 将对应的数据设置到隐藏域上.
                $('#click_set_data_info').val(clickData);
                $('#click-set-span-info').text('点击设置已设置!');
                layer.close(index);
            },
            btn2: function (index, layero) {

            }

        });
    })
}

function str_repeat(n, char='&nbsp;&nbsp;')
{
    var arr = [];
    for (var m = 0;m<n;m++) {
        arr.push(char);
    }
    return arr.join("");
}


function delDirEvents(layerSelector)
{
    $(layerSelector).on('click', '.source-dir-del', function () {
        $(this).parents('.clickSetItem').remove();
    });
}

// 点击设置中，添加同级目录和下级目录的时候，触发的事件
function dirAboutEvents(layerSelector) {

    // 添加子目录
    $(layerSelector).on('click', '.addSubDir', function () {
        //子目录是
        var divEle = $(this).parents('.clickSetItem');
        var parentEle = divEle.parent();
        var levelValStr = divEle.attr('class');
        console.log('class', levelValStr);
        var levelValArr = levelValStr.split('level');
        var levelVal = levelValArr[levelValArr.length-1];

        // 当前元素的level 值加1,
        // 父元素的index值

        var no = divEle.find('.clickSetNo').text();

        var lastChild = parentEle.find('div[data-pid="'+no+'"]:last');
        console.log('lastChild', lastChild);
        if (lastChild.length>0)
        {
            var newNo =  parseInt(lastChild.find('.clickSetNo').text()) +1;
        }
        else
        {
            var newNo =  parseInt(no) * 10+1;
        }
        // 需要判断当前元素是否存在子元素，如果存在，则获取子元素里面的最大的那个数字

        // 获取当前元素的prefix 值.
        var prefix = divEle.find('.clickPrefix').text();

        // dir_level 如何对这个值进行处理，然后判断呢?
        // level_
        var newPrefix = prefix+ "&nbsp;&nbsp;";
        var tpl = layui.laytpl;

        tpl($('#click_add_dir_templet').html()).render({
            no: newNo,// 这个值为当前元素的子元素的最后一个元素的序号值+1
            prefix: newPrefix, // 这个值，需要在缩进上再加1
            dir_level: parseInt(levelVal)+1, // 这个值，需要在当前level 上加1
            pid: no // 这个值，为当前元素对应的序号值.
        },function(html){
            console.log('children:', html);
            if (lastChild.length>0) {
                lastChild.after(html);
            } else {
                divEle.after(html);
            }
            var f = layui.form;
            f.render('select');
        });
    });


    // 同级目录
    $(layerSelector).on('click', '.addSiblingDir', function () {
        // 同级目录,最后一个元素，对应的文本
        var divEle = $(this).parents('.clickSetItem');
        var levelValStr = divEle.attr('class');
        console.log('class tongji:', levelValStr);
        var levelValArr = levelValStr.split('level');
        var levelVal = levelValArr[levelValArr.length-1];
        var parentEle = divEle.parent();
        //
        var no = parentEle.find('.level'+levelVal +':last').text();
        var newNo = no ? parseInt(no)+1:1;

        var pidVal = divEle.attr('data-pid');
        // 获取当前元素的prefix 值.
        var prefix = divEle.find('.clickPrefix').text();
        var tpl = layui.laytpl;
        tpl($('#click_add_dir_templet').html()).render({
            no: newNo,
            prefix: prefix, // 和当前的元素的属性一致
            dir_level: levelVal, // 和当前的元素的属性一致
            pid: pidVal // 和当前的元素的属性一致
        },function(html){
            console.log('tongji:', html);
            parentEle.append(html);
            var f = layui.form;
            f.render('select');
        });
    });
}

function getPlatForm(platFormName) {
    if (platFormName == 'PC')
    {
        return 1;
    }
    else if (platFormName == '移动端')
    {
        return 2;
    }
}

function getBrowserTypeText(val) {
    var json = {
        1:'IE浏览器',
        2:'火狐浏览器',
        3:'Chrome浏览器',
        4:'Opera浏览器',
        5:'Safari浏览器'
    };
    return json[val];
}

function getPlatFormText(val) {
    var json = {
        1:'PC',
        2:'移动端',
    };
    return json[val];
}

function getBrowser(browserText) {
    var v = '';
    switch (browserText) {
        case 'IE浏览器':
            v = 1;
            break;
        case '火狐浏览器':
            v = 2;
            break;
        case 'Chrome浏览器':
            v = 3;
            break;
        case 'Opera浏览器':
            v = 4;
            break;
        case 'Safari浏览器':
            v = 5;
            break;
    }
    return v;
}

// ua 设置
function uaSetEvent() {
    $('#ua-set-btn').on('click', function (e) {
        layer.open({
            type: 1,
            title: 'ua设置',
            btn: ['确定', '取消'],
            area: ['1000px', '400px'],
            content: $('#ua-set-modal').html(),
            success: function(layero, index) {
                var form = layui.form;
                form.render('select');
                form.render('checkbox');
                form.render('radio');
                tabChangeRadio('ua-set-filter', 'ua_mode');
                uaFunc();
                delUaTableRow();
                uploadUa();
                editUaTableRow();
                var uaStyle = $('#is_ua').val();
                var ele = layui.element;
                if (uaStyle!='') {
                    var uaStyleMap = [
                        '',
                        'auto_ua',
                        'custom_ua',
                        'batch_export_ua',
                        'upload_file_ua'
                    ];
                    ele.tabChange('ua-set-filter', uaStyleMap[uaStyle]);
                }
                var json = $.trim($('#ua-set-json').val());
                if (uaStyle == 2) {
                    var tpl = layui.laytpl;
                    if (json!='') {
                        var jsonData = JSON.parse(json);
                        for (var m=0;m<jsonData.length;m++)
                        {
                            jsonData[m].browser = getBrowserTypeText(jsonData[m].browser);
                            jsonData[m].device_type = getPlatFormText(jsonData[m].device_type);
                        }
                    } else {
                        var jsonData = {};
                    }
                    tpl($('#multi_ua_set').html()).render({list: jsonData}, function(html){
                        $('#layui-ua-body').html(html);
                    });
                } else if (uaStyle == 3) {
                    $('#multi_ua_textarea').val(json);
                } else if (uaStyle == 4) {
                    // ua style 为3，将对应的label 赋值
                    var uaVal = $.trim($('#ua-set-json').val());
                    if (uaVal) {
                        $('#label_file_name').text(uaVal);
                    }
                }
            },
            cancel: function (index, layero) {

            },
            yes: function (index, layero) {
                // 1. 需要获取对应的设置ua的方式.
                // 2. 然后将对应的值设置到隐藏域中
                var thisLiEle = $('#layui-layer'+index).find('li.layui-this');
                var uaStyle = parseInt(thisLiEle.index()) +1;
                // 设置样式
                $('#is_ua').val(uaStyle);
                var json = getTableBodyData('layui-ua-body', ['no', 'device_type', 'browser', 'visit_ratio']);
                var sum = 0;
                for(var f = 0;f<json.length;f++)
                {
                    json[f].browser = getBrowser(json[f].browser);
                    json[f].device_type = getPlatForm(json[f].device_type);
                    var v = $.trim(json[f].visit_ratio);
                    sum+=parseInt(v.substr(0, v.length -1));
                }
                // ua-set-json,设置ua 信息json.
                if (uaStyle == 2) {
                    if (json.length == 0) {
                        layer.alert('请添加自定义ua后再保存!');
                        return;
                    } else if (sum!=100){
                        layer.alert('自定义UA比率之和必须为100!');
                        return;
                    } else {
                        $('#ua-set-json').val(JSON.stringify(json));
                    }
                } else if (uaStyle == 3) {
                    var uaStr = $.trim($('#multi_ua_textarea').val());
                    if (!uaStr) {
                        layer.alert('请在文本框中填入批量的UA标识!');
                        return false;
                    }
                    $('#ua-set-json').val(uaStr);
                } else if (uaStyle == 4) {
                    // 如果上传文件,这里需要将对应的上
                    // 文件上传的ua 设置
                    var v = $.trim($('#label_file_name').text());
                    if (!v) {
                        layer.alert('请上传UA文件!');
                        return;
                    }
                    $('#ua-set-json').val(v);
                }
                $('#ua-set-span-info').text('ua已经设置');
                layer.close(index);
            },
            btn2: function (index, layero) {

            }

        });
    })
}

// 渠道设置
function channelSetEvent() {
    $('#dq-qd-set-btn').on('click', function (index, layero) {
        layer.open({
            type: 2,
            title: '地区/渠道设置',
            btn: ['确定', '取消'],
            area: ['1200px', '600px'],
            content: '/admin/pcTask/area',
            cancel: function (index, layero) {

            },
            yes: function (index, layero) {
                // 获取对应的选中的checkbox 的值
                var checkedEles = layer.getChildFrame('input[type="checkbox"]:checked', index);
                // 这里获取对应的style 值.根据导航高亮的style 属性.
                var styleVal = layer.getChildFrame('li.layui-this', index).index();
                var pc_num_show_val = 0;
                var area_channel_info = [];
                var datesVal = $.trim(layer.getChildFrame('#dates', index).val());
                var ratioVal = $.trim(layer.getChildFrame('#ratio', index).val());

                checkedEles.each(function (index, item) {
                    var v = parseInt($(this).val());
                    var liEle = $(this).parents('li');
                    console.log('liEle', liEle);
                    // 父标签没有隐藏
                    if (!liEle.hasClass('layui-hide')) {
                        pc_num_show_val += v;
                        var nameStr = $(this).attr('name');
                        var json = getAreaJson(nameStr, styleVal, v);
                        if (datesVal) {
                            json.dates = datesVal;
                        }
                        // 1 表示渠道
                        if (ratioVal && styleVal == '1') {
                            json.ratio = ratioVal;
                        }
                        area_channel_info.push(json);
                    }
                });
                $('#aq_type').val(styleVal);
                $('#area_channel_info').val(JSON.stringify(area_channel_info));
                if (styleVal == '0') {
                    $('#pc_num_show').val(pc_num_show_val);
                } else {
                    $('#pc_num_show').val(pc_num_show_val);
                }
                $('#areaChannelInfo').text('地区/渠道信息已设置');
                layer.closeAll('iframe');
            },
            btn2: function (index, layero) {

            },
            success: function (layero, index) {
                // 这里根据隐藏域中对应的值,将checkbox选中,将对应的导航高亮.
                var showStyleVal = $('#aq_type').val();
                // 所有导航先移除高亮的部分
                var element = document.getElementById('layui-layer-iframe'+index).contentWindow.layui.element;
                var tabArr = [ 'area_tab_li', 'channel_tab_li' ];
                element.tabChange('docDemoTabBrief', tabArr[showStyleVal]);
                var val = '';
                if (showStyleVal == '0') {
                    // 0 表示地区, 1表示渠道
                    var val = $('#area_channel_info').val();
                } else if (showStyleVal == '1') {
                    var val = $('#area_channel_info').val();
                }
                if (val) {
                    var areaChannelArr = JSON.parse(val);
                    var len = areaChannelArr.length;
                    var selectorArr = [];
                    for (var j=0;j<len;j++) {
                        // 表示地区
                        if (showStyleVal == '0') {
                            var cityIdVal = areaChannelArr[j].city_id;
                            var provinceIdVal = areaChannelArr[j].province_id;
                            if (cityIdVal == '0') {
                                var selector = 'input[name="area_group_ids[' + provinceIdVal + '"]';
                            } else {
                                var selector = 'input[name="area_group_ids[' + provinceIdVal + '][' + cityIdVal + '"]';
                            }
                            selectorArr.push(selector);
                        } else if (showStyleVal == '1') {
                            var gIdVal = areaChannelArr[j].g_id;
                            var uIdVal = areaChannelArr[j].u_id;
                            if (uIdVal == '0') {
                                var selector = 'input[name="channel_group_ids[' + gIdVal + ']"]';
                            } else {
                                var selector = 'input[name="channel_group_ids[' + gIdVal + '][' + uIdVal + ']"]';
                            }
                            selectorArr.push(selector);
                        }
                    }
                    if (selectorArr.length>0) {
                        var selectorStr = selectorArr.join(',');
                        layer.getChildFrame(selectorStr, index).prop('checked', true);
                    }
                }
                var f = document.getElementById('layui-layer-iframe'+index).contentWindow.layui.form;
                f.render();
            }
        });
    })
}


function getAreaJson(nameStr, index, v, layerIndex) {
    var preg = /\[(.*?)\]/g;
    var nameArr = nameStr.match(preg);
    var json = {};

    if (index == '0') {
        var provinceIdVal = nameArr[0].substr(1,  nameArr[0].length - 2 );
        json = {
            province_id: provinceIdVal,
            num_ip: v,
            city_id: 0,
        };
        if (nameArr[1]!=undefined) {
            var cityIdVal = nameArr[1].substr(1, nameArr[1].length - 2);
            json.city_id = cityIdVal;
        }
        return json;
    } else if (index == '1') {
        var gIdVal = nameArr[0].substr(1,  nameArr[0].length - 2 );
        json = {
            g_id: gIdVal,
            u_id: 0,
        };
        if (nameArr[1]!=undefined) {
            var uIdVal = nameArr[1].substr(1, nameArr[1].length - 2);
            json.u_id = uIdVal;
        }
        return json;
    }
}

/**
 * 对时段分配对应的数据，进行校验
 * @param layero
 * @returns {boolean}
 */
function validateDateZones(layero) {
    // 需要做校验,判断
    var index = $(layero).find('li.layui-this').index();
    var m = /\d{1,}/;
    if (index == '1') {
        var zonesEle = $('.date-zones');
        zonesEle.each(function() {
            var v = $(this).val();
            if (v && !m.test(v)) {
                layer.alert("时段分配中只能输入数字!");
                return false;
            }
        });
    }
    return index;
}

// 时段设置
function timeRangeSetEvent() {
    $('#time-range-set').on('click', function (index, layero) {
        layer.open({
            type: 1,
            title: '时段设置',
            btn: ['确定', '取消'],
            area: ['800px', '300px'],
            content: $('#time-range-modal').html(),
            cancel: function (index, layero) {

            },
            yes: function (index, layero) {
                var f = validateDateZones(layero);
                if (f === false) {
                  return false;
                }
                // 时段设置的方式
                $('#choice_type').val(f);
                if (f == '1') {
                    var dateZones = {};
                    $('.date-zones').each(function () {
                        var v = $(this).val();
                        var nameStr = $(this).attr('name');
                        var nameArr = nameStr.split(/[\[\]]/);
                        if (v) {
                            dateZones[nameArr[1]] = v;
                        }
                    });
                    $('#time_zone').val(JSON.stringify(dateZones));
                } else if (f == '0') {
                    var qxTypeVal = $.trim($(layero).find('input[name="qx_type_ele"]:checked').val());
                    $('#qx_type_val').val(qxTypeVal);
                }
                $('#timeRange').html('已设置时段');
                layer.close(index);
            },
            btn2: function (index, layero) {

            },
            success: function (layero, index) {
                tabChangeRadio('time_range_set_filter', 'time_range_mode');
                var form = layui.form;
                var styleVal = $('#choice_type').val();
                var zoneVal  = $.trim($('#time_zone').val());
                if (styleVal!='') {
                    // 点亮导航, 动态切换tab
                    var element = layui.element;
                    var map = [
                        'quxian_li',
                        'time_range_li'
                    ];
                    element.tabChange('time_range_set_filter', map[styleVal]);
                }
                if (styleVal == '0') {
                    // $('#curve').val(zoneVal);
                    // 默认将PC 曲线选中, 首先读取qx_type_val 元素对应的隐藏域的值
                    var v = $.trim($('#qx_type_val').val());
                    var ele = $('input[name="qx_type_ele"][value="'+v+'"]');
                    ele.attr('checked', 'checked');
                    $('input[name="qx_type_ele"][value!="'+v+'"]').removeAttr('checked');
                } else if (styleVal == '1') {
                    if (zoneVal) {
                        var zoneObj = JSON.parse(zoneVal);
                        console.log(typeof zoneObj, 'zoneObj:', zoneObj);
                        var keys = Object.keys(zoneObj);
                        for (var j = 0; j < keys.length; j++) {
                            var ele = $('input[name="dateZones[' + keys[j] + ']"');
                            ele.val(zoneObj[keys[j]]);
                        }
                    }
                }
                form.render();
            }
        });
    });
}

/**
 * 获取表格body体里面的内容
 * @param id
 * @param fields 字段名称数组
 */
function getTableBodyData(id, fields = []) {
    var htmlStr = $('#'+id).html();
    var res = htmlStr.split(/<tr>(.*?)<\/tr>/);
    var jsonData = [];
    var trRes = filterEmptyData(res);
    var len = trRes.length;
    for (var j = 0;j<len;j++)
    {
        var row = {};
        for (var m=0;m<fields.length;m++)
        {
            row[fields[m]] = '';
        }
        var tr = trRes[j];
        var tdArr = tr.split(/<td>(.*?)<\/td>/);
        var tdNewArr = filterEmptyData(tdArr);
        for (var h=0;h<tdNewArr.length;h++)
        {
            if (!fields[h]) {
                continue;
            }
            row[fields[h]] = tdNewArr[h];
        }
        jsonData.push(row);
    }
    return jsonData;
}

function filterEmptyData(res) {
    var trRes = res.filter(function (value) {
        var v = $.trim(value);
        if (v!='')
        {
            return true;
        }
    });
    return trRes;
}

// 统计链接设置
function statisticsUrlSetEvent() {
    $('#statistics-btn').on('click', function (e) {
        layer.open({
            type: 1,
            title: '统计链接',
            btn: ['确定', '取消'],
            area: ['600px', '300px'],
            content: $('#statistics-modal').html(),
            success: function(layero, index) {
                var codeVal = $('input[name="statistics_code"]').val();
                var linkVal = $('input[name="statistics_link"]').val();
                // 将填写的统计链接和统计代码，在模态框内容框中显示出来.
                $('#layui-layer'+index).find('#statistics_code').val(codeVal);
                $('#layui-layer'+index).find('#statistics_link').val(linkVal);
            },
            cancel: function (layero, index) {

            },yes: function (index, layero) {
                var codeVal = $.trim($('#statistics_code').val());
                var linkVal = $.trim($('#statistics_link').val());
                if (codeVal && linkVal) {
                    $('input[name="statistics_code"]').val(codeVal);
                    $('input[name="statistics_link"]').val(linkVal);
                    $('#statistics_info').text('已填写统计代码和统计链接');
                }
                else {
                    if (codeVal) {
                        $('input[name="statistics_code"]').val(codeVal);
                        $('#statistics_info').text('已填写统计代码');
                    }
                    else if (linkVal) {
                        $('input[name="statistics_link"]').val(linkVal);
                        $('#statistics_info').text('已填写统计链接');
                    }
                }
                layer.close(index);
            }, btn2: function (layero, index) {

            }
        })
    });
}

// 任务页面
function taskUrlSetEvent()
{
    var f = $('#task_url_set').on('click', function (e) {
        layer.open({
            type: 1,
            title: '任务页面',
            btn: ['确定', '取消'],
            area: [ 'auto', 'auto' ],
            content: $('#task-url-modal').html(), //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响,
            cancel: function (index, layero) {
                layer.close(index);
                return false;
            },
            yes: function (index, layero) {
                var taskUrlStyle = $(layero).find('.layui-this').index();
                if (taskUrlStyle == '0') {
                    var tsVal = $.trim($('#textarea_t_s').val());
                    if (tsVal!='') {
                        $('#t_s').val(tsVal);
                        $('#task_url').addClass('layui-hide');
                        $('#task_url_set_info').removeClass('layui-hide').text('已设置任务页面');
                    } else {
                        layer.alert('请先设置任务页面');
                        return false;
                    }
                } else {
                    var v = $.trim($('#label_file_name').text());
                    if (!v) {
                       layer.alert('请先导入文件!');
                       return false;
                    } else {
                        $('#t_s').val(v);
                        $('#task_url').addClass('layui-hide');
                        $('#task_url_set_info').removeClass('layui-hide').text('已设置任务页面');
                    }
                }
                $('#task_url_style').val(taskUrlStyle);
                layer.close(index);
            },
            btn2: function (index, layero) {
                console.log('btn2');
            },
            success: function (index, layero) {
                tabChangeRadio('task_url_set_filter', 'task_url_input');
                var tsVal = $('#t_s').val();
                $('#textarea_t_s').val(tsVal);
                layui.form.render();
                uploadTaskUrl();
                var styleVal = $('#task_url_style').val();
                // 0 表示
                var filterArr = [
                  'task_url_li_0', 'task_url_li_1'
                ];
                var e = layui.element;
                e.tabChange('task_url_set_filter', filterArr[styleVal]);
                if (styleVal == '0') {
                    $('#textarea_t_s').text(tsVal);
                } else if (styleVal == '1') {
                    $('#label_file_name').text(tsVal);
                }
            }
        });
    });
}

function uaFunc()
{
    var table = layui.table;
    var i = 0;
    $('.layui-layer-page').on('click', '#addUaBtn', function () {
        // 访问比例
        var visitRatio = $('#ua_visit_radio').val();
        // 浏览器
        var browserTextEle = $('#ua-browser').find('option:selected');
        var browser = browserTextEle.text();
        // 设备类型
        var deviceTypeEle = $('#ua-isPc').find('option:selected')
        var deviceType = deviceTypeEle.text();
        var data = {
            no: i++,
            device_type: deviceType,
            browser: browser,
            visit_ratio: visitRatio+'%'
        };
        var tpl = layui.laytpl;
        var uaRowHtml = $('#ua_tr').html();
        tpl(uaRowHtml).render(data, function(html){
            var table = layui.table;
            $("#layui-ua-body").append(html);
        });
    });
}

function initTable(data) {
    var table = layui.table;
    table.render({
        elem:'#ua_table',
        data: data?data:[],
        url: '',
        toolbar: false,
        even: true,
        cols: [
            [
                {type:'checkbox', width: 100, title:'序号'},
                {field:'device_type', title:'设备类型'},
                {field:'browser', title: '浏览器类型'},
                {field:'visit_radio', title: '访问比例'},
            ]
        ]
    });
}

function addSourceRowEvt() {
    var i = 0;
    //添加事件监听
    $('.layui-layer-page').on('click', '#source_btn_add', function () {
        var url = $('#source_site_url').val();
        var ratio = $('#source_ratio').val();
        var json = {
            no: i++,
            source_content: url,
            visit_ratio: ratio+'%'
        };
        var tpl = layui.laytpl;
        tpl($('#source_url_tr').html()).render(json, function (html) {
            $('#source-layui-body').append(html);
        });
    });
}

function laytplConfig()
{
    var tpl = layui.laytpl;
    tpl.config({
        open: '<%',
        close: '%>'
    });
}

// 上传ua 头
function uploadUa()
{
    var upload = layui.upload;
    //执行实例
    var uploadInst = upload.render({
        elem: '#uploadUa',
        accept: "file",
        field: 'file1',
        exts: 'xls|xlsx',
        url: "/admin/upload?type=2",
        done: function(res) {
            if (res.code == 0) {
                //上传完毕回调
                $('#label_file_name').text(res.filepath);
            }
        },
        error: function() {
            //请求异常回调
        }
    });
}

function getClickSetData() {
    var data = [];
    $('.clickSetItem').each(function (index, item) {
        var timeVal = $(this).find('.interval-time').val();
        var clickAreaVal = $(this).find('.click-area').val();
        var clickOddsVal = $(this).find('.click-odds').val();
        var tagVal = $(this).find('.tag').val();
        var no = $(this).find('.clickSetNo').text();
        var parentIdVal = 0;
        if (no.length == 1) {
            parentIdVal = 0;
        } else {
            parentIdVal = no.substr(0, no.length - 1);
        }
        var json = {
            time_interval: timeVal,
            type: tagVal,
            cid: no,
            click_area: clickAreaVal,
            odds: clickOddsVal,
            parent_id: parentIdVal
        };
        data.push(json);

    });
    return data;
}

function clickSetValidate() {

    $('.interval-time').each(function () {
        var intervalTimeVal = $.trim($(this).val());
        var preg1 = /(\d{1,})|({\d{1,}\,\d{1,}})/;
        if(!preg1.test(intervalTimeVal)) {
            layer.msg('点击间隔时间需要按照格式填写!');
            return;
        }
    });

    $('.click-area').each(function () {
        var clickAreaVal = $.trim($(this).val());
    });

    $('.click-odds').each(function () {
        var clickOddsVal = $.trim($(this).val());

    });
}

// 上传来源
function uploadSource()
{
    var upload = layui.upload;
    //执行实例
    var uploadInst = upload.render({
        elem: '#uploadSource',
        accept: "file",
        field: 'file1',
        exts: 'xls|xlsx',
        url: "/admin/upload?type=3",
        done: function(res){
            //上传完毕回调
            $('#label_file_name').text(res.filepath);
            console.log('upload success!')
        },
        error: function(){
            //请求异常回调
        }
    });
}

/**
 * 上传任务页面.
 */
function uploadTaskUrl()
{
    var upload = layui.upload;
    upload.render({
        elem: '#uploadUrlFile',
        accept: "file",
        field: 'file1',
        exts: 'xls|xlsx',
        url: "/admin/upload?type=4",
        done: function(res) {
            //上传完毕回调
            $('#label_file_name').text(res.filepath);
            console.log('upload success!')
        },
        error: function(){
            //请求异常回调
        }
    });
}

/**
 * 词库设置，对应的JS文件上传代码
 */
function uploadWordSet() {
    var upload = layui.upload;
    upload.render({
        elem: '#uploadWord',
        accept: "file",
        field: 'file1',
        exts: 'xls|xlsx',
        url: "/admin/upload?type=5",
        done: function(res) {
            //上传完毕回调
            $('#label_file_name').text(res.filepath);
            console.log('upload success!')
        },
        error: function(){
            //请求异常回调
        }
    });
}

/**
 * 来源设置，包含比率,对应的JS文件上传代码
 */
function uploadSourceRatio() {
    var upload = layui.upload;
    upload.render({
        elem: '#uploadSourceRatio',
        accept: "file",
        field: 'file1',
        exts: 'xls|xlsx',
        url: "/admin/upload?type=3",
        done: function(res) {
            //上传完毕回调
            $('#label_ratio_file_name').text(res.filepath);
            console.log('upload success!')
        },
        error: function(){
            //请求异常回调
        }
    });
}

/**
 * 词库设置
 */
function wordSetFunc() {
    $('#word-set-btn').on('click', function () {
        layer.open({
            type: 1,
            title: '词库设置',
            btn: ['确定', '取消'],
            area: [ 'auto', 'auto' ],
            content: $('#word-set-modal').html(),
            cancel: function (index, layero) {

            },
            success: function (layero, index) {
                uploadWordSet();
                // 从隐藏域中获取对应的设置方式
                var styleVal = $('#word_set_style').val();
                layui.form.render();
                // 根据词库设置的方式，将对应的tab 选项卡高亮
                var filterArr = [
                  'batch_ua_import', 'custom_word_set', 'out_link_set'
                ];
                var e = layui.element;
                if (styleVal!='') {
                    e.tabChange('word-set-filter', filterArr[styleVal]);
                }
                if (styleVal == 0) {
                    // 将导入的文本框里面的内容
                    $('#word-set-textarea').val($('#word_set_info').val());
                } else {
                    // 如果文件上传的.
                    var s = $('#word_set_info').val();
                    $('#label_file_name').text(s);
                }
            },
            yes: function (index, layero) {
                // 点击确定了后，要保存对应的词库设置的方式.
                // 以及对应的信息到隐藏域中.
                var style = $(layero).find('.layui-this').index();
                $('#word_set_style').val(style);
                if (style == '0') {
                    // 0 值表示将文本框的值，设置到隐藏域中.
                    var s = $('#word-set-textarea').val();
                    $('#word_set_info').val(s);
                } else if (style == '1') {
                    var fileName = $('#label_file_name').text();
                    $('#word_set_info').val(fileName);
                }
                $('#word-set-span-info').text('已经设置词库');
                layer.close(index);
            }
        });
    });
}

function tabChangeRadio(filter, nameSelector) {
    var element = layui.element;
    element.on('tab('+filter+')', function(data){
        var index = data.index; //得到当前Tab的所在下标
        var radioEle = $('input[name="'+nameSelector+'"][value="'+index+'"]');
        radioEle.prop('checked', true);
        $('input[name="'+nameSelector+'"][value!="'+index+'"]').prop('checked', false);
        layui.form.render('radio');
    });
}

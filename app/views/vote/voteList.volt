<style type="text/css" rel="stylesheet">
    .hidden-none {
        display: none;
    }
    .relative {
        position: relative;
    }
    div.datagrid * {
        vertical-align: middle;
    }
    input[type=text].validatebox-invalid {
        border-color: #ffa8a8;
        background-color: #fff3f3;
        color: #000;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">投票</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="vote_grid"></div>
            </div>
            <div id="vote_cu_window" style="padding:5px;">
                <form id="vote_cu_form" action="">
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">名称</span>
                            <input name="id" type="hidden"/>
                            <input class="input-medium" type="text" name="name"/>
                        </div>
                        <div class="span6">
                            <span class="label">相关链接</span>
                            <input class="input-medium" type="text" name="url"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span4">
                            <span class="label">自动生成链接</span>
                            <select class="input-mini" name="auto_url">
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                        <div class="span4">
                            <span class="label">投票类型</span>
                            <select class="input-mini" name="type">
                                <option value="1">单选</option>
                                <option value="2">多选</option>
                            </select>
                        </div>
                        <div class="span4" style="display: none;" id="vote_max_option">
                            <span class="label">最多可选</span>
                            <input class="input-mini" type="text"  name="max_option"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">开始时间</span>
                            <input class="input-medium" type="text" name="start_date"/>
                        </div>
                        <div class="span6">
                            <span class="label">结束时间</span>
                            <input class="input-medium" type="text" name="end_date"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span4">
                            <span class="label">状态</span>
                            <select class="input-small" name="state">
                                <option value="1">未启动</option>
                                <option value="2">进行中</option>
                                <option value="3">已过期</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <fieldset class="well well-small">
                            <legend>投票选项</legend>
                            <div id="vote_option_container" class="row-fluid">
                                <div class="row-fluid vote-option">
                                    <div class="span7">
                                        <span class="badge badge-info">1</span>
                                        <span class="label">选项</span>
                                        <input name="oid" type="hidden"/>
                                        <input name="vname" class="input-medium" type="text"/>
                                    </div>
                                    <div class="span5">
                                        <span class="label">简称</span>
                                        <input name="short_name" class="input-small" type="text"/>
                                    </div>
                                </div>
                                <div class="row-fluid vote-option">
                                    <div class="span7">
                                        <span class="badge badge-info">2</span>
                                        <span class="label">选项</span>
                                        <input name="oid" type="hidden"/>
                                        <input name="vname" class="input-medium" type="text"/>
                                    </div>
                                    <div class="span5">
                                        <span class="label">简称</span>
                                        <input name="short_name" class="input-small" type="text"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row-fluid">
                                <button class="btn span3" id="vote_add_option_btn">添加选项</button>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
            <div id="vote_detail_window">
                <table id="vote_detail_table" class="table">
                    <tr>
                        <th>标题</th>
                        <td data-field="name"></td>
                        <th>类型</th>
                        <td data-field="type"></td>
                        <th>最多可选</th>
                        <td data-field="max_option"></td>
                    </tr>
                    <tr>
                        <th>投票情况</th>
                        <td colspan="5" align="center" valign="middle">
                            <div id="vote_detail_chart_container">

                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    /**
     * 页面加载后事件
     */
    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */

        //添加(编辑)投票窗口
        var vote_cu_dialog = $('#vote_cu_window').dialog({
            title: '投票添加',
            iconCls: 'icon-plus',
            width: 500,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-ok',
                    handler: function() {
                        var waiting = vote_cu_dialog.data('waiting');
                        if (waiting) return;

                        var win_state = vote_cu_dialog.data('state');

                        var ajax_url = null;
                        var ajax_data = {};
                        var ajax_method = null;
                        var op = null;

                        ajax_data.name = $('#vote_cu_form [name=name]').val();
                        ajax_data.url = $('#vote_cu_form [name=url]').val();
                        ajax_data.auto_url = $('#vote_cu_form [name=auto_url]').val();
                        ajax_data.type = $('#vote_cu_form [name=type]').val();
                        ajax_data.max_option = $('#vote_cu_form [name=max_option]').val();
                        ajax_data.start_date = start_date.datetimebox('getValue');
                        ajax_data.end_date = end_date.datetimebox('getValue');

                        var options = [];

                        $('#vote_option_container .vote-option').each(function (i, n) {
                            var option = {};

                            if (win_state == 'update') {
                                option.id = $(n).find('[name="oid"]').val();
                            }

                            option.vname = $(n).find('[name="vname"]').val();
                            option.short_name = $(n).find('[name="short_name"]').val();
                            options.push(option);
                        });

                        if (options.length > 0) {
                            ajax_data.options = options;
                        }

                        if (win_state == 'create') {
                            ajax_url = '/vote.json';
                            ajax_method = 'POST';
                            ajax_data = {creates: [ajax_data]};
                            op = '添加';
                        }
                        else if (win_state == 'update') {
                            ajax_url = '/vote.json';
                            ajax_method = 'PUT';
                            ajax_data.id = $('#vote_cu_form [name="id"]').val();
                            ajax_data.state = $('#vote_cu_form [name="state"]').val();
                            ajax_data = {updates: [ajax_data]};
                            op = '更新';
                        }

                        $.ajax({
                            url: ajax_url,
                            method: ajax_method,
                            data: ajax_data,
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            vote_cu_dialog.data('waiting', false);
                            if (!data.success) return;
                            vote_grid.datagrid('load');
                            $.messager.show({
                                title: '系统消息',
                                msg: '投票' + op + '成功'
                            });
                        });

                        vote_cu_dialog.dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        vote_cu_dialog.dialog('close');
                    }
                }
            ]
        });

        var vote_detail_window = $('#vote_detail_window').window({
            title: '投票详情',
            iconCls: 'icon-info-sign',
            width: 600,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //投票表格
        var vote_grid = $('#vote_grid').datagrid({
            url: '/voteList.json',
            title: '投票列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            loadFilter: function(data){
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;

                var id = null;
                var index = null;
                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];
                    if(row.id == id)
                    {
                        pass_rows[index].options.push(
                            {
                                id: row.oid,
                                name: row.oname,
                                short_name: row.oshortName,
                                create_date: row.ocreateTime,
                                count: row.userCount
                            }
                        );
                        continue;
                    }
                    id = row.id;
                    row.options = [
                        {
                            id: row.oid,
                            name: row.oname,
                            short_name: row.oshortName,
                            create_date: row.ocreateTime,
                            count: row.userCount
                        }
                    ];
                    pass_rows.push(row);
                    index = pass_rows.length - 1;
                }
                data.rows = pass_rows;
                data.count = pass_rows.length;
                return data;
            },
            toolbar: [
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(){
                        //清空表单
                        $('#vote_cu_form input').val('');
                        $('#vote_cu_form select').val('1');
                        start_date.datetimebox('clear');
                        start_date.datetimebox('clear');
                        $('#vote_cu_form [name="state"]').val('0').parent().hide();
                        //清楚添加的选项
                        $('#vote_option_container .addon-option').remove();

                        vote_cu_dialog.data('state', 'create');
                        var opt = vote_cu_dialog.dialog('options');
                        opt.title = '添加投票';
                        opt.iconCls = 'icon-wrench';
                        opt.buttons[0].text = '添加';
                        vote_cu_dialog
                            .dialog(opt)
                            .dialog('open');
                    }
                }
            ],
            columns:[[
                {field:'id', title:'ID', width:'5%', align:'center'},
                {field:'name', title:'名称', width:'20%', align:'center'},
                {field:'url', title:'相关链接', width:'15%', align:'center'},
                {field:'createTime', title:'创建时间', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'startDate', title:'开始时间', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'endDate', title:'结束时间', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'state', title:'当前状态', width:'5%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    if(value == 2)
                    {
                        return '进行中';
                    }
                    else if(value == 3)
                    {
                        return '已过期';
                    }
                    else
                    {
                        return '未启动';
                    }
                }},
                {field:'voteCount', title:'参与人数', width:'5%', align:'center'},
                {field:'options', title:'选项', width:'5%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    return value.length;
                }},
                {field:'rownum', title:'操作', width:'15%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    if(row.state < 3)
                    {
                        return '<div class="btn-group"><button class="btn btn-info vote-detail-btn" data-id="' + row.id + '"><i class="iconfa-eye-open"></i></button><button class="btn btn-warning vote-edit-btn" data-id="' + row.id + '"><i class="iconfa-edit"></i></button><button class="btn btn-danger vote-del-btn" data-id="' + row.id + '"><i class="iconfa-trash"></i></button></div>';
                    }
                    else
                    {
                        return '<div class="btn-group"><button class="btn btn-info vote-detail-btn" data-id="' + row.id + '"><i class="iconfa-eye-open"></i></button></div>';
                    }
                }}
            ]]
        });

        //日期空间
        var start_date = $('#vote_cu_form [name="start_date"]').datetimebox({editable: false});
        var end_date = $('#vote_cu_form [name="end_date"]').datetimebox({editable: false});

        //图表
        var pie_options = {
            chart: {
                width:500,
                height:200,
                backgroundColor: 'none',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: ''
            },
            tooltip: {
                //pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                useHTML: true,
                formatter: function(){
                    return '<b>'+ this.point.name +'</b></br> '+'票数:'+this.point.y + "<br/>" + "百分比:" +this.percentage.toFixed(2) +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: []
        };

        $('#vote_detail_chart_container').highcharts(pie_options);

        var vote_detail_chart = $('#vote_detail_chart_container').highcharts();

        /**
         * 事件相关
         */
        $('#vote_add_option_btn').click(function(event){
            event.preventDefault();
            var option_index = $('#vote_option_container .vote-option').length + 1;
            $('#vote_option_container').append(
                '<div class="row-fluid vote-option addon-option">' +
                    '<div class="span7">' +
                        '<span class="badge badge-info">' + option_index + '</span>'+
                        '<span class="label">选项</span>' +
                        '<input name="oid" type="hidden"/>' +
                        '<input name="vname" class="input-medium" type="text"/>' +
                    '</div>' +
                    '<div class="span5">' +
                        '<span class="label">简称</span>' +
                        '<input name="short_name" class="input-small" type="text"/>' +
                    '</div>' +
                '</div>'
            );
            return false;
        });

        //投票种类选择框改变事件
        $('#vote_cu_form [name="type"]').change(function(event){
            var new_value = $(this).val();
            if(new_value == '1')
            {
                $('#vote_max_option').hide()
            }
            else
            {
                $('#vote_max_option').show()
            }
        });

        //投票详情按钮点击事件
        $(document).on('click', '.vote-detail-btn', function(event){
            var vote = vote_grid.datagrid('getSelected');
            $('#vote_detail_table [data-field="name"]').text(vote.name);

            var type_name = '';
            if(vote.type == '1')
            {
                type_name = '单选';
            }
            else
            {
                type_name = '多选'
            }

            $('#vote_detail_table [data-field="type"]').text(type_name);
            $('#vote_detail_table [data-field="max_option"]').text(vote.maxOption);

            var series_data = [];

            var options = vote.options;
            var len = options.length;

            for(var i = 0; i < len; i++)
            {
                var option = options[i];
                series_data.push([option.name, Number(option.count)]);
            }


            var series = {
                type: 'pie',
                data: series_data
            }

            pie_options.series = [series];

            vote_detail_chart.hideLoading();
            vote_detail_chart.destroy();

            $('#vote_detail_chart_container').highcharts(pie_options);

            vote_detail_chart = $('#vote_detail_chart_container').highcharts();

            vote_detail_window.window('open');
        });

        //投票编辑按钮点击事件
        $(document).on('click', '.vote-edit-btn', function(event){

            var vote = vote_grid.datagrid('getSelected');
            $('#vote_cu_form [name=id]').val(vote.id);
            $('#vote_cu_form [name=name]').val(vote.name);
            $('#vote_cu_form [name=url]').val(vote.url);
            $('#vote_cu_form [name=auto_url]').val(vote.autoUrl);
            $('#vote_cu_form [name=type]').val(vote.type);
            $('#vote_cu_form [name=type]').change();
            $('#vote_cu_form [name=state]').val(vote.state).parent().show();
            $('#vote_cu_form [name=max_option]').val(vote.maxOption);
            start_date.datetimebox('setValue', vote.startDate);
            end_date.datetimebox('setValue', vote.endDate);

            $('#vote_option_container .addon-option').remove();
            $('#vote_option_container .vote-option [name]').val('');


            var len = vote.options ? vote.options.length : 0;
            for(var i = 0; i < len; i++)
            {
                if(i >= 2)
                {
                    $('#vote_option_container').append(
                        '<div class="row-fluid vote-option addon-option">' +
                        '<div class="span7">' +
                        '<span class="badge badge-info">' + ( i + 1 ) + '</span>'+
                        '<span class="label">选项</span>' +
                        '<input name="oid" type="hidden" value="' + vote.options[i]['id'] +'"/>' +
                        '<input name="vname" class="input-medium" type="text" value="' + vote.options[i]['name'] + '"/>' +
                        '</div>' +
                        '<div class="span5">' +
                        '<span class="label">简称</span>' +
                        '<input name="short_name" class="input-small" type="text" value="' + vote.options[i]['short_name'] + '"/>' +
                        '</div>' +
                        '</div>'
                    );
                }
                else
                {
                    $('#vote_option_container .vote-option').eq(i).find('[name="oid"]').val(vote.options[i]['id']);
                    $('#vote_option_container .vote-option').eq(i).find('[name="vname"]').val(vote.options[i]['name']);
                    $('#vote_option_container .vote-option').eq(i).find('[name="short_name"]').val(vote.options[i]['short_name']);
                }
            }

            vote_cu_dialog.data('state', 'update');
            var opt = vote_cu_dialog.dialog('options');
            opt.title = '编辑投票';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '更新';
            vote_cu_dialog
                .dialog(opt)
                .dialog('open');
        });

        //删除投票按钮点击事件
        $(document).on('click', '.vote-del-btn', function(event){
            var vote_id = $(this).attr('data-id');
            $.messager.confirm('删除投票', '是否确定删除', function(is_ok){
                if(!is_ok) return;
                $.ajax({
                    url: '/vote/' + vote_id + '.json',
                    method: 'DELETE',
                    dataType: 'json',
                    global: true
                }).done(function(data){
                    if (!data.success) return;
                    vote_grid.datagrid('load');
                    $.messager.show({
                        title: '系统消息',
                        msg: '投票删除成功'
                    });
                });
            });
        });

        /**
         * 页面离开时事件
         */
        this.on_leave = function(){
            //销毁对话框
            vote_cu_dialog.dialog('destroy');
            vote_detail_window.window('destroy');
            //清除动态绑定事件
            $(document).off('click', '.vote-detail-btn');
            $(document).off('click', '.vote-edit-btn');
            $(document).off('click', '.vote-del-btn');
        };
    };
</script>
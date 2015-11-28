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
    <div class="row-fluid">
        <div id="draw_period_grid_tb">
            <div class="row-fluid">
                <div class="span4">
                    <span class="label">活动名称</span>
                    <span>{{ activity.name }}</span>
                </div>
                <div class="span4">
                    <button id="draw_period_add_btn" class="btn"><i class="iconfa-plus"></i>添加时段</button>
                </div>
            </div>
        </div>
        <div id="draw_period_grid"></div>
    </div>
    <div id="draw_period_cu_window">
        <form id="draw_period_cu_form" action="" style="padding:5px">
            <div class="row-fluid">
                <div class="span5">
                    <input type="hidden" name="id" />
                    <input type="text" name="start_time" class="input-small"/>
                </div>
                <div class="span2">
                    <span>至</span>
                </div>
                <div class="span5">
                    <input type="text" name="end_time" class="input-small"/>
                </div>
            </div>
        </form>
    </div>
    <div id="draw_period_award_window"></div>
</div>

<script type="text/javascript">
    //页面加载完成事件
    $(document).one('pageLoad:drawPeriod', function(event){

        var aid = {{ activity.id }};

        /*
            控件
         */
        
        //时间控件
        var start_time = $('#draw_period_cu_form [name="start_time"]').timespinner({
            showSeconds: false
        });

        var end_time = $('#draw_period_cu_form [name="end_time"]').timespinner({
            showSeconds: false
        });

        /*
            窗口
         */
        //抽奖时段窗口
        var period_dialog = $('#draw_period_cu_window').dialog({
            title: '抽奖时段添加',
            iconCls: 'icon-plus',
            width: '300',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-ok',
                    handler: function(){
                        var state = period_dialog.data('state');

                        var period = {};

                        period.start_time = start_time.timespinner('getText');
                        period.end_time = end_time.timespinner('getText');

                        if(state == 'create')
                        {
                            addPeriod(period);
                        }
                        else if(state == 'update')
                        {
                            period.id = $('#draw_period_cu_form [name="id"]').val();
                            updatePeriod(period);
                        }
                        
                        period_dialog.dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        period_dialog.dialog('close');
                    }
                }
            ],
            onBeforeClose: function(){
                //关闭前清空表单
                $('#draw_period_cu_form [name="id"]').val('');
                start_time.timespinner('setValue', '');
                end_time.timespinner('setValue', '');
            }
        });

        //时段奖品窗口
        var award_window = $('#draw_period_award_window').window({
            title: '时段奖品',
            iconCls: 'icon-info-sign',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onOpen: function(){
                award_window.window('center');
            },
            onLoad: function(){
                award_window.window('center');
                $(document).trigger('pageLoad:drawPeriodAward');
            },
            onBeforeClose: function(){
                $(document).trigger('pageLeave:drawPeriodAward');
            }
        });

        /*
            表格
         */
        var period_grid = $('#draw_period_grid').datagrid({
            url: '/activity/{{ activity.id }}/periodList.json',
            title: '抽奖时段列表',
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
            idField: 'id',
            toolbar: '#draw_period_grid_tb',
            columns:[[
                {field:'start_time', title:'开始时间', width:'40%', align:'center'},
                {field:'end_time', title:'结束时间', width:'40%', align:'center'},
                {field:'id', title:'操作', width:'20%', align:'center', formatter: function(value, row, index){
                    return '<div class="btn-group"><button title="添加奖品" class="btn btn-mini btn-primary draw-period-award-btn" data-id="' + row.id +'"><i class="icon-gift"></i></button><button class="btn btn-mini btn-warning draw-period-item-edit-btn" title="编辑" data-id="' + row.id +'"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger draw-period-item-del-btn" title="删除" data-id="' + row.id +'"><i class="icon-trash"></i></button></div>'

                }},
            ]]
        });

        /*
            事件相关
         */
        //添加时段按钮点击事件
        $('#draw_period_add_btn').click(function(event){
            event.preventDefault();
            period_dialog.data('state', 'create');
            var opt = period_dialog.dialog('options');
            opt.title = '添加时间段';
            opt.iconCls = 'icon-plus';
            opt.buttons[0].text = '添加';
            period_dialog.dialog(opt).dialog('open');
            return false;
        });

        //编辑时段按钮点击事件
        $(document).on('click', '.draw-period-item-edit-btn', function(event){
            event.preventDefault();
            period_dialog.data('state', 'update');
            var id = $(this).attr('data-id');
            var index = period_grid.datagrid('getRowIndex', id);
            var row = period_grid.datagrid('getRows')[index];

            $('#draw_period_cu_form [name="id"]').val(row.id);
            start_time.timespinner('setText', row.start_time);
            end_time.timespinner('setText', row.end_time);

            var opt = period_dialog.dialog('options');
            opt.title = '编辑时间段';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '编辑';
            period_dialog.dialog(opt).dialog('open');
            return false;
        });

        //删除时段按钮点击事件
        $(document).on('click', '.draw-period-item-del-btn', function(event){
            event.preventDefault();
            var id = $(this).attr('data-id');
            delPeriod({id: id});
            return false;
        });

        //添加奖品按钮点击事件
        $(document).on('click', '.draw-period-award-btn', function(event){
            event.preventDefault();
            var id = $(this).attr('data-id');
            var opt = award_window.window('options');
            opt.href = '/activity/period/' + id + '/award';
            award_window.window(opt).window('open');
            return false;
        });

        /*
            数据相关
         */
        
        function addPeriod(data, callback)
        {
            $.ajax({
                url: '/activity/' + aid + '/period.json',
                method: 'POST',
                data: {creates: [data]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段添加失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段添加成功'
                    });
                }
                period_grid.datagrid('reload');
            });
        }

        function updatePeriod(data, callback)
        {
            $.ajax({
                url: '/activity/period/' + data.id + '.json',
                method: 'PUT',
                data: {updates: [data]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段更新失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽检时段更新成功'
                    });
                }
                period_grid.datagrid('reload');
            });
        }

        function delPeriod(data, callback)
        {
            $.ajax({
                url: '/activity/period/' + data.id + '.json',
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段删除失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段删除成功'
                    });
                }
                period_grid.datagrid('reload');
            });
        }

        //页面离开时事件
        $(document).one('pageLeave:drawPeriod', function(event){
            //销毁窗口
            period_dialog.dialog('destroy');
            award_window.window('destroy');
            //清除动态绑定事件
            $(document).off('click', '.draw-period-item-edit-btn');
            $(document).off('click', '.draw-period-item-del-btn');
            $(document).off('click', '.draw-period-award-btn');
        });
    });
    
</script>


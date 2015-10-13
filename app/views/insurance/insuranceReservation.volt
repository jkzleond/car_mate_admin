<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">保险预约列表</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="insurance_reservation_grid_tb">
                    <div class="row-fluid">
                        <div class="span3">
                            <span class="label">用户ID</span>
                            <input type="text" name="user_id" class="input-medium"/>
                        </div>
                        <div class="span3">
                            <span class="label">姓名</span>
                            <input type="text" name="user_name" class="input-small"/>
                        </div>
                        <div class="span3">
                            <span class="label">电话</span>
                            <input type="text" name="phone" class="input-small"/>
                        </div>
                        <div class="span3">
                            <span class="label">车牌号</span>
                            <input type="text" name="hphm" class="input-small"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span5">
                            <span class="label">预约时间</span>
                            <input type="text" name="start_date" class="input-small"/>
                            至
                            <input type="text" name="end_date" class="input-small"/>
                        </div>
                        <div class="span5"></div>
                        <div class="span2">
                            <button class="btn btn-primary" id="insurance_reservation_search_btn"><i class="icon-search"></i>查找</button>
                        </div>
                    </div>
                </div>
                <div id="insurance_reservation_grid"></div>
            </div>
        </div>
        <div id="insurance_reservation_window">
            <div class="row-fluid">
                <div class="span12">
                    
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //日期控件

        var start_date = $('#insurance_reservation_grid_tb [name="start_date"]').datebox();
        var end_date = $('#insurance_reservation_grid_tb [name="end_date"]').datebox();

        //记录下日期控件以便销毁
        $('#insurance_reservation_grid_tb').data('start_date', start_date);
        $('#insurance_reservation_grid_tb').data('end_date', end_date);

        //保险表格
        $('#insurance_reservation_grid').datagrid({
            url: '/insuranceReservationList.json',
            title: '保险列表',
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

            toolbar: '#insurance_reservation_grid_tb',

            columns:[[
                {field:'user_id',title:'用户ID',width:'20%',align:'center'},
                {field:'uname',title:'真实姓名',width:'10%',align:'center'},
                {field:'phone',title:'电话号码',width:'10%',align:'center'},
                {field:'hphm',title:'车牌号',width:'8%',align:'center'},
                {field:'auto_name',title:'品牌型号',width:'10%',align:'center'},
                {field:'frame_number',title:'车架号',width:'10%',align:'center'},
                {field:'engine_number',title:'发动机号',width:'10%',align:'center'},
                {field:'create_date',title:'预约时间',width:'8%',align:'center'},
                {field:'offer_date',title:'报价时间',width:'8%',align:'center'},

                {field:'id',title:'操作',width:'8%',align:'center', formatter: function(value, row, index){
                    if(row.mark == 'MARK_SUCCESS')
                    {
                        return '已报价';
                    }
                    else
                    {
                        return '<button class="btn btn-primary insurance-reservation-mark-btn" title="报价标记" data-id="' + row.id +'"><i class="iconfa-money"></i></button>'
                    }
                }}
            ]]
        });

        //窗口
        var insurance_reservation_window = $('#insurance_reservation_window').dialog({
            title: '报价标记',
            iconCls: 'icon-wrench',
            width: 240,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '标记',
                    handler: function(){
                        var order_id = $('#insurance_reservation_window [name="reservation_id"]').val();
                        var mark = $('#insurance_reservation_window [name="mark"]:checked').val();
                        $.ajax({
                            url: '/insuranceReservationProcess/' + order_id + '.json',
                            method: 'PUT',
                            data: {criteria: {mark: mark, fail_reason: fail_reason}},
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '报价标记失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '报价标记成功'
                                });
                                illegal_grid.datagrid('reload');
                            }
                        });
                        insurance_reservation_window.window('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        insurance_reservation_window.window('close');
                    }
                }
            ],
            onClose: function(){
                $(this).find('[name="mark"][value="PROCESS_SUCCESS"]').click();
                $(this).find('[name="fail_reason"]').val('');
            }
        });

        /**
         * 事件相关
         */

        //搜索按钮点击事件
        $('#insurance_reservation_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#insurance_reservation_grid_tb [name="user_id"]').val();
            criteria.user_name = $('#insurance_reservation_grid_tb [name="user_name"]').val();
            criteria.phone = $('#insurance_reservation_grid_tb [name="phone"]').val();
            criteria.hphm = $('#insurance_reservation_grid_tb [name="hphm"]').val();
            criteria.start_date = start_date.datebox('getValue');
            criteria.end_date = end_date.datebox('getValue');

            $('#insurance_reservation_grid').datagrid('load', {criteria: criteria});
        });

        //交易完成按钮点击事件
        $(document).on('click', '.insurance-reservation-mark-btn', function(event){

            var id = $(this).attr('data-id');

            $.messager.confirm('报价标记', '确认报价标记', function(is_ok){
                if(is_ok)
                {
                    $.ajax({
                        url: '/insuranceReservationProcess/' + id +'.json',
                        method: 'PUT',
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(data.success)
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '报价标记成功'
                            });
                            $('#insurance_reservation_search_btn').click();
                        }
                    });
                }
            });
        });
    };

    CarMate.page.on_leave = function(){
        //销毁窗口
        $('#insurance_reservation_window').window('destroy');

        //销毁日期控件
        $('#insurance_reservation_grid_tb').data('start_date').datebox('destroy');
        $('#insurance_reservation_grid_tb').data('end_date').datebox('destroy');


        //清除动态绑定事件
        $(document).off('click', '.insurance-op');
        $(document).off('click', '.insurance-complete');
        $(document).off('click', '.insurance-reservation-mark-btn');
    };

</script>
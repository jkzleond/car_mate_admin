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
    .table td, .table th {
        text-align: center;
        vertical-align: middle;
    }

</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">挪车业务订单管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="move_car_grid_tb">
                    <div class="row-fluid" id="move_car_search_bar">
                        <div class="row-fluid">
                            <div class="span3">
                                <span class="label">用户名</span>
                                <input type="text" name="user_id" class="input-medium" />
                            </div>
                            <div class="span3">
                                <span class="label">手机号</span>
                                <input type="text" name="phone" class="input-small" />
                            </div>
                            <div class="span2">
                                <span class="label">车牌号</span>
                                <input type="text" name="hphm" class="input-mini" />
                            </div>
                            <div class="span4">
                                <span class="label">时间段</span>
                                <input type="text" name="start_date" class="input-small">-
                                <input type="text" name="end_date" class="input-small">
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span3">
                                <span class="label">客户端</span>
                                <select name="client_type" class="input-small">
                                    <option value="">全部</option>
                                    {% for client_type in client_types %}
                                    {% if client_type != 'unknown' %}
                                    <option value="{{ client_type }}">{{ client_type }}</option>
                                    {% else %}
                                    <option value="{{ client_type }}">未知</option>
                                    {% endif %}
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="span3">
                                <span class="label">支付方式</span>
                                <select name="pay_type" class="input-small">
                                    <option value="">全部</option>
                                    <option value="alipay">支付宝</option>
                                    <option value="wxpay">微信支付</option>
                                </select>
                            </div>
                            <div class="span3">
                                <span class="label">支付状态</span>
                                <select name="pay_state" class="input-small">
                                    <option value="">全部</option>
                                    <option value="1">未支付</option>
                                    <option value="2">已支付</option>
                                    <option value="3">已关闭</option>
                                    <option value="4">免费</option>
                                </select>
                            </div>
                            <div class="span3">
                                <span class="label">申诉状态</span>
                                <select name="appeal_state" class="input-small">
                                    <option value="">全部</option>
                                    <option value="0">未处理</option>
                                    <option value="1">已处理</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span3">
                                <span class="label">接通状态</span>
                                <select name="is_link" class="input-medium">
                                    <option value="">全部</option>
                                    <option value="1">已接通</option>
                                    <option value="0">未接通</option>
                                </select>
                            </div>
                            <div class="span3">
                                <span class="label">反馈状态</span>
                                <select name="is_feedbacked" class="input-medium">
                                    <option value="">全部</option>
                                    <option value="1">已反馈</option>
                                    <option value="0">未反馈</option>
                                </select>
                            </div>
                            <div class="span3">
                                <button class="btn btn-primary" id="move_car_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                    <!--<div class="row-fluid">
                        <div class="span12">
                            <div class="btn-group">
                                <button class="btn" id="move_car_select_all_btn">全选/全不选</button>
                                <button class="btn" id="move_car_batch_onshelf_btn"><i class="iconfa-arrow-up"></i>批量上架</button>
                                <button class="btn" id="move_car_batch_offshelf_btn"><i class="iconfa-arrow-down"></i>批量下架</button>
                                <button class="btn" id="move_car_batch_del_btn"><i class="iconfa-trash"></i>批量删除</button>
                                <button class="btn btn-primary" id="move_car_add_btn"><i class="iconfa-plus"></i>添加</button>
                            </div>
                        </div>
                    </div>-->
                </div>
                <div id="move_car_grid"></div>
            </div>
            <div id="move_car_detail_window"></div>
            <div id="move_car_process_window">
                <div id="move_car_process_form">
                    <div class="row-fluid" id="move_car_fail_reason_container">
                        <div class="span12">
                            <span class="label">备注</span>
                            <input type="hidden" name="order_id">
                            <textarea name="process_des" id="" cols="30" rows="10"></textarea>
                        </div>
                    </div>
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
        //时间控件
        var start_datebox = $('#move_car_grid_tb [name="start_date"]').datebox({editable: false});
        var end_datebox = $('#move_car_grid_tb [name="end_date"]').datebox({editable: false});

        //数据表格
        var move_car_grid = $('#move_car_grid').datagrid({
            url: '/move_car/orderList.json',
            title: '订单列表',
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
            toolbar: '#move_car_grid_tb',
            idField: 'id',
            frozenColumns: [[
                {field: 'id', title: '操作', width: '10%', align: 'center', formatter: function(value, row, index){
                    var info_btn_html = '<button class="btn btn-info move_car-order-detail-btn" data-id="'+ value +'">明细</button>';
                    var process_btn_html = '<button class="btn btn-primary move_car-process-btn" data-id="' + value + '">处理</button>';
                    if(row.appeal_date)
                    {
                        return info_btn_html + process_btn_html;
                    }
                    else
                    {
                        return info_btn_html;
                    }
                }},
                {field:'pay_type', title:'支付方式', width:'6%', align:'center', formatter: function(value, row, index){
                    if(value == 'alipay')
                    {
                        return '支付宝';
                    }
                    if(value == 'wxpay')
                    {
                        return '微信支付';
                    }
                    if(value == 'offline')
                    {
                        return '线下支付';
                    }
                }},
                {field:'pay_state', title:'支付状态', width:'6%', align:'center', formatter: function(value, row, index){
                    if(value == 'TRADE_SUCCESS' || value == 'TRADE_FINISHED')
                    {
                        return '已支付';
                    }
                    else if(value == 'TRADE_CLOSED')
                    {
                        return '<span class="label label-important">已关闭</span>';
                    }
                    else if(value == 'ORDER_FREE')
                    {
                        return '免费';
                    }
                    else
                    {
                        return '未支付';
                    }
                }},
                {field:'appeal_state', title:'申诉状态', width:'10%', align:'center', formatter: function(value, row, index){
                    if(value == 0)
                    {
                        return '<span class="label label-important">未处理</span>';
                    }
                    else if(value == 1)
                    {
                        return '已处理' + '[' + row.appeal_process_des + ']';
                    }
                    else
                    {
                        return '<span class="label label-success">无申诉</span>';
                    }
                }}
            ]],
            columns:[[
                {field:'source', title:'来源', width: '6%', align:'center', formatter: function(value, row, index){
            if(value == 'cm')
            {
                return 'app';
            }
            else
            {
                return '微信';
            }
        }},
                {field:'order_no', title:'订单号', width:'15%', align:'center'},
                {field:'create_date', title:'订单时间', width:'12%', align:'center'},
                {field:'client_type', title:'客户端', width:'6%', align:'center', formatter: function(value, row, index){
                    if( value == 'unknown' )
                    {
                        return '未知';
                    }
                    else
                    {
                        return value;
                    }
                }},
                {field:'user_id', title:'用户名', width:'15%', align:'center'},
                {field:'user_name', title:'姓名', width:'6%', align:'center'},
                {field:'phone', title:'手机号', width:'9%', align:'center'},
                {field:'hphm', title:'车牌号', width:'7%', align:'center'},
                {field:'origin_price', title:'原价', width:'6%', align:'center'},
                {field:'order_fee', title:'订单金额', width:'6%', align:'center'},
                {field:'is_link', title:'接通状态', width:'6%', align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '<span class="label label-success">已接通</span>';
                    }
                    else
                    {
                        return '<span class="label label-important">未接通</span>';
                    }
                }},
                {field:'call_count', title:'通话次数', width:'6%', align:'center'},
                {field:'bill', title:'使用话费', width:'6%', align:'center'},
                {field:'last_call_time', title:'最近通知时间', width:'12%', align:'center'},
                {field:'feedback_date', title:'反馈时间', width:'12%', align:'center'},

                {field:'mark_time', title:'处理时间', width:'12%', align:'center'}
            ]]
        });

        //窗口
        var move_car_detail_window = $('#move_car_detail_window').window({
            title: '挪车业务订单明细',
            iconCls: 'icon-info-sign',
            width: '90%',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onOpen: function(){
                $(this).window('resize', {
                    width: 'auto',
                    height: 'auto'
                });
                $(this).window('center');
            },
            onLoad: function(){

                var resize_width = 'auto';
                var resize_height = 'auto';

                var $panel = $(this).window('panel');
                var panel_width = $panel.outerWidth();
                var panel_height = $panel.outerHeight();
                var client_width = window.innerWidth;
                var client_height = window.innerHeight;

                if(client_width < panel_width)
                {
                    resize_width = Math.round(client_width * 0.8);
                }

                if(client_height < panel_height)
                {
                    resize_height = Math.round(client_height * 0.8);
                }

                $(this).window('resize', {
                    width: resize_width,
                    height: resize_height
                });

                $(this).window('center');

                $(document).trigger('pageLoad:illegalOrderDetail');
            },
            onBeforeClose: function(){
                $(document).trigger('pageLeave:illegalOrderDetail');
            }
        });

        var move_car_process_window = $('#move_car_process_window').dialog({
            title: '挪车业务申诉处理',
            iconCls: 'icon-wrench',
            width: 240,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '处理',
                    handler: function(){
                        var order_id = $('#move_car_process_window [name="order_id"]').val();
                        var process_des = $('#move_car_process_window [name="process_des"]').val();
                        $.ajax({
                            url: '/move_car/appeal_process/' + order_id + '.json',
                            method: 'PUT',
                            data: {criteria: {process_des: process_des}},
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '申诉处理失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '申诉处理成功'
                                });
                                move_car_grid.datagrid('reload');
                            }
                        });
                        move_car_process_window.window('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        move_car_process_window.window('close');
                    }
                }
            ],
            onClose: function(){
                $(this).find('[name="process_des"]').val('');
            }
        });

        /**
         * 事件相关
         */
            //查找按钮点击事件
        $('#move_car_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#move_car_search_bar [name="user_id"]').val();
            criteria.phone = $('#move_car_search_bar [name="phone"]').val();
            criteria.hphm = $('#move_car_search_bar [name="hphm"]').val();
            criteria.pay_type = $('#move_car_search_bar [name="pay_type"]').val();
            criteria.pay_state = $('#move_car_search_bar [name="pay_state"]').val();
            criteria.client_type = $('#move_car_search_bar [name="client_type"]').val();
            criteria.appeal_state = $('#move_car_search_bar [name="appeal_state"]').val();
            criteria.is_link = $('#move_car_search_bar [name="is_link"]').val();
            criteria.is_feedbacked = $('#move_car_search_bar [name="is_feedbacked"]').val();
            criteria.start_date = start_datebox.datebox('getValue');
            criteria.end_date = end_datebox.datebox('getValue');
            move_car_grid.datagrid('load',{criteria: criteria});
        });

        //挪车业务订单明细按钮点击事件
        $(document).on('click', '.move_car-order-detail-btn', function(event){
            var order_id = $(this).attr('data-id');
            var opt = move_car_detail_window.window('options');
            opt.href = '/move_car/orderDetail/' + order_id;
            move_car_detail_window
                .window(opt)
                .window('setTitle', '挪车业务订单明细')
                .window('open');
        });

        //挪车业务处理按钮点击事件
        $(document).on('click', '.move_car-process-btn', function(event){
            var order_id = $(this).attr('data-id');
            move_car_process_window.find('[name=order_id]').val(order_id);
            move_car_process_window.window('open');
        });


        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            move_car_detail_window.window('destroy');
            move_car_process_window.dialog('destroy');

            //销毁时间控件
            start_datebox.datebox('destroy');
            end_datebox.datebox('destroy');

            //清除动态绑定事件
            $(document).off('click', '.move_car-order-detail-btn');
            $(document).off('click', '.move_car-process-btn');
        };
    };
</script>
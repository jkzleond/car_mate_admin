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
        <h4 class="widgettitle">违章代缴流水管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="illegal_transaction_grid_tb">
                    <div class="row-fluid" id="illegal_transaction_search_bar">
                        <div class="row-fluid">
                            <div class="span3">
                                <span class="label">订单号</span>
                                <input type="text" name="order_no" class="input-medium" />
                            </div>
                            <div class="span3">
                                <span class="label">流水号</span>
                                <input type="text" name="trade_no" class="input-medium" />
                            </div>
                            <div class="span2">
                                <span class="label">退款情况</span>
                                <select name="refund_state" class="input-small">
                                    <option value="">全部</option>
                                    <option value="1">未退款</option>
                                    <option value="2">部分退款</option>
                                    <option value="3">全退款</option>
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">时间段</span>
                                <input type="text" name="start_date" class="input-small">-
                                <input type="text" name="end_date" class="input-small">
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span2">
                                <span class="label">支付方式</span>
                                <select name="pay_type" class="input-mini">
                                    <option value="">全部</option>
                                    <option value="alipay">支付宝</option>
                                    <option value="wxpay">微信支付</option>
                                </select>
                            </div>
                            <div class="span2">
                                <button class="btn btn-primary" id="illegal_transaction_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="illegal_transaction_grid"></div>
            </div>
            <div id="illegal_transaction_edit_window">
                
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
        var start_datebox = $('#illegal_transaction_grid_tb [name="start_date"]').datebox({editable: false});
        var end_datebox = $('#illegal_transaction_grid_tb [name="end_date"]').datebox({editable: false});

        //数据表格
        var illegal_transaction_grid = $('#illegal_transaction_grid').datagrid({
            url: '/illegal/transactionList.json',
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
            toolbar: '#illegal_transaction_grid_tb',
            idField: 'id',
            frozenColumns:[[
                {field: 'id', title: '操作', width: '6%', align: 'center', formatter: function(value, row, index){
                    return '<button class="btn btn-warning illegal-order-detail-btn" data-id="'+ value +'" title="编辑"><i class="iconfa-edit"></i></button>';
                }}
            ]],
            columns:[[
                {field:'create_date', title:'订单时间', width:'15%', align:'center'},
                {field:'order_no', title:'订单号', width:'15%', align:'center'},
                {field:'trade_no', title:'交易号', width:'25%', align:'center'},
                {field:'pay_type', title:'支付方式', width:'8%', align:'center', formatter: function(value, row, index){
                    if(value == 'alipay')
                    {
                        return '支付宝';
                    }
                    if(value == 'wxpay')
                    {
                        return '微信支付';
                    }
                }},
                {field:'illegal_num', title:'违章条数', width:'8%', align:'center'},
                {field:'order_fee', title:'订单金额', width:'8%', align:'center'},
                {field:'refund_state', title:'退款情况', width:'8%', align:'center', formatter: function(value, row, index){
                    if(value == 'REFUND_FULL')
                    {
                        return '全部退款';
                    }
                    else if(value == 'REFUND_PART')
                    {
                        return '部分退款';
                    }
                    else
                    {
                        return '未退款';
                    }
                }},
                {field:'refund_fee', title:'退款金额', width:'6%', align:'center'},
                {field:'poundage', title:'手续费', width:'6%', align:'center'},
                {field:'real_income', title:'实际收入', width:'6%', align:'center'},
                {field:'des', title:'备注', width:'10%', align:'center'}
            ]]
        });

        //窗口
        var illegal_transaction_detail_window = $('#illegal_transaction_detail_window').window({
            title: '违章代缴订单明细',
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

        var illegal_transaction_process_window = $('#illegal_transaction_process_window').dialog({
            title: '违章代缴处理',
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
                        var order_id = $('#illegal_transaction_process_window [name="order_id"]').val();
                        var mark = $('#illegal_transaction_process_window [name="mark"]:checked').val();
                        var fail_reason = $('#illegal_transaction_process_window [name="fail_reason"]').val();
                        $.ajax({
                            url: '/illegal/orderProcess/' + order_id + '.json',
                            method: 'PUT',
                            data: {criteria: {mark: mark, fail_reason: fail_reason}},
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '违章处理结果提交失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '违章处理结果提交成功'
                                });
                                illegal_transaction_grid.datagrid('reload');
                            }
                        });
                        illegal_transaction_process_window.window('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        illegal_transaction_process_window.window('close');
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
        //查找按钮点击事件
        $('#illegal_transaction_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#illegal_transaction_search_bar [name="user_id"]').val();
            criteria.phone = $('#illegal_transaction_search_bar [name="phone"]').val();
            criteria.hphm = $('#illegal_transaction_search_bar [name="hphm"]').val();
            criteria.pay_type = $('#illegal_transaction_search_bar [name="pay_type"]').val();
            criteria.pay_state = $('#illegal_transaction_search_bar [name="pay_state"]').val();
            criteria.client_type = $('#illegal_transaction_search_bar [name="client_type"]').val();
            criteria.mark = $('#illegal_transaction_search_bar [name="mark"]').val();
            criteria.start_date = start_datebox.datebox('getValue');
            criteria.end_date = end_datebox.datebox('getValue');
            illegal_transaction_grid.datagrid('load',{criteria: criteria});
        });

        //违章代缴订单明细按钮点击事件
        $(document).on('click', '.illegal-order-detail-btn', function(event){
            var order_id = $(this).attr('data-id');
            var opt = illegal_transaction_detail_window.window('options');
            opt.href = '/illegal/orderDetail/' + order_id;
            illegal_transaction_detail_window
                .window(opt)
                .window('setTitle', '违章代缴订单明细')
                .window('open');
        });

        //违章代缴处理按钮点击事件
        $(document).on('click', '.illegal-process-btn', function(event){
            var order_id = $(this).attr('data-id');
            illegal_transaction_process_window.find('[name=order_id]').val(order_id);
            illegal_transaction_process_window.window('open');
        });

        //当选择违章无法处理时的事件
        $('#illegal_transaction_process_form [name="mark"]').change(function(event){
            var is_fail = $(this).val() == 'PROCESS_FAILED';

            if(is_fail)
            {
                $('#illegal_transaction_fail_reason_container').show();
            }
            else
            {
                $('#illegal_transaction_fail_reason_container').hide();
            }
        });


        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            illegal_transaction_detail_window.window('destroy');
            illegal_transaction_process_window.dialog('destroy');

            //销毁时间控件
            start_datebox.datebox('destroy');
            end_datebox.datebox('destroy');

            //清除动态绑定事件
            $(document).off('click', '.illegal-order-detail-btn');
            $(document).off('click', '.illegal-process-btn');
        };
    };
</script>
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
        <h4 class="widgettitle">违章代缴订单管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="illegal_grid_tb">
                    <div class="row-fluid" id="illegal_search_bar">
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
                            <div class="span2">
                                <span class="label">支付方式</span>
                                <select name="pay_type" class="input-mini">
                                    <option value="">全部</option>
                                    <option value="alipay">支付宝</option>
                                    <option value="wxpay">微信支付</option>
                                </select>
                            </div>
                            <div class="span2">
                                <span class="label">支付状态</span>
                                <select name="pay_state" class="input-mini">
                                    <option value="">全部</option>
                                    <option value="1">未支付</option>
                                    <option value="2">已支付</option>
                                    <option value="3">已关闭</option>
                                </select>
                            </div>
                            <div class="span3">
                                <span class="label">处理结果</span>
                                <select name="mark" class="input-small">
                                    <option value="">全部</option>
                                    <option value="1">未处理</option>
                                    <option value="2">已处理</option>
                                    <option value="3">无法处理</option>
                                </select>
                            </div>
                            <div class="span2">
                                <button class="btn btn-primary" id="illegal_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                    <!--<div class="row-fluid">
                        <div class="span12">
                            <div class="btn-group">
                                <button class="btn" id="illegal_select_all_btn">全选/全不选</button>
                                <button class="btn" id="illegal_batch_onshelf_btn"><i class="iconfa-arrow-up"></i>批量上架</button>
                                <button class="btn" id="illegal_batch_offshelf_btn"><i class="iconfa-arrow-down"></i>批量下架</button>
                                <button class="btn" id="illegal_batch_del_btn"><i class="iconfa-trash"></i>批量删除</button>
                                <button class="btn btn-primary" id="illegal_add_btn"><i class="iconfa-plus"></i>添加</button>
                            </div>
                        </div>
                    </div>-->
                </div>
                <div id="illegal_grid"></div>
            </div>
            <div id="illegal_detail_window"></div>
            <div id="illegal_process_window">
                <div id="illegal_process_form">
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">处理结果</span>
                            <input type="hidden" name="order_id">
                            <input type="radio" name="mark" value="PROCESS_SUCCESS" checked><span style="color:limegreen">成功处理</span>
                            <input type="radio" name="mark" value="PROCESS_FAILED"><span style="color:orangered">无法处理</span>
                        </div>
                    </div>
                    <div class="row-fluid" id="illegal_fail_reason_container" style="display:none">
                        <div class="span12">
                            <span class="label">无法处理原因</span>
                            <textarea name="fail_reason" id="" cols="30" rows="10"></textarea>
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
        var start_datebox = $('#illegal_grid_tb [name="start_date"]').datebox({editable: false});
        var end_datebox = $('#illegal_grid_tb [name="end_date"]').datebox({editable: false});

        //数据表格
        var illegal_grid = $('#illegal_grid').datagrid({
            url: '/illegal/orderList.json',
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
            toolbar: '#illegal_grid_tb',
            idField: 'id',
            frozenColumns: [[
                {field: 'id', title: '操作', width: '10%', align: 'center', formatter: function(value, row, index){
                    var info_btn_html = '<button class="btn btn-info illegal-order-detail-btn" data-id="'+ value +'">明细</button>';
                    var process_btn_html = '<button class="btn btn-primary illegal-process-btn" data-id="' + value + '">处理</button>';
                    if(row.pay_state == 'TRADE_SUCCESS' || row.pay_state == 'TRADE_FINISHED')
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
                    else
                    {
                        return '未支付';
                    }
                }},
                {field:'mark', title:'处理结果', width:'10%', align:'center', formatter: function(value, row, index){
                    if(value == 'PROCESS_SUCCESS')
                    {
                        return '处理完成';
                    }
                    else if(value == 'PROCESS_FAILED')
                    {
                        return '因[' + row.fail_reason + ']而无法处理';
                    }else
                    {
                        return '未处理';
                    }
                }}
            ]],
            columns:[[
                {field:'order_no', title:'订单号', width:'15%', align:'center'},
                {field:'create_date', title:'订单时间', width:'12%', align:'center'},
                {field:'user_id', title:'用户名', width:'15%', align:'center'},
                {field:'user_name', title:'姓名', width:'6%', align:'center'},
                {field:'phone', title:'手机号', width:'9%', align:'center'},
                {field:'hphm', title:'车牌号', width:'7%', align:'center'},
                {field:'illegal_num', title:'违章条数', width:'6%', align:'center'},
//                {field:'sum_fkje', title:'处罚金额', width:'6%', align:'center'},
                {field:'order_fee', title:'订单金额', width:'6%', align:'center'},
                {field:'client_type', title:'客户端', width:'6%', align:'center', formatter: function(value, row, index){
                    if( value == 'unknown' )
                    {
                        return '未知';
                    }
                    else
                    {
                        return value;
                    }
                }}
            ]]
        });

        //窗口
        var illegal_detail_window = $('#illegal_detail_window').window({
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

        var illegal_process_window = $('#illegal_process_window').dialog({
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
                        var order_id = $('#illegal_process_window [name="order_id"]').val();
                        var mark = $('#illegal_process_window [name="mark"]:checked').val();
                        var fail_reason = $('#illegal_process_window [name="fail_reason"]').val();
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
                                illegal_grid.datagrid('reload');
                            }
                        });
                        illegal_process_window.window('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        illegal_process_window.window('close');
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
        $('#illegal_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#illegal_search_bar [name="user_id"]').val();
            criteria.phone = $('#illegal_search_bar [name="phone"]').val();
            criteria.hphm = $('#illegal_search_bar [name="hphm"]').val();
            criteria.pay_type = $('#illegal_search_bar [name="pay_type"]').val();
            criteria.pay_state = $('#illegal_search_bar [name="pay_state"]').val();
            criteria.client_type = $('#illegal_search_bar [name="client_type"]').val();
            criteria.mark = $('#illegal_search_bar [name="mark"]').val();
            criteria.start_date = start_datebox.datebox('getValue');
            criteria.end_date = end_datebox.datebox('getValue');
            illegal_grid.datagrid('load',{criteria: criteria});
        });

        //违章代缴订单明细按钮点击事件
        $(document).on('click', '.illegal-order-detail-btn', function(event){
            var order_id = $(this).attr('data-id');
            var opt = illegal_detail_window.window('options');
            opt.href = '/illegal/orderDetail/' + order_id;
            illegal_detail_window
                .window(opt)
                .window('setTitle', '违章代缴订单明细')
                .window('open');
        });

        //违章代缴处理按钮点击事件
        $(document).on('click', '.illegal-process-btn', function(event){
            var order_id = $(this).attr('data-id');
            illegal_process_window.find('[name=order_id]').val(order_id);
            illegal_process_window.window('open');
        });

        //当选择违章无法处理时的事件
        $('#illegal_process_form [name="mark"]').change(function(event){
            var is_fail = $(this).val() == 'PROCESS_FAILED';

            if(is_fail)
            {
                $('#illegal_fail_reason_container').show();
            }
            else
            {
                $('#illegal_fail_reason_container').hide();
            }
        });


        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            illegal_detail_window.window('destroy');
            illegal_process_window.dialog('destroy');

            //销毁时间控件
            start_datebox.datebox('destroy');
            end_datebox.datebox('destroy');

            //清除动态绑定事件
            $(document).off('click', '.illegal-order-detail-btn');
            $(document).off('click', '.illegal-process-btn');
        };
    };
</script>
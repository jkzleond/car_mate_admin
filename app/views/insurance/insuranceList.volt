<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">保险列表</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="insurance_grid_tb">
                    <div class="row-fluid">
                        <div class="span3">
                            <span class="label">用户ID</span>
                            <input type="text" name="user_id" class="input-medium"/>
                        </div>
                        <div class="span5">
                            <span class="label">申请时间</span>
                            <input type="text" name="start_date" class="input-medium"/>
                            至
                            <input type="text" name="end_date" class="input-medium"/>
                        </div>
                        <div class="span2">
                            <span class="label">姓名</span>
                            <input type="text" name="user_name" class="input-small"/>
                        </div>
                        <div class="span2">
                            <span class="label">车牌号</span>
                            <input type="text" name="car_no" class="input-small"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span2">
                            <span class="label">电话</span>
                            <input type="text" name="phone_no" class="input-small"/>
                        </div>
                        <div class="span3">
                            <span class="label">邮箱地址</span>
                            <input type="text" name="email_addr" class="input-medium"/>
                        </div>
                        <div class="span3">
                            <span class="label">精算状态</span>
                            <select name="state" class="input-medium">
                                <option value="-1">全部</option>
                                {% for exact_state in exact_state_list %}
                                <option value="{{ exact_state.id }}">{{ exact_state.state }}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="span3">
                            <span class="label">车辆类型</span>
                            <select name="car_type" class="input-medium">
                                <option value="-1">全部</option>
                                {% for car_type in car_type_list %}
                                <option value="{{ car_type.code }}">{{ car_type.name }}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="span1">
                            <button class="btn btn-primary" id="insurance_search_btn"><i class="icon-search"></i>查找</button>
                        </div>
                    </div>
                </div>
                <div id="insurance_grid"></div>
            </div>
        </div>
        <div id="insurance_window"></div>
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //日期控件

        var start_date = $('#insurance_grid_tb [name="start_date"]').datebox();
        var end_date = $('#insurance_grid_tb [name="end_date"]').datebox();

        //记录下日期控件以便销毁
        $('#insurance_grid_tb').data('start_date', start_date);
        $('#insurance_grid_tb').data('end_date', end_date);


        $('.datebox input').keydown(function(event){
            event.preventDefault();
            return false;
        });

        //保险表格
        $('#insurance_grid').datagrid({
            url: '/insuranceList.json',
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

            toolbar: '#insurance_grid_tb',

            columns:[[
                {field:'typeName', title:'车辆类型', width:'8%', align:'center'},
                {field:'c_hphm',title:'车牌号',width:'6%',align:'center'},
                {field:'c_frameNumber',title:'车架号',width:'12%',align:'center'},
                {field:'userid',title:'用户ID',width:'15%',align:'center'},
                {field:'userName',title:'车主姓名',width:'6%',align:'center'},
                {field:'uname',title:'真实姓名',width:'6%',align:'center'},
                {field:'createDate',title:'申请时间',width:'8%',align:'center'},
                {field:'phoneNo',title:'电话号码',width:'8%',align:'center'},
                {field:'stateName',title:'精算状态',width:'6%',align:'center'},
                {field:'r_totalStandard',title:'全额保价',width:'8%',align:'center', formatter: function(value, row, index){
                    var formated = null;
                    if(row.f_totalStandard)
                    {
                        formated = Number(row.f_totalStandard).toFixed(2);
                    }
                    else
                    {
                        formated = Number(value).toFixed(2);
                    }
                    return formated;
                }},
                {field:'r_totalAfterDiscount',title:'优惠保价',width:'8%',align:'center', formatter: function(value, row, index){
                    var formated = null;
                    if(row.f_totalAfterDiscount)
                    {
                        formated = Number(row.f_totalAfterDiscount).toFixed(2);
                    }
                    else
                    {
                        formated = Number(value).toFixed(2);
                    }
                    return formated;
                }},

                {field:'id',title:'操作',width:'10%',align:'center', formatter: function(value, row, index){
                    var insurance_info_id = value;
                    var state_id = row.state_id;
                    var pay_state = row.payState;

                    var insurance_result_url = "{{ url('/insuranceResult') }}";
                    var insurance_buy_url = "{{ url('/insuranceBuy') }}";
                    var insurance_order_info_url = "{{ url('/insuranceOrderInfo') }}";

                    var exact_param = {
                        info_id: row.id,
                        param_id: row.insuranceParam_id,
                        result_id: row.r_id,
                        final_result_id: row.f_id,
                        final_param_id: row.finalParam_id,
                        state: row.state_id
                    };

                    var exact_param_str = '';

                    for(var prop in exact_param)
                    {
                        if(exact_param[prop])
                        {
                            exact_param_str += prop + '=' + exact_param[prop] + '&';
                        }
                    }

                    //去除最后一个&符
                    exact_param_str = exact_param_str.substring(0, exact_param_str.length -1);

                    insurance_result_url = insurance_result_url + '?' + exact_param_str;


                    var buy_param = {
                        info_id: row.id,
                        param_id: row.finalParam_id,
                        result_id: row.f_id
                    };

                    var buy_param_str = '';

                    for(var prop in buy_param)
                    {
                        if(buy_param[prop])
                        {
                            buy_param_str += prop + '=' + buy_param[prop] + '&';
                        }
                    }

                    //去除最后一个&符
                    buy_param_str = buy_param_str.substring(0, buy_param_str.length -1);

                    insurance_buy_url = insurance_buy_url + '?' + buy_param_str;


                    insurance_order_info_url = insurance_order_info_url + '/' + insurance_info_id;


                    if(state_id == 2)
                    {
                        return '<button class="btn insurance-op" data-state="exact" data-state-id="' + state_id + '" data-url="' + insurance_result_url + '">精算</button>';
                    }
                    else if(state_id == 4)
                    {
                        if(pay_state)
                        {
                            return '<button class="btn btn-info insurance-op" data-state="order_info" data-url="' + insurance_order_info_url + '">订单详情</button><button class="btn insurance-op" data-state="buy" data-state-id="' + state_id + '" data-url="' + insurance_buy_url + '" >购买处理</button>';
                        }
                    }
                    else if(state_id == 5)
                    {
                        return '<button class="btn btn-primary insurance-complete" data-id="' + value + '">交易完成</button>';
                    }
                    else
                    {
                        return '<button class="btn insurance-op" data-state="detail" data-state-id="' + state_id + '" data-url="' + insurance_result_url + '">详细</button>';
                    }
                }}
            ]]
        });

        //保险详情/精算/购买处理窗口
        $('#insurance_window').window({
            title: '保险窗口',
            iconCls: 'icon-info-sign',
            width: '80%',
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
            onBeforeLoad: function(){
                //为避免两次加载(奇怪的bug),加入标志
                var load_once = $('#insurance_window').data('load_once');

                if(load_once)
                {
                    return false;
                }
                else
                {
                    $('#insurance_window').data('load_once', true);
                    return true;
                }

            },
            onLoad: function(){
                $('#insurance_window').data('load_once', false);

                var resize_width = 'auto';
                var resize_height = 'auto';

                var $panel = $(this).window('panel');
                //var panel_width = $panel.outerWidth();
                //var panel_height = $panel.outerHeight();
                var client_width = $(window).width();
                var client_height = $(window).height();

                //if(client_width < panel_width)
                //{
                    resize_width = Math.round(client_width * 0.8);
                //}

                //if(client_height < panel_height)
                //{
                    resize_height = Math.round(client_height * 0.8);
                //}
                console.log(resize_width + ':' + resize_height);
                $(this).window('resize', {
                    width: resize_width,
                    height: resize_height
                });

                $(this).window('center');
            },
            onBeforeClose: function(){
                /*var state_id = $('#insurance_window').data('state_id');
                console.log(state_id);
                if (state_id == 2 || state_id == 3 || state_id == 4 || state_id == 7 )
                {
                    $('#insurance_reason_dialog').dialog('destroy');
                }

                if (state_id == 3)
                {
                    $('#insurance_issuing_dialog').dialog('destroy');
                }*/

                var need_destroy = $('#insurance_window').data('need_destroy');

                if(need_destroy && (need_destroy instanceof Array))
                {
                    var len = need_destroy.length;
                    for(var i = 0; i < len; i++)
                    {
                        var dialog = need_destroy[i];
                        dialog.dialog('destroy');
                    }

                    //清空数组
                    need_destroy.splice(0);
                }
            }
        });

        /**
         * 事件相关
         */

        //搜索按钮点击事件
        $('#insurance_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#insurance_grid_tb [name="user_id"]').val();
            criteria.start_date = start_date.datebox('getValue');
            criteria.end_date = end_date.datebox('getValue');
            criteria.user_name = $('#insurance_grid_tb [name="user_name"]').val();
            criteria.car_no = $('#insurance_grid_tb [name="car_no"]').val();
            criteria.phone_no = $('#insurance_grid_tb [name="phone_no"]').val();
            criteria.email_addr = $('#insurance_grid_tb [name="email_addr"]').val();
            criteria.state = $('#insurance_grid_tb [name="state"]').val();
            criteria.car_type = $('#insurance_grid_tb [name="car_type"]').val();

            $('#insurance_grid').datagrid('load', {criteria: criteria});
        });

        $(document).on('click', '.insurance-op', function(event){
            event.preventDefault();
            var btn_state = $(this).attr('data-state');
            var state_id = $(this).attr('data-state-id');
            $('#insurance_window').data('state_id', state_id);
            if(btn_state == 'exact')
            {
                var exact_url = $(this).attr('data-url');
                $('#insurance_window')
                    .window('setTitle', '保险精算')
                    .window('open', true)
                    .window('refresh', exact_url)
                    .window('center');
            }
            else if(btn_state == 'buy')
            {
                var buy_url = $(this).attr('data-url');
                $('#insurance_window')
                    .window('setTitle', '保险购买处理')
                    .window('open', true)
                    .window('refresh', buy_url)
                    .window('center');
            }
            else if(btn_state == 'detail')
            {
                var detail_url = $(this).attr('data-url');
                $('#insurance_window')
                    .window('setTitle', '保险详情')
                    .window('open', true)
                    .window('refresh', detail_url)
                    .window('center');
            }
            else if(btn_state == 'order_info')
            {
                var detail_url = $(this).attr('data-url');
                $('#insurance_window')
                    .window('setTitle', '保险订单信息')
                    .window('open', true)
                    .window('refresh', detail_url)
                    .window('center');
            }

            return false;
        });

        //交易完成按钮点击事件
        $(document).on('click', '.insurance-complete', function(event){

            var id = $(this).attr('data-id');

            $.ajax({
                url: '/insuranceComplete.json',
                method: 'PUT',
                data: {
                    data:{
                        id: id
                    }
                },
                dataType: 'json',
                global: true
            }).done(function(data){
                if(data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '保险交易完成'
                    });
                    $('#insurance_search_btn').click();
                }
            });
        });
    };

    CarMate.page.on_leave = function(){
        //销毁窗口
        $('#insurance_window').window('destroy');

        //销毁日期控件
        $('#insurance_grid_tb').data('start_date').datebox('destroy');
        $('#insurance_grid_tb').data('end_date').datebox('destroy');


        //清除动态绑定事件
        $(document).off('click', '.insurance-op');
        $(document).off('click', '.insurance-complete');
    };

</script>
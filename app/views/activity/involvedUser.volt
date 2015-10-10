<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">参与用户列表</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="activity_user_grid_tb">
                    <div class="row-fluid" id="activity_user_filter_grp">

                        <div class="span3">
                            <span class="label">用户ID</span>
                            <input name="user_id" type="text" class="input-medium"/>
                        </div>
                        <div class="span3" data-rel-info="idno">
                            <span class="label">身份证号</span>
                            <input name="id_card_no" type="text" class="input-medium"/>
                        </div>
                        <div class="span3">
                            <span class="label">真实姓名</span>
                            <input name="uname" type="text" class="input-small"/>
                        </div>
                        <div class="span3" data-rel-info="phone">
                            <span class="label">电话/手机</span>
                            <input name="phone" type="text" class="input-medium"/>
                        </div>
                        <div class="span3" data-rel-info="qqNum">
                            <span class="label">QQ号</span>
                            <input name="qq_num" type="text" class="input-medium"/>
                        </div>
                        <div class="span2">
                            <span class="label">领取状态</span>
                            <select name="state" class="input-small">
                                <option value="">-请选择状态-</option>
                                <option value="0">未领取</option>
                                <option value="1">领取</option>
                            </select>
                        </div>
                        <div class="span2">
                            <span class="label">通知状态</span>
                            <select name="is_noticed" class="input-small">
                                <option value="">-请选择状态-</option>
                                <option value="0">未通知</option>
                                <option value="1">已通知</option>
                            </select>
                        </div>

                        <div class="span3">
                            <span class="label">支付状态</span>
                            <select name="pay_state" class="input-small">
                                <option value="">-请选择状态-</option>
                                <option value="0">未支付</option>
                                <option value="1">已支付</option>
                            </select>
                        </div>
                        <div class="span3">
                            <span class="label">支付类型</span>
                            <select name="pay_type" class="input-medium">
                                <option value="">-请选择类型-</option>
                                <option value="CASH">线下现金</option>
                                <option value="POS">线下POS机</option>
                                <option value="ONLINE">在线支付</option>
                                <option value="TRANSFER">支付宝转账</option>
                            </select>
                        </div>
                        <div class="span3">
                            <span class="label">排序字段</span>
                            <select name="order_column" class="input-medium">
                                <option value="submitTime">提交时间</option>
                                <option value="payTime">付款时间</option>
                                <option value="gainTime">领取时间</option>
                            </select>
                        </div>
                        <div class="span3" data-rel-info="select">
                            <span class="label">this place display select info</span>
                            <select name="selected"></select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="btn-group" id="activity_user_btn_grp" style="display: none">
                                <button class="btn" id="activity_user_select_all_btn">全选/全不选</button>
                                <button class="btn" id="activity_user_batch_notice_btn"><i class="icon-bell"></i>通知</button>
                                <button class="btn" id="activity_user_batch_pay_btn"><i class="iconfa-credit-card"></i>付款</button>
                                <button class="btn" id="activity_user_batch_gain_btn"><i class="iconfa-gift"></i>领取</button>
                                <button class="btn" id="activity_user_submit_btn"><i class="icon-pencil"></i>登记</button>
                            </div>
                        </div>
                        <div class="span6">
                            <div class="span9">
                                <span class="label">活动</span>
                                <select name="aid" id="activity_user_activity_select"></select>
                            </div>
                            <div class="span3">
                                <button class="btn btn-primary" id="activity_user_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="activity_user_grid">
                </div>
            </div>
        </div>
        <div id="activity_user_window">
            <iframe src="" frameborder="0" style="width: 100%; height:100%"></iframe>
        </div>
        <div id="activity_user_order_detail_window"></div>
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //活动combogrid
        var activity_select = $('#activity_user_activity_select').combogrid({
            url: '/activityList.json',
            title: '活动列表',
            width: 300,
            panelWidth: 500,
            panelHeight: 350,
            idField: 'id',
            textField: 'name',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            onChange: function(new_value, old_value){
                var activity = activity_select.combogrid('grid').datagrid('getSelected');
                var info_arr = activity.infos.split(', ');

                //根据活动需要填写的用户信息显示信息滤器
                $('#activity_user_grid_tb [data-rel-info]').each(function(i, n){
                    var info = $(n).attr('data-rel-info');
                    if(info_arr.indexOf(info) != -1)
                    {
                        if(info == 'select')
                        {
                            $(n).children('span.label').text(activity.sname);
                            var short_names = activity.shortNames.split(', ');
                            var option_list = activity.optionList.split(', ');

                            var len = short_names.length;

                            $(n).children('select').empty();
                            $(n).children('select').append('<option value="">全部</option>');
                            for(var i = 0; i < len; i++)
                            {
                                $(n).children('select').append('<option value="' + short_names[i] + '">' + option_list[i] + '</option>');
                            }
                        }
                        $(n).show();
                    }
                    else
                    {
                        $(n).hide();
                    }
                });

                //根据活动需要流程(需要通知,需要支付等)的显示相关滤器

                if(activity.needNotice == 1)
                {
                    $('#activity_user_grid_tb [name=is_noticed]').parent().show();
                }
                else
                {
                    $('#activity_user_grid_tb [name=is_noticed]').parent().hide();
                }

                if(activity.needPay == 1)
                {
                    $('#activity_user_grid_tb [name=pay_state]').parent().show();
                    $('#activity_user_grid_tb [name=pay_type]').parent().show();
                    user_grid.datagrid('getColumnOption', 'payType').hidden = false;
                }
                else
                {
                    $('#activity_user_grid_tb [name=pay_state]').parent().hide();
                    $('#activity_user_grid_tb [name=pay_type]').parent().hide();
                    user_grid.datagrid('getColumnOption', 'payType').hidden = true;
                }

                //更具活动要求登记的用户信息显示活动参与用户表格的字段
                var info_list = ['uname', 'phone',
                    'idcardno', 'pca',
                    'address', 'sinaWeibo',
                    'weixin', 'hphm',
                    'sex', 'people',
                    'auto', 'select','qqNum'
                ];

                $.each(info_list, function(i, n){
                    var field_name = n;

                    if(n == 'auto')
                    {
                        field_name = 'options';
                    }
                    else if(n == 'select')
                    {
                        field_name = 'selected';
                    }
                    else if(n == 'pca')
                    {
                        field_name = 'province';
                    }

                    if(info_arr.indexOf(n) != -1)
                    {
                        //设置延时,避免点击combobox的同时渲染表格引起的卡顿
                        user_grid.datagrid('getColumnOption', field_name).hidden = false;

                        if(n == 'auto')
                        {
                            user_grid.datagrid('getColumnOption', field_name).title = activity.option;
                        }
                        else if(n == 'select')
                        {
                            user_grid.datagrid('getColumnOption', field_name).title = activity.sname;
                        }
                    }
                    else
                    {
                        user_grid.datagrid('getColumnOption', field_name).hidden = true;
                    }
                });

                //根据活动状态更改按显示钮组按钮(全选/全不选和批量处理等)的显示
                if(activity.state == 1)
                {
                    $('#activity_user_btn_grp').show();
                    if(activity.needNotice == 1)
                    {
                        $('#activity_user_batch_notice_btn').show();
                    }
                    else
                    {
                        $('#activity_user_batch_notice_btn').hide();
                    }

                    if(activity.needPay == 1)
                    {
                        $('#activity_user_batch_pay_btn').show();
                    }
                    else
                    {
                        $('#activity_user_batch_pay_btn').hide();
                    }
                }
                else
                {
                    $('#activity_user_btn_grp').hide();
                }

                return new_value;
            },
            loadFilter: function(data){
                //因为两个单元格同事需要id,为避免字段冲突智能复制该字段
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;

                var id = null;
                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];
                    if(row.id == id) continue;
                    id = row.id;
                    pass_rows.push(row);
                }
                data.rows = pass_rows;
                data.count = pass_rows.length;
                return data;
            },
            columns:[[
                {field:'typeName',title:'类型',width:'6%',align:'center'},
                {field:'name', title:'活动名称', width:'75%', align:'center'},
                {field:'state',title:'当前状态',width:'10%',align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '进行中';
                    }
                    else if(value == 2)
                    {
                        return '已过期';
                    }
                    else
                    {
                        return '未启动';
                    }
                }}
            ]]
        });

        //活动参与用户表格
        var user_grid = $('#activity_user_grid').datagrid({
            url: '/activityUserList.json',
            title: '活动列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: false,
            ctrlSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            toolbar: '#activity_user_grid_tb',
            frozenColumns: [[
                {field:'id',title:'操作',width:'15%',align:'center', formatter: function(value, row, index){
                    var edit_url = 'http://116.55.248.76:8080/car/userInfo.do?userid=' + row.userid + '&aid=' + row.aid;
                    var edit_btn = '<button class="btn btn-warning activity-user-edit-btn" title="用户编辑" data-url="' + edit_url + '"><i class="icon-edit"></i></button>';

                    var other_btn = null;

                    if( row.needNotice == 1 && ( row.isNoticed == 0 || !row.isNoticed ) )
                    {
                        other_btn = '<button class="btn btn-primary activity-user-notice-btn" title="通知用户" data-id="' + value + '"><i class="icon-bell"></i></button>';
                    }
                    else if( (row.isNoticed == 1 || row.needNotice == 0) && row.needPay == 1 && row.payState == 0 && !row.onlinePay )
                    {
                        other_btn = '<button class="btn btn-primary activity-user-pay-btn " title="用户付款" data-id="' + value + '"><i class="iconfa-credit-card" style="color:#000"></i></button>';
                    }
                    else if( row.state == 0 )
                    {
                        other_btn = '<button class="btn btn-primary activity-user-gain-btn " title="领取" data-id="' + value + '"><i class="iconfa-gift" style="color:#000"></i></button>';
                    }

                    if(row.needPay == 1 && ( row.orderId === '0' || row.orderId ))
                    {
                        other_btn += '<button class="btn btn-info activity-user-order-detail-btn" title="订单明细" data-order-id="' + row.orderId + '"><i class="icon-list"></i></button>';
                    }

                    return '<div class="btn-group">' + edit_btn + other_btn + '</div>';
                }},
                {field:'state',title:'状态',width:'10%',align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        var conv = CarMate.utils.date.mssqlToJs(row.gainTime);
                        var gain_time = CarMate.utils.date('Y-m-d H:i:s', conv);
                        return '已领取[' + gain_time + ']';
                    }
                    else
                    {
                        return '未领取';
                    }
                }},
                {field:'aname',title:'活动名称',width:'20%',align:'center'},
                {field:'userid', title:'用户名', width:'15%', align:'center'}
            ]],
        columns:[[
                {field:'uname',title:'姓名',width:'5%',align:'center'},
                {field:'phone', title:'手机号', width:'10%', align:'center', hidden: true},
                {field:'idcardno', title:'身份证号', width:'15%', align:'center', hidden: true},
                {field:'province', title:'省市', width:'15%', align:'center', formatter: function(value, row, index){
                    return value + ' ' + row.city + ' ' + row.area;
                }, hidden: true},
                {field:'address', title:'详细地址', width:'15%', align:'center', hidden: true},
                {field:'sinaWeibo', title:'新浪微博', width:'10%', align:'center', hidden: true},
                {field:'weixin', title:'微信', width:'10%', align:'center', hidden: true},
                {field:'hphm', title:'号牌', width:'10%', align:'center', hidden: true, formatter: function(value, row, index){
                    return value + '[' + row.hpzl + ']';
                }},
                {field:'sex',title:'性别',width:'5%',align:'center', hidden: true, formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '男';
                    }
                    else if(value == 2)
                    {
                        return '女';
                    }
                    else
                    {
                        return '你猜';
                    }
                }},
                {field:'people',title:'随行人数',width:'5%',align:'center', hidden: true},
                {field:'options',title:'其他信息',width:'10%',align:'center', hidden: true},
                {field:'selected',title:'下拉列表',width:'10%',align:'center', hidden: true},
                {field:'qqNum',title:'qq号码',width:'10%',align:'center', hidden: true, formatter: function(value, row, index){
                    if(!value) return;
                    var open_qq_url = 'http://wpa.qq.com/msgrd?v=3&uin=' + value + '&site=qq&menu=yes';
                    return '<span>' + value + '</span><a target="_blank" href="' + open_qq_url + '"><img src="http://pub.idqqimg.com/qconn/wpa/button/button_121.gif" alt="点击联系该用户"/></a>';
                }},
                {field:'payType',title:'缴费情况',width:'10%',align:'center', hidden: true, formatter: function(value, row, index){
                    if(!value) return;
                    var type = null;

                    switch(value)
                    {
                        case 'POS':
                            type = '线下POS机';
                            break;
                        case 'CASH':
                            type = '线下现金';
                            break;
                        case 'ONLINE':
                            type = '在线支付';
                            break;
                        case 'TRANSFER':
                            type = '支付宝转账';
                            break;
                        default:
                            type = '未知类型';
                            break;
                    }

                    var state = null;

                    if(row.payState == 1 || row.onlinePay)
                    {
                        var conv = CarMate.utils.date.mssqlToJs(row.payTime);
                        var time = CarMate.utils.date('Y-m-d H:i:s', conv);
                        state = '[完成]' + time;
                    }
                    else
                    {
                        state = '[未完成]';
                    }

                    return type + state;
                }},
                {field:'submitTime',title:'登记时间',width:'10%',align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }}
            ]]
        });

        /*   窗口    */
        
        //编辑窗口
        var user_window = $('#activity_user_window').window({
            title: '活动参与用户编辑',
            iconCls: 'icon-edit',
            width: 315,
            height: 525,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //订单明细窗口
        var order_detail_window = $('#activity_user_order_detail_window').window({
            title: '活动订单明细',
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

                $(document).trigger('pageLoad:activityUserOrderDetail');
            },
            onBeforeClose: function(){
                $(document).trigger('pageLeave:activityUserOrderDetail');
            }
        });



        /**
         * 事件相关
         */

        //查找按钮点击事件
        $('#activity_user_search_btn').click(function(event){
            var aid = activity_select.combogrid('getValue');
            //var user_id = $('#activity_user_grid_tb [name=user_id]').val();
            //var uname = $('#activity_user_grid_tb [name=uname]').val();

            var criteria = {};
            $('#activity_user_filter_grp div:visible>:nth-child(2)').each(function(i, n){
                var field_name = $(n).attr('name');
                if(field_name === undefined) console.log(n);
                var value = $(n).val();
                criteria[field_name] = value;
            });
            criteria.aid = aid;
            var old_option = user_grid.datagrid('options');
            old_option.queryParams = {criteria: criteria};
            //重新配置选项后加载数据,为了改变的columns option 能够生效
            user_grid.datagrid(old_option);
        });

        //全选/全不选按钮点击事件
        $('#activity_user_select_all_btn').click(function(evetn){
            var is_select_all = $(this).data('is_select_all');
            if(is_select_all)
            {
                user_grid.datagrid('unselectAll');
                //记录全选状态
                $(this).data('is_select_all', false);
            }
            else
            {
                user_grid.datagrid('selectAll');
                //记录全选状态
                $(this).data('is_select_all', true);
            }
        });

        //批量通知
        $('#activity_user_batch_notice_btn').click(function(event){
            var url = '/activityNoticeUser';
            batch_process('通知', url);
        });

        //批量付款
        $('#activity_user_batch_pay_btn').click(function(event){
            var url = '/activityUserPay';
            batch_process('付款', url);
        });

        //批量领取
        $('#activity_user_batch_gain_btn').click(function(event){
            var url = '/activityUserGain';
            batch_process('领取', url);
        });

        //登记新用户
        $('#activity_user_submit_btn').click(function(event){

            user_window
                .window({title: '用户登记', iconCls: 'iconfa-pencil'})
                .window('open')
                .window('center');
            var aid = activity_select.combogrid('getValue');
            $('#activity_user_window iframe').attr('src', 'http://116.55.248.76:8080/car/userInfo.do?userid=SYSTEM_ACCOUNT&aid=' + aid);
        });

        //用户编辑按钮点击事件
        $(document).on('click', '.activity-user-edit-btn', function(event){
            user_window
                .window({title: '用户编辑', iconCls: 'icon-edit'})
                .window('open')
                .window('center');

            var url = $(this).attr('data-url');
            $('#activity_user_window iframe').attr('src', url);
        });

        //用户通知按钮
        $(document).on('click', '.activity-user-notice-btn', function(event){
            var id = $(this).attr('data-id');
            var url = '/activityNoticeUser/' + id + '.json';
            single_process('通知', url);
        });

        //用户领取按钮
        $(document).on('click', '.activity-user-gain-btn', function(event){
            var id = $(this).attr('data-id');
            var url = '/activityUserGain/' + id + '.json';
            single_process('领取', url);
        });

        //用户付款按钮
        $(document).on('click', '.activity-user-pay-btn', function(event){
            var id = $(this).attr('data-id');
            var url = '/activityUserPay/' + id + '.json';
            single_process('付款', url);
        });

        //订单明细按钮点击事件
        $(document).on('click', '.activity-user-order-detail-btn', function(event){
            var order_id = $(this).attr('data-order-id');
            var opt = order_detail_window.window('options');
            opt.href = '/activityUserOrderDetail/' + order_id;
            order_detail_window
                .window(opt)
                .window('setTitle', '活动订单明细')
                .window('open');
        });

        /**
         * 函数相关
         */

        //批量处理
        function batch_process(op, url)
        {
            var selections = user_grid.datagrid('getSelections');
            if(selections.length == 0)
            {
                $.messager.alert('没有选择记录', '请至少选择一条记录', 'icon-warning-sign');
                return;
            }

            var ids = '';
            var len = selections.length;
            for(var i = 0; i < len; i++)
            {
                ids += selections[i].id + ',';
            }

            ids = ids.substring(0, ids.length - 1);

            var target_url = url + '/' + ids + '.json';

            $.messager.confirm('是否确定提交', '提交后无法撤消,是否确定要进行批量[' + op + ']操作', function(is_ok){
                if(is_ok)
                {
                    $.ajax({
                        url: target_url,
                        method: 'PUT',
                        data: {},
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(!data.success) return;
                        //提交成功后,再次点击搜索按钮,以刷新显示的数据
                        $('#activity_user_search_btn').click();
                    });
                }
            });
        }

        //单个用户处理
        function single_process(op, url)
        {
            $.messager.confirm('是否确定提交', '提交后无法撤消,是否确定要进行[' + op + ']操作', function(is_ok){
                if(is_ok)
                {
                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: {},
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(!data.success) return;
                        //提交成功后,再次点击搜索按钮,以刷新显示的数据
                        $('#activity_user_search_btn').click();
                    });
                }
            });
        }
    };

    CarMate.page.on_leave = function(){
        //销毁combogrid
        $('#activity_user_activity_select').combogrid('destroy');

        //销毁窗口
        $('#activity_user_winodw').window('destroy');

        //清除动态绑定事件
        $(document).off('click', '.activity-user-edit-btn');
        $(document).off('click', '.activity-user-notice-btn');
        $(document).off('click', '.activity-user-gain-btn');
        $(document).off('click', '.activity-user-pay-btn');
    };

</script>
<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
<div class="row-fluid" id="museum">
    <div class="span12">
        <h4 class="widgettitle">博物馆记戳有奖</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="activity_user_grid_tb">
                    <div class="filter-form row-fluid">
                        <div class="row-fluid">
                            <div class="span4" data-rel-info="idno">
                                <span class="label">约章本号</span>
                                <input name="book_no" type="text" class="input-medium"/>
                            </div>
                            <div class="span4">
                                <span class="label">真实姓名</span>
                                <input name="user_name" type="text" class="input-small"/>
                            </div>
                            <div class="span4">
                                <span class="label">电话/手机</span>
                                <input name="phone" type="text" class="input-medium"/>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">状态</span>
                                <select name="state" class="input-small">
                                    <option value="">-请选择状态-</option>
                                    <option value="NO_WIN">未中奖</option>
                                    <option value="HALF_YEAR_WIN">半年奖</option>
                                    <option value="FULL_YEAR_WIN">全年奖</option>
                                    <option value="NO_EXCHANGED">未领取</option>
                                    <option value="EXCHANGED">已领取</option>
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">排序字段</span>
                                <select name="order_by" class="input-medium">
                                    <option value="draw_date">抽奖时间</option>
                                    <option value="exchange_date">领取时间</option>
                                </select>
                            </div>
                            <div class="span4">
                                <button class="btn btn-primary" id="activity_user_search_btn">
                                    <i class="iconfa-search"></i>
                                    查找
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <div class="btn-group">
                                <button class="btn draw-btn"><i class="iconfa-random"></i>摇奖</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="activity_user_grid">
                </div>
            </div>
        </div>
        <div class="draw-window-toolbar">
            <div class="row-fluid">
                <div class="span12">
                    <span class="label">摇奖类型</span>
                    <select name="draw_type">
                        <option value="HALF_YEAR" selected>半年奖</option>
                        <option value="FULL_YEAR">全年奖</option>
                    </select>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <span class="label">奖品</span>
                    <select name="award" id=""></select>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <span class="label">人数</span>
                    <input type="text" name="people_num" class="input-mini">
                </div>
                <div class="span4">
                    <button class="btn btn-primary random-user-btn">
                        <i class="iconfa-random"></i>
                        摇号
                    </button>
                </div>
            </div>
        </div>
        <div class="draw-window">
            <div class="random-user-grid"></div>
        </div>
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //活动参与用户表格
        var user_grid = $('#activity_user_grid').datagrid({
            url: '/gygd/museum/activity_users.json',
            title: '活动参与用户列表',
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
                    var delete_btn = '<button class="btn btn-danger activity-user-del-btn" title="删除" data-user_id="' + row.user_id + '" data-activity_id="' + row.activity_id + '"><i class="iconfa-trash"></i></button>';
                    var other_btn = null;

                    if( (row.half_year_win == 1 && !row.half_year_exchange_date) || (row.full_year_win == 1 && !row.full_year_exchange_date) )
                    {
                        other_btn = '<button class="btn btn-primary activity-user-gain-btn " title="领取" data-user_id="' + row.user_id + '" data-activity_id="' + row.activity_id + '"><i class="iconfa-gift" style="color:#000"></i></button>';
                    }

                    return '<div class="btn-group">' + delete_btn + other_btn + '</div>';
                }}
            ]],
            columns:[[
                {field:'user_name',title:'姓名',width:'15%',align:'center'},
                {field:'phone', title:'手机号', width:'15%', align:'center'},
                {field:'book_no', title:'约章本号', width:'15%', align:'center'},
                {field:'half_year_exchange_date', title:'领取时间', width:'15%', align:'center', formatter: function(value, row, index){
                    return row.half_year_exchange_date || row.full_year_exchange_date;
                }},
                {field:'is_win',title:'状态',width:'15%',align:'center', formatter: function(value, row, index){
                    if (row.half_year_win == 1)
                    {
                        return '获半年奖[' + row.half_year_awards + ']' + (row.half_year_exchange_date ? '[已领取]' : '[未领取]');
                    }
                    else if (row.full_year_win == 1)
                    {
                        return '获全年奖[' + row.full_year_awards + ']' + (row.full_year_exchange_date ? '[已领取]' : '[未领取]');
                    }
                    else
                    {
                        return '未中奖';
                    }
                }}
            ]]
        });

        //随机摇号用户列表
        var random_user_grid = $('#museum .draw-window .random-user-grid').datagrid({
            title: '摇号列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 280,
            fitColumns: true,
            singleSelect: false,
            ctrlSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            columns:[[
                {field:'user_name',title:'姓名',width:'33%',align:'center'},
                {field:'phone', title:'手机号', width:'33%', align:'center'},
                {field:'book_no', title:'约章本号', width:'33%', align:'center'}
            ]]
        }); 

        /*   窗口    */

        //编辑窗口
        var draw_dialog = $('#museum .draw-window').dialog({
            title: '摇奖',
            iconCls: 'iconfa-random',
            width: 315,
            height: 525,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            toolbar: '#museum .draw-window-toolbar',
            buttons: [
                {
                    text: '确定',
                    handler: function(event){
                        event.preventDefault();
                        var draw_type = $('.draw-window-toolbar [name="draw_type"]').val();
                        var draw_type_name = draw_type == 'HALF_YEAR' ? '半年奖' : '全年奖';
                        var award_id = award_combobox.combobox('getValue');
                        var award_name = award_combobox.combobox('getText');
                        var win_users = random_user_grid.datagrid('getData').rows;
                        if (!win_users || win_users.length == 0)
                        {
                            $.messager.alert('警告', '没有摇出获奖用户, 请先点击摇号按钮');
                            return false;
                        }
                        $.messager.confirm('确定结果', '该操作无法撤回, 请确认以上' + win_users.length + '名用户获得' + draw_type_name + award_name, function(is_ok){
                            if (!is_ok) return;
                            $.ajax({
                                url: '/gygd/museum/activity/' + draw_type +'/users/win.json',
                                method: 'POST',
                                data: {
                                    award_id: award_id,
                                    users: win_users
                                },
                                global: true
                            }).done(function(resp){
                                if (resp.success)
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '摇奖成功'
                                    });
                                    random_user_grid.datagrid('reload');
                                }
                                else
                                {
                                    $.messager.show({
                                        title: '系统消息',
                                        msg: '摇奖失败'
                                    });
                                }
                            });
                        });
                    }
                },
                {
                    text: '取消',
                    handler: function(event){
                        draw_dialog.dialog('close');
                        return false
                    }
                }
            ]
         });

        //奖品选择框
        var award_combobox = $('.draw-window-toolbar [name="award"]').combobox({
            url: '/gygd/museum/activity/HALF_YEAR/awards.json',
            method: 'GET',
            valueField: 'id',
            textField: 'name',
            editable: false,
            loadFilter: function(resp){
                if (resp.success)
                {
                    award_combobox.combobox('setValue', resp.rows[0].id);
                    return resp.rows;
                }
                return null;
            }
        });

        //人数
        var people_numberbox = $('.draw-window-toolbar [name="people_num"]').numberbox({
            min: 1,
            value: 1,
        });

        /**
         * 事件相关
         */

        var filter_form = $('#museum .filter-form');
        //查找按钮点击事件
        $('#activity_user_search_btn').click(function(event){
            var criteria = {};
            filter_form.find('[name]').each(function(i, n){
                var field_name = $(n).attr('name');
                var value = $(n).val();
                criteria[field_name] = value;
            });
            user_grid.datagrid('reload', {criteria: criteria});
        });

        $('.draw-btn').click(function(event){
            draw_dialog.dialog('center').dialog('open');
        });

        $('.draw-window-toolbar [name="draw_type"]').change(function(event){
            var draw_type = $(this).val();
            var url = '/gygd/museum/activity/' + draw_type + '/awards.json';
            award_combobox.combobox('reload', url);
            random_user_grid.datagrid('loadData', []);
        });

        //摇号按钮点击事件
        $('.random-user-btn').click(function(event){
            var draw_type = $('.draw-window-toolbar [name="draw_type"]').val();
            var people_num = people_numberbox.numberbox('getValue');
            $.ajax({
                url: '/gygd/museum/activity/' + draw_type +'/users/random/' + people_num +'.json',
                method: 'GET',
                global: true
            }).done(function(resp){
                if (resp.success)
                {
                    random_user_grid.datagrid('loadData', resp.list);
                }
            });
        });

        //用户领取按钮
        $(document).on('click', '.activity-user-gain-btn', function(event){
            var user_id = $(this).attr('data-user_id');
            var activity_id = $(this).attr('data-activity_id');
            gain({
                user_id: user_id,
                activity_id: activity_id
            }, function(){
                user_grid.datagrid('reload');
            });
        });

        //删除参与用户信息
        $(document).on('click', '.activity-user-del-btn', function(event){
            var user_id = $(this).attr('data-user_id');
            var activity_id = $(this).attr('data-activity_id');
            del(activity_id, user_id, function(){
                user_grid.datagrid('reload');
            });
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

        //抽奖配置保存
        function save_config(data)
        {
            $.ajax({
                url: '/gygd/museum/activity.json',
                method: 'PUT',
                data: {data: data},
                global: true
            }).done(function(resp){
                if(resp.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖配置保存成功'
                    });
                }
            });
        }

        //领取
        function gain(data, callback)
        {
            $.messager.confirm('确认', '确定对该用户的奖品已领取?', function(is_ok){
                if (is_ok)
                {
                    $.ajax({
                        url: '/gygd/museum/activity/user/gain.json',
                        method: 'PUT',
                        data: {data: data},
                        global: true
                    }).done(function(resp){
                        if (resp.success)
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '用户领取标记成功'
                            });

                            if (typeof callback == 'function')
                            {
                                callback();
                            }
                        }
                        else
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '用户领取标记失败'
                            });
                        }
                    });
                }
            });
        }

        /**
         * 删除参与用户信息
         * @param activity_id
         * @param user_id
         * @param callback
         */
        function del(activity_id, user_id, callback)
        {
            $.messager.confirm('确认', '确定对删除该参与用户信息', function(is_ok){
                if (is_ok)
                {
                    $.ajax({
                        url: '/gygd/museum/activity/' + activity_id +'/users/' + user_id + '.json',
                        method: 'DELETE',
                        global: true
                    }).done(function(resp){
                        if (resp.success)
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '删除该参与用户信息成功'
                            });

                            if (typeof callback == 'function')
                            {
                                callback();
                            }
                        }
                        else
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '删除该参与用户信息失败'
                            });
                        }
                    });
                }
            });
        }

        //页面离开事件
        this.on_leave = function(){
            //销毁combobox
            award_combobox.combobox('destroy');

            //销毁窗口
            $('#activity_user_winodw').window('destroy');
            draw_dialog.window('destroy');

            //清除动态绑定事件
            $(document).off('click', '.activity-user-edit-btn');
            $(document).off('click', '.activity-user-notice-btn');
            $(document).off('click', '.activity-user-gain-btn');
            $(document).off('click', '.activity-user-del-btn');
            $(document).off('click', '.activity-user-pay-btn');
            $(document).off('click', '.activity-user-order-detail-btn');
        };
    };

</script>
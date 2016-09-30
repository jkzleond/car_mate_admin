<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
<div class="row-fluid" id="stadium">
    <div class="span12">
        <h4 class="widgettitle">体育惠民一本通活动</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid config-form">
                <fieldset class="well well-samll span12">
                    <legend><i class="iconfa-cog"></i>抽奖设置</legend>
                    <div class="row-fluid">
                        <div class="span2">
                            <span class="label">抽奖份数</span>
                            <input name="win_limit" type="text" class="input-mini" value="{{ activity.win_limit  }}">
                            <i class="iconfa-question-sign cursor-pointer" title="-1为无限"></i>
                        </div>
                        <div class="span2">
                            <span class="label">中奖率设置</span>
                            <input name="win_rate" type="text" class="input-mini" value="{{ activity.win_rate }}">
                        </div>
                        <div class="span3">
                            <span class="label">手机号规则</span>
                            <input name="phone_rule" type="text" class="input-medium" value="{{activity.win_rule.phone}}">
                            <i class="iconfa-question-sign cursor-pointer" title="限制开头如:132*;结尾:*4567;含有:*888*"></i>
                        </div>
                        <div class="span3">
                            <span class="label">身份证规则</span>
                            <input name="id_no_rule" type="text" class="input-medium" value="{{activity.win_rule.id_no}}">
                            <i class="iconfa-question-sign cursor-pointer" title="限制开头如:53*;结尾:**650;含有:*999*"></i>
                        </div>
                        <div class="span2">
                            <button class="save-config-btn btn btn-warning"><i class="iconfa-wrench"></i>保存</button>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="row-fluid filter-form">
                <div id="activity_user_grid_tb">
                    <div class="row-fluid">
                        <div class="span4" data-rel-info="idno">
                            <span class="label">身份证号</span>
                            <input name="id_no" type="text" class="input-medium"/>
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
                                <option value="0">未中奖</option>
                                <option value="1">中奖</option>
                                <option value="2">领取</option>
                                <option value="3">未领取</option>
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

        var win_rate_numbox = $('[name=win_rate]').numberspinner({
            width: 80,
            height: 30,
            max: 10000,
            suffix: '‱'
        });

        //活动参与用户表格
        var user_grid = $('#activity_user_grid').datagrid({
            url: '/gygd/stadium/activity_users.json',
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

                    if( row.is_win == 1 && !row.exchange_date )
                    {
                        other_btn = '<button class="btn btn-primary activity-user-gain-btn " title="领取" data-user_id="' + row.user_id + '" data-activity_id="' + row.activity_id + '"><i class="iconfa-gift" style="color:#000"></i></button>';
                    }

                    return '<div class="btn-group">' + delete_btn + other_btn + '</div>';
                }}
            ]],
            columns:[[
                {field:'user_name',title:'姓名',width:'15%',align:'center'},
                {field:'phone', title:'手机号', width:'15%', align:'center'},
                {field:'id_no', title:'身份证号', width:'15%', align:'center'},
                {field:'draw_date', title:'抽奖时间', width:'15%', align:'center'},
                {field:'exchange_date', title:'领取时间', width:'15%', align:'center'},
                {field:'is_win',title:'状态',width:'15%',align:'center', formatter: function(value, row, index){
                    if (row.is_win == 1)
                    {
                        return (row.exchange_date ? '已领取' : '未领取');
                    }
                    else
                    {
                        return '未中奖';
                    }
                }}
            ]]
        });

        /*   窗口    */

        //编辑窗口
        /*var user_window = $('#activity_user_window').window({
         title: '活动参与用户编辑',
         iconCls: 'icon-edit',
         width: 315,
         height: 525,
         closed: true,
         shadow: false,
         modal: true,
         openAnimation: 'fade'
         });*/


        /**
         * 事件相关
         */

        var config_form = $('#stadium .config-form');

        //抽奖配置保存按钮点击事件
        $('.save-config-btn').click(function(event){
            var win_limit = config_form.find('[name=win_limit]').val();
            var phone_rule = config_form.find('[name=phone_rule]').val();
            var id_no_rule = config_form.find('[name=id_no_rule]').val();
            var win_rate = win_rate_numbox.numberspinner('getValue');
            save_config({
                win_limit: win_limit,
                win_rate: win_rate,
                win_rule: {
                    phone: phone_rule,
                    id_no: id_no_rule
                }
            })
        });

        var filter_form = $('#stadium .filter-form');
        //查找按钮点击事件
        $('#activity_user_search_btn').click(function(event){
            var criteria = {};
            filter_form.find('[name]').each(function(i, n){
                var field_name = $(n).attr('name');
                var value = $(n).val();
                criteria[field_name] = value;
            });
//            var old_option = user_grid.datagrid('options');
//            old_option.queryParams = {criteria: criteria};
            //重新配置选项后加载数据,为了改变的columns option 能够生效
//            user_grid.datagrid(old_option);
            user_grid.datagrid('reload', {criteria: criteria});
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

        //批量领取
        $('#activity_user_batch_gain_btn').click(function(event){
            var url = '/activityUserGain';
            batch_process('领取', url);
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
                url: '/gygd/stadium/activity.json',
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
                        url: '/gygd/stadium/activity/user/gain.json',
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
                        url: '/gygd/stadium/activity/' + activity_id +'/users/' + user_id + '.json',
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
            //销毁窗口
            $('#activity_user_winodw').window('destroy');

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
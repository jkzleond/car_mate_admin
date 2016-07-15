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
        <h4 class="widgettitle">车友互动管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="talk_grid_tb">
                    <div class="row-fluid">
                        <div class="row-fluid" id="talk_search_bar">
                            <div class="span4">
                                <span class="label">用户名</span>
                                <input type="text" name="user_id" class="input-medium" />
                            </div>
                            <div class="span4">
                                <span class="label">关键字</span>
                                <input type="text" name="contents" class="input-medium" />
                            </div>
                            <div class="span4">
                                <span class="label">时间段</span>
                                <input type="text" name="start_publish_time" class="input-small">-
                                <input type="text" name="end_publish_time" class="input-small">
                            </div>
                        </div>
                        <div class="row-fluid" id="talk_order_bar">
                            <div class="span2">
                                <span class="label">排序字段</span>
                                <select name="order" class="input-small">
                                    <option value="publishTime">发布时间</option>
                                    <option value="replyTime">回复时间</option>
                                </select>                               
                            </div>
                            <div class="span2">
                                <button class="btn btn-primary" id="talk_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="talk_grid"></div>
            </div>
            <div id="talk_reply_list_window">
                <div id="talk_reply_grid"></div>
            </div>
            <div id="talk_reply_window">
                <div class="row-fluid" id="talk_replay_container">
                    <div class="span12">
                        <span class="label">回复内容</span>
                        <input type="hidden" name="id">
                        <textarea name="comment" cols="50" rows="10" class="input-xxlarge"></textarea>
                    </div>
                </div>
            </div>
            <div id="no_talk_window">
                <div class="row-fluid">
                    <div class="span6">
                        <span class="label">截至时间</span>
                    </div>
                    <div class="span6">
                        <input type="hidden" name="user_id">
                        <input type="text" class="input-small" name="no_talk_date">
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
        var start_datebox = $('#talk_grid_tb [name="start_publish_time"]').datebox({editable: false});
        var end_datebox = $('#talk_grid_tb [name="end_publish_time"]').datebox({editable: false});
        var no_talk_datebox = $('#no_talk_window [name="no_talk_date"]').datebox({editable: false});

        //数据表格
        var talk_grid = $('#talk_grid').datagrid({
            url: '/talk/list.json',
            title: '车友互动列表',
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
            toolbar: '#talk_grid_tb',
            idField: 'id',
            frozenColumns: [[
                {field: 'id', title: '操作', width: '15%', align: 'center', formatter: function(value, row, index){
                    var reply_list_btn_html = '<button class="btn btn-info talk-reply-list-btn" data-id="'+ value +'" title="回复列表"><i class="iconfa-list"></i></button>';
                    var reply_btn_html = '<button class="btn btn-primary talk-reply-btn" data-id="'+ value +'" title="回复"><i class="iconfa-comment"></i></button>';
                    var no_talk_btn_html = '<button class="btn btn-primary no-talk-btn" data-user_id="'+ row.user_id +'" title="禁言"><i class="iconfa-remove-circle"></i></button>';
                    var delete_btn_html = '<button class="btn btn-danger talk-delete-btn" data-id="' + value + '" title="删除"><i class="iconfa-trash"></i></button>';
                    return reply_list_btn_html + reply_btn_html + no_talk_btn_html + delete_btn_html;
                }},
                {field:'province_name', title:'省份', width:'6%', align:'center'}
            ]],
            columns:[[
                {field:'user_id', title:'用户名', width:'15%', align:'center'},
                {field:'nick_name', title:'昵称', width:'10%', align:'center'},
                {field:'publish_time', title:'发表时间', width:'10%', align:'center'},
                {field:'contents', title:'内容', width:'30%', align:'center'},
                {field:'rownum', title:'附件', width:'15%', align:'center', formatter:function(value, row, index){
                    if(row.pic)
                    {
                        return '<img src="/talk/' + row.id + '.png" />';
                    }
                }},
                {field:'state', title:'发布状态', width:'5%', align:'center', formatter:function(value, row, index){
                    if(value == 0)
                    {
                        return '<button class="btn talk-state-on-btn" data-id="' + row.id +'"><i class="iconfa-circle-blank"></i></button>';
                    }
                    else
                    {
                        return '<button class="btn talk-state-off-btn" data-id="' + row.id +'"><i class="iconfa-circle"></i></button>';
                    }
                }},
                {field:'no_reply', title:'可回复', width:'5%', align:'center', formatter:function(value, row, index){
                    if(value == 0)
                    {
                        return '<button class="btn talk-noreply-on-btn" data-id="' + row.id + '"><i class="iconfa-circle"></i></button>';
                    }
                    else
                    {
                        return '<button class="btn talk-noreply-off-btn" data-id="' + row.id + '"><i class="iconfa-circle-blank"></i></button>';
                    }
                }}
            ]]
        });

        var reply_grid = $('#talk_reply_grid').datagrid({
            method: 'GET',
            title: '车友互动列表',
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
            columns:[[
                {field:'user_id', title:'用户名', width:'15%', align:'center'},
                {field:'title', title:'昵称', width:'10%', align:'center'},
                {field:'comment_time', title:'回复时间', width:'10%', align:'center'},
                {field:'comment', title:'内容', width:'50%', align:'center'},
                {field: 'id', title: '操作', width: '15%', align: 'center', formatter: function(value, row, index){
                    var delete_btn_html = '<button class="btn btn-danger talk-reply-delete-btn" data-id="' + value + '" title="删除"><i class="iconfa-trash"></i></button>';
                    return delete_btn_html;
                }},
            ]]
        });

        //窗口
        var talk_reply_list_window = $('#talk_reply_list_window').window({
            title: '回复列表',
            iconCls: 'icon-info-sign',
            width: '90%',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        var talk_reply_window = $('#talk_reply_window').dialog({
            title: '车友互动回复',
            iconCls: 'iconfa-comment',
            width: 500,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '回复',
                    handler: function(){
                        var talk_id = $('#talk_reply_window [name="id"]').val();
                        var comment = $('#talk_reply_window [name="comment"]').val();
                        $.ajax({
                            url: '/talk/' + talk_id + '/reply.json',
                            method: 'POST',
                            data: {comment: comment},
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '车友互动回复失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '车友互动回复成功'
                                });
                                talk_grid.datagrid('reload');
                            }
                        });
                        talk_reply_window.window('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        talk_reply_window.window('close');
                    }
                }
            ],
            onClose: function(){
                $(talk_reply_window).find('[name="id"]').val('');
                $(talk_reply_window).find('[name="comment"]').val('');
            }
        });

        var no_talk_window = $('#no_talk_window').dialog({
            title: '用户禁言',
            iconCls: 'iconfa-remove-circle',
            width: 260,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '禁言',
                    handler: function(){
                        var user_id = $(no_talk_window).find('[name="user_id"]').val();
                        var no_talk_date = no_talk_datebox.datebox('getValue');
                        $.ajax({
                            url: '/user/' + user_id + '/no_talk.json',
                            method: 'PUT',
                            data: {no_talk: no_talk_date},
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '用户禁言失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '用户禁言成功'
                                });
                            }
                        });
                        no_talk_window.dialog('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        no_talk_window.dialog('close');
                    }
                }
            ],
            onClose: function(){
                $(no_talk_window).find('[name="user_id"]').val('');
                no_talk_datebox.datebox('setValue', '');
            }
        });

        /**
         * 事件相关
         */
        //查找按钮点击事件
        $('#talk_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#talk_search_bar [name="user_id"]').val();
            criteria.contents = $('#talk_search_bar [name="contents"]').val();
            criteria.start_publish_time = start_datebox.datebox('getValue');
            criteria.end_publish_time = end_datebox.datebox('getValue');
            var order = $('#talk_order_bar [name="order"]').val();
            talk_grid.datagrid('load',{criteria: criteria, order: order});
        });

        //回复列表按钮点击事件
        $(document).on('click', '.talk-reply-list-btn', function(event){
            var talk_id = $(this).attr('data-id');
            var opt = reply_grid.datagrid('options');
            opt.url = '/talk/' + talk_id + '/reply_list.json';
            reply_grid.datagrid(opt);
            talk_reply_list_window.window('open');
        });

        //回复按钮点击事件
        $(document).on('click', '.talk-reply-btn', function(event){
            var talk_id = $(this).attr('data-id');
            talk_reply_window.find('[name="id"]').val(talk_id);
            talk_reply_window.dialog('open');
        });

        //删除回复按钮点击事件
        $(document).on('click', '.talk-reply-delete-btn', function(event){
            var talk_id = $(this).attr('data-id');
            $.messager.confirm('删除车友互动', '是否确认删除?', function(is_ok){
                if(!is_ok) return;
                deleteReply(talk_id, function(){
                    reply_grid.datagrid('reload');
                });
            });
        });

        //禁言按钮点击事件
        $(document).on('click', '.no-talk-btn', function(event){
            var user_id = $(this).attr('data-user_id');
            $(no_talk_window).find('[name=user_id]').val(user_id);
            no_talk_window.dialog('open');
        });

        //车友互动删除按钮点击事件
        $(document).on('click', '.talk-delete-btn', function(event){
            var talk_id = $(this).attr('data-id');
            $.messager.confirm('删除车友互动', '是否确认删除?', function(is_ok){
                if(!is_ok) return;
                deleteTalk(talk_id, function(){
                    talk_grid.datagrid('reload');
                });
            });
        });

        //车友互动发布按钮点击事件
        $(document).on('click', '.talk-state-on-btn', function(event){
            var talk_id = $(this).attr('data-id');
            changeTalkState(talk_id, 1, function(){
                talk_grid.datagrid('reload');
            });
        });

        //车友互动取消发布点击事件
        $(document).on('click', '.talk-state-off-btn', function(event){
            var talk_id = $(this).attr('data-id');
            changeTalkState(talk_id, 0, function(){
                talk_grid.datagrid('reload');
            });
        });

        //启用回复
        $(document).on('click', '.talk-noreply-off-btn', function(event){
            var talk_id = $(this).attr('data-id');
            changeTalkNoReply(talk_id, 0, function(){
                talk_grid.datagrid('reload');
            });
        });

        //禁用回复
        $(document).on('click', '.talk-noreply-on-btn', function(event){
            var talk_id = $(this).attr('data-id');
            changeTalkNoReply(talk_id, 1, function(){
                talk_grid.datagrid('reload');
            });
        })




        //改变车友互动发布状态
        function changeTalkState(talk_id, new_state, callback)
        {
            $.ajax({
                url: '/talk/' + talk_id + '/state/' + new_state + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                var msg = null;
                if(new_state == 1){
                    msg = '发布';
                }
                else
                {
                    msg = '取消发布';
                }

                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: msg + '失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: msg + '成功'
                    });

                    if(typeof callback == 'function')
                    {
                        callback(data);
                    }
                }
            });
        }

        //禁止或启用回复
        function changeTalkNoReply(talk_id, no_reply, callback)
        {
            $.ajax({
                url: '/talk/' + talk_id + '/no_reply/' + no_reply + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                var msg = null;

                if(no_reply)
                {
                    msg = '禁用回复';
                }
                else
                {
                    msg = '启用回复';
                }

                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: msg + '失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: msg + '成功'
                    });

                    if(typeof callback == 'function')
                    {
                        callback(data);
                    }
                }
            });
        }
        
        //删除车友互动
        function deleteTalk(id, callback)
        {
            $.ajax({
                url: '/talk/' + id + '.json',
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '车友互动删除失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '车友互动删除成功'
                    });
                    if(typeof callback == 'function')
                    {
                        callback(data);
                    }
                }
            });
        }

        //删除回复
        function deleteReply(reply_id, callback)
        {
            $.ajax({
                url: '/talk/reply/' + reply_id + '.json',
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '回复删除失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '回复删除成功'
                    });
                    if(typeof callback == 'function')
                    {
                        callback(data);
                    }
                }
            });
        }

        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            talk_reply_list_window.window('destroy');
            talk_reply_window.dialog('destroy');
            no_talk_window.dialog('dialog');

            //销毁时间控件
            start_datebox.datebox('destroy');
            end_datebox.datebox('destroy');

            //清除动态绑定事件
            $(document).off('click', '.talk-reply-list-btn');
            $(document).off('click', '.talk-reply-delete-btn');
            $(document).off('click', '.talk-reply-btn');
            $(document).off('click', '.no-talk-btn');
            $(document).off('click', '.talk-delete-btn');
            $(document).off('click', '.talk-state-on-btn');
            $(document).off('click', '.talk-state-off-btn');
            $(document).off('click', '.talk-noreply-on-btn');
            $(document).off('click', '.talk-noreply-off-btn');
        };
    };
</script>
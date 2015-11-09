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
                <div id="push_grid_tb">
                    <div class="row-fluid" id="push_search_bar">
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">用户名</span>
                                <input type="text" name="user_id" class="input-medium" />
                            </div>
                            <div class="span4">
                                <span class="label">姓名</span>
                                <input type="text" name="user_name" class="input-medium" />
                            </div>
                            <div class="span4">
                                <span class="label">手机号</span>
                                <input type="text" name="phone" class="input-medium" />
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">登录时间</span>
                                <input type="text" name="login_start_date" class="input-small">-
                                <input type="text" name="login_end_date" class="input-small">
                            </div>
                            <div class="span4">
                                <span class="label">客户端</span>
                                <select name="client_type" class="input-medium">
                                    <option value="">全部</option>
                                    {% for client_type in client_type_list %}
                                    <option value="{{ client_type['client_type'] }}">{{ client_type['client_type'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            <div class="span2">
                                <button class="btn btn-primary" id="push_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                            <div class="span2">
                                <button class="btn btn-warning" id="push_btn"><i class="iconfa-envelope"></i>推送</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="push_grid"></div>
            </div>
            <div id="push_window">
                <div id="push_form">
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">消息类别</span>
                            <select name="msg_type">
                                <option value="1">热门活动</option>
                                <option value="2">订单消息</option>
                                <option value="3">系统消息</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">标题</span>
                            <input type="text" name="title">
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">内容</span>
                            <textarea name="content" id="push_content" cols="30" rows="10"></textarea>
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
        
        //创建ckeditor
        var content_editor = CKEDITOR.replace( 'push_content', {
            enterMode: CKEDITOR.ENTER_P,
            height: 270,
            removePlugins : 'save'
            //filebrowserImageUploadUrl : 'ckUploadImage?command=QuickUpload&type=Images'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(content_editor, finder_path);

        //时间控件
        var login_start_datebox = $('#push_grid_tb [name="login_start_date"]').datebox({editable: false});
        var login_end_datebox = $('#push_grid_tb [name="login_end_date"]').datebox({editable: false});

        //数据表格
        var push_grid = $('#push_grid').datagrid({
            url: '/user/list.json',
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
            toolbar: '#push_grid_tb',
            idField: 'id',
            columns:[[
                {field:'user_id', title:'用户名', width:'20%', align:'center'},
                {field:'user_name', title:'姓名', width:'15%', align:'center'},
                {field:'phone', title:'手机号', width:'20%', align:'center'},
                {field:'create_date', title:'注册时间', width:'15%', align:'center'},
                {field:'last_login_time', title:'最后登录时间', width:'15%', align:'center'},
                {field:'client_type', title:'客户端', width:'15%', align:'center', formatter: function(value, row, index){
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

        var push_window = $('#push_window').dialog({
            title: '消息推送',
            iconCls: 'icon-envelope',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '推送',
                    handler: function(){
                        var title = $('#push_window [name="title"]').val();
                        var content = content_editor.getData();
                        var msg_type = $('#push_window [name="msg_type"]').val();

                        var user_criteria = {};
                        user_criteria.user_id = $('#push_search_bar [name="user_id"]').val();
                        user_criteria.user_name = $('#push_search_bar [name="user_name"]').val();
                        user_criteria.phone = $('#push_search_bar [name="phone"]').val();
                        user_criteria.hphm = $('#push_search_bar [name="hphm"]').val();
                        user_criteria.client_type = $('#push_search_bar [name="client_type"]').val();
                        user_criteria.login_start_date = login_start_datebox.datebox('getValue');
                        user_criteria.login_end_date = login_end_datebox.datebox('getValue');

                        $.ajax({
                            url: '/push_message.json',
                            method: 'POST',
                            data: {criteria: {title: title, content: content, msg_type: msg_type}, user_criteria: user_criteria},
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '推送失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '推送成功'
                                });
                            }
                        });
                        push_window.window('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        push_window.window('close');
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
        $('#push_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#push_search_bar [name="user_id"]').val();
            criteria.user_name = $('#push_search_bar [name="user_name"]').val();
            criteria.phone = $('#push_search_bar [name="phone"]').val();
            criteria.hphm = $('#push_search_bar [name="hphm"]').val();
            criteria.client_type = $('#push_search_bar [name="client_type"]').val();
            criteria.login_start_date = login_start_datebox.datebox('getValue');
            criteria.login_end_date = login_end_datebox.datebox('getValue');
            push_grid.datagrid('load',{criteria: criteria});
        });

        //推送按钮点击事件
        $('#push_btn').on('click', function(event){
            push_window.window('open').window('center');
        });


        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            push_window.dialog('destroy');

            //销毁时间控件
            login_start_datebox.datebox('destroy');
            login_end_datebox.datebox('destroy');

            //清除动态绑定事件
        };
    };
</script>
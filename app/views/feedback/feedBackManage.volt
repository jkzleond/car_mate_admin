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
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">意见反馈信息</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="feedback_grid_tb">
                    <div class="row-fluid">
                        <div class="span3">
                            <span class="label">用户ID</span>
                            <input type="text" name="user_id"/>
                        </div>
                        <div class="span3">
                            <span class="label">用户姓名</span>
                            <input type="text" name="uname"/>
                        </div>
                        <div class="span3">
                            <span class="label">电话</span>
                            <input type="text" name="phone"/>
                        </div>
                        <div class="span2">
                            <button id="feedback_search_btn" class="btn btn-primary pull-left"><i class="iconfa-search"></i>查找</button>
                        </div>
                    </div>
                </div>
                <div id="feedback_grid"></div>
            </div>
        </div>
    </div>
    <div id="feedback_reply_window">
        <div class="row-fluid">
            <span class="label">反馈内容</span>
            <pre id="feedback_content"></pre>
        </div>
        <div class="row-fluid">
            <span class="label">回复内容</span>
            <pre id="feedback_reply_content"></pre>
        </div>
        <div class="row-fluid" id="feedback_replay_container">
            <div class="span12">
                <span class="label">回复内容</span>
                <input type="hidden" name="id">
                <textarea name="content" cols="50" rows="10" class="input-xxlarge"></textarea>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */
        var feedback_reply_window = $('#feedback_reply_window').dialog({
            title: '意见反馈回复',
            iconCls: 'iconfa-comment',
            width: 560,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '回复',
                    handler: function(){
                        var feedback_id = feedback_reply_window.find('[name="id"]').val();
                        var content = feedback_reply_window.find('[name="content"]').val();
                        $.ajax({
                            url: '/feedBack/' + feedback_id + '/reply.json',
                            method: 'POST',
                            data: {content: content},
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '意见反馈回复失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '意见反馈回复成功'
                                });
                                feedback_grid.datagrid('reload');
                            }
                        });
                        feedback_reply_window.window('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        feedback_reply_window.window('close');
                    }
                }
            ],
            onClose: function(){
                feedback_reply_window.find('[name="id"]').val('');
                feedback_reply_window.find('[name="content"]').val('');
            }
        });

        //意见反馈列表
        var feedback_grid = $('#feedback_grid').datagrid({
            url: '/feedBackList.json',
            title: '意见反馈列表',
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
            toolbar: '#feedback_grid_tb',
            columns:[[
                {field:'province', title:'省份', width:'5%', align:'center'},
                {field:'user_id', title:'用户ID', width:'15%', align:'center'},
                {field:'uname', title:'用户姓名', width:'5%', align:'center'},
                {field:'contents', title:'意见反馈内容', width:'27%', align:'center'},
                {field:'reply_contents', title:'回复内容', width:'27%', align:'center'},
                {field:'create_date', title:'发生时间', width:'10%', align:'center'},
                {field:'id',title:'操作',width:'10%',align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    return '<div class="btn-group"><button class="btn btn-mini btn-info feedback-reply-btn" title="回复" data-id="' + value + '"><i class="iconfa-comment"></i></button></div><button class="btn btn-mini btn-danger feedback-del-btn" title="删除" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        /**
         * 事件相关
         */
        //查看意见反馈回复按钮点击事件
        $(document).on('click', '.feedback-reply-btn', function(event){
            var feedback = feedback_grid.datagrid('getSelected');
            $('#feedback_content').text(feedback.contents);
            $('#feedback_reply_content').text(feedback.reply_contents || '');
            feedback_reply_window.find('[name=id]').val(feedback.id);
            feedback_reply_window.window('open').window('center');
        });

        //
        $(document).on('click', '.feedback-del-btn', function(event){
            var id = $(this).attr('data-id');

            $.messager.confirm('删除意见反馈信息', '是否删除意见反馈信息', function(is_ok){
                if(is_ok)
                {
                    $.ajax({
                        url: '/feedBack/' + id + '.json' ,
                        method: 'DELETE',
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(!data.success) return;

                        $.messager.show({
                            title: '系统消息',
                            msg: '意见反馈信息删除成功'
                        });

                        feedback_grid.datagrid('reload');
                    });
                }
            });
        });

        //查找按钮点击事件
        $('#feedback_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#feedback_grid_tb [name="user_id"]').val();
            criteria.uname = $('#feedback_grid_tb [name="uname"]').val();
            criteria.phone = $('#feedback_grid_tb [name="phone"]').val();

            feedback_grid.datagrid('load', {criteria: criteria});
        });

        /**
         * 页面离开时事件
         */

        this.on_leave = function(){

            //销毁窗口
            feedback_reply_window.window('destroy');
            //清除动态绑定事件
            $(document).off('click', '.feedback-reply-btn');
            $(document).off('click', '.feedback-del-btn');
        };
    };


</script>
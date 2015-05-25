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
        <h4 class="widgettitle">异常信息</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="exception_grid"></div>
            </div>
        </div>
    </div>
    <div id="app_exception_window">
        <pre id="app_exception_content"></pre>
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */

        //异常内容显示窗口
        var exception_window = $('#app_exception_window').window({
            title: '异常内容详情',
            iconCls: 'icon-eye-open',
            width: '80%',
            height: 500,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //异常列表
        var exception_grid = $('#exception_grid').datagrid({
            url: '/appExceptionList.json',
            title: '奖品列表',
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
            columns:[[
                {field:'provinceName', title:'省份', width:'5%', align:'center'},
                {field:'userId', title:'用户ID', width:'15%', align:'center'},
                {field:'uName', title:'用户姓名', width:'5%', align:'center'},
                {field:'content', title:'异常内容', width:'55%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    return '<pre style="height: 200px; background: none;">' + value + '</pre>';
                }},
                {field:'occurTime', title:'发生时间', width:'10%', align:'center',formatter: function(value, row, index){
                    if(!value) return '';
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'id',title:'操作',width:'10%',align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    return '<div class="btn-group"><button class="btn btn-mini btn-info exception-view-btn" data-id="' + value + '"><i class="icon-eye-open"></i></button></div><button class="btn btn-mini btn-danger exception-del-btn" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        /**
         * 事件相关
         */

        //查看异常详情按钮点击事件
        $(document).on('click', '.exception-view-btn', function(event){
            var exception = exception_grid.datagrid('getSelected');
            $('#app_exception_content').text(exception.content);
            exception_window.window('open').window('center');
        });

        //
        $(document).on('click', '.exception-del-btn', function(event){
            var id = $(this).attr('data-id');

            $.messager.confirm('删除异常信息', '是否删除异常信息', function(is_ok){
                if(is_ok)
                {
                    $.ajax({
                        url: '/appException/' + id + '.json' ,
                        method: 'DELETE',
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(!data.success) return;

                        $.messager.show({
                            title: '系统消息',
                            msg: '异常信息删除成功'
                        });

                        exception_grid.datagrid('reload');
                    });
                }
            });
        });


        /**
         * 页面离开时事件
         */

        this.on_leave = function(){

            //销毁窗口
            exception_window.window('destroy');
            //清除动态绑定事件
            $(document).off('click', '.exception-view-btn');
            $(document).off('click', '.exception-del-btn');
        };
    };


</script>
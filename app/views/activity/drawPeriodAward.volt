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
    <div class="row-fluid">
        <div id="draw_period_award_grid_tb">
            <div class="row-fluid">
                <div class="span4">
                    <span class="label">活动名称</span>
                    <span>{{ activity.name }}</span>
                </div>
                <div class="span4">
                    <span class="label">时段</span>
                    <span>{{ period.start_time }}至{{ period.end_time }}</span>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <select name="award_id"></select>    
                </div>
                <div class="span4">
                    <button id="draw_period_award_add_btn" class="btn" style="display:none"><i class="iconfa-plus"></i>添加奖品</button>
                </div>
            </div>
        </div>
        <div id="draw_period_award_grid"></div>
    </div>
</div>

<script type="text/javascript">
    //页面加载完成事件
    $(document).one('pageLoad:drawPeriodAward', function(event){

        var aid = {{ activity.id }};
        var period_id = {{ period.id }};

        /*
            表格
         */
        var period_award_grid = $('#draw_period_award_grid').datagrid({
            url: '/activity/period/' + period_id +'/awardList.json',
            title: '抽奖时段奖品列表',
            iconCls: 'icon-list',
            width: '100%',
            height: '650',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            idField: 'id',
            toolbar: '#draw_period_award_grid_tb',
            columns:[[
                {field:'award_id', title:'图片', width:'20%', align:'center', formatter: function(value, row, index){
                    if(!value) return '无';
                    return '<img src="/award/' + value + '/pic.png" alt="奖品图片"/>';
                }},
                {field:'name', title:'名称', width:'15%', align:'center'},
                {field:'des', title:'说明', width:'15%', align:'center'},
                {field:'num', title:'数量', width:'15%', align:'center'},
                {field:'rate', title:'概率(万分之一)', width:'15%', align:'center'},
                {field:'id', title:'操作', width:'20%', align:'center', formatter: function(value, row, index){
                    return '<div class="btn-group"><button class="btn btn-mini btn-danger draw-period-award-del-btn" title="删除" data-id="' + row.id +'"><i class="icon-trash"></i></button></div>';

                }}
            ]]
        });
        
        //奖品选择框
        var award_combogrid = $('#draw_period_award_grid_tb [name="award_id"]').combogrid({
            url: '/awardList.json',
            queryParams: {aid: aid},
            title: '奖品列表',
            panelWidth: '700',
            panelHeight: '300',
            idField: 'id',
            textField: 'name',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            columns:[[
                {field:'pic', title:'图片', width:'20%', align:'center', formatter: function(value, row, index){
                    if(!value) return '无';
                    return '<img src="data:image/png;base64,' + value + '" alt="奖品图片"/>';
                }},
                {field:'name', title:'名称', width:'15%', align:'center'},
                {field:'des', title:'说明', width:'15%', align:'center'},
                {field:'num', title:'数量', width:'15%', align:'center'},
                {field:'rate', title:'概率(万分之一)', width:'15%', align:'center'},
                {field:'state',title:'当前状态',width:'10%',align:'center', formatter: function(value, row, index){
                    if(value === 0 || value === '0')
                    {
                        return '<span class="label label-success">未领完</span>';
                    }
                    else if(value == 1)
                    {
                        return '<span class="label label-important">已领完</span>';
                    }
                }}    
            ]],
            onChange: function(){
                $('#draw_period_award_add_btn').show();
            }
        });

        /*
            事件相关
         */
        //添加奖品按钮点击事件
        $('#draw_period_award_add_btn').click(function(event){
            event.preventDefault();
            var data = {};
            data.aid = aid;
            data.period_id = period_id;
            data.award_id = award_combogrid.combogrid('getValue');
            addPeriodAward(data);
            return false;
        });

        //删除奖品按钮点击事件
        $(document).on('click', '.draw-period-award-del-btn', function(event){
            event.preventDefault();
            var id = $(this).attr('data-id');
            delPeriodAward({id: id});
            return false;
        });


        /*
            数据相关
         */
        
        function addPeriodAward(data, callback)
        {
            $.ajax({
                url: '/activity/period/' + period_id + '/award.json',
                method: 'POST',
                data: {creates: [data]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段添加失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段添加成功'
                    });
                }
                period_award_grid.datagrid('reload');
            });
        }

        function delPeriodAward(data, callback)
        {
            $.ajax({
                url: '/activity/periodAward/' + data.id +'.json',
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段删除失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '抽奖时段删除成功'
                    });
                }
                period_award_grid.datagrid('reload');
            });
        }

        //页面离开时事件
        $(document).one('pageLeave:drawPeriodAward', function(event){
            //销毁combogrid
            award_combogrid.combogrid('destroy');
            //清除动态绑定事件
            $(document).off('click', '.draw-period-award-del-btn');
        });
    });
    
</script>


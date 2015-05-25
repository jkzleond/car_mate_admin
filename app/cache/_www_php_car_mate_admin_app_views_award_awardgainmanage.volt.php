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
        <h4 class="widgettitle">中奖管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="award_gain_grid_tb">
                    <div class="row-fluid">
                        <div class="span3">
                            <span class="label">用户ID</span>
                            <input type="text" name="user_id" class="input-medium">
                        </div>
                        <div class="span3">
                            <div class="span12">
                                <span class="label">活动</span>
                                <select name="aid" id="award_gain_activity_list"></select>
                            </div>
                        </div>
                        <div class="span3">
                            <span class="label">奖品</span>
                            <select name="award_id" id="award_gain_award_list"></select>
                        </div>
                        <div class="span2">
                            <span class="label">状态</span>
                            <select name="state" class="input-small">
                                <option value="">请选择状态</option>
                                <option value="0">未领取</option>
                                <option value="1">已领取</option>
                            </select>
                        </div>
                        <div class="span1">
                            <button id="award_gain_search_btn" class="btn btn-primary"><i class="iconfa-search"></i>查找</button>
                        </div>
                    </div>
                    <div class="row-fluid hidden-none" id="award_gain_create_grp">
                        <div class="span3">
                            <span class="label">中奖时间</span>
                            <input id="award_gain_win_date" type="text" name="win_date" class="input-medium"/>
                        </div>
                        <div class="span3">
                            <button id="award_gain_create_btn" class="btn btn-primary"><i class="iconfa-plus"></i>添加中奖记录</button>
                        </div>
                    </div>
                </div>
                <div id="award_gain_grid"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    /**
     *页面加载时事件
     **/
    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         **/

        //活动选择框
        var activity_select = $('#award_gain_activity_list').combogrid({
            url: '/awardActivityList.json',
            title: '活动列表',
            width: 200,
            height: 30,
            panelWidth: 300,
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
                var aid = new_value;
                award_select
                    .combogrid('reset')
                    .combogrid('grid')
                    .datagrid({
                    url: '/awardList.json',
                    queryParams: {
                        aid: new_value
                    }
                });
                return new_value;
            },
            columns:[[
                {field:'name', title:'活动名称', width:'90%', align:'center'}
            ]]
        });

        //奖品combogrid
        var award_select = $('#award_gain_award_list').combogrid({
            title: '奖品列表',
            width: 200,
            height: 30,
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
            //pageList:[50,100,150,200],
            onChange: function(new_value, old_value){
                $('#award_gain_create_grp').show();
                return new_value;
            },
            columns:[[
                {field:'name', title:'名称', width:'20%', align:'center'},
                {field:'des', title:'说明', width:'10%', align:'center'},
                {field:'pic', title:'图片', width:'30%', align:'center', formatter: function(value, row, index){
                    if(!value) return '无';
                    return '<img  src="data:image/png;base64,' + value + '" alt="奖品图片"/>';
                }},
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
            ]]
        });

        //获奖信息表格
        var award_gain_grid = $('#award_gain_grid').datagrid({
            url: '/awardGainList.json',
            title: '中奖概况列表',
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
            view: $.fn.datagrid.defaults.detailview,
            toolbar: '#award_gain_grid_tb',
            detailFormatter:function(index,row){
                return '<div style="padding:2px"><table class="ddv"></table></div>';
            },
            onExpandRow: function(index, row){
                var ddv = $(this).datagrid('getRowDetail',index).find('table.ddv');
                var subgrid_index = index;
                ddv.datagrid({
                    url:'/awardUserGainList.json',
                    queryParams: {
                        user_id: row.userid,
                        criteria : {
                            aid: row.aid,
                            awid: row.awid,
                            state: row.state
                        }
                    },
                    title: '中奖明细',
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
                    columns:[[
                        {field:'awardName', title:'奖品名称', width:'15%'},
                        {field:'winDate', title:'中奖时间', width:'15%', align:'center', formatter: function(value, row, index){
                            if(!value) return '';
                            var conv = CarMate.utils.date.mssqlToJs(value);
                            return CarMate.utils.date('Y-m-d H:i:s', conv);
                        }},
                        {field:'gainDate', title:'领奖时间', width:'15%', align:'center', formatter: function(value, row, index){
                            if(!value) return '';
                            var conv = CarMate.utils.date.mssqlToJs(value);
                            return CarMate.utils.date('Y-m-d H:i:s', conv);
                        }},
                        {field:'randomCode', title:'领奖验证码', width:'10%', align:'center', formatter: function(value, row, index){
                            if(!value || value === 'null') return '';
                            var color = row.randomColor || '#000';
                            return '<span style="color:' + color + '">' + value + '</span>';
                        }},
                        {field:'state', title:'状态', width:'5%', align:'center', formatter: function(value, row, index){
                            if(value == 1)
                            {
                                return '已领取';
                            }
                            else
                            {
                                return '未领取'
                            }
                        }},
                        {field:'id', title:'操作', width:'10%', align:'center', formatter: function(value, row, index){
                            if(!value) return;
                            if(row.state != 1)
                            {
                                return '<div class="btn-group"><button class="btn btn-mini btn-warning award-gain-btn" data-id="' + row.id + '" data-subgrid-index="' + subgrid_index +'" title="领取"><i class="iconfa-gift"></i></button></div>';
                            }
                        }}
                    ]],
                    onResize:function(){
                        award_gain_grid.datagrid('fixDetailRowHeight',index);
                    },
                    onLoadSuccess:function(){
                        setTimeout(function(){
                            award_gain_grid.datagrid('fixDetailRowHeight',index);
                        },0);
                    }
                });
                award_gain_grid.datagrid('fixDetailRowHeight',index);
            },
            columns:[[
                {field:'userid', title:'用户ID', width:'15%', align:'center'},
                {field:'checkCount', title:'签到抽奖次数', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '0';
                    return value;
                }},
                {field:'wishCount', title:'愿望抽奖次数', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '0';
                    return value;
                }},
                {field:'totalChance', title:'总抽奖次数', width:'10%', align:'center',formatter: function(value, row, index){
                    if(!value) return '0';
                    return value;
                }},
                {field:'winNum', title:'中奖次数', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '0';
                    return value;
                }},
                {field:'activityName',title:'活动名称',width:'40%',align:'center'}
            ]]
        });

        //中奖日期datebox
        var win_datebox = $('#award_gain_win_date').datetimebox({
            editable: false
        });

        /**
         * 事件相关
         */

        //查找按钮点击事件
        $('#award_gain_search_btn').click(function(event){
            var award_id = award_select.combogrid('getValue');
            var aid = activity_select.combogrid('getValue');
            var state = $('#award_gain_grid_tb [name="state"]').val();
            var user_id = $('#award_gain_grid_tb [name="user_id"]').val();
            award_gain_grid.datagrid('load', {
                criteria: {
                    award_id: award_id,
                    aid: aid,
                    state: state,
                    user_id: user_id
                }
            });
        });

        //添加中奖记录按钮点击事件
        $('#award_gain_create_btn').click(function(event){

            var aid = activity_select.combogrid('getValue');
            var award_id = award_select.combogrid('getValue');
            var user_id = $('#award_gain_grid_tb [name="user_id"]').val();
            var win_date = win_datebox.datetimebox('getValue');

            $.ajax({
                url: '/awardUserGain.json',
                method: 'POST',
                data: {
                    aid: aid,
                    award_id: award_id,
                    user_id: user_id,
                    win_date: win_date
                },
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: data.msg
                    });
                }
                else
                {
                    $('#award_gain_search_btn').click();
                    $.messager.show({
                        title: '系统消息',
                        msg: '中奖记录添加成功'
                    });
                }
            });

        });

        //领取按钮点击事件
        $(document).on('click', '.award-gain-btn', function(event){
            var id = $(this).attr('data-id');
            var subgird_index = $(this).attr('data-subgrid-index');

            $.messager.confirm('确定领取', '请确认用户信息正确,领取后无法撤消', function(is_ok){
                if(!is_ok) return;

                $.ajax({
                    url:'/awardGain/' + id + '.json',
                    method: 'PUT',
                    dataType: 'json',
                    global: true
                }).done(function(data){
                    if(!data) return;

                    $.messager.show({
                        title: '系统消息',
                        msg: '奖品领取成功'
                    });

                    var subgrid = award_gain_grid.datagrid('getRowDetail', subgird_index).find('table.ddv');
                    subgrid.datagrid('reload');
                });
            });
        });

        /**
         * 页面离开时事件
         */
        this.on_leave = function(){

            //销毁combogrid
            activity_select.combogrid('destroy');
            award_select.combogrid('destroy');

            //销毁datetimebox
            win_datebox.datetimebox('destroy');

            //清除动态绑定事件
            $(document).off('click', '.award-gain-btn');
        };
    };
</script>
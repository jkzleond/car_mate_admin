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
        <h4 class="widgettitle">流水记录</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="transaction_grid_tb">
                    <div class="row-fluid" id="transaction_search_bar">
                        <div class="span3">
                            <span class="label">用户ID</span>
                            <input type="text" name="user_id" />
                        </div>
                        <div class="span2">
                            <span class="label">类型</span>
                            <select name="transaction_type" class="input-small">
                                <option value="">全部</option>
                                <option value="IN">收入</option>
                                <option value="OUT">支出</option>
                            </select>
                        </div>
                        <div class="span2">
                            <span class="label">分配类型</span>
                            <select name="distribute_type" class="input-small">
                                <option value="">全部</option>
                                <?php foreach ($distribute_types as $type) { ?>
                                <option value="<?php echo $type['name']; ?>"><?php echo $type['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="span2">
                            <button class="btn btn-primary" id="transaction_search_btn"><i class="iconfa-search"></i>查找</button>
                        </div>
                    </div>
                </div>
                <div id="transaction_grid"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */

        //流水记录表格
        var transaction_grid = $('#transaction_grid').datagrid({
            url: '/transactionList.json',
            title: '流水记录列表',
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
            toolbar: '#transaction_grid_tb',
            idField: 'id',
            columns:[[
                {field:'userid', title:'用户ID', width:'15%', align:'center'},
                {field:'uname', title:'姓名', width:'10%', align:'center'},
                {field:'nickname', title:'昵称', width:'10%', align:'center'},
                {field:'num', title:'金币数目', width:'10%', align:'center'},
                {field:'transactionType', title:'交易类型', width:'10%', align:'center', formatter: function(value, row, index){
                    if(value == 'OUT')
                    {
                        return '支出('  + row.expenditureType + ')';
                    }
                    else if(value == 'IN')
                    {
                        return '收入';
                    }
                }},
                {field:'distributeType', title:'分配类型', width:'10%', align:'center', formatter: function(value, row, index){
                    if(value == 'SYSTEM')
                    {
                        return '系统';
                    }
                    else
                    {
                        return value + row.distributor;
                    }
                }},
                {field:'createDate', title:'时间', width:'15%', align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'des', title:'备注', width:'20%', align:'center'}
            ]]
        });

        /**
         * 事件相关
         */

        $('#transaction_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#transaction_search_bar [name="user_id"]').val();
            criteria.transaction_type = $('#transaction_search_bar [name="transaction_type"]').val();
            criteria.distribute_type = $('#transaction_search_bar [name="distribute_type"]').val();

            transaction_grid.datagrid('load', {criteria: criteria});
        });

        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){

        };
    };
</script>
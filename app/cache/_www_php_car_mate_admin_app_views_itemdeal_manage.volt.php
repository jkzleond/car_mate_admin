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
        <h4 class="widgettitle">订单管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="deal_grid_tb">
                    <div class="row-fluid" id="deal_search_bar">
                        <div class="span3">
                            <span class="label">用户ID</span>
                            <input type="text" name="user_id" />
                        </div>
                        <div class="span2">
                            <span class="label">商品类型</span>
                            <select name="visual" class="input-small">
                                <option value="">全部</option>
                                <option value="0">实物</option>
                                <option value="1">虚拟</option>
                            </select>
                        </div>
                        <div class="span2">
                            <span class="label">状态</span>
                            <select name="state" class="input-small">
                                <option value="">全部</option>
                                <option value="0">未发货</option>
                                <option value="1">已发货</option>
                                <option value="2">已签收</option>
                            </select>
                        </div>
                        <div class="span2">
                            <button class="btn btn-primary" id="deal_search_btn"><i class="iconfa-search"></i>查找</button>
                        </div>
                    </div>
                </div>
                <div id="deal_grid"></div>
                <div id="deal_deliver_window">
                    <form id="deal_deliver_form" action="">
                        <div class="row-fluid">
                            <div class="span5">
                                <input type="hidden" name="id"/>
                                <span class="label">物流</span>
                                <select name="comp_id" id="" class="input-medium">
                                    <?php foreach ($company_list as $company) { ?>
                                    <option value="<?php echo $company['id']; ?>"><?php echo $company['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="span7">
                                <span class="label">单号</span>
                                <input type="text" name="order_no"/>
                            </div>
                        </div>
                    </form>
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

        //流水记录表格
        var deal_grid = $('#deal_grid').datagrid({
            url: '/dealList.json',
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
            toolbar: '#deal_grid_tb',
            idField: 'id',
            columns:[[
                {field:'id', title:'ID', width:'10%', align:'center'},
                {field:'userid', title:'用户ID', width:'10%', align:'center'},
                {field:'trueName', title:'姓名', width:'5%', align:'center'},
                {field:'itemName', title:'商品', width:'20%', align:'center'},
                {field:'goldPrice', title:'对换币值', width:'5%', align:'center'},
                {field:'state', title:'当前状态', width:'10%', align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '已发货';
                    }
                    else if(value == 2)
                    {
                        return '已签收';
                    }
                    else
                    {
                        return '未发货';
                    }
                }},
                {field:'phone', title:'电话', width:'10%', align:'center'},
                {field:'address', title:'地址', width:'20%', align:'center', formatter: function(value, row, index){
                    var addr_str = '';

                    if(row.province)
                    {
                        addr_str += row.province;
                    }

                    if(row.city)
                    {
                        addr_str += row.city;
                    }

                    if(row.area)
                    {
                        addr_str += row.area;
                    }

                    if(row.address)
                    {
                        addr_str += row.address;
                    }

                    return addr_str;
                }},
                {field:'rownum', title:'操作', width:'10%', align:'center', formatter: function(value, row, index){
                    if(row.state > 0) return;
                    return '<button class="btn btn-primary deal-deliver-btn" title="发货" data-id="' + row.id + '"><i class="iconfa-truck"></i></button>';
                }}
            ]]
        });

        //发货窗口
        var deal_deliver_dialog = $('#deal_deliver_window').dialog({
            title: '发货',
            iconCls: 'iconfa-truck',
            width: 500,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '发货',
                    iconCls: 'icon-ok',
                    handler: function(){
                        var id = $('#deal_deliver_form [name="id"]').val();
                        var comp_id = $('#deal_deliver_form [name="comp_id"]').val();
                        var order_no = $('#deal_deliver_form [name="order_no"]').val();

                        if(order_no)
                        {
                            $.ajax({
                                url: '/dealDeliver/' + id + '.json',
                                method: 'PUT',
                                data: {comp_id: comp_id, order_no: order_no},
                                dataType: 'json',
                                global: true
                            }).done(function(data){
                                if(!data.success) return;
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '发货成功'
                                });
                                deal_grid.datagrid('reload');
                            });
                            deal_deliver_dialog.dialog('close');
                        }
                        else
                        {
                            $.messager.alert('警告', '请填写订单号');
                        }
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        item_type_cu_dialog.dialog('close');
                    }
                }
            ]
        });

        /**
         * 事件相关
         */

        //订单查找点击事件
        $('#deal_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#deal_search_bar [name="user_id"]').val();
            criteria.visual = $('#deal_search_bar [name="visual"]').val();
            criteria.state = $('#deal_search_bar [name="state"]').val();

            deal_grid.datagrid('load', {criteria: criteria});
        });

        //发货按钮点击事件
        $(document).on('click', '.deal-deliver-btn', function(event){
            var deal = deal_grid.datagrid('getSelected');
            if(deal.visual == 1)
            {
                $.messager.confirm('确定发货', '确定为该订单发货?', function(is_ok){
                    if(!is_ok) return;

                    $.ajax({
                        url: '/dealDeliver/' + deal.id + '.json',
                        method: 'PUT',
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(!data.success) return;
                        $.messager.show({
                            title: '系统消息',
                            msg: '发货成功'
                        });
                        deal_grid.datagrid('reload');
                    });
                });
            }
            else
            {
                $('#deal_deliver_form [name="id"]').val(deal.id);
                deal_deliver_dialog.dialog('open').dialog('center');
            }
        });

        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            deal_deliver_dialog.dialog('destroy');

            //清除动态绑定事件
            $(document).off('click', '.deal-deliver-btn');
        };
    };
</script>
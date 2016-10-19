<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">保险订单管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="insurance_newinfo_grid_tb">
                    <div class="row-fluid" id="insurance_newinfo_search_bar">
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">用户名</span>
                                <input type="text" name="user_id" class="input-medium" />
                            </div>
                            <div class="span4">
                                <span class="label">手机号</span>
                                <input type="text" name="phone" class="input-small" />
                            </div>
                            <div class="span4">
                                <span class="label">时间段</span>
                                <input type="text" name="start_date" class="input-small">-
                                <input type="text" name="end_date" class="input-small">
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">保险公司</span>
                                <select name="company_id" class="input">
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">险种</span>
                                <select name="type_id" class="input">
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">保单号</span>
                                <select name="policy_no" class="input">
                                </select>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">状态</span>
                                <select name="state" class="input-mini">
                                    <option value="">全部</option>
                                    <option value="1">已初算</option>
                                    <option value="2">待精算</option>
                                    <option value="3">已精算</option>
                                    <option value="4">待出单</option>
                                    <option value="5">已出单</option>
                                </select>
                            </div>
                            <div class="span4">
                                <span class="label">支付状态</span>
                                <select name="pay_state" class="input-mini">
                                    <option value="">全部</option>
                                    <option value="1">未支付</option>
                                    <option value="2">已支付</option>
                                </select>
                            </div>
                            <div class="span4">
                                <button class="btn btn-primary" id="insurance_newinfo_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="insurance_newinfo_grid"></div>
            </div>
            <div id="insurance_newinfo_detail_window"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */
        //时间控件
        var start_datebox = $('#insurance_newinfo_grid_tb [name="start_date"]').datebox({editable: false});
        var end_datebox = $('#insurance_newinfo_grid_tb [name="end_date"]').datebox({editable: false});

        //combogrid
        var company_combogrid = $('#insurance_newinfo_search_bar [name=company_id]').combogrid({
            url: 'insuranceCompanyList.json',
            title: '保险公司列表',
            width: 240,
            panelWidth: 300,
            panelHeight: 350,
            idField: 'companyId',
            textField: 'companyName',
            fitColumns: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            columns: [[
                {field:'shortName',title:'简称',width:'20%',align:'center'},
                {field:'companyName', title:'全称', width:'75%', align:'center'},
            ]]
        });

        var insurance_type_combogrid = $('#insurance_newinfo_search_bar [name="type_id"]').combogrid({
            url: '/insurance/insuranceTypeList.json',
            title: '本地惠列表',
            width: 240,
            panelWidth: 350,
            panelHeight: 350,
            idField: 'id',
            textField: 'name',
            fitColumns: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            columns: [[
                {field:'cates', title:'类目', width:'50%', align:'center'},
                {field:'name',title:'名称',width:'50%',align:'center'}
            ]]
        });

        //窗口
        var newinfo_detail_window = $('#insurance_newinfo_detail_window').window({
            title: '保险订单明细(全险种)',
            iconCls: 'icon-info-sign',
            width: '90%',
            height: '90%',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            method: 'GET',
            onOpen: function(){
                // $(this).window('resize', {
                //     width: 'auto',
                //     height: 'auto'
                // });
                $(this).window('center');
            },
            onLoad: function(){

                var resize_width = 'auto';
                var resize_height = 'auto';

                var $panel = $(this).window('panel');
                var panel_width = $panel.outerWidth();
                var panel_height = $panel.outerHeight();
                var client_width = window.innerWidth;
                var client_height = window.innerHeight;

                if(client_width < panel_width)
                {
                    resize_width = Math.round(client_width * 0.95);
                }

                if(client_height < panel_height)
                {
                    resize_height = Math.round(client_height * 0.95);
                }

                $(this).window('resize', {
                    width: resize_width,
                    height: resize_height
                });

                $(this).window('center');

                $(document).trigger('pageLoad:insuranceNewInfoDetail');
            },
            onBeforeClose: function(){
                $(document).trigger('pageLeave:insuranceNewInfoDetail');
            }
        });

        //表格
        var insurance_newinfo_grid = $('#insurance_newinfo_grid').datagrid({
            url: '/insurance/newInfoList.json',
            title: '保险订单列表列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）,
            toolbar: '#insurance_newinfo_grid_tb',
            frozenColumns:[[
                {field:'id',title:'操作',width:'12%',align:'center', formatter: function(value, row, index){
                    return '<div class="btn-group"><button class="btn btn-info insurance-newinfo-detail-btn" title="明细" data-id="' + value +'">明细</button></div>';
                }},
                {field:'user_id', title:'用户名', width:'15%', align:'center'},
                {field:'phone', title:'电话', width:'10%', align:'center'},
                {field:'user_name', title:'姓名', width:'10%', align:'center'},
                {field:'type_name',title:'险种名称',width:'15%',align:'center'}
            ]],
            columns:[[
                {field:'preliminary_premium',title:'初算保费',width:'10%',align:'center'},
                {field:'policy_fee',title:'出单金额',width:'10%',align:'center'},
                {field:'state',title:'状态',width:'10%',align:'center', formatter: function(value, row, index){
                        if(value == 1)
                        {
                            return '已初算';
                        }
                        else if(value == 2)
                        {
                            return '待精算';
                        }
                        else if(value == 3)
                        {
                            return '已尽算';
                        }
                        else if(value == 4)
                        {
                            return '待出单';
                        }
                        else if(value == 5)
                        {
                            return '已出单';
                        }
                }},
                {field:'pay_state',title:'支付状态',width:'10%',align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '未支付';
                    }
                    else if(value == 2)
                    {
                        return '已支付';
                    }
                }},
                {field:'company_name',title:'保险公司',width:'10%',align:'center'},
                {field:'policy_no',title:'保单号',width:'10%',align:'center'},
                {field:'create_date',title:'提交时间',width:'10%',align:'center'},
                {field:'pay_date',title:'支付时间',width:'10%',align:'center'}
            ]]
        });

        /**
         * 事件相关
         */
        
        //查找按钮点击事件
        $(document).on('click', '#insurance_newinfo_search_btn', function(event){
            event.preventDefault();
            var criteria = {};
            criteria.user_id = $('#insurance_newinfo_search_bar [name="user_id"]').val();
            criteria.phone = $('#insurance_newinfo_search_bar [name="phone"]').val();
            criteria.start_date = start_datebox.datebox('getValue');
            criteria.end_date = end_datebox.datebox('getValue');
            criteria.company_id = company_combogrid.combogrid('getValue');
            criteria.type_id = insurance_type_combogrid.combogrid('getValue');
            criteria.policy_no = $('#insurance_newinfo_search_bar [name="policy_no"]').val();
            criteria.state = $('#insurance_newinfo_search_bar [name="state"]').val();
            criteria.pay_state = $('#insurance_newinfo_search_bar [name="pay_state"]').val();
            insurance_newinfo_grid.datagrid('reload', {criteria: criteria});
            return false;
        })

         //订单明细按钮点击事件
         $(document).on('click', '.insurance-newinfo-detail-btn', function(event){
            var info_id = $(this).attr('data-id');
            var opt = newinfo_detail_window.window('options');
            opt.href = '/insurance/newInfoDetail/' + info_id;
            newinfo_detail_window
                .window(opt)
                .window('setTitle', '保险订单明细(全险种)')
                .window('open');
         });
        

        /**
         * 数据相关
         */

        //页面离开时事件
        CarMate.page.on_leave = function(){
            //销毁窗口
            newinfo_detail_window.window('destroy');

            //销毁时间控件
            start_datebox.datebox('destroy');
            end_datebox.datebox('destroy');

            //销毁combogrid
            company_combogrid.combogrid('destroy');
            insurance_type_combogrid.combogrid('destroy');

            //清除动态绑定事件
            $(document).off('click', '.insurance-newinfo-detail-btn');
            $(document).off('click', '#insurance_newinfo_search_btn');
        };
    };

</script>
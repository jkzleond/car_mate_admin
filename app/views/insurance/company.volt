<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">保险公司管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="insurance_company_grid"></div>
            </div>
        </div>
        <div id="insurance_company_cu_dialog">
            <form id="insurance_company_cu_form" class="form-horizontal" action="">
                <input name="company_id" type="hidden"/>
                <div class="control-group">
                    <span class="control-label">公司名称</span>
                    <div class="controls">
                        <input name="company_name" type="text"/>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">简称</span>
                    <div class="controls">
                        <input name="short_name" type="text"/>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">英文名称</span>
                    <div class="controls">
                        <input name="ename" type="text"/>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">折扣</span>
                    <div class="controls">
                        <input class="numberspinner-float" name="discount" type="text"/>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">车价折扣</span>
                    <div class="controls">
                        <input class="numberspinner-float" name="car_price_discount" type="text"/>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">礼包1</span>
                    <div class="controls">
                        <input class="numberspinner-float" name="gift" type="text"/>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">礼包2</span>
                    <div class="controls">
                        <input class="numberspinner-float" name="gift2" type="text"/>
                    </div>
                </div>
                <div class="control-group">
                    <span class="control-label">礼包3</span>
                    <div class="controls">
                        <input class="numberspinner-float" name="gift3" type="text"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //spinner

        var discount_spinner = $('#insurance_company_cu_form [name="discount"]').numberspinner({
            precision: 2,
            max: 1,
            min: 0.01,
            value: 0.5,
            increment: 0.01,
            width: 60,
            height: 30
        });

        var car_price_discount_spinner = $('#insurance_company_cu_form [name="car_price_discount"]').numberspinner({
            precision: 2,
            max: 1,
            min: 0.01,
            value: 0.5,
            increment: 0.01,
            width: 60,
            height: 30
        });

        var gift_spinner = $('#insurance_company_cu_form [name="gift"]').numberspinner({
            precision: 2,
            max: 1,
            min: 0.01,
            value: 0.5,
            increment: 0.01,
            width: 60,
            height: 30
        });

        var gift2_spinner = $('#insurance_company_cu_form [name="gift2"]').numberspinner({
            precision: 2,
            max: 1,
            min: 0.01,
            value: 0.5,
            increment: 0.01,
            width: 60,
            height: 30
        });

        var gift3_spinner = $('#insurance_company_cu_form [name="gift3"]').numberspinner({
            precision: 2,
            max: 1,
            min: 0.00,
            value: 0.5,
            increment: 0.01,
            width: 60,
            height: 30
        });

        //窗口
        $('#insurance_company_cu_dialog').dialog({
            title: '保险公司添加',
            iconCls: 'icon-plus',
            width: 500,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-ok',
                    handler: function(){

                        var btn_state = $('#insurance_company_cu_dialog').data('state');

                        var company = new InsuranceCompany();
                        var compamy_id = $('#insurance_company_cu_form [name="company_id"]').val();
                        var company_name = $('#insurance_company_cu_form [name="company_name"]').val();
                        var short_name = $('#insurance_company_cu_form [name="short_name"]').val();
                        var ename = $('#insurance_company_cu_form [name="ename"]').val();
                        var discount = discount_spinner.numberspinner('getValue');
                        var car_price_discount = car_price_discount_spinner.numberspinner('getValue');
                        var gift = gift_spinner.numberspinner('getValue');
                        var gift2 = gift2_spinner.numberspinner('getValue');
                        var gift3 = gift3_spinner.numberspinner('getValue');

                        company.set('company_name', company_name);
                        company.set('short_name', short_name);
                        company.set('ename', ename);
                        company.set('discount', discount);
                        company.set('car_price_discount', car_price_discount);
                        company.set('gift', gift);
                        company.set('gift2', gift2);
                        company.set('gift3', gift3);

                        if ( btn_state == 'update' )
                        {
                            company.set('id', compamy_id);
                            company.update();
                        }
                        else if ( btn_state == 'create' )
                        {
                            company.create();
                        }

                        InsuranceCompany.commit();
                        $('#insurance_company_cu_dialog').dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#insurance_company_cu_dialog').dialog('close');
                    }
                }
            ]
        });

        //表格
        $('#insurance_company_grid').datagrid({
            url: '/insuranceCompanyList.json',
            title: '保险公司列表',
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
            toolbar: [{
                text: '添加',
                iconCls: 'icon-plus',
                handler: function(){
                    //打开窗口前,需要清空表单
                    $('#insurance_company_cu_form [name="company_id"]').val('');
                    $('#insurance_company_cu_form [name="company_name"]').val('');
                    $('#insurance_company_cu_form [name="short_name"]').val('');
                    $('#insurance_company_cu_form [name="ename"]').val('');
                    discount_spinner.numberspinner('setValue', 0.5);
                    car_price_discount_spinner.numberspinner('setValue', 0.5);
                    gift_spinner.numberspinner('setValue', 0.5);
                    gift2_spinner.numberspinner('setValue', 0.5);
                    gift3_spinner.numberspinner('setValue', 0.5);
                    //设置窗口状态,并打开
                    //设置窗口状态,并打开
                    $('#insurance_company_cu_dialog').data('state', 'create');

                    var opt = $('#insurance_company_cu_dialog').dialog('options');
                    opt.title = '保险公司添加';
                    opt.iconCls = 'icon-plus';
                    opt.buttons[0].text = '添加';
                    $('#insurance_company_cu_dialog').dialog(opt).dialog('open').dialog('center');

                }
            }],
            columns:[[
                {field:'companyName', title:'公司名称', width:'15%', align:'center'},
                {field:'shortName', title:'简称', width:'10%', align:'center'},
                {field:'ename',title:'英文名称',width:'15%',align:'center'},
                {field:'discount',title:'折扣',width:'10%',align:'center'},
                {field:'carPriceDiscount',title:'车价折扣',width:'10%',align:'center'},
                {field:'gift',title:'礼包',width:'10%',align:'center'},
                {field:'gift2',title:'礼包2',width:'10%',align:'center'},
                {field:'gift3',title:'礼包3',width:'10%',align:'center'},
                {field:'companyId',title:'操作',width:'12%',align:'center', formatter: function(value, row, index){
                    return '<div class="btn-group"><button class="btn btn-warning insurance-company-edit-btn"><i class="icon-edit"></i></button><button class="btn btn-danger insurance-company-del-btn" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        /**
         * 事件相关
         */
        //保险公司编辑按钮点击事件
        $(document).on('click', '.insurance-company-edit-btn', function(event){

            var company = $('#insurance_company_grid').datagrid('getSelected');

            $('#insurance_company_cu_form [name="company_id"]').val(company.companyId);
            $('#insurance_company_cu_form [name="company_name"]').val(company.companyName);
            $('#insurance_company_cu_form [name="short_name"]').val(company.shortName);
            $('#insurance_company_cu_form [name="ename"]').val(company.ename);
            discount_spinner.numberspinner('setValue', company.discount);
            car_price_discount_spinner.numberspinner('setValue', company.carPriceDiscount);
            gift_spinner.numberspinner('setValue', company.gift);
            gift2_spinner.numberspinner('setValue', company.gift2);
            gift3_spinner.numberspinner('setValue', company.gift3);

            $('#insurance_company_cu_dialog').data('state', 'update');

            var opt = $('#insurance_company_cu_dialog').dialog('options');
            opt.title = '保险公司编辑';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '更新';
            $('#insurance_company_cu_dialog').dialog(opt).dialog('open').dialog('center');
        });

        //保险公司删除按钮点击事件
        $(document).on('click', '.insurance-company-del-btn', function(event){
            var id = $(this).attr('data-id');

            $.messager.confirm('删除保险公司', '是否删除保险公司', function(result){
                if(result)
                {
                    var company = new InsuranceCompany();
                    company.set('id', id);
                    company.delete();
                    InsuranceCompany.commit();
                }
            });
        });

        /**
         * 数据相关
         */

        var InsuranceCompany = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){
                    if(action == 'update' || action == 'create') return '/insuranceCompany.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            })
                            var ids_str = ids.join('-');
                            return '/insuranceCompany/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/insuranceCompany/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        InsuranceCompany.on('created', function(event, data){
            if(!data.success)
            {
                $.messager.show({title: '系统消息', msg: '保险公司添加失败'});
                return;
            }
            $.messager.show({title: '系统消息', msg: '保险公司添加成功'});
            $('#insurance_company_grid').datagrid('reload');
        });

        InsuranceCompany.on('deleted', function(event, data){
            if(!data.success)
            {
                $.messager.show({title: '系统消息', msg: '保险公司删除失败'});
                return;
            }
            $.messager.show({title: '系统消息', msg: '保险公司删除成功'});
            $('#insurance_company_grid').datagrid('reload');
        });

        InsuranceCompany.on('updated', function(event, data){
            if(!data.success)
            {
                $.messager.show({title: '系统消息', msg: '保险公司更新失败'});
                return;
            }
            $.messager.show({title: '系统消息', msg: '保险公司更新成功'});
            $('#insurance_company_grid').datagrid('reload');
        });
    };

    CarMate.page.on_leave = function(){
        //销毁窗口
        $('#insurance_company_cu_dialog').dialog('destroy');

        //清除动态绑定事件
        $(document).off('click', '.insurance-company-edit-btn');
        $(document).off('click', '.insurance-company-del-btn');
    };
</script>
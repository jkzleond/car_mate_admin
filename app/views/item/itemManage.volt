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
        <h4 class="widgettitle">商品管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="item_grid_tb">
                    <div class="row-fluid" id="item_search_bar">
                        <div class="span3">
                            <span class="label">商品名称</span>
                            <input type="text" name="name" />
                        </div>
                        <div class="span3">
                            <span class="label">分类</span>
                            <select name="type_id" id="item_type_list_for_search"></select>
                        </div>
                        <div class="span2">
                            <span class="label">类别</span>
                            <select name="visual" class="input-mini">
                                <option value="0">实物</option>
                                <option value="1">虚拟</option>
                            </select>
                        </div>
                        <div class="span2">
                            <span class="label">状态</span>
                            <select name="state" class="input-small">
                                <option value="1">未兑完</option>
                                <option value="2">已兑完</option>
                            </select>
                        </div>
                        <div class="span2">
                            <button class="btn btn-primary" id="item_search_btn"><i class="iconfa-search"></i>查找</button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="btn-group">
                                <button class="btn" id="item_select_all_btn">全选/全不选</button>
                                <button class="btn" id="item_batch_onshelf_btn"><i class="iconfa-arrow-up"></i>批量上架</button>
                                <button class="btn" id="item_batch_offshelf_btn"><i class="iconfa-arrow-down"></i>批量下架</button>
                                <button class="btn" id="item_batch_del_btn"><i class="iconfa-trash"></i>批量删除</button>
                                <button class="btn btn-primary" id="item_add_btn"><i class="iconfa-plus"></i>添加</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="item_grid"></div>
            </div>
            <div id="item_cu_window">
                <form id="item_cu_form" action="">
                    <div class="row-fluid">
                        <div class="span4">
                            <span class="label">名称</span>
                            <input type="hidden" name="id"/>
                            <input type="text" name="name"/>
                        </div>
                        <div class="span2">
                            <span class="label">价格</span>
                            <input type="text" name="real_price" id="item_real_price_numbox"/>
                        </div>
                        <div class="span2">
                            <span class="label">兑换币值</span>
                            <input type="text" name="gold_price" id="item_gold_price_numbox"/>
                        </div>
                        <div class="span2">
                            <span class="label">分类</span>
                            <select name="type_id" id="item_type_list_for_create"></select>
                        </div>
                        <div class="span2">
                            <span class="label">数量</span>
                            <input type="text" name="num" id="item_num_numbox"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">图片</span>
                            <input id="item_pic_file" type="file" name="picf" accept="image/jpeg, image/png"/>
                        </div>
                        <div class="span2">
                            <img id="item_pic" name="pic_data" src="" alt="" class="hidden-none"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">内容</span>
                            <textarea name="contents" id="item_contents_editor" cols="30" rows="10">
                            </textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div id="item_type_cu_window">
                <form id="item_type_cu_form" action="">
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">名称</span>
                            <input type="hidden" name="id" />
                            <input type="text" name="name"/>
                        </div>
                        <div class="span6">
                            <span class="label">类型</span>
                            <select name="visual">
                                <option value="0" selected>实物</option>
                                <option value="1">虚拟</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div id="item_exchange_grid_window">
                <div id="item_exchange_grid_tb">
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">用户ID</span>
                            <input type="hidden" name="item_id"/>
                            <input type="text" name="user_id" class="input-medium"/>
                            <button class="btn btn-primary" id="item_exchange_search_btn"><i class="iconfa-search"></i>查找</button>
                        </div>
                    </div>
                </div>
                <div id="item_exchange_grid"></div>
            </div>
            <div id="item_detail_window">
                <table class="table">
                    <tr>
                        <th rowspan="6">图片</th>
                        <td rowspan="6" data-field="pic_data"></td>
                        <th>名称</th>
                        <td data-field="name" colspan="4"></td>
                    </tr>
                    <tr>
                        <th>类型</th>
                        <td data-field="visual"></td>
                        <th>类型</th>
                        <td data-field="type"></td>
                    </tr>
                    <tr>
                        <th>价格</th>
                        <td data-field="real_price"></td>
                        <th>兑换币值</th>
                        <td data-field="gold_price"></td>
                    </tr>
                    <tr>
                        <th>数量</th>
                        <td data-field="num"></td>
                        <th>兑换人数</th>
                        <td data-field="exchange_num"></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td data-field="create_time"></td>
                        <th>最近修改</th>
                        <td data-field="last_modify_time"></td>
                    </tr>
                    <tr>
                        <th>最新兑换</th>
                        <td data-field="last_exchange_time"></td>
                        <th>当前状态</th>
                        <td data-field="state"></td>
                    </tr>
                    <tr>
                        <th>内容</th>
                        <td colspan="3"><div data-field="contents"></div></td>   
                    </tr>
                </table>
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
        var item_contents_editor = CKEDITOR.replace( 'item_contents_editor', {
            enterMode: CKEDITOR.ENTER_P,
            height: 400,
            removePlugins : 'save'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(item_contents_editor, finder_path);

        //商品类别选择框
        var item_type_list_for_search = $('#item_type_list_for_search').combogrid({
            url: '/itemTypeList.json',
            title: '商品分类列表',
            width: 100,
            height: 30,
            panelWidth: 300,
            panelHeight: 'auto',
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
                {field:'name', title:'分类名称', width:'90%', align:'center'}
            ]]
        });

        var item_type_list_for_create = $('#item_type_list_for_create').combogrid({
            url: '/itemTypeList.json',
            title: '商品分类列表',
            width: 100,
            height: 30,
            panelWidth: 300,
            panelHeight: 'auto',
            idField: 'id',
            textField: 'name',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            toolbar: [
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(){
                        $('#item_type_cu_form [name="id"]').val('');
                        $('#item_type_cu_form [name="name"]').val('');
                        $('#item_type_cu_form [name="visual"]').val('0');
                        item_type_cu_dialog.data('state', 'create');
                        var opt = item_type_cu_dialog.dialog('options');
                        opt.iconCls = 'icon-plus';
                        opt.title = '商品类目添加';
                        opt.buttons[0].text = '添加';
                        item_type_cu_dialog.dialog(opt).dialog('open').dialog('center');
                    }
                }
            ],
            columns:[[
                {field:'name', title:'分类名称', width:'45%', align:'center'},
                {field:'id', title:'操作', width:'45%', align:'center', formatter: function(value, row, index){
                    if(value <=3 ) return;
                    return '<div class="btn-group"><button class="btn btn-warning item-type-edit-btn"><i class="icon-edit"></i></button><button class="btn btn-danger item-type-del-btn" data-id="' + value + '"><i class="icon-trash"></i></button></div>'
                }}
            ]]
        });

        //数据表格
        var item_grid = $('#item_grid').datagrid({
            url: '/itemList.json',
            title: '商品列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: false,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            toolbar: '#item_grid_tb',
            idField: 'id',
            columns:[[
                {field:'id', title:'ID', width:'5%', align:'center'},
                {field:'name', title:'名称', width:'25%', align:'center'},
                {field:'pic', title:'图片', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '无';
                    return '<img  src="data:image/png;base64,' + value + '" alt="奖品图片"/>';
                }},
                {field:'realPrice', title:'价格', width:'8%', align:'center', formatter: function(value, row, index){
                    return Number(value).toFixed(2);
                }},
                {field:'goldPrice', title:'兑换币值', width:'8%', align:'center'},
                {field:'num', title:'数量', width:'5%', align:'center'},
                {field:'typeName', title:'类型', width:'5%', align:'center'},
                {field:'exchangeNum', title:'兑换人数', width:'5%', align:'center', formatter: function(value, row, index){
                    return '<button class="btn btn-info item-exchange-list-btn" data-id="' + row.id + '"><i class="iconfa-user"></i>' + value + '</button>';
                }},
                {field:'lastExchangeTime', title:'最后兑换', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'state', title:'当前状态', width:'5%', align:'center', formatter: function(value, row, index){
                    if(value == 1 )
                    {
                        return '<span class="label label-success">未兑完</span>';
                    }
                    else if(value == 2)
                    {
                        return '<span class="label label-important">已兑完</span>';
                    }
                }},
                {field:'rownum', title:'操作', width:'15%', align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    var btn_string = '<button class="btn btn-info item-detail-btn" title="详细" data-id="' + row.id + '"><i class="icon-info-sign"></i></button>';

                    if(row.state == 1)
                    {
                        if(row.onShelf == 1)
                        {
                            btn_string += '<button class="btn btn-mini item-off-shelf-btn" title="下架" data-id="' + row.id + '"><i class="icon-arrow-down"></i></button>';
                        }
                        else
                        {
                            btn_string += '<button class="btn btn-mini item-on-shelf-btn" title="上架" data-id="' + row.id + '"><i class="icon-arrow-up"></i></button>';
                        }

                        btn_string += '<button class="btn btn-mini btn-warning item-edit-btn" data-id="' + row.id + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger item-del-btn" data-id="' + row.id + '"><i class="icon-trash"></i></button>';
                    }
                    else if(row.state == 2)
                    {
                        btn_string += '<button class="btn btn-mini btn-warning item-edit-btn" data-id="' + row.id + '"><i class="icon-edit"></i></button>';

                    }

                    return '<div class="btn-group">' + btn_string + '</div>';

                }}
            ]]
        });

        var item_exchange_grid = $('#item_exchange_grid').datagrid({
            url: '/itemExchangeList.json',
            title: '商品对换信息列表',
            iconCls: 'icon-list',
            width: '100%',
            height: '100%',
            fitColumns: true,
            singleSelect: false,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            toolbar: '#item_exchange_grid_tb',
            idField: 'id',
            columns:[[
                {field:'userid', title:'用户ID', width:'25%', align:'center'},
                {field:'exchangeDate', title:'对换时间', width:'20%', align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'phone', title:'电话', width:'15%', align:'center'},
                {field:'trueName', title:'真实姓名', width:'10%', align:'center'},
                {field:'address', title:'地址', width:'30%', align:'center', formatter: function(value, row, index){
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
                }}
            ]]
        });

        //窗口
        var item_cu_dialog = $('#item_cu_window').dialog({
            title: '商品添加',
            iconCls: 'icon-plus',
            width: 'auto',
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
                        var waiting = item_cu_dialog.data('waiting');
                        if(waiting) return;

                        var win_state = item_cu_dialog.data('state');

                        var name = $('#item_cu_form [name="name"]').val();
                        var real_price = real_price_numbox.numberspinner('getValue');
                        var gold_price = gold_price_numbox.numberspinner('getValue');
                        var type_id = item_type_list_for_create.combogrid('getValue');
                        var num = num_numbox.numberspinner('getValue');
                        var pic_data = $('#item_cu_form [name=pic_data]').attr('src').match(/base64,(.*)/)[1];
                        var contents = item_contents_editor.getData();

                        var item = new Item();

                        item.set('name', name);
                        item.set('real_price', real_price);
                        item.set('gold_price', gold_price);
                        item.set('type_id', type_id);
                        item.set('num', num);
                        item.set('pic_data', pic_data);
                        item.set('contents', contents);

                        if(win_state == 'create')
                        {
                            item.create();
                        }
                        else if(win_state == 'update')
                        {
                            var id = $('#item_cu_form [name="id"]').val();
                            item.set('id', id);
                            item.update();
                        }

                        ItemType.commit({complete: function(data){
                            item_cu_dialog.data('waiting', false);
                        }});

                        item_cu_dialog.data('waiting', true);
                        item_cu_dialog.dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        item_cu_dialog.dialog('close');
                    }
                }
            ]
        });

        var item_type_cu_dialog = $('#item_type_cu_window').dialog({
            title: '类目添加',
            iconCls: 'icon-plus',
            width: 'auto',
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
                        var waiting = item_type_cu_dialog.data('waiting');
                        if(waiting) return;

                        var win_state = item_type_cu_dialog.data('state');

                        var name = $('#item_type_cu_form [name="name"]').val();
                        var visual = $('#item_type_cu_form [name="visual"]').val();

                        var item_type = new ItemType();

                        item_type.set('name', name);
                        item_type.set('visual', visual);

                        if(win_state == 'create')
                        {
                            item_type.create();
                        }
                        else if(win_state == 'update')
                        {
                            var id = $('#item_type_cu_form [name="id"]').val();
                            item_type.set('id', id);
                            item_type.update();
                        }

                        ItemType.commit({complete: function(data){
                            item_type_cu_dialog.data('waiting', false);
                        }});

                        item_type_cu_dialog.data('waiting', true);
                        item_type_cu_dialog.dialog('close');
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

        var item_exchange_grid_window = $('#item_exchange_grid_window').window({
            title: '商品对换信息',
            iconCls: 'icon-info',
            width: 600,
            height: 500,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        var item_detail_window = $('#item_detail_window').window({
            title: '商品详细信息',
            iconCls: 'icon-info',
            width: 800,
            height: 600,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //数字输入框
        var real_price_numbox = $('#item_real_price_numbox').numberspinner({
            width: 80,
            height: 30,
            precision: 2,
            min: 0,
            value: 0
        });
        var gold_price_numbox = $('#item_gold_price_numbox').numberspinner({
            width: 80,
            height: 30,
            min: 0,
            value: 0
        });
        var num_numbox = $('#item_num_numbox').numberspinner({
            width: 80,
            height: 30,
            min: 0,
            value: 0
        });



        /**
         * 事件相关
         */

        //查找按钮点击事件
        $('#item_search_btn').click(function(event){
            var criteria = {};
            criteria.name = $('#item_search_bar [name="name"]').val();
            criteria.type_id = $('#item_search_bar [name="type_id"]').val();
            criteria.visual = $('#item_search_bar [name="visual"]').val();
            criteria.state = $('#item_search_bar [name="state"]').val();
            item_grid.datagrid('load',{criteria: criteria});
        });

        //全选/全不选按钮点击时间
        $('#item_select_all_btn').click(function(event){
            var is_all = $(this).data('is_all');

            if(!is_all)
            {
                item_grid.datagrid('selectAll');
                $(this).data('is_all', true);
            }
            else
            {
                item_grid.datagrid('unselectAll');
                $(this).data('is_all', false);
            }
        });

        //商品批量上架按钮
        $('#item_batch_onshelf_btn').click(function(event){
            var items = item_grid.datagrid('getSelections');
            var ids = '';
            var len = items.length;

            for(var i = 0; i < len; i++)
            {
                var item = items[i];
                if(item.state == 2) continue;
                ids += item.id + '-';
            }

            ids = ids.slice(0, ids.length - 1);

            onShelfItem(ids);
        });

        //商品批量下架按钮
        $('#item_batch_offshelf_btn').click(function(event){
            var items = item_grid.datagrid('getSelections');
            var ids = '';
            var len = items.length;

            for(var i = 0; i < len; i++)
            {
                var item = items[i];
                if(item.state == 2) continue;
                ids += item.id + '-';
            }

            ids = ids.slice(0, ids.length - 1);

            offShelfItem(ids);
        });

        //批量删除按钮
        $('#item_batch_del_btn').click(function(event){
            $.messager.confirm('确定删除', '该操作无法撤消,是否确定删除?', function(is_ok){
                if(!is_ok) return;
                var items = item_grid.datagrid('getSelections');
                var ids = '';
                var len = items.length;

                for(var i = 0; i < len; i++)
                {
                    var item = items[i];
                    if(item.state == 2) continue;
                    ids += item.id + '-';
                }

                ids = ids.slice(0, ids.length - 1);

                delItem(ids);
            });
        });

        //商品兑换信息列表按钮点击事件
        $(document).on('click', '.item-exchange-list-btn', function(event){
            var criteria = {};
            var item_id = $(this).attr('data-id');
            criteria.item_id = item_id;
            item_exchange_grid.datagrid('load', {criteria: criteria});
            item_exchange_grid_window.data('item_id', item_id);
            item_exchange_grid_window.window('open').window('center');
        });

        //商品详细信息按钮点击事件
        $(document).on('click', '.item-detail-btn', function(event){
            var id = $(this).attr('data-id');
            var index = item_grid.datagrid('getRowIndex', id);
            var item = item_grid.datagrid('getRows')[index];
            $('#item_detail_window [data-field="pic_data"]').empty().append('<img src="data:image/png;base64,' + item.pic + '" style="width: 100%;" />');
            $('#item_detail_window [data-field="name"]').text(item.name);
            var visual_text = '实物';
            if(item.visual == 1) visual_text = '虚拟';
            $('#item_detail_window [data-field="visual"]').text(visual_text);
            $('#item_detail_window [data-field="type"]').text(item.typeName);
            $('#item_detail_window [data-field="real_price"]').text(Number(item.realPrice).toFixed(2));
            $('#item_detail_window [data-field="gold_price"]').text(item.goldPrice);
            $('#item_detail_window [data-field="num"]').text(item.num);
            $('#item_detail_window [data-field="exchange_num"]').text(item.exchangeNum);
            var create_date = item.createDate && CarMate.utils.date.mssqlToJs(item.createDate);
            create_date = create_date && CarMate.utils.date('Y-m-d H:i:s', create_date);
            create_date = create_date || '';
            $('#item_detail_window [data-field="create_time"]').text(create_date);
            var last_modify_date = item.lastModifiedTime && CarMate.utils.date.mssqlToJs(item.lastModifiedTime);
            last_modify_date = last_modify_date && CarMate.utils.date('Y-m-d H:i:s', last_modify_date);
            last_modify_date = last_modify_date || '';
            $('#item_detail_window [data-field="last_modify_time"]').text(last_modify_date);
            var last_exchange_time = item.lastExchangeTime && CarMate.utils.date.mssqlToJs(item.lastExchangeTime);
            last_exchange_time = last_exchange_time && CarMate.utils.date('Y-m-d H:i:s', last_exchange_time);
            last_exchange_time = last_exchange_time || '';
            $('#item_detail_window [data-field="last_exchange_time"]').text(last_exchange_time);

            var state_text = '';
            if(item.state == 1)
            {
                state_text = '未兑完';
            }
            else if(item.state == 2)
            {
                state_text = '已兑完';
            }

            $('#item_detail_window [data-field="state"]').text(state_text);
            $('#item_detail_window [data-field="contents"]').empty().append(item.contents);
            item_detail_window.window('open').window('center');
        });

        //商品上架按钮点击事件
        $(document).on('click', '.item-on-shelf-btn', function(event){
            var id = $(this).attr('data-id');
            onShelfItem(id);
        });

        //商品下架按钮点击事件
        $(document).on('click', '.item-off-shelf-btn', function(event){
            var id = $(this).attr('data-id');
            offShelfItem(id);
        });

        //商品编辑按钮点击事件
        $(document).on('click', '.item-edit-btn', function(event){
            var id = $(this).attr('data-id');
            var index = item_grid.datagrid('getRowIndex', id);
            var item = item_grid.datagrid('getRows')[index];
            $('#item_cu_form [name="id"]').val(item.id);
            $('#item_cu_form [name="name"]').val(item.name);
            real_price_numbox.numberspinner('setValue', item.realPrice);
            gold_price_numbox.numberspinner('setValue', item.goldPrice);
            item_type_list_for_create.combogrid('setValue', item.typeId);
            num_numbox.numberspinner('setValue', item.num);
            $('#item_cu_form [name="picf"]').val('');
            $('#item_pic').attr('src', 'data:image/png;base64,' + item.pic).show();
            item_contents_editor.setData(item.contents);

            var opt = item_cu_dialog.dialog('options');
            opt.iconCls = 'icon-plus';
            opt.title = '添加商品';
            opt.buttons[0].text = '添加';
            item_cu_dialog.data('state', 'update');
            item_cu_dialog.dialog(opt).dialog('open').dialog('center');
        });

        //商品删除按钮点击事件
        $(document).on('click', '.item-del-btn', function(event){
            var id = $(this).attr('data-id');
            $.messager.confirm('确定删除', '该操作无法撤消, 是否确定删除操作?', function(is_ok){
                if(!is_ok) return;
                delItem(id);
            });
        });

        //商品添加按钮点击事件
        $('#item_add_btn').click(function(event){
            //清空表单
            $('#item_cu_form [name="id"]').val('');
            $('#item_cu_form [name="name"]').val('');
            real_price_numbox.numberspinner('reset');
            gold_price_numbox.numberspinner('reset');
            num_numbox.numberspinner('reset');
            item_type_list_for_create.combogrid('clear');
            $('#item_cu_form [name="picf"]').val('');
            $('#item_pic').attr('src', '').hide();
            item_contents_editor.setData('');

            var opt = item_cu_dialog.dialog('options');
            opt.iconCls = 'icon-plus';
            opt.title = '添加商品';
            opt.buttons[0].text = '添加';
            item_cu_dialog.data('state', 'create');
            item_cu_dialog.dialog(opt).dialog('open').dialog('center');
        });

        //商品图片选择器改变事件
        $('#item_pic_file').change(function(event){
            var file = this.files[0];
            var reader = new FileReader();
            reader.addEventListener('load', function(event){
                var img_url = event.target.result;
                $('#item_cu_form [name="pic_data"]').attr('src', img_url).fadeIn(500);
            });
            reader.readAsDataURL(file);
        });

        //商品兑换信息查找按钮点击事件
        $('#item_exchange_search_btn').click(function(event){
            var item_id = item_exchange_grid_window.data('item_id');
            var user_id = $('#item_exchange_grid_tb [name="user_id"]').val();

            var criteria = {};
            criteria.item_id = item_id;
            criteria.user_id = user_id;

            item_exchange_grid.datagrid('load', {criteria: criteria});
        });


        //商品类目编辑按钮点击事件
        $(document).on('click', '.item-type-edit-btn', function(event){
            var item_type = item_type_list_for_create.combogrid('grid').datagrid('getSelected');
            $('#item_type_cu_form [name="id"]').val(item_type.id);
            $('#item_type_cu_form [name="name"]').val(item_type.name);
            $('#item_type_cu_form [name="visual"]').val(item_type.visual);
            item_type_cu_dialog.data('state', 'update');
            var opt = item_type_cu_dialog.dialog('options');
            opt.iconCls = 'icon-edit';
            opt.title = '商品类目编辑';
            opt.buttons[0].text = '编辑';
            item_type_cu_dialog.dialog(opt).dialog('open').dialog('center');
        });

        //商品类目删除按钮点击事件
        $(document).on('click', '.item-type-del-btn', function(event){
            var id = $(this).attr('data-id');
            var item_type = new ItemType();
            item_type.set('id', id);
            item_type.delete();
            ItemType.commit();
        });

        /**
         * 数据相关
         */

        var Item = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){

                    if(action == 'update' || action == 'create') return '/item.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            });
                            var ids_str = ids.join('-');
                            return '/item/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/item/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        Item.on('created', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '商品添加成功'
            });
            item_grid.datagrid('load');
        });

        Item.on('updated', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '商品更新成功'
            });
            item_grid.datagrid('load');
        });

        //商品类目模型
        var ItemType = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){

                    if(action == 'update' || action == 'create') return '/itemType.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            });
                            var ids_str = ids.join('-');
                            return '/itemType/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/itemType/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        ItemType.on('created', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '商品类目添加成功'
            });
            item_type_list_for_create.combogrid('grid').datagrid('load');
        });

        ItemType.on('deleted', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '商品类目删除成功'
            });
            item_type_list_for_create.combogrid('grid').datagrid('load');
        });

        ItemType.on('updated', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '商品类目更新成功'
            });
            item_type_list_for_create.combogrid('grid').datagrid('load');
        });

        function onShelfItem(ids)
        {
            var url = '/itemOnShelf/' + ids + '.json';
            $.ajax({
                url: url,
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '商品上架成功'
                });
                item_grid.datagrid('reload');
            });
        }

        function offShelfItem(ids)
        {
            var url = '/itemOffShelf/' + ids + '.json';
            $.ajax({
                url: url,
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '商品下架成功'
                });
                item_grid.datagrid('reload');
            });
        }

        function delItem(ids)
        {
            var url = '/item/' + ids + '.json';
            $.ajax({
                url: url,
                method: 'DELETE',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '商品删除成功'
                });
                item_grid.datagrid('reload');
            });
        }



        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){

            //销毁combogrid
            item_type_list_for_search.combogrid('destroy');
            item_type_list_for_create.combogrid('destroy');

            //销毁窗口
            item_cu_dialog.dialog('destroy');
            item_type_cu_dialog.dialog('destroy');
            item_exchange_grid_window.window('destroy');
            item_detail_window.window('destroy');

            //清除动态绑定事件
            $(document).off('click', '.item-exchange-list-btn');
            $(document).off('click', '.item-detail-btn');
            $(document).off('click', '.item-on-shelf-btn');
            $(document).off('click', '.item-off-shelf-btn');
            $(document).off('click', '.item-edit-btn');
            $(document).off('click', '.item-del-btn');
            $(document).off('click', '.item-type-edit-btn');
            $(document).off('click', '.item-type-del-btn');


        };
    };
</script>
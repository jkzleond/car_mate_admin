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
        <h4 class="widgettitle">公告管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="notice_grid"></div>
            </div>
            <div id="notice_cu_window">
                <form id="notice_cu_form" action="">
                    <div class="row-fluid">
                        <div class="span6">
                            <input name="id" type="hidden"/>
                            <span class="label">标题</span>
                            <input type="text" name="title"/>
                        </div>
                        <div class="span3">
                            <span class="label">分类</span>
                            <select name="type_id" class="input-small">
                                {% for index, type in notice_types %}
                                <option value="{{ type.id }}">{{ type.name }}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="span3">
                            <span class="label">省份</span>
                            <select name="province_id" class="input-small">
                                {% for province in province_list %}
                                <option value="{{ province.id }}">{{ province.name }}</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">内容</span>
                            <textarea name="contents" id="notice_contents_editor" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div id="notice_detail_dialog">
                <div id="notice_detail_contents_container">

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
        var notice_contents_editor = CKEDITOR.replace( 'notice_contents_editor', {
            enterMode: CKEDITOR.ENTER_P,
            height: 400,
            removePlugins : 'save'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(notice_contents_editor, finder_path);


        //公告表格
        var notice_grid = $('#notice_grid').datagrid({
            url: '/notice/list.json',
            title: '公告列表',
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
            toolbar: '#item_grid_tb',
            idField: 'id',
            loadFilter: function(data){
                //因为两个单元格同事需要id,为避免字段冲突智能复制该字段
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;

                var cur_row = null;
                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];
                    var local_favour_adv = {};

                    if(cur_row && row.id == cur_row.id){
                        local_favour_adv.id = row.adv_id;
                        local_favour_adv.isOrder = row.adv_isOrder;
                        local_favour_adv.isState = row.adv_isState;
                        local_favour_adv.adv = row.adv_adv;
                        local_favour_adv.adv3 = row.adv_adv3;
                        row.local_favour_advs.push(local_favour_adv);
                    }
                    else
                    {
                        row.local_favour_advs = [];
                        local_favour_adv.id = row.adv_id;
                        local_favour_adv.isOrder = row.adv_isOrder;
                        local_favour_adv.isState = row.adv_isState;
                        local_favour_adv.adv = row.adv_adv;
                        local_favour_adv.adv3 = row.adv_adv3;
                        row.local_favour_advs.push(local_favour_adv);
                        cur_row = row;
                        pass_rows.push(row);
                    }
                }
                data.rows = pass_rows;
                data.count = pass_rows.length;
                return data;
            },
            toolbar: [
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(){
                        $('#notice_cu_form [name="title"]').val('');
                        $('#notice_cu_form [name="type_id"]').val('1');
                        $('#notice_cu_form [name="province_id"]').val('0');
                        notice_contents_editor.setData('');

                        var opt = notice_cu_dialog.dialog('options');
                        opt.title = '公告添加';
                        opt.iconCls = 'icon-plus';
                        opt.buttons[0].text = '添加';
                        notice_cu_dialog.data('state', 'create');
                        notice_cu_dialog.dialog(opt).dialog('open').dialog('center');
                    }
                }
            ],
            columns:[[
                {field:'provinceName', title:'省份', width:'5%', align:'center'},
                {field:'typeName', title:'分类', width:'10%', align:'center'},
                {field:'title', title:'标题', width:'30%', align:'center'},
                {field:'publishTime', title:'发布日期', width:'15%', align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    var conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', conv);
                }},
                {field:'isOrder', title:'排序', width:'8%', align:'center', formatter: function(value, row, index){
                    return '<input class="notice-order-box" style="width: 30px; height:10px" type="text" value="' + value + '" data-id="' + row.id + '"/>';
                }},
                {field:'isState', title:'使用状态', width:'8%', align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '<button class="btn btn-mini notice-disable-btn" data-id="' + row.id + '"><i class="iconfa-circle"></i></botton>';
                    }
                    else
                    {
                        return '<button class="btn btn-mini notice-enable-btn" data-id="' + row.id + '"><i class="iconfa-circle-blank"></i></botton>';
                    }
                }},
                /*{field:'local_favour_adv', title:'推广到首页', width:'10%', align:'center', formatter: function(value, row, index){
                 if(value.length == 0)
                 {
                 return '<button title="推广" class="btn btn-mini notice-extend-btn"><i class="iconfa-star-empty"></i></botton>';
                 }
                 else
                 {
                 return '<button title="已推广" class="btn btn-mini notice-unextend-btn"><i class="iconfa-star"></i></botton>">';
                 }
                 }},*/
                {field:'id', title:'操作', width:'15%', align:'center', formatter: function(value, row, index){
                    if(!value) return;

                    return '<div class="btn-group"><button class="btn btn-info notice-detail-btn" title="详细" data-id="' + value + '"><i class="icon-info-sign"></i></button><button class="btn btn-mini btn-warning notice-edit-btn" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger notice-del-btn" data-id="' + value + '"><i class="icon-trash"></i></button></div>';

                }}
            ]]
        });

        //公告编辑删除窗口
        var notice_cu_dialog = $('#notice_cu_window').dialog({
            title: '开屏广告添加',
            iconCls: 'icon-plus',
            width: 850,
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
                        var waiting = notice_cu_dialog.data('waiting');
                        if(waiting) return;

                        var win_state = notice_cu_dialog.data('state');

                        var title = $('#notice_cu_form [name="title"]').val();
                        var type_id = $('#notice_cu_form [name="type_id"]').val();
                        var province_id = $('#notice_cu_form [name="province_id"]').val();
                        var contents = notice_contents_editor.getData();

                        var notice = new Notice();

                        notice.set('title', title);
                        notice.set('type_id', type_id);
                        notice.set('province_id', province_id);
                        notice.set('contents', contents);

                        if(win_state == 'update')
                        {
                            var id = $('#notice_cu_form [name="id"]').val();
                            notice.set('id', id);
                            notice.update();
                        }
                        else if(win_state == 'create')
                        {
                            notice.create();
                        }

                        Notice.commit({complete: function(){
                            notice_cu_dialog.data('waiting', false);
                        }});

                        notice_cu_dialog.data('waiting', true);
                        notice_cu_dialog.dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        notice_cu_dialog.dialog('close');
                    }
                }
            ]
        });

        //公告详细窗口
        var notice_detail_dialog = $('#notice_detail_dialog').dialog({
            title: '公告详细窗口',
            iconCls: 'icon-plus',
            width: 320,
            height: 480,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        /**
         * 事件相关
         */

        //公告弃用按钮点击事件
        $(document).on('click', '.notice-disable-btn', function(event){

            var id = $(this).attr('data-id');

            console.log(id);
            $.ajax({
                url: '/noticeDisable/' + id + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '公告弃用成功'
                });
                notice_grid.datagrid('reload');
            });
        });

        //公告使用按钮点击事件
        $(document).on('click', '.notice-enable-btn', function(event){
            var id = $(this).attr('data-id');
            $.ajax({
                url: '/noticeEnable/' + id + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '公告使用成功'
                });
                notice_grid.datagrid('reload');
            });
        });

        //公告首页推广点击事件
        $(document).on('click', '.notice-extend-btn', function(event){
            var id = $(this).attr('data-id');
            $.ajax({
                url: '',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '公告推广成功'
                });
                notice_grid.datagrid('reload');
            });
        });

        //公告取消推广点击事件
        $(document).on('click', '.notice-unextend-btn', function(event){
            var id = $(this).attr('data-id');
            $.ajax({
                url: '',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '公告取消成功'
                });
                notice_grid.datagrid('reload');
            });
        });

        //公告详情点击事件
        $(document).on('click', '.notice-detail-btn', function(event){
            var notice = notice_grid.datagrid('getSelected');
            $('#notice_detail_contents_container').empty().append(notice.contents);
            notice_detail_dialog.dialog('open').dialog('center');
        });

        //公告编辑点击事件
        $(document).on('click', '.notice-edit-btn', function(event){

            var notice = notice_grid.datagrid('getSelected');

            $('#notice_cu_form [name="id"]').val(notice.id);
            $('#notice_cu_form [name="title"]').val(notice.title);
            $('#notice_cu_form [name="type_id"]').val(notice.typeId);
            $('#notice_cu_form [name="province_id"]').val(notice.provinceId);
            notice_contents_editor.setData(notice.contents);

            var opt = notice_cu_dialog.dialog('options');
            opt.title = '公告编辑';
            opt.iconCls = 'icon-edit';
            opt.buttons[0].text = '编辑';
            notice_cu_dialog.data('state', 'update');
            notice_cu_dialog.dialog(opt).dialog('open').dialog('center');
        });

        //公告删除点击事件
        $(document).on('click', '.notice-del-btn', function(event){
            var id = $(this).attr('data-id');

            $.messager.confirm('删除公告', '该操作不可撤消,是否继续', function(is_ok){
                if(!is_ok) return;

                var notice = new Notice();
                notice.set('id', id);
                notice.delete();
                Notice.commit();
            });
        });

        //公告排序输入框改变事件
        $(document).on('change', '.notice-order-box', function(event){
            var id = $(this).attr('data-id');
            var is_order = $(this).val();
            var notice = new Notice();
            notice.set('id', id);
            notice.set('is_order', is_order);
            notice.update();
            Notice.commit();
        });

        /**
         * 数据相关
         */
        //Notice模型
        var Notice = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){
                    if(action == 'update' || action == 'create') return '/notice.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            })
                            var ids_str = ids.join('-');
                            return '/notice/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/notice/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        Notice.on('created', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '公告添加成功'
            });
            notice_grid.datagrid('load');
        });

        Notice.on('deleted', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '公告删除成功'
            });
            notice_grid.datagrid('reload');
        });

        Notice.on('updated', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '公告更新成功'
            });
            notice_grid.datagrid('reload');
        });

        /**
         * 页面离开时事件
         */
        this.on_leave = function()
        {
            //销毁窗口
            notice_cu_dialog.dialog('destroy');
            notice_detail_dialog.dialog('destroy');

            //清除同态绑定事件
            $(document).off('click', '.notice-disable-btn');
            $(document).off('click', '.notice-enable-btn');
            $(document).off('click', '.notice-extend-btn');
            $(document).off('click', '.notice-unextend-btn');
            $(document).off('click', '.notice-detail-btn');
            $(document).off('click', '.notice-edit-btn');
            $(document).off('click', '.notice-del-btn');
            $(document).off('change', '.notice-order-box');
        }
    };
</script>

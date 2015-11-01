<style type="text/css" rel="stylesheet">

    .top {
        z-index: 999;
    }

    #car_service_list .item{
        position:relative;
        width:120px;
        height:120px;
        margin-left: 5px;
        text-align: center;
        float:left;
    }
    
    #car_service_list .item .url {
        position: absolute; 
        bottom: 5px; 
        background-color:#eee; 
        width: 120px;
    }

    .car-service-pic img {
        width: 100%;
    }
    .car-service-pic .tools {
        display:none;
        position:absolute;
        right: 0px;
        font-size: 20px;
        z-index: 999;
        cursor: pointer;
    }
    .car-service-pic .tools.tools-top {
        top: 0px;
    }
    .car-service-pic .tools.tools-bottom {
        bottom: 0px;
    }
    .car-service-pic:hover .tools {
        display: block;
    }
    .add-car-service:hover, .tools:hover {
        color:#0866c6;
    }

    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">汽车服务内容管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div class="span12">
                    <span class="label">正在使用的</span><span><i class="icon-hand-up"></i>拖放可以改变显示顺序</span>
                    <div id="car_service_list">
                        <div id="car_service_use_list" style="fload:left">

                        </div>
                        <div class="add-car-service item  well well-small" style="text-align: center; vertical-align: middle; line-height:150px; cursor: pointer">
                            <i class="iconfa-plus" style="font-size:40px;margin-top: 40px"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 汽车服务表格窗口 -->
            <div id="car_service_window">
                <div id="car_service_grid">
                </div>
            </div>

            <!-- 添加汽车服务窗口 -->
            <div id="create_car_service_window">
                <form id="create_car_service_form" class="well well-small" action="" method="post" enctype="multipart/form-data">
                    <div class="row-fluid">
                        <div class="span10">
                            <span class="label">名称</span>
                            <input name="id" type="hidden">
                            <input name="name" type="text" class="input-small"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span10">
                            <span class="label">图片</span>
                            <input name="img" type="file" class="input-small" accept="image/gif, image/png, image/jpeg"/>
                            <img id="thumbnail_img" style="height: 100px; display: none" />
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span8">
                            <span class="label">关联类型</span>
                            <input name="link_type" type="radio" value="1" rel-sel="#car_service_url_section"/><span>链接</span>
                            <input name="link_type" type="radio" value="2" rel-sel="#car_service_static_section"/><span>静态页</span>
                            <input name="link_type" type="radio" value="3" rel-sel="#car_service_lcoal_favour_section" checked/><span>本地惠</span>
                            <input name="link_type" type="radio" value="4" rel-sel="#car_service_activity_section"/><span>活动</span>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div id="car_service_link_section"class="span12">
                            <span class="label">关联内容</span>
                            <div id="car_service_url_section" style="display: none">
                                <input name="url" type="text" />
                            </div>
                            <div id="car_service_static_section" style="display: none">
                                <input type="hidden" name="content_id">
                                <textarea id="car_service_content" name="content" type="text"></textarea>
                            </div>
                            <div id="car_service_lcoal_favour_section">
                                <select id="car_service_local_favour_combogrid"></select>
                            </div>
                            <div id="car_service_activity_section" style="display: none">
                                <select id="car_service_activity_combogrid"></select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--   汽车服务表格    -->
            <div class="row-fluid">
                <div id="local_favour_car_service_grid"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    //页面加载完成事件
    CarMate.page.on_loaded = function(){

        //初始化记录使用列表数据的数组
        var car_service_use = [];
        var car_service_unuse = [];
        var ad_is_change = false;

        $('#car_service_use_list .car-service-pic').each(function(index, item){
            var id = $(item).attr('data-id')
            car_service_use.push(id);
        });

        function unuseCarservice(id, callback)
        {
            $.ajax({
                url: '/carService/' + id + '.json',
                method: 'PUT',
                data:{updates:[{is_use: false}]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务弃用失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务弃用成功'
                    });
                    if(typeof callback == 'function') callback(data);
                }
            });
        }

        function useCarservice(id, callback)
        {
            var is_already_use = $('.car-service-item[data-id="' + id + '"]').length > 0;
            if(is_already_use) return;

            $.ajax({
                url: '/carService/' + id + '.json',
                method: 'PUT',
                data:{updates:[{is_use: true}]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务弃用失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务启用成功'
                    });
                    if(typeof callback == 'function') callback(data);
                }
            });
        }

        function resortCarservice(id, new_index, callback)
        {
            $.ajax({
                url: '/carService/' + id + '.json',
                method: 'PUT',
                data:{updates:[{display_order: new_index}]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务重排序失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务重排序成功'
                    });
                    if(typeof callback == 'function') callback(data);
                }
            });
        }


        function renderCarserviceUseList(data)
        {
            var tmp_item_id = 'car_service_item_' + Date.now();
            $('#car_service_use_list').append(
                '<div class="drag-item pull-left car-service-item" id="' + tmp_item_id + '" data-id="' + data.id + '">' +
                    '<div class="well well-small car-service-pic item" data-id="' + data.id +'">' +
                        '<i class="iconfa-remove tools tools-top tools-unuse" data-id="' + data.id +'"></i>' +
                        '<span class="tools tools-bottom">' +
                            '<i class="iconfa-edit tools-edit" data-item-id="' + tmp_item_id + '"></i>' +
                        '</span>' +
                        '<img class="pic" src="{{ url("/carService/img/") }}' + data.id + '.png?_=' + Date.now() + '" />' +
                        '<div class="url"><span>' + data.name + '</span></div>' +
                    '</div>' +
                '</div>'
            );

            $('#'+tmp_item_id).data('car_service', data);

            registerDraggable();
        }

        function registerDraggable()
        {
            $('#car_service_list .drag-item').draggable({
                revert: true,
                deltaX: 50,
                deltaY: 50,
                edge: 20,
                onBeforeDrag: function(e){
                    $(this).addClass('top');
                },
                onStopDrag: function(e){
                    $(this).removeClass('top');
                }
            }).droppable({
                onDragOver:function(e,source){

                },
                onDragLeave:function(e,source){

                },
                onDrop:function(e,source){
                    var index = $('.car-service-item').index(this)
                    var data_id = $(source).attr('data-id');
                    var target = this;
                    resortCarservice(data_id, index, function(data){
                        $(source).insertBefore(target);
                    });
                }
            });
        }

        /**
         * 控件相关
         */
        
        //创建ckeditor
        var content_editor = CKEDITOR.replace( 'car_service_content', {
            enterMode: CKEDITOR.ENTER_P,
            height: 270,
            removePlugins : 'save'
            //filebrowserImageUploadUrl : 'ckUploadImage?command=QuickUpload&type=Images'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(content_editor, finder_path);
        
        //汽车服务窗口
        $('#car_service_window').window({
            title: '双击列表项可将广告添加到滚动列表',
            iconCls: 'icon-hand-up',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //添加汽车服务窗口
        var create_car_service_window = $('#create_car_service_window').dialog({
            title: '添加汽车服务',
            iconCls: 'icon-plus',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onBeforeClose: function(){
                $('#create_car_service_window [name="name"]').val('');
                $('#create_car_service_window [name="img"]').val('');
                $('#thumbnail_img').attr('src', '');
                $('#create_car_service_window [name="link_type"] [value="3"]').click();
                $('#create_car_service_window [name="url"]').val('');
                content_editor.setData('');
                local_favour_combogrid.combogrid('clear');
                activity_combogrid.combogrid('clear');
            },
            buttons:[
                {
                    text: '添加',
                    iconCls: 'icon-ok',
                    handler: function(){
                        var img_src = $('#thumbnail_img').attr('src');
                        var name = $('#create_car_service_form [name="name"]').val();
                        var img = null;
                        var img_file_name = $('#create_car_service_form [name=img]').val();
                        if(img_file_name)
                        {
                            img = img_src.match(/data:(?:.*);base64,(.*)/)[1];
                        }
                        var link_type = $('#create_car_service_form [name=link_type]:checked').val();
                        var rel_id = null;
                        var url = null
                        var content = null;

                        if(link_type == 1)
                        {
                            url = $('#create_car_service_form [name="url"]').val();
                        }
                        else if(link_type == 2)
                        {
                            content = content_editor.getData();
                        }
                        else if(link_type == 3)
                        {
                            rel_id = local_favour_combogrid.combogrid('getValue'); 
                        }
                        else if(link_type == 4)
                        {
                            rel_id = activity_combogrid.combogrid('getValue'); 
                        }

                        var state = create_car_service_window.data('win_state');

                        var car_service = {
                            name: name,
                            img: img,
                            link_type: link_type,
                            rel_id: rel_id,
                            url: url,
                            content: content
                        };

                        if(state == 'create')
                        {
                            addCarService(car_service, function(data){
                                car_service_grid.datagrid('reload');
                            });
                        }
                        else if(state == 'update')
                        {
                            var id = $('#create_car_service_form [name=id]').val();
                            var content_id = $('#create_car_service_form [name=content_id]').val();
                            car_service.id = id;
                            car_service.content_id = content_id;
                            updateCarService(car_service, function(data){
                                car_service_grid.datagrid('reload');
                            });
                        }

                        create_car_service_window.dialog('close', true);
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        create_car_service_window.dialog('close', true);
                    }
                }
            ]
        });

        //汽车服务表格
        var car_service_grid = $('#car_service_grid').datagrid({
            url: '/carService/list.json',
            title: '汽车服务列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 500,
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            onDblClickRow: function(index, row){
                //如果该广告以在使用中
                useCarservice(row.id, function(data){
                    renderCarserviceUseList(row);
                })
                $('#car_service_window').window('close');
            },
            toolbar:[
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(){

                        $('#create_car_service_form [name=id]').val('');
                        $('#create_car_service_form [name=pic]').val('');
                        $('#thumbnail_img').attr('src', '');
                        $('#thumbnail_img').hide();
                        $('#create_car_service_form [name=type][value=LocalFavour]').click();
                        $('#create_car_service_form [name=link]').val('');
                        $('#car_service_local_favour').combogrid('clear');
                        create_car_service_window.data('win_state', 'create');
                        var opt = create_car_service_window.dialog('options');
                        opt.title = '添加汽车服务';
                        opt.iconCls = 'icon-plus';
                        opt.buttons[0].text = '添加';
                        create_car_service_window.dialog(opt)
                                                 .dialog('open', true)
                                                 .dialog('hcenter')
                                                 .dialog('move',{top:200})
                    }
                }
            ],
            columns:[[
                {field:'rownum', title:'图标', width:'20%', align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    return '<img src="{{ url('/carService/img/') }}' + row.id +'.png?_' + Date.now() + '"/>';
                }},
                {field:'name', title:'名称', width:'15%', align:'center'},
                {field:'url', title:'关联URL', width:'25%', align:'center'},
                {field:'link_type', title:'关联类型', width:'10%', align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '链接'
                    }
                    else if(value == 2)
                    {
                        return '静态页';
                    }
                    else if(value == 3)
                    {
                        return '本地惠';
                    }
                    else if(value == 4)
                    {
                        return '活动';
                    }
                }},
                {field:'id', title:'操作', width:'20%', align:'center', formatter:function(value, row, index){
                    if(!value) return;
                    return '<div class="btn-group"><button class="btn btn-mini btn-warning edit-car-service" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger del-car-service" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        //本地惠选择控件
        var local_favour_combogrid = $('#car_service_local_favour_combogrid').combogrid({
            url: '/localFavourList.json',
            title: '本地惠列表',
            width: 500,
            panelHeight: 350,
            idField: 'id',
            textField: 'title',
            fitColumns: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //数据过滤,以去除重复id
            loadFilter: function(data){
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;


                var id = null;
                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];
                    if(row.id == id) continue;
                    id = row.id;
                    pass_rows.push(row);
                }
                data.rows = pass_rows;
                data.count = pass_rows.length;
                return data;
            },
            columns: [[
                {field:'typeName', title:'分类', width:'10%', align:'center'},
                {field:'provinceName',title:'省份',width:'8%',align:'center'},
                {field:'title',title:'标题',width:15,align:'center'}
            ]]
        });

        var activity_combogrid = $('#car_service_activity_combogrid').combogrid({
            url: '/activityList.json',
            title: '本地惠列表',
            width: 500,
            panelHeight: 350,
            idField: 'id',
            textField: 'name',
            fitColumns: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //数据过滤,以去除重复id
            loadFilter: function(data){
                //因为两个单元格同事需要id,为避免字段冲突智能复制该字段
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;

                var cur_row = {};

                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];

                    if(cur_row.id != row.id)
                    {
                        cur_row = row;
                        cur_row.pay_items = [];
                        pass_rows.push(cur_row);
                    }

                    if(row.pay_item_id)
                    {
                        cur_row.pay_items.push({
                            id: row.pay_item_id,
                            name: row.pay_item_name,
                            price: row.pay_item_price
                        });
                    }
                }
                data.rows = pass_rows;
                data.count = pass_rows.length;
                return data;
            },
            columns: [[
                {field:'typeName', title:'分类', width:'20%', align:'center'},
                {field:'name',title:'名称',width:'78%',align:'center'}
            ]]
        });

        /**
         * 事件相关
         */

        //关联类型单选按钮切换事件
        var current_show_sel = $('#create_car_service_form [name=link_type]:checked').attr('rel-sel');

        $('#create_car_service_form [name=link_type]').change(function(event){
            var rel_sel = $(this).attr('rel-sel');
            $(current_show_sel).hide();
            $(rel_sel).show();
            current_show_sel = rel_sel;
        });

        $('#create_car_service_form [name=type]:checked').change();

        //.add-car-service 点击事件

        $('.add-car-service').click(function(event){
            $('#car_service_window').window('open', true).window('center');
        });

        //文件(图片)选择器change事件

        $('#create_car_service_form [name=img]').change(function(){
            var file = $(this).prop('files')[0];
            var reader = new FileReader();
            reader.onload = function(event){
                var result = event.target.result;
                $('#thumbnail_img').attr('src', result).fadeIn(1000);
            };
            reader.readAsDataURL(file);
        });

        //删除汽车服务按钮点击事件
        $(document).on('click', '.del-car-service', function(event){
            var car_service_id = $(this).attr('data-id');
            $.messager.confirm('确定删除汽车服务', '该操作将删除汽车服务,确定要删除?', function(is_ok){
                if(!is_ok) return;
                delCarService({id: car_service_id}, function(data){
                    car_service_grid.datagrid('reload');
                });
            });
        });

        //编辑汽车服务按钮点击事件
        $(document).on('click', '.edit-car-service', function(event){
            var car_service = $('#car_service_grid').datagrid('getSelected');
            $('#create_car_service_form [name=id]').val(car_service.id);
            $('#create_car_service_form [name=name]').val(car_service.name);
            $('#thumbnail_img').attr('src', "{{url('/carService/img/')}}" + car_service.id + '.png?_=' + Date.now());
            $('#thumbnail_img').fadeIn(1000);
            $('#create_car_service_form [name=link_type][value="' + car_service.link_type +'"]').click();
            if(car_service.link_type == 1)
            {
                $('#create_car_service_form [name=url]').val(car_service.url);
            }
            else if(car_service.link_type == 2)
            {
                content_editor.setData(car_service.content);
            }
            else if(car_service.link_type == 3)
            {
                local_favour_combogrid.combogrid('setValue', car_service.rel_id);
            }
            else if(car_service.link_type == 4)
            {
                activity_combogrid.combogrid('setValue', car_service.rel_id);
            }
            $('#create_car_service_form [name=content_id]').val(car_service.content_id);
            create_car_service_window.data('win_state', 'update');
            var opt = create_car_service_window.dialog('options');
            opt.title = '编辑汽车服务';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '更新';
            create_car_service_window.dialog(opt)
                                     .dialog('open').dialog('hcenter').dialog('move', {top:200});
        });

        //.car-service-pic .tools.tools-unuse(图片右上角的X)点击事件

        $(document).on('click', '.tools-unuse', function(event){
            event.stopPropagation();
            var car_service_id = $(this).attr('data-id');
            unuseCarservice(car_service_id, function(data){
                $('.car-service-item[data-id="' + car_service_id +'"]').fadeOut(1000,function(event){
                    $(this).remove();
                });
            });
            return false;
        });

        //图片右下角编辑按钮点击事件

        var editing_use = null;

        $(document).on('click', '.tools-edit', function(event){
            event.stopPropagation();
            var item_id = $(this).attr('data-item-id');
            var car_service = $('#'+item_id).data('car_service');
            $('#create_car_service_form [name=id]').val(car_service.id);
            $('#create_car_service_form [name=name]').val(car_service.name);
            $('#thumbnail_img').attr('src', "{{url('/carService/img/')}}" + car_service.id + '.png?_=' + Date.now());
            $('#thumbnail_img').fadeIn(1000);
            $('#create_car_service_form [name=link_type][value="' + car_service.link_type +'"]').click();
            if(car_service.link_type == 1)
            {
                $('#create_car_service_form [name=url]').val(car_service.url);
            }
            else if(car_service.link_type == 2)
            {
                content_editor.setData(car_service.content);
            }
            else if(car_service.link_type == 3)
            {
                local_favour_combogrid.combogrid('setValue', car_service.rel_id);
            }
            else if(car_service.link_type == 4)
            {
                activity_combogrid.combogrid('setValue', car_service.rel_id);
            }
            $('#create_car_service_form [name=content_id]').val(car_service.content_id);
            create_car_service_window.data('win_state', 'update');
            var opt = create_car_service_window.dialog('options');
            opt.title = '编辑汽车服务';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '更新';
            create_car_service_window.dialog(opt)
                                     .dialog('open').dialog('hcenter').dialog('move', {top:200});
            return false;
        });

        /**
         * 数据相关
         */
        //获取使用列表
        getCarServiceList({is_use:true}, function(data){
            $(data.rows).each(function(i, n){
                renderCarserviceUseList(n);
            });
        })

        
        function getCarServiceList(criteria, callback)
        {
            $.ajax({
                url: '/carService/list.json',
                method: 'POST',
                data: {criteria: criteria},
                dataType: 'json',
                global: true
            }).done(callback);
        }

        function addCarService(data, callback)
        {
            $.ajax({
                url: '/carService.json',
                method: 'POST',
                data: {creates: [data]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务添加失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务添加成功'
                    });
                    if(typeof callback == 'function') callback(data);
                }
            });
        }

        function updateCarService(data, callback)
        {
            $.ajax({
                url: '/carService/' + data.id + '.json',
                method: 'PUT',
                data: {updates: [data]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务更新失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务更新成功'
                    });
                    if(typeof callback == 'function') callback(data);
                }
            });
        }

        function delCarService(data, callback)
        {
            $.ajax({
                url: '/carService/' + data.id + '.json',
                method: 'DELETE',
                data: {updates: [data]},
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务删除失败'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '汽车服务删除成功'
                    });
                    if(typeof callback == 'function') callback(data);
                }
            });
        }
        
        //页面离开事件
        CarMate.page.on_leave = function(){
            //销毁复选框
            local_favour_combogrid.combogrid('destroy');
            activity_combogrid.combogrid('destroy');
            //销毁窗口
            $('#car_service_window').window('destroy');
            create_car_service_window.dialog('destroy');

            //清除动态绑定事件
            $(document).off('click', '.edit-car-service');
            $(document).off('click', '.del-car-service');
            $(document).off('click', '.tools-unuse');
            $(document).off('click', '.tools-edit');
        }

    };

</script>
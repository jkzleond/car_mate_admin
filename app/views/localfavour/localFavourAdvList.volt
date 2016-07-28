<style type="text/css" rel="stylesheet">

    .top {
        z-index: 999;
    }

    #local_favour_adv_list .item{
        position:relative;
        width:240px;
        height:140px;
        margin-left: 5px;
        text-align: center;
        float:left;
    }
    .scroll-pic img {
        width: 100%;
    }
    .scroll-pic .tools {
        display:none;
        position:absolute;
        right: 0px;
        font-size: 20px;
        z-index: 999;
        cursor: pointer;
    }
    .scroll-pic .tools.tools-top {
        top: 0px;
    }
    .scroll-pic .tools.tools-bottom {
        bottom: 0px;
    }
    .scroll-pic:hover .tools {
        display: block;
    }
    .scroll-pic .url {
        position: absolute;
        bottom: 5px;
        background-color:#000;
        opacity: 0.8;
        color: #fff;
        width:240px;
    }
    .scroll-pic:hover .url {
        background-color: #eee;
        color: black;
    }
    .add-pic:hover, .tools:hover {
        color:#0866c6;
    }

    div.datagrid * {
        vertical-align: middle;
    }
    div.win-loading {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 999;
        color: #000;
        display: none;
    }

    div.win-loading>img:first-child {
        position: absolute;
        top: 0px;
        left: 0px;
        bottom: 0px;
        right: 0px;
        margin:auto;
        width:25px;
        height:25px
    }

</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">首页推广管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div class="span12">
                    <span class="label">正在使用的首页推广</span><span><i class="icon-hand-up"></i>拖放可以改变显示顺序</span>
                    <div id="local_favour_adv_list">
                        <div id="local_favour_adv_use_list" style="fload:left">
                            {% for local_favour_adv in adv_use_list %}
                            <div class="drag-item pull-left">
                                <div class="well well-small scroll-pic item" data-id="{{ local_favour_adv['id'] }}">
                                    <i class="iconfa-remove tools tools-top tools-unuse"></i>
                                <span class="tools tools-bottom">
                                    <i class="iconfa-edit tools-edit"></i>
                                </span>
                                    <img class="pic" src="{{ local_favour_adv['adv3'] }}" />
                                    {% if local_favour_adv['type'] == 'Link' %}
                                    <div class="url" style=""><span>{{ local_favour_adv['contents'] }}</span></div>
                                    {% endif %}
                                </div>
                            </div>
                            {% endfor%}
                        </div>
                        <div class="add-pic item  well well-small" style="text-align: center; vertical-align: middle; line-height:150px">
                            <i class="iconfa-plus" style="font-size:40px;margin-top: 40px"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 首页推广表格窗口 -->
            <div id="local_favour_adv_window">
                <div id="local_favour_adv_grid">
                </div>
            </div>

            <!-- 添加推广窗口 -->
            <div id="create_local_favour_adv_window" style="position:relative;">
                <div class="win-loading"><img src="{{  url('/images/loading.gif') }}"/></div>
                <form id="create_local_favour_adv_form" class="well well-small" action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id">
                    <div class="row-fluid">
                        <span class="label">推广省份</span>
                        <select name="province_id" class="input-small">
                            {% for province in province_list %}
                            {% if current_province_id == province.id %}
                            <option value="{{ province.id }}" selected>{{ province.name }}</option>
                            {% else %}
                            <option value="{{ province.id }}">{{ province.name }}</option>
                            {% endif%}
                            {% endfor%}
                        </select>
                    </div>
                    <div class="row-fluid">
                        <span class="label">图片</span>
                        <input name="pic" type="file" class="input-small" accept="image/gif, image/png, image/jpeg"/>
                        <img id="thumbnail_adv" style="height: 100px; display: none" />
                    </div>
                    <div class="row-fluid">
                            <span class="label">推广消息类型</span>
                            <input name="type" type="radio" value="LocalFavour" rel-sel="#adv_local_favour" checked /><span>本地惠</span>
                            <input name="type" type="radio" value="Notice" rel-sel="#adv_notice"/><span>公告</span>
                            <input name="type" type="radio" value="Link" rel-sel="#adv_link"/><span>URL</span>
                            <input name="type" type="radio" value="Other" rel-sel="#adv_other_editor"/><span>其他</span>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">推广信息</span>
                            <div>
                                <select name="local_favour_id" id="adv_local_favour"></select>
                            </div>
                            <div style="display: none">
                                <select name="notice_id" id="adv_notice"></select>
                            </div>
                            <div style="display: none">
                                <input id="adv_link" name="link" type="text" class="input-xxlarge"/>
                            </div>
                            <div style="display: none">
                                <textarea name="contents"  class="ckeditor" id="adv_other_editor"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--   首页推广表格    -->
            <div class="row-fluid">
                <div id="local_favour_local_favour_adv_grid"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    //页面加载完成事件
    CarMate.page.on_loaded = function(){
        //初始化记录使用列表数据的数组
        var local_favour_adv_use = [];
        var local_favour_adv_unuse = [];
        var ad_is_change = false;

        $('#local_favour_adv_use_list .scroll-pic').each(function(index, item){
            var id = $(item).attr('data-id');
            local_favour_adv_use.push(id);
        });

        function unuseAd(id)
        {
            var start = local_favour_adv_use.indexOf(id);
            if (start != -1)
            {
                local_favour_adv_use.splice(start, 1);
            }
            if(local_favour_adv_unuse.indexOf(id) == -1)
            {
                local_favour_adv_unuse.push(id);
                ad_is_change = true;
                startAdTask();
                return true;
            }
            return false;
        }

        function useAd(id)
        {
            var start = local_favour_adv_unuse.indexOf(id);
            if (start != -1)
            {
                local_favour_adv_unuse.splice(start, 1);
            }
            if(local_favour_adv_use.indexOf(id) == -1)
            {
                local_favour_adv_use.push(id);
                ad_is_change = true;
                startAdTask();
                return true;
            }
            return false;
        }

        function resortAd()
        {
            //清空数组
            local_favour_adv_use.splice(0);
            $('#local_favour_adv_use_list .scroll-pic').each(function(index, item){
                var id = $(item).attr('data-id');
                local_favour_adv_use.push(id);
            });
            ad_is_change = true;
            startAdTask();
        }


        function startAdTask()
        {
            setTimeout(function(){
                if(!ad_is_change) return;

                $.each(local_favour_adv_use, function(index, item){
                    var local_favour_adv = new LocalFavourAdv();
                    local_favour_adv.set('id', item);
                    local_favour_adv.set('is_state', 1);
                    local_favour_adv.set('is_order', index + 1);
                    local_favour_adv.update();
                });

                $.each(local_favour_adv_unuse, function(index, item){
                    var local_favour_adv = new LocalFavourAdv();
                    local_favour_adv.set('id', item);
                    local_favour_adv.set('is_state', 0);
                    local_favour_adv.update();
                });

                local_favour_adv_unuse.splice(0);
                LocalFavourAdv.commit();
                ad_is_change = false;
            },500);
        }

        function renderAdUseList(data)
        {

            $('#local_favour_adv_use_list').append(
                    '<div class="drag-item pull-left">' +
                    '<div class="well well-small scroll-pic item" data-id="' + data.id +'">' +
                    '<i class="iconfa-remove tools tools-top tools-unuse"></i>' +
                    '<span class="tools tools-bottom">' +
                    '<i class="iconfa-edit tools-edit"></i>' +
                    '</span>' +
                    '<img class="pic" src="' + data.adv3 + '" />' +
                    '<div class="url" style="position: absolute; bottom: 5px; background-color:#eee; width:240px;"><span>' + (data.type == 'Link' ? data.contents : (data.title ? data.title : '')) + '</span></div>' +
                    '</div>' +
                    '</div>'
            );

            registerDraggable();
        }

        function registerDraggable()
        {
            $('#local_favour_adv_list .drag-item').draggable({
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
                    $(source).insertBefore(this);
                    resortAd();
                }
            });
        }

        //上传文件
        function upload(files, on_progress)
        {
            var form_data = new FormData();

            for(var key in files)
            {
                //console.log(files[key]);
                form_data.append(key, files[key]);
            }
            return $.ajax({
                url: '/upload.json',
                method: 'POST',
                data: form_data,
                dataType: 'json',
                xhr: function(){
                    var new_xhr = $.ajaxSettings.xhr();
                    if(new_xhr.upload)
                    {
                        new_xhr.upload.addEventListener('progress', function(e){
                            if(e.lengthComputable && typeof on_progress == 'function')
                            {
                                on_progress(e.loaded, e.total);
                            }
                        }, false);
                    }
                    return new_xhr;
                },
                processData: false,
                contentType: false,
                global: true
            });
        }

        /**
         * 控件相关
         */
        //创建ckeditor
        var editor = CKEDITOR.replace( 'adv_other_editor', {
            enterMode: CKEDITOR.ENTER_P,
            height: 270,
            removePlugins : 'save',
            filebrowserImageUploadUrl : 'ckUploadImage?command=QuickUpload&type=Images'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";

        //本地惠选择控件
        var local_favour_cmgrid = $('#adv_local_favour').combogrid({
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

        //公告选择控件
        var adv_notice_cmgrid = $('#adv_notice').combogrid({
            url: '/notice/list.json',
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
            pageNumber:1,///(当前页码)
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
        
        //首页推广窗口
        var local_favour_adv_window = $('#local_favour_adv_window').window({
            title: '双击列表项可将广告添加到滚动列表',
            iconCls: 'icon-hand-up',
            width: 900,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //添加首页推广窗口
        var create_local_favour_adv_dialog = $('#create_local_favour_adv_window').dialog({
            title: '添加首页推广',
            iconCls: 'icon-plus',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-ok',
                    handler: function(e){

                        var local_favour_adv = new LocalFavourAdv();

                        var province_id = $('#create_local_favour_adv_form [name=province_id]').val();
                        var type = $('#create_local_favour_adv_form [name=type]:checked').val();
                        var rele_id = null;
                        var contents = null;

                        if( type == 'LocalFavour' )
                        {
                            rele_id = $('#adv_local_favour').combogrid('getValue');
                            province_id = $('#adv_local_favour').combogrid('grid').datagrid('getSelected').provinceId;
                        }
                        else if( type == 'Notice' )
                        {
                            rele_id = $('#adv_notice').combogrid('getValue');
                            province_id = $('#adv_notice').combogrid('grid').datagrid('getSelected').provinceId;

                        }
                        else if( type == 'Link')
                        {
                            contents = $('#create_local_favour_adv_form [name=link]').val();
                        }
                        else
                        {
                            contents = editor.getData();
                        }

                        var adv_src = $('#create_local_favour_adv_form #thumbnail_adv').attr('src');

                        local_favour_adv.set('adv_src', adv_src);
                        local_favour_adv.set('type', type);
                        local_favour_adv.set('province_id', province_id);

                        if(rele_id)
                        {
                            local_favour_adv.set('rele_id', rele_id);
                        }

                        if(contents)
                        {
                            local_favour_adv.set('contents', contents);
                        }

                        var state = create_local_favour_adv_dialog.data('state');

                        if(state == 'create')
                        {
                            local_favour_adv.create();
                        }
                        else if(state == 'update')
                        {
                            var id = $('#create_local_favour_adv_form [name=id]').val();
                            local_favour_adv.set('id', id);
                            local_favour_adv.update();
                            
                        }

                        LocalFavourAdv.commit();
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(e){
                        create_local_favour_adv_dialog.dialog('close');
                    }
                }
            ],
            onBeforeClose: function(){
                //关闭前清复位单
                $('#create_local_favour_adv_form [name=id]').val('');
                $('#create_local_favour_adv_form [name=type][value=LocalFavour]').click();
                $('#create_local_favour_adv_form [name=link]').val('');
                $('#thumbnail_adv').attr('src', '').hide();
            }
        });

        //首页推广表格

        var local_favour_adv_grid = $('#local_favour_adv_grid').datagrid({
            url: '/localFavourAdvList.json',
            title: '首页推广列表',
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
                if(useAd(row.id))
                {
                    renderAdUseList(row);
                }
                $('#local_favour_adv_window').window('close');
            },
            toolbar:[
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(){

                        $('#create_local_favour_adv_form [name=id]').val('');
                        $('#create_local_favour_adv_form [name=pic]').val('');
                        $('#thumbnail_adv').attr('src', '');
                        $('#thumbnail_adv').hide();
                        $('#create_local_favour_adv_form [name=type][value=LocalFavour]').click();
                        $('#create_local_favour_adv_form [name=link]').val('');
                        local_favour_cmgrid.combogrid('clear');
                        create_local_favour_adv_dialog.data('state', 'create');
                        var opts = create_local_favour_adv_dialog.dialog('options');
                        opts.buttons[0].text = '添加';
                        opts.iconCls = 'icon-plus';
                        opts.title = '添加首页推广';
                        create_local_favour_adv_dialog.dialog(opts);
                        create_local_favour_adv_dialog.window('open', true).window('center');
                    }
                }
            ],
            columns:[[
                {field:'adv3', title:'图片', width:'35%', align:'center', formatter: function(value, row, index){
                    if(!value) return '无';
                    return '<img src="' + value + '"/>';
                }},
                {field:'type', title:'类型', width:'10%', align:'center', formatter: function(value, row, index){
                    if(value == 'LocalFavour')
                    {
                        return '本地惠';
                    }
                    else if(value == 'Notice')
                    {
                        return '公告';
                    }
                    else if(value == 'Link')
                    {
                        return '链接';
                    }
                    else if(value == 'Other')
                    {
                        return '其他';
                    }
                }},
                {field: 'content', title: 'url/标题', width: '30%', align: 'center', formatter: function(value, row, index){
                    if(row.type == 'LocalFavour' || row.type  == 'Notice' || row.type == 'Other')
                    {
                        return row.title;
                    }
                    else
                    {
                        return row.contents;
                    }
                }},
                {field:'id', title:'操作', width:'20%', align:'center', formatter:function(value, row, index){
                    if(!value) return;
                    return '<div class="btn-group"><button class="btn btn-mini btn-warning edit-scroll-ad" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger del-scroll-ad" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        /**
         * 事件相关
         */

        //关联类型单选按钮切换事件
        var current_show_sel = $('#create_local_favour_adv_form [name=type]:checked').attr('rel-sel');

        $('#create_local_favour_adv_form [name=type]').change(function(event){
            var rel_sel = $(this).attr('rel-sel');
            $(current_show_sel).parent().hide();
            $(rel_sel).parent().show();
            current_show_sel = rel_sel;
        });

        //.add-pic 点击事件

        $('.add-pic').click(function(event){
            $('#local_favour_adv_window').window('open', true).window('center');
        });

        //文件(图片)选择器change事件

        $('#create_local_favour_adv_form [name=pic]').change(function(){
            var img_file = $(this).prop('files')[0];
            upload({img_file: img_file}, function(loaded, total){
                console.log(arguments);
            }).done(function(resp){
                if(resp.success)
                {
                    var img_src = resp.data['img_file'];
                    $('#thumbnail_adv').attr('src', img_src).hide().fadeIn(1000);
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '图片上传失败'
                    });
                    return false;
                }
            });
        });


        //删除首页推广按钮点击事件
        $(document).on('click', '.del-scroll-ad', function(event){
            var local_favour_adv_id = $(this).attr('data-id');
            $.messager.confirm('删除首页推广', '确认删除该首页推广?', function(is_ok){
                    if(is_ok)
                    {
                        //获取绑定到删除对话框的 local_favour_adv id
                        var local_favour_adv = new LocalFavourAdv();
                        local_favour_adv.set('id', local_favour_adv_id);
                        local_favour_adv.delete();
                        LocalFavourAdv.commit();
                    }
                }
            );
        });

        //编辑首页推广按钮点击事件
        $(document).on('click', '.edit-scroll-ad', function(event){

            var local_favour_adv = local_favour_adv_grid.datagrid('getSelected');

            $('#create_local_favour_adv_form [name=id]').val(local_favour_adv.id);
            $('#create_local_favour_adv_form [name=provice_id]').val(local_favour_adv.provinceId);
            $('#create_local_favour_adv_form [name=pic]').val('');
            if(local_favour_adv.adv3)
            {
                $('#thumbnail_adv').attr('src', local_favour_adv.adv3);
                $('#thumbnail_adv').fadeIn(1000);
            }

            if(local_favour_adv.type == 'LocalFavour')
            {
                $('#create_local_favour_adv_form [name=type][value=LocalFavour]').click();
                local_favour_cmgrid.combogrid('setValue', local_favour_adv.releId);
            }
            else if(local_favour_adv.type == 'Notice')
            {
                $('#create_local_favour_adv_form [name=type][value=Notice]').click();
                local_favour_cmgrid.combogrid('setValue', local_favour_adv.releId);

            }
            else if(local_favour_adv.type == 'Link')
            {
                $('#create_local_favour_adv_form [name=type][value=Link]').click();
                $('#create_local_favour_adv_form [name=link]').val(local_favour_adv.contents);
            }
            else if(local_favour_adv.type == 'Other')
            {
                $('#create_local_favour_adv_form [name=type][value=Other]').click();
                editor.setData(local_favour_adv.contents);
            }

            create_local_favour_adv_dialog.data('state', 'update');
            var opts = create_local_favour_adv_dialog.dialog('options');
            opts.buttons[0].text = '修改';
            opts.iconCls = 'icon-edit';
            opts.title = '修改首页推广';
            create_local_favour_adv_dialog.dialog(opts);
            create_local_favour_adv_dialog.window('open', true).window('center');
        });

        //.scroll-pic .tools.tools-unuse(图片右上角的X)点击事件

        $(document).on('click', '.tools-unuse', function(event){
            event.stopPropagation();
            $(this).parent().fadeOut(1000,function(event){
                var local_favour_adv_id = $(this).attr('data-id');
                unuseAd(local_favour_adv_id);
                $(this).remove();
            });
            return false;
        });

        //图片右下角编辑按钮点击事件

        var editing_use = null;

        $(document).on('click', '.tools-edit', function(event){
            event.stopPropagation();
            var parent = $(this).parent().parent();

            editing_use = parent;


            create_local_favour_adv_dialog.data('state', 'update');
            var opts = create_local_favour_adv_dialog.dialog('options');
            opts.buttons[0].text = '修改';
            opts.iconCls = 'icon-edit';
            opts.title = '修改首页推广';
            create_local_favour_adv_dialog.dialog(opts);
            create_local_favour_adv_dialog.window('open', true).window('center');
            create_local_favour_adv_dialog.find('win-loading').show();
            var local_favour_adv_id = parent.attr('data-id');
            LocalFavourAdv.getById(local_favour_adv_id).done(function(resp){
                create_local_favour_adv_dialog.find('win-loading').hide();
                if(resp.success)
                {
                    var local_favour_adv = resp.data;
                    $('#create_local_favour_adv_form [name=id]').val(local_favour_adv.id);
                    $('#create_local_favour_adv_form [name=provice_id]').val(local_favour_adv.provinceId);

                    $('#create_local_favour_adv_form [name=pic]').val('');
                    if(local_favour_adv.adv3)
                    {
                        $('#thumbnail_adv').attr('src', local_favour_adv.adv3);
                        $('#thumbnail_adv').fadeIn(1000);
                    }

                    if(local_favour_adv.type == 'LocalFavour')
                    {
                        $('#create_local_favour_adv_form [name=type][value=LocalFavour]').click();
                        local_favour_cmgrid.combogrid('setValue', local_favour_adv.releId);
                    }
                    else if(local_favour_adv.type == 'Notice')
                    {
                        $('#create_local_favour_adv_form [name=type][value=Notice]').click();
                        local_favour_cmgrid.combogrid('setValue', local_favour_adv.releId);

                    }
                    else if(local_favour_adv.type == 'Link')
                    {
                        $('#create_local_favour_adv_form [name=type][value=Link]').click();
                        $('#create_local_favour_adv_form [name=link]').val(local_favour_adv.contents);
                    }
                    else if(local_favour_adv.type == 'Other')
                    {
                        $('#create_local_favour_adv_form [name=type][value=Other]').click();
                        editor.setData(local_favour_adv.contents);
                    }
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '首页推广数据获取失败'
                    });
                    create_local_favour_adv_dialog.dialog('close');
                }
            });

            return false;
        });


        //拖放事件

        registerDraggable();

        /**
         * 数据相关
         */
        //LocalFavourAdv模型
        var LocalFavourAdv = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){
                    if(action == 'update' || action == 'create') return '/localFavourAdv.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            })
                            var ids_str = ids.join('-');
                            return '/localFavourAdv/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/localFavourAdv/' + condition.id + '.json';
                        }
                    }
                },
                getById: function(id){
                    return $.ajax({
                        url: '/localFavourAdv/' + id + '.json',
                        method: 'GET',
                        dataType: 'json',
                        global: true
                    });
                }
            }
        });


        LocalFavourAdv.on('beforeCreate', function(){
            create_local_favour_adv_dialog.find('.win-loading').show();
            $('#create_local_favour_adv_btn').prop('disabled', true);
            $('#create_local_favour_adv_btn').addClass('btn-inverse');
        });

        LocalFavourAdv.on('created', function(event, data){
            if(!data.success) return;
            create_local_favour_adv_dialog.find('.win-loading').hide();
            $('#create_local_favour_adv_btn').prop('disabled', false);
            $('#create_local_favour_adv_btn').removeClass('btn-inverse');

            $('#local_favour_adv_grid').datagrid('reload',{
                page: 1
            });

            $('#create_local_favour_adv_window').window('close', true);

        });

        LocalFavourAdv.on('beforeUpdate', function(){
            create_local_favour_adv_dialog.find('.win-loading').show();
            $('#create_local_favour_adv_btn').prop('disabled', true);
            $('#create_local_favour_adv_btn').addClass('btn-inverse');
        });

        LocalFavourAdv.on('updated', function(event, data){
            if(!data.success) {
                $.messager.show({
                    title: '系统消息',
                    msg: '首页推广更新失败'
                });
                return;
            }

            create_local_favour_adv_dialog.find('.win-loading').hide();
            $('#create_local_favour_adv_btn').prop('disabled', false);
            $('#create_local_favour_adv_btn').removeClass('btn-inverse');

            $('#create_local_favour_adv_window').window('close', true);

            $('#local_favour_adv_grid').datagrid('reload');
            $.messager.show({
                title: '系统消息',
                msg: '首页推广更新成功'
            });
        });

        LocalFavourAdv.on('deleted', function(event, data){
            if(!data.success) {
                $.messager.show({
                    title: '系统消息',
                    msg: '首页推广删除失败'
                });
                return;
            }
            $('#local_favour_adv_grid').datagrid('reload');
            $.messager.show({
                title: '系统消息',
                msg: '首页推广删除成功'
            });
        });


        //页面离开事件
        CarMate.page.on_leave = function(){
            //销毁复选框
            local_favour_cmgrid.combogrid('destroy');
            adv_notice_cmgrid.combogrid('destroy');

            //销毁窗口
            local_favour_adv_window.window('destroy');
            create_local_favour_adv_dialog.window('destroy');
            //销毁对话框
            $('#del_local_favour_adv_dialog').window('destroy');

            //清除动态绑定事件
            $(document).off('click', '.del-scroll-ad');
            $(document).off('click', '.tools-unuse');
            $(document).off('click', '.tools-edit');
            $(document).off('click', '.edit-scroll-ad');

        }

    };
</script>
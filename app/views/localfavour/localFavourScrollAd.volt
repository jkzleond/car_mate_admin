<style type="text/css" rel="stylesheet">

    .top {
        z-index: 999;
    }

    #scroll_ad_list .item{
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
    .add-pic:hover, .tools:hover {
        color:#0866c6;
    }

    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">滚动广告管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div class="span12">
                    <span class="label">正在使用的滚动广告</span><span><i class="icon-hand-up"></i>拖放可以改变显示顺序</span>
                    <div id="scroll_ad_list">
                        <div id="scroll_ad_use_list" style="fload:left">
                            {% for scroll_ad in use_list %}
                            <div class="drag-item pull-left">
                                <div class="well well-small scroll-pic item" data-id="{{ scroll_ad.id }}">
                                    <i class="iconfa-remove tools tools-top tools-unuse"></i>
                                <span class="tools tools-bottom">
                                    <i class="iconfa-edit tools-edit"></i>
                                </span>
                                    <img class="pic" src="data:image/png;base64,{{ scroll_ad.picData }}" />
                                    <div class="url" style="position: absolute; bottom: 5px; background-color:#eee; width:240px;"><span>{{ scroll_ad.redirectUrl }}</span></div>
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

            <!-- 滚动广告表格窗口 -->
            <div id="scroll_ad_window">
                <div id="scroll_ad_grid">
                </div>
            </div>

            <!-- 添加滚动窗口 -->
            <div id="create_scroll_ad_window">
                <form id="create_scroll_ad_form" class="well well-small" action="" method="post" enctype="multipart/form-data">
                    <div class="row-fluid">
                        <div class="span10">
                            <span class="label">图片</span>
                            <input name="pic" type="file" class="input-small" accept="image/gif, image/png, image/jpeg"/>
                            <img id="thumbnail_pic" style="height: 100px; display: none" />
                        </div>
                        <div class="span2">
                            <input name="id" type="hidden"/>
                            <button id="create_scroll_ad_btn" class="btn btn-primary">添加</button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span5">
                            <span class="label">关联类型</span>
                            <input name="type" type="radio" value="LocalFavour" rel-sel="#scroll_local_favour" checked /><span>本地惠</span>
                            <input name="type" type="radio" value="Link" rel-sel="#scroll_link"/><span>URL</span>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">关联内容</span>
                            <div>
                                <select name="local_favour_id" id="scroll_local_favour"></select>
                            </div>
                            <div style="display: none">
                                <input id="scroll_link" name="link" type="text"" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!--   滚动广告表格    -->
            <div class="row-fluid">
                <div id="local_favour_scroll_ad_grid"></div>
            </div>

            <!--   删除确认对话框   -->
            <div id="del_scroll_ad_dialog">
                <div>
                    <h4>是否删除滚动广告?</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    //页面加载完成事件
    CarMate.page.on_loaded = function(){

        //初始化记录使用列表数据的数组
        var scroll_ad_use = [];
        var scroll_ad_unuse = [];
        var ad_is_change = false;

        $('#scroll_ad_use_list .scroll-pic').each(function(index, item){
            var id = $(item).attr('data-id')
            scroll_ad_use.push(id);
        });

        function unuseAd(id)
        {
            var start = scroll_ad_use.indexOf(id);
            if (start != -1)
            {
                scroll_ad_use.splice(start, 1);
            }
            if(scroll_ad_unuse.indexOf(id) == -1)
            {
                scroll_ad_unuse.push(id);
                ad_is_change = true;
                startAdTask();
                return true;
            }
            return false;
        }

        function useAd(id)
        {
            var start = scroll_ad_unuse.indexOf(id);
            if (start != -1)
            {
                scroll_ad_unuse.splice(start, 1);
            }
            if(scroll_ad_use.indexOf(id) == -1)
            {
                scroll_ad_use.push(id);
                ad_is_change = true;
                startAdTask();
                return true;
            }
            return false;
        }

        function resortAd()
        {
            //清空数组
            scroll_ad_use.splice(0);
            $('#scroll_ad_use_list .scroll-pic').each(function(index, item){
                var id = $(item).attr('data-id');
                scroll_ad_use.push(id);
            });
            ad_is_change = true;
            startAdTask();
        }


        function startAdTask()
        {
            setTimeout(function(){
                if(!ad_is_change) return;

                $.each(scroll_ad_use, function(index, item){
                    var scroll_ad = new LocalFavourScrollAd();
                    scroll_ad.set('id', item);
                    scroll_ad.set('is_state', 1);
                    scroll_ad.set('show_order', index + 1);
                    scroll_ad.update();
                });

                $.each(scroll_ad_unuse, function(index, item){
                    var scroll_ad = new LocalFavourScrollAd();
                    scroll_ad.set('id', item);
                    scroll_ad.set('is_state', 0);
                    scroll_ad.update();
                });

                scroll_ad_unuse.splice(0);

                //console.log(LocalFavourScrollAd._updates);

                LocalFavourScrollAd.commit();

                ad_is_change = false;

            },5000);
        }

        function renderAdUseList(data)
        {
            $('#scroll_ad_use_list').append(
                '<div class="drag-item pull-left">' +
                    '<div class="well well-small scroll-pic item" data-id="' + data.id +'">' +
                        '<i class="iconfa-remove tools tools-top tools-unuse"></i>' +
                        '<span class="tools tools-bottom">' +
                            '<i class="iconfa-edit tools-edit"></i>' +
                        '</span>' +
                        '<img class="pic" src="data:image/png;base64,' + data.picData + '" />' +
                        '<div class="url" style="position: absolute; bottom: 5px; background-color:#eee; width:240px;"><span>' + data.redirectUrl + '</span></div>' +
                    '</div>' +
                '</div>'
            );

            registerDraggable();
        }

        function registerDraggable()
        {
            $('#scroll_ad_list .drag-item').draggable({
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

        /**
         * 控件相关
         */

        //滚动广告窗口
        $('#scroll_ad_window').window({
            title: '双击列表项可将广告添加到滚动列表',
            iconCls: 'icon-hand-up',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //添加滚动广告窗口
        $('#create_scroll_ad_window').window({
            title: '添加滚动广告',
            iconCls: 'icon-plus',
            width: 600,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //删除滚动广告对话框
        $('#del_scroll_ad_dialog').dialog({
            title: '滚动广告删除',
            iconCls: 'icon-warning-sign',
            closed: true,
            modal: true,
            buttons:[
                {
                    text: '确定',
                    iconCls: 'icon-ok',
                    handler: function(){
                        //获取绑定到删除对话框的 scroll_ad id
                        var id = $('#del_scroll_ad_dialog').data('id');
                        var scroll_ad = new LocalFavourScrollAd();
                        scroll_ad.set('id', id);
                        scroll_ad.delete();
                        LocalFavourScrollAd.commit();
                        $('#del_scroll_ad_dialog').dialog('close', true);
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#del_scroll_ad_dialog').dialog('close', true);
                    }
                }
            ]
        });

        //滚动广告表格

        $('#scroll_ad_grid').datagrid({
            url: '/localFavourScrollAdList.json',
            title: '滚动广告列表',
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
                $('#scroll_ad_window').window('close');
            },
            toolbar:[
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(){

                        $('#create_scroll_ad_form [name=id]').val('');
                        $('#create_scroll_ad_form [name=pic]').val('');
                        $('#thumbnail_pic').attr('src', '');
                        $('#thumbnail_pic').hide();
                        $('#create_scroll_ad_form [name=type][value=LocalFavour]').click();
                        $('#create_scroll_ad_form [name=link]').val('');
                        $('#scroll_local_favour').combogrid('clear');
                        $('#create_scroll_ad_btn').attr('data-state', 'create');
                        $('#create_scroll_ad_btn').text('添加');
                        $('#create_scroll_ad_window').window({iconCls: 'icon-plus'});
                        $('#create_scroll_ad_window').window('setTitle', '添加滚动广告')
                        $('#create_scroll_ad_window').window('open', true).window('center');
                    }
                }
            ],
            columns:[[
                {field:'picData', title:'图片', width:'35%', align:'center', formatter: function(value, row, index){
                    if(!value) return '无';
                    return '<img src="data:image/png;base64,' + value + '"/>';
                }},
                {field:'redirectUrl', title:'关联URL', width:'40%', align:'center'},
                {field:'id', title:'操作', width:'20%', align:'center', formatter:function(value, row, index){
                    if(!value) return;
                    return '<div class="btn-group"><button class="btn btn-mini btn-warning edit-scroll-ad" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger del-scroll-ad" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        //本地惠选择控件
        $('#scroll_local_favour').combogrid({
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


        /**
         * 事件相关
         */

        //关联类型单选按钮切换事件
        var current_show_sel = $('#create_scroll_ad_form [name=type]:checked').attr('rel-sel');

        $('#create_scroll_ad_form [name=type]').change(function(event){
            var rel_sel = $(this).attr('rel-sel');
            $(current_show_sel).parent().hide();
            $(rel_sel).parent().show();
            current_show_sel = rel_sel;
        });

        //.add-pic 点击事件

        $('.add-pic').click(function(event){
            $('#scroll_ad_window').window('open', true).window('center');
        });

        //文件(图片)选择器change事件

        $('#create_scroll_ad_form [name=pic]').change(function(){
            var file = $(this).prop('files')[0];
            var reader = new FileReader();
            reader.onload = function(event){
                var result = event.target.result;
                $('#thumbnail_pic').attr('src', result).fadeIn(1000);
                $('#create_scroll_ad_btn').prop('disabled', false);
                $('#create_scroll_ad_btn').removeClass('btn-inverse');
            };
            reader.readAsDataURL(file);
            $('#create_scroll_ad_btn').prop('disabled', true);
            $('#create_scroll_ad_btn').addClass('btn-inverse');
        });

        //添加(提交)滚动广告按钮点击事件
        $('#create_scroll_ad_btn').click(function(event){
            event.preventDefault();

            var pic_data_src = $('#thumbnail_pic').attr('src');

            var pic_data = pic_data_src.match(/data:(?:.*);base64,(.*)/)[1];

            var type = $('#create_scroll_ad_form [name=type]:checked').val();

            var redirect_url = null;

            if(type == 'LocalFavour')
            {
                var local_favour_id = $('#scroll_local_favour').combogrid('getValue');
                redirect_url = 'http://116.55.248.76:8080/car/favourContent.do?id=' + local_favour_id;
            }
            else if(type === 'Link')
            {
                redirect_url = $('#create_scroll_ad_form [name=link]').val();
            }

            var scroll_ad = new LocalFavourScrollAd();

            scroll_ad.set('pic_data', pic_data);
            scroll_ad.set('redirect_url', redirect_url);

            var state = $(this).attr('data-state');

            if(state == 'create')
            {
                scroll_ad.create();
            }
            else if(state == 'update')
            {
                var id = $('#create_scroll_ad_form [name=id]').val();
                scroll_ad.set('id', id);
                scroll_ad.update();

                if(editing_use)
                {
                    editing_use.children('.pic').attr('src', pic_data_src);
                    editing_use.children('.url').children('span').text(redirect_url);
                    editing_use = null;
                }
            }
            LocalFavourScrollAd.commit();

            return false;
        });

        //删除滚动广告按钮点击事件
        $(document).on('click', '.del-scroll-ad', function(event){
            var scroll_ad_id = $(this).attr('data-id');
            $('#del_scroll_ad_dialog').data('id', scroll_ad_id);
            $('#del_scroll_ad_dialog').dialog('open', true).dialog('center');
        });

        //编辑滚动广告按钮点击事件
        $(document).on('click', '.edit-scroll-ad', function(event){
            var scroll_ad = $('#scroll_ad_grid').datagrid('getSelected');
            $('#create_scroll_ad_form [name=id]').val(scroll_ad.id);
            $('#create_scroll_ad_form [name=pic]').val('');
            if(scroll_ad.picData)
            {
                $('#thumbnail_pic').attr('src', 'data:image/png;base64,' + scroll_ad.picData);
                $('#thumbnail_pic').fadeIn(1000);
            }
            $('#create_scroll_ad_form [name=type][value=Link]').click();
            $('#create_scroll_ad_form [name=link]').val(scroll_ad.redirectUrl);
            $('#create_scroll_ad_btn').attr('data-state', 'update');
            $('#create_scroll_ad_btn').text('修改');
            $('#create_scroll_ad_window').window({iconCls: 'icon-edit'});
            $('#create_scroll_ad_window').window('setTitle', '修改滚动广告');
            $('#create_scroll_ad_window').window('open', true).window('center');
        });

        //.scroll-pic .tools.tools-unuse(图片右上角的X)点击事件

        $(document).on('click', '.tools-unuse', function(event){
            event.stopPropagation();
            $(this).parent().fadeOut(1000,function(event){
                var scroll_ad_id = $(this).attr('data-id');
                unuseAd(scroll_ad_id);
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

            var scroll_ad_id = parent.attr('data-id');
            var scroll_url = parent.children('.url').children('span').text();
            var pic_data_src = parent.children('.pic').attr('src');
            $('#create_scroll_ad_form [name=id]').val(scroll_ad_id);
            $('#create_scroll_ad_form [name=pic]').val('');
            if(pic_data_src)
            {
                $('#thumbnail_pic').attr('src', pic_data_src);
                $('#thumbnail_pic').fadeIn(1000);
            }
            $('#create_scroll_ad_form [name=type][value=Link]').click();
            $('#create_scroll_ad_form [name=link]').val(scroll_url);
            $('#create_scroll_ad_btn').attr('data-state', 'update');
            $('#create_scroll_ad_btn').text('修改');
            $('#create_scroll_ad_window').window({iconCls: 'icon-edit'});
            $('#create_scroll_ad_window').window('setTitle', '修改滚动广告')
            $('#create_scroll_ad_window').window('open', true).window('center');
            return false;
        });


        //拖放事件

        registerDraggable();

        /**
         * 数据相关
         */

        var LocalFavourScrollAd = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){
                    if(action == 'update' || action == 'create') return '/localFavourScrollAd.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            })
                            var ids_str = ids.join('-');
                            return '/localFavourScrollAd/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/localFavourScrollAd/' + condition.id + '.json';
                        }
                    }
                }
            }
        });


        LocalFavourScrollAd.on('beforeCreate', function(){
            $('#create_scroll_ad_btn').text('提交中');
            $('#create_scroll_ad_btn').prop('disabled', true);
            $('#create_scroll_ad_btn').addClass('btn-inverse');
        });

        LocalFavourScrollAd.on('created', function(event, data){
            if(!data.success) return;

            $('#create_scroll_ad_btn').text('添加');
            $('#create_scroll_ad_btn').prop('disabled', false);
            $('#create_scroll_ad_btn').removeClass('btn-inverse');

            $('#scroll_ad_grid').datagrid('reload',{
                page: 1
            });

            $('#create_scroll_ad_window').window('close', true);

        });

        LocalFavourScrollAd.on('beforeUpdate', function(){
            $('#create_scroll_ad_btn').text('修改中');
            $('#create_scroll_ad_btn').prop('disabled', true);
            $('#create_scroll_ad_btn').addClass('btn-inverse');
        });

        LocalFavourScrollAd.on('updated', function(event, data){
            if(!data.success) return;

            $('#create_scroll_ad_btn').text('修改');
            $('#create_scroll_ad_btn').prop('disabled', false);
            $('#create_scroll_ad_btn').removeClass('btn-inverse');

            $('#create_scroll_ad_window').window('close', true);

            $('#scroll_ad_grid').datagrid('reload');
        });

        LocalFavourScrollAd.on('deleted', function(event, data){
            if(!data.success) return;
            $('#scroll_ad_grid').datagrid('reload');
        });


    };

    //页面离开事件
    CarMate.page.on_leave = function(){
        //销毁复选框
        $('#scroll_local_favour').combogrid('destroy');

        //销毁窗口
        $('#scroll_ad_window').window('destroy');
        $('#create_scroll_ad_window').window('destroy');

        //销毁对话框
        $('#del_scroll_ad_dialog').window('destroy');

        //清除动态绑定事件
        $(document).off('click', '.del-scroll-ad');
        $(document).off('click', '.tools-unuse');
        $(document).off('click', '.tools-edit');

    }
</script>
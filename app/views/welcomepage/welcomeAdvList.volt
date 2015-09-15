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

    #welcome_adv_pagination.pagination table td {
        vertical-align: middle;
    }

    div.welcome-adv-pic-container {
        width: 18%;
        position: relative;
        float: left;
        margin: 0.1%;
    }

    .welcome-adv-pic-container img{
        width:100%;
    }

    div.welcome-adv-pic-toolbar-top{
        width: 100%;
        position: absolute;
        top: 0px;
        left: 0px;
    }

    div.welcome-adv-pic-toolbar-bottom{
        width: 100%;
        position: absolute;
        bottom: 0px;
        left: 0px;
    }
    .welcome-adv-pic-tool{
        font-size: 20px;
        margin-left: 2px;
        float: right;
        cursor: pointer;
    }
    .welcome-adv-pic-tool:hover{
        color: deepskyblue;
        text-decoration: none;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">开屏广告管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div class="span12">
                    <div id="welcome_adv_pagination"></div>
                </div>
            </div>
            <div class="row-fluid">
                <div id="welcome_adv_grid"></div>
            </div>
            <div id="welcome_adv_cu_window">
                <form id="welcome_adv_cu_form" action="">
                    <div class="row-fluid">
                        <input name="id" type="hidden"/>
                        <div class="span6">
                            <span class="label">所在省份</span>
                            <select name="province_id">
                                {% for province in province_list %}
                                <option value="{{ province.id }}">{{ province.name }}</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">相关网址</span>
                            <input type="text" name="url"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">图片</span>
                            <input id="welcome_adv_pic_file" type="file" name="picf"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <img id="welcome_adv_pic" src="" alt="" class="hidden-none"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){


        /**
         * 控件相关
         */

        var img_base_url = "{{ url('/welcomeAdvPic') }}";

        var welcome_adv_grid = $('#welcome_adv_grid');

        //分页器
        var welcome_adv_pagination = $('#welcome_adv_pagination').pagination({
            pageSize: 5,
            pageList: [5, 10, 20],
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(event){
                        $('#welcome_adv_cu_form [name="id"]').val('');
                        $('#welcome_adv_cu_form [name="province_id"]').val('');
                        $('#welcome_adv_cu_form [name="url"]').val('');
                        $('#welcome_adv_cu_form [name="picf"]').val('');
                        $('#welcome_adv_pic').attr('src', '').hide();

                        welcome_adv_cu_dialog.data('state', 'create');
                        var opt = welcome_adv_cu_dialog.dialog('options');
                        opt.title = '添加开屏广告';
                        opt.iconCls = 'icon-plus';
                        opt.buttons[0].text = '添加';
                        welcome_adv_cu_dialog.dialog(opt).dialog('open').dialog('center');
                    }
                }
            ],
            onSelectPage: function(page_num, page_size){
                getAdvList(page_num, page_size, renderAdvGrid);
            }
        });



        //窗口
        var welcome_adv_cu_dialog = $('#welcome_adv_cu_window').dialog({
            title: '开屏广告添加',
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
                        var waiting = welcome_adv_cu_dialog.data('waiting');
                        if(waiting) return;

                        var win_state = welcome_adv_cu_dialog.data('state');

                        var province_id = $('#welcome_adv_cu_form [name="province_id"]').val();
                        var url = $('#welcome_adv_cu_form [name="url"]').val();
                        var picf = $('#welcome_adv_cu_form [name="picf"]').val();
                        var pic_data = null;

                        if(picf)
                        {
                            pic_data = $('#welcome_adv_pic').attr('src').match(/base64,(.*)/)[1];
                        }


                        var welcome_adv = new WelcomeAdv();

                        welcome_adv.set('province_id', province_id);
                        welcome_adv.set('url', url);
                        welcome_adv.set('pic_data', pic_data);

                        if(win_state == 'create')
                        {
                            welcome_adv.create();
                        }
                        else if(win_state == 'update')
                        {
                            var id = $('#welcome_adv_cu_form [name="id"]').val();
                            welcome_adv.set('id', id);
                            welcome_adv.update();
                        }

                        WelcomeAdv.commit({complete: function(data){
                            welcome_adv_cu_dialog.data('waiting', false);
                        }});

                        welcome_adv_cu_dialog.data('waiting', true);
                        welcome_adv_cu_dialog.dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        welcome_adv_cu_dialog.dialog('close');
                    }
                }
            ]
        });

        /**
         * 事件相关
         */

        //图片选择器改变事件
        $('#welcome_adv_pic_file').change(function(event){
            var file = this.files[0];
            if(file.size > 512000)
            {
                $.messager.show({
                    title: '系统消息',
                    msg: '上传图片必须小于500Kb'
                });
                return;
            }
            var reader = new FileReader();
            reader.addEventListener('load', function(event){
                var img_src = event.target.result;
                $('#welcome_adv_pic').attr('src', img_src).fadeIn();
            });
            reader.readAsDataURL(file);
        });

        //开屏广告使用
        $(document).on('click', '.welcome-adv-enable-btn', function(event){
            var id = $(this).attr('data-id');
            $.ajax({
                url: '/welcomeAdvEnable/' + id + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '开屏广告成功使用'
                });
                welcome_adv_pagination.pagination('select');
            });
        });

        //开屏广告弃用
        $(document).on('click', '.welcome-adv-disable-btn', function(event){
            var id = $(this).attr('data-id');
            $.ajax({
                url: '/welcomeAdvDisable/' + id + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '开屏广告成功弃用'
                });
                welcome_adv_pagination.pagination('select');
            });
        });

        //开屏广告编辑按钮点击事件
        $(document).on('click', '.welcome-adv-edit-btn', function(event){
            var index = $(this).attr('data-index');
            var adv = welcome_adv_grid.data('rows')[index];

            $('#welcome_adv_cu_form [name="id"]').val(adv.id);
            $('#welcome_adv_cu_form [name="province_id"]').val(adv.provinceId);
            $('#welcome_adv_cu_form [name="url"]').val(adv.url);
            $('#welcome_adv_cu_form [name="picf"]').val('');
            $('#welcome_adv_pic').attr('src', img_base_url + '/' + adv.id + '?' + Date.now()).show();

            welcome_adv_cu_dialog.data('state', 'update');
            var opt = welcome_adv_cu_dialog.dialog('options');
            opt.title = '编辑开屏广告';
            opt.iconCls = 'icon-edit';
            opt.buttons[0].text = '编辑';
            welcome_adv_cu_dialog.dialog(opt).dialog('open').dialog('center');
        });

        //开屏广告删除按钮点击事件
        $(document).on('click', '.welcome-adv-del-btn', function(event){
            var id = $(this).attr('data-id');
            $.messager.confirm('删除开屏广告', '该操作无法撤消,是否继续', function(is_ok){
                if(!is_ok) return;
                var adv = new WelcomeAdv();
                adv.set('id', id);
                adv.delete();
                WelcomeAdv.commit();
            });
        });

        /**
         * 数据相关
         */

        var WelcomeAdv = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){

                    if(action == 'update' || action == 'create') return '/welcomeAdv.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            });
                            var ids_str = ids.join('-');
                            return '/welcomeAdv/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/welcomeAdv/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        WelcomeAdv.on('created', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '开屏广告添加成功'
            });
            welcome_adv_pagination.pagination('select', 1);
        });

        WelcomeAdv.on('updated', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '开屏广告更新成功'
            });
            welcome_adv_pagination.pagination('select');
        });

        WelcomeAdv.on('deleted', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '开屏广告删除成功'
            });
            welcome_adv_pagination.pagination('select');
        });


        function getAdvList(page_num, page_size, callback)
        {
            $.ajax({
                url: '/welcomeAdvList.json',
                method: 'POST',
                data: {
                    page: page_num,
                    rows: page_size
                },
                dataType: 'json',
                global: true
            }).done(function(data){
                welcome_adv_pagination.pagination('loaded');
                var opt = welcome_adv_pagination.pagination('options');
                opt.total = data.total;
                welcome_adv_pagination.pagination(opt);
                welcome_adv_grid.data('rows', data.rows);
                callback.call(this, data);
            });
            welcome_adv_pagination.pagination('loading');
        }

        function renderAdvGrid(data)
        {
            var rows = data.rows;
            var total = data.total;
            var count = data.count;

            var len = rows.length;
            $('#welcome_adv_grid').empty();
            for(var i = 0; i < len; i++)
            {
                var row = rows[i];
                //var pic = row.pic;
                var src = img_base_url + '/' + row.id + '?' + Date.now();

                var tools_top = '';
                var tools_bottom = '';

                tools_top += '<i class="iconfa-trash welcome-adv-pic-tool welcome-adv-del-btn" data-id="' + row.id +'"></i>';

                tools_bottom += '<i>' + row.provinceName + '</i>';
                tools_bottom += '<i class="iconfa-edit welcome-adv-pic-tool welcome-adv-edit-btn" data-index="' + i + '"></i>';

                if(row.isState == 1)
                {
                    tools_bottom += '<i title="弃用" class="iconfa-star welcome-adv-pic-tool welcome-adv-disable-btn" data-id="' + row.id +'"></i>';
                }
                else
                {
                    tools_bottom += '<i title="使用" class="iconfa-star-empty welcome-adv-pic-tool welcome-adv-enable-btn" data-id="' + row.id +'"></i>'
                }

                $('#welcome_adv_grid').append(
                    '<div class="welcome-adv-pic-container well well-small">' +
                        '<div class="welcome-adv-pic-toolbar-top">' +
                            tools_top +
                        '</div>' +
                        '<img src="' + src + '" />' +
                        '<div class="welcome-adv-pic-toolbar-bottom">' +
                            tools_bottom +
                        '</div>' +
                    '</div>'
                );

            }
        }

        //初始化广告列表
        getAdvList(1, 5, renderAdvGrid);

        /**
         * 页面离开时事件,用于清理资源
         */
        this.on_leave = function(){

            //销毁窗口
            welcome_adv_cu_dialog.dialog('destroy');

            //清除动态绑定事件
            $(document).off('click', '.welcome-adv-enable-btn');
            $(document).off('click', '.welcome-adv-disable-btn');
            $(document).off('click', '.welcome-adv-edit-btn');
            $(document).off('click', '.welcome-adv-del-btn');
        };

    };
</script>


<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">本地惠发布与管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="local_favour_grid"></div>
            </div>

            <!-- 发布/编辑本地惠窗口 -->
            <div id="local_favour_editor_window">
                <form id="local_favour_push_form" class="well" action="" method="post" enctype="multipart/form-data">
                    <div class="row-fluid">
                        <div class="span4">
                            <input name="id" type="hidden" />
                            <span class="label">标题</span>
                            <input name="title" class="input-large"/>
                        </div>
                        <div class="span2">
                            <span class="label">分类</span>
                            <select name="type_id" class="input-small">
                                {% for type in local_favour_types %}
                                <option value="{{ type.id }}">{{ type.typeName }}</option>
                                {% endfor%}
                            </select>
                        </div>
                        <div class="span4">
                            <span class="label">选择省市</span>
                            <select name="province_id" class="input-small">
                                {% for province in province_list %}
                                {% if current_province_id == province.id %}
                                <option value="{{ province.id }}" selected>{{ province.name }}</option>
                                {% else %}
                                <option value="{{ province.id }}">{{ province.name }}</option>
                                {% endif%}
                                {% endfor%}
                            </select>
                            <span>省</span>
                            <select name="city_id" class="input-small">
                                {% for city in city_list %}
                                <option value="{{ city.id }}">{{ city.name }}</option>
                                {% endfor%}
                            </select>
                            <span>市</span>
                        </div>
                        <div class="span2">
                            <button id="push_local_favour_btn" class="btn btn-primary">发布</button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span5">
                            <span class="label">简介</span>
                            <textarea name="des" rows="3" cols="55" class="input-xlarge"/>
                        </div>
                        <div class="span4">
                            <span class="label">图片</span>
                            <input type="file" name="pic" accept="image/gif, image/png, image/jpeg"/>
                            <input type="hidden" name="pic_id"/>
                            <input type="hidden" name="tmp_name" />
                        </div>
                        <div class="span3">
                            <img id="thumbnail" src="" width="80" height="80" style="display: none"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="item field_label">内容</div>
                        <div class="item field" id="editable">
                            <textarea name="contents"  class="ckeditor" id="local_favour_editor"/>					            </div>
                        <div style="clear:both;"></div>
                    </div>
                </form>
            </div>

            <!--   设置置顶对话框   -->
            <div id="set_top_dialog">
                <input type="text" class="datetimebox"/>
            </div>
            <!--   评论回复窗口         -->
            <div id="local_favour_comment_window"></div>
            <!--   预览窗口  -->
            <div id="local_favour_preview_window"></div>
            <!--   删除确认对话框   -->
            <div id="del_local_favour_dialog">
                <div>
                    <h4>是否删除本地惠?</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    //页面加载完成事件
    CarMate.page.on_loaded = function(){

        //创建ckeditor
        var editor = CKEDITOR.replace( 'local_favour_editor', {
            enterMode: CKEDITOR.ENTER_P,
            height: 270,
            removePlugins : 'save',
            filebrowserImageUploadUrl : 'ckUploadImage?command=QuickUpload&type=Images'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(editor, finder_path);

        //创建datagrid控件
        $('#local_favour_grid').datagrid({
            url: '/localFavourList.json',
            title: '本地惠列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            rownumbers:true,///行数
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            loadFilter: function(data){
                //因为两个单元格同事需要id,为避免字段冲突智能复制该字段
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;

                var id = null;
                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];
                    if(row.id == id) continue;
                    id = row.id;
                    row.cp_id = row.id;
                    pass_rows.push(row);
                }
                data.rows = pass_rows;
                data.count = pass_rows.length;
                return data;
            },
            toolbar: [{
                text: '添加',
                iconCls: 'icon-plus',
                handler: function(){

                    //打开窗口前,需要清空表单
                    $('#local_favour_push_form [name=id]').val('');
                    $('#local_favour_push_form [name=title]').val('');
                    var default_type_id = $('#local_favour_push_form [name=type_id] :first-child').val();
                    $('#local_favour_push_form [name=type_id]').val(default_type_id);
                    $('#local_favour_push_form [name=province_id]').val('0');
                    $('#local_favour_push_form [name=city_id]').val(null);
                    $('#local_favour_push_form [name=des]').val('');
                    //写入ckeditor内容
                    editor.setData('');
                    //清空临时上传的文件名
                    $('#local_favour_push_form [name=pic]').val('');
                    $('#local_favour_push_form [name=tmp_name]').val('');
                    $('#local_favour_push_form [name=pic_id]').val('');
                    $('#local_favour_push_form #thumbnail').attr('src', '').css('display', 'none');

                    $('#push_local_favour_btn').text('添加');

                    $('#push_local_favour_btn').attr('data-state', 'create');

                    $('#local_favour_editor_window').window('setTitle', '添加本地惠').window('open', true).window('center');

                    $('#local_favour_editor_window').window('open', true).window('center');
                }
            }],
            columns:[[
                {field:'typeName', title:'分类', width:'6%', align:'center'},
                {field:'provinceName',title:'省份',width:'6%',align:'center'},
                {field:'pic_id',title:'图片',width:15,align:'center', formatter: function(value, row, index){
                    if(value)
                    {
                        var pic_url = "{{ url('/localFavourPic/') }}" + value;
                        return '<img src="' + pic_url + '" style="width:80px;height:80px" />';
                    }
                    else
                    {
                        return '无';
                    }

                }},
                {field:'title',title:'标题',width:15,align:'center'},
                {field:'des',title:'简介',width:15,align:'center'},
                {field:'publishTime',title:'发布日期',width:15,align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var time = value.replace(/\s+/g, '/');
                    time = time.replace(/:\d+(AM|PM)/, ' $1');
                    return CarMate.utils.date('Y-m-d H:i:s', time);
                }},
                {field:'isState',title:'发布状态',width:'6%',align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    if(value == '1')
                    {
                        return '<a class="change-state btn btn-mini" data-id="' + row.id + '" data-is-state="' + value + '"><i class="iconfa-circle"></i></a>';
                    }
                    else
                    {
                        return '<a class="change-state btn btn-mini" data-id="' + row.id + '" data-is-state="' + value + '"><i class="iconfa-circle-blank"></i></a>';
                    }
                }},
//                {field:'adv_isState',title:'首页推广',width:'6%',align:'center'},
                {field:'orderTime',title:'是否置顶',width:'6%',align:'center', formatter: function(value, row, index){
                    if(!value) return;

                    var now = Date.now();

                    var time = CarMate.utils.date.mssqlToJs(value);

                    var order_time = Date.parse(time);
                    if(!value || order_time < now)
                    {
                        return '<a class="set-top btn btn-mini" data-id="' + row.id + '"><i class="icon-star-empty"></i></a>';
                    }
                    else
                    {
                        return '<a class="set-top btn btn-mini" data-id="' + row.id + '"><i class="icon-star"></i></a>';
                    }
                }},
                {field:'countFavourRead',title:'点击量',width:'6%',align:'center'},
                {field:'id',title:'评论',width:'6%',align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    return '<a href="/localFavourComment/' + value + '" class="local-favour-comment btn"><i class="icon-comment"></i></a>';
                }},
                {field:'cp_id',title:'操作',width:'12%',align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    return '<div class="btn-group"><button class="btn btn-mini btn-info preview-local-favour" data-id="' + value + '"><i class="icon-eye-open"></i></button><button class="btn btn-mini btn-warning update-local-favour" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger del-local-favour" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        //创建对话框
        $('#set_top_dialog').dialog({
            title: '设置置顶结束时间',
            iconCls: 'icon-wrench',
            closed: true,
            //inline: true,
            openAnimation: 'fade',
            closeAnimation: 'slide',
            closeDuration: 200,
            shadow: false,
            width: 'auto',
            height: 'auto',
            modal: true,
            buttons:[
                {
                    text: '确定',
                    iconCls: 'icon-ok',
                    handler: function(){
                        var local_favour = new LocalFavour();
                        var id = $('#set_top_dialog').data('id');
                        var order_time = $('#set_top_dialog .datetimebox').datetimebox('getValue');
                        local_favour.set('id', id);
                        local_favour.set('order_time', order_time);
                        local_favour.update();
                        LocalFavour.commit();
                        $('#set_top_dialog').dialog('close', true);
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#set_top_dialog').dialog('close', true);
                    }
                }
            ]
        });

        $('#del_local_favour_dialog').dialog({
            title: '本地惠删除',
            iconCls: 'icon-warning-sign',
            closed: true,
            modal: true,
            buttons:[
                {
                    text: '确定',
                    iconCls: 'icon-ok',
                    handler: function(){
                        //获取绑定到删除对话框的 local favour id
                        var id = $('#del_local_favour_dialog').data('id');
                        var local_favour = new LocalFavour();
                        local_favour.set('id', id);
                        local_favour.delete();
                        LocalFavour.commit();
                        $('#del_local_favour_dialog').dialog('close', true);
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#del_local_favour_dialog').dialog('close', true);
                    }
                }
            ]
        });

        //创建窗口
        $('#local_favour_comment_window').window({
            title: '评论回复',
            iconCls: 'icon-comment',
            width: '60%',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'slide'
        });

        $('#local_favour_preview_window').window({
            title: '本地惠预览',
            iconCls: 'icon-eye-open',
            width: 320,
            height: 480,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        $('#local_favour_editor_window').window({
            title: '发布本地惠',
            iconCls: 'icon-plus',
            width: 1080,
            height: 650,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });


        //日期控件
        $('.datetimebox').datetimebox();

        /**
         * 事件
         */

        //添加本地惠按钮点击事件
        $('#push_local_favour_btn').click(function(event){
            event.preventDefault();

            //data-state用于判断是添加/修改
            var btn_state = $(this).attr('data-state');

            var local_favour = new LocalFavour();
            var title = $('#local_favour_push_form [name=title]').val();
            var type_id = $('#local_favour_push_form [name=type_id]').val();
            var province_id = $('#local_favour_push_form [name=province_id]').val();
            var city_id = $('#local_favour_push_form [name=city_id]').val();
            var des = $('#local_favour_push_form [name=des]').val();
            //获取ckeditor内容
            var contents = editor.getData();
            //获取临时上传的文件名
            var tmp_name = $('#local_favour_push_form [name=tmp_name]').val();

            local_favour.set('title', title);
            local_favour.set('type_id', type_id);
            local_favour.set('province_id', province_id);
            local_favour.set('city_id', city_id);
            local_favour.set('des', des);
            local_favour.set('tmp_name', tmp_name);
            local_favour.set('contents', contents);

            if(btn_state === 'create')
            {
                local_favour.create();
            }
            else if(btn_state === 'update')
            {
                var id = $('#local_favour_push_form [name=id]').val();
                var pic_id = $('#local_favour_push_form [name=pic_id]').val();
                local_favour.set('id', id);
                local_favour.set('pic_id', pic_id);
                local_favour.update()
            }
            LocalFavour.commit();
            return false;
        });

        //省份选择框change事件(动态加载城市市的数据)

        var city_cache = {};

        $('#local_favour_push_form [name=province_id]').change(function(){
            var province_id = $(this).val();
            if(city_cache[province_id])
            {
                render_city_select(city_cache[province_id])
            }
            else
            {
                $.ajax({
                    url: '/province/' + province_id + '/city.json',
                    method: 'GET',
                    dataType: 'json',
                    global: true
                }).done(function(data){
                    var city_list = data.rows;
                    city_cache[province_id] = city_list;
                    render_city_select(city_list);
                });
            }
        });

        //渲染城市select
        function render_city_select(data)
        {

            var options_html = '';

            $.each(data, function(index, item){
                options_html += '<option value="' + item.id + '">' + item.name + '</option>';
            });

            $('#local_favour_push_form [name=city_id]').empty().append(options_html);

            var city_id = $('#local_favour_push_form [name=city_id]').data('city_id');
            if(city_id)
            {
                $('#local_favour_push_form [name=city_id]').val(city_id);
            }
        }

        //文件选择器change事件

        $('#local_favour_push_form [name=pic]').change(function(){
            var file = $(this).prop('files')[0];
            var reader = new FileReader();
            reader.onload = function(event){
                var result = event.target.result;
                $('#thumbnail').attr('src', result).fadeIn(1000);

                var form_data = new FormData();
                form_data.append('pic', file);

                //ajax上传文件
                $.ajax({
                    url:'/localFavourTempPic.json',
                    method: 'POST',
                    data: form_data,
                    dataType: 'json',
                    processData: false,
                    contentType: false
                }).done(function(data){
                    if(data.success)
                    {
                        $('#local_favour_push_form [name=tmp_name]').val(data.tmp_name);
                        $('#push_local_favour_btn').prop('disabled', false);
                        $('#push_local_favour_btn').removeClass('btn-inverse');
                    }
                });

            };
            reader.readAsDataURL(file);
            $('#push_local_favour_btn').prop('disabled', true);
            $('#push_local_favour_btn').addClass('btn-inverse');
        });

        //设置发布状态按钮点击事件
        $(document).on('click', '.change-state', function(){
            var local_favour = new LocalFavour();
            var id = $(this).attr('data-id');
            var is_state = $(this).attr('data-is-state');
            if(is_state === '1')
            {
                is_state = '0';
            }
            else
            {
                is_state = '1';
            }
            local_favour.set('id', id);
            local_favour.set('is_state', is_state);
            local_favour.update();
            LocalFavour.commit();
        });

        //设置置顶点击事件
        $(document).on('click', '.set-top', function(){
            $('#set_top_dialog').dialog('open', true);
            $('#set_top_dialog').dialog('center');
            //保存当前操作的本地惠id
            $('#set_top_dialog').data('id', $(this).attr('data-id'));
        });

        //评论链接点击事件
        $(document).on('click', '.local-favour-comment', function(event){
            event.preventDefault();
            var url = $(this).attr('href');

            $('#local_favour_comment_window')
                .window('open', true)
                .window('refresh', url)
                .window('center');
            return false;
        });

        //预览按钮点击事件
        $(document).on('click', '.preview-local-favour', function(){
            var local_favour = $('#local_favour_grid').datagrid('getSelected');
            var contents = local_favour.contents;
            $('#local_favour_preview_window').empty().append(contents);
            $('#local_favour_preview_window').window('open', true).window('center');
        });

        //修改按钮点击事件
        $(document).on('click', '.update-local-favour', function(){

            var local_favour = $('#local_favour_grid').datagrid('getSelected');

            $('#local_favour_push_form [name=id]').val(local_favour.id);
            $('#local_favour_push_form [name=title]').val(local_favour.title);
            $('#local_favour_push_form [name=type_id]').val(local_favour.typeId);
            $('#local_favour_push_form [name=province_id]').val(local_favour.provinceId);
            $('#local_favour_push_form [name=city_id]').val(local_favour.cityId);
            $('#local_favour_push_form [name=city_id]').data('city_id', local_favour.cityId);
            $('#local_favour_push_form [name=des]').val(local_favour.des);
            $('#local_favour_push_form [name=pic_id]').val(local_favour.pic_id);

            $('#local_favour_push_form [name=province_id]').change();

            //清除缩略图
            $('#thumbnail').attr('src', '').hide();

            if(local_favour.pic_id)
            {
                $('#thumbnail').attr('src', "{{ url('/localFavourPic/') }}" + local_favour.pic_id ).fadeIn(1000);
            }

            //写入ckeditor内容
            editor.setData(local_favour.contents);
            //获取临时上传的文件名
            $('#local_favour_push_form [name=tmp_name]').val('');

            $('#push_local_favour_btn').text('修改');

            $('#push_local_favour_btn').attr('data-state', 'update');

            $('#local_favour_editor_window').window('setTitle', '修改本地惠').window('open', true).window('center');
        });


        //删除按钮点击事件
        $(document).on('click', '.del-local-favour', function(){
            var local_favour_id = $(this).attr('data-id');
            $('#del_local_favour_dialog').data('id', local_favour_id);
            $('#del_local_favour_dialog').dialog('open', true);
            $('#del_local_favour_dialog').dialog('center');
        });

        /**
         * 数据相关
         */

        //LocalFavour模型
        var LocalFavour = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){
                    if(action == 'update' || action == 'create') return '/localFavour.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            })
                            var ids_str = ids.join('-');
                            return '/localFavour/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/localFavour/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        LocalFavour.on('beforeUpdate', function(event){
            $('#push_local_favour_btn').text('修改中');
            $('#push_local_favour_btn').prop('disabled', true);
            $('#push_local_favour_btn').addClass('btn-inverse');
        });

        LocalFavour.on('updated', function(event, data){
            if(!data.success) return;
            var page = $('.pagination-num').val();
            var rows = $('.pagination-page-list').val();
            $('#local_favour_grid').datagrid('reload', {
                page: page,
                rows: rows
            });
            $('#push_local_favour_btn').text('修改');
            $('#push_local_favour_btn').prop('disabled', false);
            $('#push_local_favour_btn').removeClass('btn-inverse');

            $('#local_favour_editor_window').window('close', true);
        });

        LocalFavour.on('beforeCreate', function(event){
            $('#push_local_favour_btn').text('添加中');
            $('#push_local_favour_btn').prop('disabled', true);
            $('#push_local_favour_btn').addClass('btn-inverse');
        });

        LocalFavour.on('created', function(event, data){
            if(!data.success) return;

            var page = $('.pagination-num').val();
            var rows = $('.pagination-page-list').val();

            $('#local_favour_grid').datagrid('reload',{
                page: 1,
                rows: rows
            });

            $('#push_local_favour_btn').text('添加');
            $('#push_local_favour_btn').prop('disabled', false);
            $('#push_local_favour_btn').removeClass('btn-inverse');

            $('#local_favour_editor_window').window('close', true);
        });

        LocalFavour.on('deleted', function(event, data){
            if(!data.success) return;

            var page = $('.pagination-num').val();
            var rows = $('.pagination-page-list').val();

            $('#local_favour_grid').datagrid('reload',{
                page: page,
                rows: rows
            });
        });

    };

    //离开页面事件
    CarMate.page.on_leave = function(){
        /**
         * 销毁窗口
         */
        $('#set_top_dialog').dialog('destroy');

        $('#del_local_favour_dialog').dialog('destroy');

        $('#local_favour_preview_window').window('destroy');

        $('#local_favour_editor_window').window('destroy');

        $('#local_favour_comment_window').window('destroy');

        /**
         * 清除动态绑定事件
         */

        //设置发布状态按钮点击事件
        $(document).off('click', '.change-state');

        //设置置顶点击事件
        $(document).off('click', '.set-top');

        //评论链接点击事件
        $(document).off('click', '.local-favour-comment');

        //修改按钮点击事件
        $(document).off('click', '.update-local-favour');

        //删除按钮点击事件
        $(document).off('click', '.del-local-favour');
    };
</script>
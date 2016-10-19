<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">本地惠稿件管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="local_favour_sub_grid"></div>
            </div>
            <!--   查看图片窗口   -->
            <div id="view_sub_pic_window">
                <div class="row-fluid" id="sub_pic_list">

                </div>
            </div>

            <!--   预览附件窗口   -->
            <div id="preview_local_favour_sub_window">
                <div id="local_favour_sub_form" class="row-fluid">
                    <div class="row-fluid">
                        <div class="span4">
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
                            <button id="pass_local_favour_sub_btn" class="btn btn-primary">审核通过</button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">简介</span>
                            <textarea name="des" rows="3" cols="55" class="input-xxlarge"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <span class="label">内容</span>
                        <div class="item field" id="editable">
                            <textarea name="contents"  class="ckeditor" id="local_favour_sub_editor"/>					            </div>
                        <div style="clear:both;"></div>
                    </div>
                </div>

                <!--   删除确认对话框   -->
                <div id="del_local_favour_sub_dialog">
                    <div>
                        <h4>是否删除本地惠稿件?</h4>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //页面加载后事件
    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //创建ckeditor
        var editor = CKEDITOR.replace( 'local_favour_sub_editor', {
            enterMode: CKEDITOR.ENTER_P,
            height: 270,
            removePlugins : 'save'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(editor, finder_path);

        //本地惠稿件表格
        $('#local_favour_sub_grid').datagrid({
            url: '/localFavourSubList.json',
            title: '本地惠列表',
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
            //数据过滤,以去除重复id
            loadFilter: function(data){
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;

                var id = null;
                var index = null;
                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];
                    if(row.id == id)
                    {
                        pass_rows[index].pic_ids.push(row.pic_id);
                        continue;
                    }
                    id = row.id;
                    row.pic_ids = [row.pic_id];
                    pass_rows.push(row);
                    index = pass_rows.length - 1;
                }
                data.rows = pass_rows;
                data.count = pass_rows.length;
                return data;
            },
            columns:[[
                {field:'provinceName', title:'省份', width:'6%', align:'center'},
                {field:'userId', title:'用户ID', width:'14%', align:'center'},
                {field:'title', title:'标题', width:'15%', align:'center'},
                {field:'contents', title:'内容', width:'15%', align:'center'},
                {field:'publishTime', title:'发布时间', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var time = value.replace(/\s+/g, '/');
                    time = time.replace(/:\d+(AM|PM)/, ' $1');
                    return CarMate.utils.date('Y-m-d H:i:s', time);
                }},
                {field:'addr', title:'地址', width:'15%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    return value;
                }},
                {field:'tel', title:'电话', width:'10%', align:'center', formatter:function(value, row, index){
                    if(!value) return '';
                    return value;
                }},
                {field:'pic_id', title:'附件', width:'6%', align:'center', formatter:function(value, row, index){
                    if(!value) return;
                    return '<button data-id="' + value + '" class="btn btn-info btn-mini view-pic-btn"><i class="icon-picture"></i></button>';
                }},
                {field:'id', title:'操作', width:'10%', align:'center', formatter:function(value, row, index){
                    if(!value) return;
                    return '<div class="btn-group"><button class="btn btn-mini btn-info preview-local-favour-sub" data-id="' + value + '"><i class="icon-eye-open"></i></button><button class="btn btn-mini btn-danger del-local-favour-sub" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                }}
            ]]
        });

        //查看图片窗口
        $('#view_sub_pic_window').window({
            title: '查看稿件图片',
            iconCls: 'icon-picture',
            width: '60%',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //预览本地惠稿件窗口
        $('#preview_local_favour_sub_window').window({
            title: '本地惠稿件预览',
            iconCls: 'icon-eye-open',
            width: '80%',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //删除本地惠稿件对话框
        $('#del_local_favour_sub_dialog').dialog({
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
                        var id = $('#del_local_favour_sub_dialog').data('id');
                        var local_favour_sub = new LocalFavourSub();
                        local_favour_sub.set('id', id);
                        local_favour_sub.delete();
                        LocalFavourSub.commit();
                        $('#del_local_favour_sub_dialog').dialog('close', true);
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#del_local_favour_sub_dialog').dialog('close', true);
                    }
                }
            ]
        });

        /**
         * 事件相关
         */

        //查看图片按钮点击事件
        $(document).on('click', '.view-pic-btn', function(event){
            event.preventDefault();

            var pic_ids = $('#local_favour_sub_grid').datagrid('getSelected').pic_ids;
            var img_url = "{{ url('/localFavourSubPic') }}";
            $('#sub_pic_list').empty();
            $.each(pic_ids, function(index, pic_id){
                $('#sub_pic_list').append('<div class="span3 well well-small"><img src="' + img_url + '/' + pic_id + '" /></div>');
            });
            $('#view_sub_pic_window').window('open', true).window('center');
            return false;
        });

        //预览稿件按钮点击事件
        $(document).on('click', '.preview-local-favour-sub', function(event){
            event.preventDefault();
            var row = $('#local_favour_sub_grid').datagrid('getSelected');

            var local_favour_sub = new LocalFavourSub();
            local_favour_sub.set('id', row.id);
            local_favour_sub.find();

            $('#pass_local_favour_sub_btn').attr('disabled', true);
            $('#pass_local_favour_sub_btn').addClass('btn-inverse');
            $('#preview_local_favour_sub_window').window('open', true).window('center');
            return false;
        });

        //删除稿件按钮点击事件
        $(document).on('click', '.del-local-favour-sub', function(event){
            event.preventDefault();
            var local_favour_sub_id = $(this).attr('data-id');
            $('#del_local_favour_sub_dialog').data('id', local_favour_sub_id);
            $('#del_local_favour_sub_dialog').dialog('open', true).window('center');
            return false;
        });

        //审核通过按钮点击事件
        $('#pass_local_favour_sub_btn').click(function(event){
            event.preventDefault();

            var local_favour = new LocalFavour();
            var title = $('#local_favour_sub_form [name=title]').val();
            var type_id = $('#local_favour_sub_form [name=type_id]').val();
            var province_id = $('#local_favour_sub_form [name=province_id]').val();
            var city_id = $('#local_favour_sub_form [name=city_id]').val();
            var des = $('#local_favour_sub_form [name=des]').val();
            //获取ckeditor内容
            var contents = editor.getData();

            local_favour.set('title', title);
            local_favour.set('type_id', type_id);
            local_favour.set('province_id', province_id);
            local_favour.set('city_id', city_id);
            local_favour.set('des', des);
            local_favour.set('contents', contents);

            local_favour.create();

            LocalFavour.commit();

            $('#pass_local_favour_sub_btn').text('提交中');
            $('#pass_local_favour_sub_btn').attr('disabled', true);
            $('#pass_local_favour_sub_btn').addClass('btn-inverse');

            return false;
        });

        //省份选择框change事件(动态加载城市市的数据)

        var city_cache = {};

        $('#local_favour_sub_form [name=province_id]').change(function(){
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

            $('#local_favour_sub_form [name=city_id]').empty().append(options_html);

            var city_id = $('#local_favour_sub_form [name=city_id]').data('city_id');
            if(city_id)
            {
                $('#local_favour_sub_form [name=city_id]').val(city_id);
            }
        }


        /**
         * 数据相关
         */

        //LocalFavourSub模型
        var LocalFavourSub = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){
                    if(action == 'update' || action == 'create') return '/localFavourSub.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            })
                            var ids_str = ids.join('-');
                            return '/localFavourSub/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/localFavourSub/' + condition.id + '.json';
                        }
                    }
                    if( action == 'findOne')
                    {
                        return '/localFavourSub/' + condition.id + '.json';
                    }
                }
            }
        });

        LocalFavourSub.on('foundOne', function(event, model, data){
            if(!data || ( (data instanceof Array) && data.length == 0 ) ) return;

            var row = null;

            var editor_data = '<p>';

            if(data instanceof Array)
            {
                row = data[0];

                $.each(data, function(index, item){

                    if(item.pic_pic)
                    {
                        editor_data += '<img style="width:100%" src="data:image/jpeg;base64,' + item.pic_pic + '" /></br>';
                    }
                });

                editor_data += row.contents + '</p>';
            }
            else
            {
                row = data;
                if(row.pic_pic)
                {
                    editor_data += '<img style="width:100%" src="data:image/jpeg;base64,' + row.pic_pic + '" /></br>';
                }
                editor_data += row.contents + '</p>';
            }

            $('#local_favour_sub_form [name=title]').val(row.title);
            $('#local_favour_sub_form [name=type_id] :first-child').attr('checked', true);
            $('#local_favour_sub_form [name=province_id]').val(row.provinceId);
            $('#local_favour_sub_form [name=city_id]').data(row.cityId);

            $('#local_favour_sub_form [name=province_id]').change();

            $('#local_favour_sub_form [name=des]').val(row.contents);

            editor.setData(editor_data);

            $('#pass_local_favour_sub_btn').attr('disabled', false);
            $('#pass_local_favour_sub_btn').removeClass('btn-inverse');
        });

        LocalFavourSub.on('deleted', function(event, data){
            if(!data.success) return;
            $('#local_favour_sub_grid').datagrid('reload');
        });

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

        LocalFavour.on('created', function(event, data){
            if(!data.success) return;

            $('#pass_local_favour_sub_btn').text('审核通过');
            $('#pass_local_favour_sub_btn').attr('disabled', false);
            $('#pass_local_favour_sub_btn').removeClass('btn-inverse');

            $.messager.show({
                title: '数据操作提示',
                msg: '本地惠添加成功',
                timeout: 2000,
                showType: 'slide'
            });


        });

    };

    //页面离开事件
    CarMate.page.on_leave = function(){

        //销毁窗口与对话框
        $('#view_sub_pic_window').window('destroy');
        $('#preview_local_favour_sub_window').window('destroy');
        $('#del_local_favour_sub_dialog').dialog('destroy');

        //清除动态绑定事件
        $(document).off('click', '.view-pic-btn');
        $(document).off('click', '.preview-local-favour-sub');
        $(document).off('click', '.del-local-favour-sub');
    };

</script>
<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">首页推广</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="local_favour_adv_grid"></div>
            </div>
            <!-- 添加推广窗口 -->
            <div id="create_adv_window">
                <form id="create_adv_form" class="well" action="" method="post" enctype="multipart/form-data">
                    <div class="row-fluid">
                        <div class="span4">
                            <span class="label">小图</span>
                            <input name="adv" type="file" class="input-small" accept="image/gif, image/png, image/jpeg"/>
                            <img id="thumbnail_adv" style="height: 100px; display: none" />
                        </div>
                        <div class="span4">
                            <span class="label">大图</span>
                            <input name="adv3" type="file" class="input-small" accept="image/gif, image/png, image/jpeg"/>
                            <img id="thumbnail_adv3" style="height: 100px; display: none" />
                        </div>
                        <div class="span2">
                            <span class="label">推广省份</span>
                            <select name="province_id" class="input-small">
                                <?php foreach ($province_list as $province) { ?>
                                <?php if ($current_province_id == $province->id) { ?>
                                <option value="<?php echo $province->id; ?>" selected><?php echo $province->name; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $province->id; ?>"><?php echo $province->name; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="span2">
                            <button id="create_adv_btn" class="btn btn-primary">推广</button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span5">
                            <span class="label">推广消息类型</span>
                            <input name="type" type="radio" value="LocalFavour" rel-sel="#adv_local_favour" checked /><span>本地惠</span>
                            <input name="type" type="radio" value="Notice" rel-sel="#adv_notice"/><span>公告</span>
                            <input name="type" type="radio" value="Link" rel-sel="#adv_link"/><span>URL</span>
                            <input name="type" type="radio" value="Other" rel-sel="#adv_other_editor"/><span>其他</span>
                        </div>
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
                                <input id="adv_link" name="link" type="text"" />
                            </div>
                            <div style="display: none">
                                <textarea name="contents"  class="ckeditor" id="adv_other_editor"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!--   删除确认对话框   -->
            <div id="del_adv_dialog" style="display: none">
                <div>
                    <h4>是否删除推广?</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //页面加载完成时
    CarMate.page.on_loaded = function(){
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
        var finder_path = "<?php echo $this->url->get('/js/ckfinder/'); ?>";

        //本地惠选择控件
        $('#adv_local_favour').combogrid({
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
        $('#adv_notice').combogrid({
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

        //本地惠推广表格

        //正在编辑的行索引
        var editing_index = null;

        $('#local_favour_adv_grid').datagrid({
            url: '/localFavourAdvList.json',
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
            onClickCell: function(row_index, field){
                if (editing_index == row_index) return;

                if(!(editing_index === null))
                {
                    $('#local_favour_adv_grid').datagrid('endEdit', editing_index);
                }

                $('#local_favour_adv_grid').datagrid('beginEdit', row_index);
                var ed = $('#local_favour_adv_grid').datagrid('getEditor', {index:row_index,field:field});
                editing_index = row_index;
            },
            toolbar: [
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(){

                        //打开窗口前,需要清空表单
                        $('#create_adv_form [name=adv]').val('');
                        $('#create_adv_form [name=adv3]').val('');
                        $('#create_adv_form [name=province_id]').val('0');
                        //回复单选框的同事触发其点击事件,以便回复
                        $('#create_adv_form [name=type][value=LocalFavour]').click();
                        $('#adv_local_favour').combogrid('clear');
                        $('#adv_notice').combogrid('clear');
                        $('#create_adv_form [name=link]').val('');
                        editor.setData('');
                        $('#create_adv_form #thumbnail_adv').attr('src', '').css('display', 'none');
                        $('#create_adv_form #thumbnail_adv3').attr('src', '').css('display', 'none');

                        $('#create_adv_window').window('setTitle', '添加本地惠').window('open', true).window('center');
                        $('#create_adv_window').window('open', true).window('center');
                    }
                },
                {
                    text: '保存',
                    iconCls: 'iconfa-save',
                    handler: function(){
                        //获取变更数据前结束当前编辑
                        $('#local_favour_adv_grid').datagrid('endEdit', editing_index);
                        var changes = $('#local_favour_adv_grid').datagrid('getChanges', 'updated');
                        for(var i = 0; i < changes.length; i++)
                        {
                            var local_favour_adv = new LocalFavourAdv();
                            local_favour_adv.set('id', changes[i]['id']);
                            local_favour_adv.set('is_state', changes[i]['isState']);
                            local_favour_adv.set('is_order', changes[i]['isOrder']);
                            local_favour_adv.update();
                        }
                        LocalFavourAdv.commit();
                    }
                }
            ],
            columns:[[
                {field:'provinceName', title:'省份', width:'15%', align:'center'},
                {field:'adv', title:'小图', width:'20%', align:'center', formatter:function(value, row, index){
                    var pattern = /null$/;
                    if(!value || pattern.test(value)) return '无';
                    return '<img style="width:100%" src="' + value + '"/>'
                }},
                {field:'adv3', title:'大图', width:'20%', align:'center', formatter:function(value, row, index){
                    var pattern = /null$/;
                    if(!value || pattern.test(value)) return '无';
                    return '<img style="width:100%" src="' + value + '"/>'
                }},
                {field:'isOrder', title:'排序', width:'15%', align:'center', editor:{type:'numberbox'}},
                {field:'isState', title:'发布', width:'15%', align:'center', editor:{type:'checkbox', options:{on: '1', off: '0'}}, formatter: function(value, row, index){
                    if(!value || value === '0') return '<i class="iconfa-circle-blank"></i>';
                    return '<i class="iconfa-circle"></i>';
                }},
                {field:'id', title:'操作', width:'14%', align:'center', formatter:function(value, row, index){
                    if(!value) return;
                    return '<button data-id="' + value + '" class="btn btn-danger btn-mini del-adv-btn"><i class="icon-trash"></i></button>';
                }}
            ]]
        });

        //添加推广窗口
        $('#create_adv_window').window({
            title: '添加推广',
            iconCls: 'icon-plus',
            width: 1100,
            height: 600,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });


        //删除推广对话框
        $('#del_adv_dialog').dialog({
            title: '推广删除',
            iconCls: 'icon-warning-sign',
            closed: true,
            modal: true,
            buttons:[
                {
                    text: '确定',
                    iconCls: 'icon-ok',
                    handler: function(){
                        //获取绑定到删除对话框的 local favour id
                        var id = $('#del_adv_dialog').data('id');
                        var local_favour_adv = new LocalFavourAdv();
                        local_favour_adv.set('id', id);
                        local_favour_adv.delete();
                        LocalFavourAdv.commit();
                        $('#del_adv_dialog').dialog('close', true);
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#del_adv_dialog').dialog('close', true);
                    }
                }
            ]
        });

        /**
         * 事件相关
         */

        //推广类型单选按钮切换事件

        var current_show_sel = $('#create_adv_form [name=type]:checked').attr('rel-sel');

        $('#create_adv_form [name=type]').change(function(event){
            var rel_sel = $(this).attr('rel-sel');
            $(current_show_sel).parent().hide();
            $(rel_sel).parent().show();
            current_show_sel = rel_sel;
        });

        //文件(图片)选择器change事件

        $('#create_adv_form [name=adv]').change(function(){
            var file = $(this).prop('files')[0];
            var reader = new FileReader();
            reader.onload = function(event){
                var result = event.target.result;
                $('#thumbnail_adv').attr('src', result).fadeIn(1000);
                $('#create_adv_btn').prop('disabled', false);
                $('#create_adv_btn').removeClass('btn-inverse');
            };
            reader.readAsDataURL(file);
            $('#create_adv_btn').prop('disabled', true);
            $('#create_adv_btn').addClass('btn-inverse');
        });

        $('#create_adv_form [name=adv3]').change(function(){
            var file = $(this).prop('files')[0];
            var reader = new FileReader();
            reader.onload = function(event){
                var result = event.target.result;
                $('#thumbnail_adv3').attr('src', result).fadeIn(1000);
                $('#create_adv_btn').prop('disabled', false);
                $('#create_adv_btn').removeClass('btn-inverse');
            };
            reader.readAsDataURL(file);
            $('#create_adv_btn').prop('disabled', true);
            $('#create_adv_btn').addClass('btn-inverse');
        });

        //推广按钮点击事件
        $('#create_adv_btn').click(function(event){
            event.preventDefault();

            var local_favour_adv = new LocalFavourAdv();

            var adv = $('#create_adv_form [name=adv]').val();
            var adv3 = $('#create_adv_form [name=adv3]').val();
            var province_id = $('#create_adv_form [name=province_id]').val();
            var type = $('#create_adv_form [name=type]:checked').val();
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
                contents = $('#create_adv_form [name=link]').val();
            }
            else
            {
                contents = editor.getData();
            }

            var adv_src = $('#create_adv_form #thumbnail_adv').attr('src');
            var adv3_src = $('#create_adv_form #thumbnail_adv3').attr('src');

            var adv3_matches = adv3_src.match('data:image/(.*);base64,(.*)');
    
            local_favour_adv.set('adv_src', adv_src);
            local_favour_adv.set('adv3_src', {
                mime_type: adv3_matches[1],
                base64_data: adv3_matches[2]
            });
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

            local_favour_adv.create();
            LocalFavourAdv.commit();
            return false;
        });


        //删除按钮点击事件
        $(document).on('click','.del-adv-btn', function(event){
            var adv_id = $(this).attr('data-id');
            $('#del_adv_dialog').data('id', adv_id);
            $('#del_adv_dialog').dialog('open', true);
            $('#del_adv_dialog').dialog('center');
        });

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
                }
            }
        });

        LocalFavourAdv.on('beforeCreate', function(event){
            $('#create_adv_btn').text('推广中');
            $('#create_adv_btn').prop('disabled', true);
            $('#create_adv_btn').addClass('btn-inverse');
        });

        LocalFavourAdv.on('created', function(event, data){
            if(!data.success) return;
            $('#local_favour_adv_grid').datagrid('reload');

            $('#create_adv_btn').text('推广');
            $('#create_adv_btn').prop('disabled', false);
            $('#create_adv_btn').removeClass('btn-inverse');

            $('#create_adv_window').window('close', true);

        });

        LocalFavourAdv.on('deleted', function(event, data){
            if(!data.success) return;
            $('#local_favour_adv_grid').datagrid('reload');
        });

        LocalFavourAdv.on('updated', function(event, data){
            if(!data.success) return;
            $('#local_favour_adv_grid').datagrid('reload', {
                page: 1
            });
        });



    }

    //页面离开时
    CarMate.page.on_leave = function(){
        //销毁复选框
        $('#adv_local_favour').combogrid('destroy');
        $('#adv_notice').combogrid('destroy');

        //销毁对话框
        $('#del_adv_dialog').dialog('destroy');

        //销毁窗口
        $('#create_adv_window').window('destroy');

        //取消删除按钮点击事件
        $(document).off('click','.del-adv-btn');
    }
</script>
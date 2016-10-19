<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    input[type=file] {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }
</style>
<div class="row-fluid" id="garage">
    <div class="span12">
        <h4 class="widgettitle">修理厂信息管理页面</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="garage_grid_tb" class="filter-form">
                    <div class="row-fluid">
                        <div class="span4" data-rel-info="idno">
                            <span class="label">商家</span>
                            <select name="mc_id"></select>
                        </div>
                        <div class="span3">
                            <span class="label">名称</span>
                            <input name="name" type="text" class="input-large"/>
                        </div>
                        <div class="span4">
                            <span class="label">电话</span>
                            <input name="tel" type="text" class="input-medium"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span4">
                            <span class="label">地址</span>
                            <input type="text" name="address">
                        </div>
                        <div class="span3">
                            <span class="label">服务</span>
                            <select name="service_id" class="input-small">
                                <option value="">全部</option>
                                {% for service in services %}
                                <option value="{{service['id']}}">{{service['name']}}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="span3">
                            <span class="label">排序字段</span>
                            <select name="order_by" class="input-medium">
                                <option value=""></option>
                                <option value="appraise">评价</option>
                                <option value="reservation_count">人气</option>
                            </select>
                        </div>
                        <div class="span2">
                            <button class="btn btn-primary" id="garage_search_btn">
                                <i class="iconfa-search"></i>
                                查找
                            </button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="btn-group">
                            <button class="btn btn-balance garage-add-btn"><i class="iconfa-plus"></i>添加</button>
                        </div>
                    </div>
                </div>
                <div id="garage_grid">
                </div>
            </div>
        </div>
        <div class="garage_cu_window">
            <div class="row-fluid">
                <input type="hidden" name="id">
            </div>
            <div class="row-fliud" style="margin-bottom: 10px;">
                <span class="label">商家</span>
                <select name="mc_id"></select>
            </div>
            <div class="row-fliud">
                <span class="label">名称</span>
                <input type="text" name="name">
            </div>
            <div class="flow-fluid">
                <span class="label">地址</span>
                <input type="text" name="address">
                <input type="hidden" name="lat">
                <input type="hidden" name="lng">
                <i style="color: #CCC; font-size: 24px; cursor: pointer" class="iconfa-map-marker address-btn" title="地理位置选取"></i>
            </div>
            <div class="row-fluid">
                <span class="label">电话</span>
                <input type="tel" name="tel">
            </div>
            <div class="row-fluid">
                <fieldset class="well well-samll span12">
                    <legend>服务</legend>
                    {% for service in services %}
                    <div class="row-fluid">
                        <input type="checkbox" name="service" value="{{service['id']}}">
                        <span>{{service['name']}}</span>
                    </div>
                    {% endfor %}
                </fieldset>
            </div>
            <div class="row-fluid">
                <div class="span5">
                    <span class="label">小图</span>
                    <input type="hidden" name="thumbnail">
                    <input style="display:none" type="file" name="thumbnail_file" accept="image/jpeg;image/png">
                    <button class="file-proxy" target=".garage_cu_window [name=thumbnail_file]">选择图片</button>
                </div>
                <div class="span6">
                    <img class="thumbnail" style="display:none;width:99%">
                </div>
            </div>
            <div class="row-fluid">
                <div class="span5">
                    <span class="label">大图</span>
                    <input type="hidden" name="img">
                    <input style="display:none" type="file" name="img_file" accept="image/jpeg;image/png">
                    <button class="file-proxy" target=".garage_cu_window [name=img_file]">选择图片</button>
                </div>
                <div class="span6">
                    <img class="img" style="display:none;width: 99%">
                </div>
            </div>
        </div>
        <div class="garage_address_window">
            <iframe id="mapPage" width="100%" height="95%" frameborder=0
                    src="http://apis.map.qq.com/tools/locpicker?search=1&type=1&policy=1&key=H3FBZ-JTYRU-5SAV7-B7BZN-R72JJ-QEBCY&referer=garage_client">
            </iframe>
        </div>
        {#<div id="garage_detail_window"></div>#}
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //修理厂表格
        var garage_grid = $('#garage_grid').datagrid({
            url: '/garage/garages.json',
            title: '修理厂列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
            idField: 'id',
            fitColumns: true,
            singleSelect: false,
            ctrlSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            toolbar: '#garage_grid_tb',
            frozenColumns: [[
                {field:'id',title:'操作',width:'15%',align:'center', formatter: function(value, row, index){
                    var delete_btn = '<button class="btn btn-danger garage-del-btn" title="删除" data-id="' + row.id + '"><i class="iconfa-trash"></i></button>';
                    var edit_btn = '<button class="btn btn-warning garage-edit-btn" title="编辑" data-id="' + row.id + '"><i class="iconfa-edit"></i></button>';

                    return '<div class="btn-group">' + delete_btn + edit_btn + '</div>';
                }}
            ]],
            columns:[[
                {field: 'merchant_name', title: '商家', width: '15%', align: 'center'},
                {field:'name', title:'名称', width:'15%', align:'center'},
                {field:'tel', title:'电话', width:'15%', align:'center'},
                {field:'address', title:'地址', width:'40%', align:'center'},
                {field:'appraise', title:'评价', width:'10%', align:'center'},
                {field:'reservation_count', title:'人气', width:'10%', align:'center'}
            ]]
        });

        //商家选择控件
        var merchant_search_combogrid = $('#garage .filter-form [name=mc_id]').combogrid({
            url: '/garage/merchant/list.json',
            title: '商家列表',
            width: 250,
            panelWidth: 250,
            panelHeight: 350,
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
                {field:'name', title:'名称', width:'90', align:'center'}
            ]]
        });
        var merchant_combogrid = $('.garage_cu_window [name=mc_id]').combogrid({
            url: '/garage/merchant/list.json',
            title: '商家列表',
            width: 250,
            panelWidth: 250,
            panelHeight: 350,
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
                {field:'name', title:'名称', width:'90', align:'center'}
            ]]
        });

        /*   窗口    */

        //修理厂数据添加或编辑窗口
        var garage_cu_window = $('#garage .garage_cu_window').dialog({
            title: '添加修理厂',
            iconCls: 'icon-plus',
            width: 400,
            height: 420,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onBeforeClose: function(){
                merchant_combogrid.combogrid('clear');
                garage_cu_window.find('[name=name], [name=address], [name=lat], [name=lng], [name=thumbnail], [name=img]').val('');
                garage_cu_window.find('input:checkbox').removeAttr('checked');
                garage_cu_window.find('.thumbnail, .img').hide();
                garage_cu_window.find('.address-btn').css('color', '#ccc');
            },
            buttons: [
                {
                    text: '确定',
                    handler: function(event){
                        event.preventDefault();
                        var garage = {};
                        var id = $('.garage_cu_window [name="id"]').val();
                        garage.id = id || null;
                        garage.mc_id = merchant_combogrid.combogrid('getValue');
                        garage.name = $('.garage_cu_window [name="name"]').val();
                        garage.tel = $('.garage_cu_window [name="tel"]').val();
                        garage.address = $('.garage_cu_window [name="address"]').val();
                        garage.lat = $('.garage_cu_window [name="lat"]').val();
                        garage.lng = $('.garage_cu_window [name="lng"]').val();
                        garage.services = [];
                        $('.garage_cu_window [name="service"]:checked').each(function(i, n){
                            garage.services.push($(n).val());
                        });
                        garage.thumbnail = $('.garage_cu_window [name=thumbnail]').val();
                        garage.img = $('.garage_cu_window [name=img]').val();
                        //验证
                        var messages = [];
                        if (!garage.mc_id)
                        {
                            messages.push('没有选择商家');
                        }

                        if (!garage.tel)
                        {
                            messages.push('没有填写电话');
                        }

                        if (!garage.name)
                        {
                            messages.push('没有填写名称');
                        }

                        if (!garage.address)
                        {
                            messages.push('没有填写地址');
                        }

                        if (!garage.lat || !garage.lng)
                        {
                            messages.push('没有选取地理位置');
                        }

                        if (garage.services.length == 0)
                        {
                            messages.push('没有勾选服务项目');
                        }

                        if (messages.length > 0)
                        {
                            $.messager.alert('验证不通过', '提交失败,可能以下原因:<br><p style="color: orangered">' + messages.join('<br>')  + '</p>');
                            return false;
                        }

                        save_garage(garage).done(function(resp){
                            if (resp.success)
                            {
                                garage_grid.datagrid('reload');
                                garage_cu_window.dialog('close');
                            }
                        });
                    }
                },
                {
                    text: '取消',
                    handler: function(event){
                        garage_cu_window.dialog('close');
                    }
                }
            ]
         });

        //地理位置选取窗口
        var garage_address_window = $('#garage .garage_address_window').dialog({
            title: '地理位置选取',
            iconCls: 'icon-plus',
            width: 600,
            height: 800,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        /**
         * 事件相关
         */
        var filter_form = $('#garage .filter-form');
        //查找按钮点击事件
        $('#garage_search_btn').click(function(event){
            var criteria = {};
            filter_form.find('[name]').each(function(i, n){
                var field_name = $(n).attr('name');
                var value = $(n).val();
                criteria[field_name] = value;
            });
            criteria['mc_id'] = merchant_search_combogrid.combogrid('getValue');
//            var old_option = user_grid.datagrid('options');
//            old_option.queryParams = {criteria: criteria};
            //重新配置选项后加载数据,为了改变的columns option 能够生效
//            user_grid.datagrid(old_option);
            garage_grid.datagrid('load', {criteria: criteria});
        });


        //修理厂添加按钮点击事件
        /*$(document).on('click', '.garage-add-btn', function(event){
            var data = {};
            $('').each(function(i, n){

            });
            save_garage({
                user_id: user_id,
                activity_id: activity_id
            }, function(){
                garage_grid.datagrid('reload');
            });
        });*/

        //修理厂添加按钮点击事件
        $('.garage-add-btn').click(function(event){
            var opts = garage_cu_window.dialog('options');
            opts.title = '添加修理厂';
            opts.iconCls = 'iconfa-plus';
            garage_cu_window.dialog(opts).dialog('center').dialog('open');
        });

        //修理厂地址选取输入框点击事件
        $('.garage_cu_window .address-btn').click(function(event){
            garage_address_window.dialog('center').dialog('open');
        });

        $('.garage_cu_window .file-proxy').click(function(event){
            var target_selector = $(this).attr('target');
            $(target_selector).click();
        });

        //小图文件选择器改变事件
        $('.garage_cu_window [name=thumbnail_file]').change(function(event){
            var file = event.target.files[0];
            var file_key = 'thumbnail';
            img_upload(file_key, file).done(function(resp){
                if (resp.success)
                {
                    $('.garage_cu_window [name=thumbnail]').val(resp.data[file_key]);
                    var file_reader = new FileReader();
                    file_reader.readAsDataURL(file);
                    file_reader.onload = function(event){
                        $('.garage_cu_window img.thumbnail').fadeIn().attr('src', event.target.result);
                    };
                }
            });
        });

        //大图文件选择器改变事件
        $('.garage_cu_window [name=img_file]').change(function(event){
            var file = event.target.files[0];
            var file_key = 'img';
            img_upload(file_key, file).done(function(resp){
                if (resp.success)
                {
                    $('.garage_cu_window [name=img]').val(resp.data[file_key])
                    var file_reader = new FileReader();
                    file_reader.readAsDataURL(file);
                    file_reader.onload = function(event){
                        $('.garage_cu_window img.img').fadeIn().attr('src', event.target.result);
                    };
                }
            });
        });

        //修理厂编辑按钮点击事件
        $(document).on('click', '.garage-edit-btn', function(event){
            var garage_id = $(this).attr('data-id');
            $.ajax({
                url: '/garage/garage/' + garage_id + '.json',
                method: 'GET',
                dataType: 'json',
                global: true
            }).done(function(resp){
                if (resp.success)
                {
                    var garage = resp.data;
                    for (var key in garage)
                    {
                        garage_cu_window.find('[name=' + key + ']').val(garage[key]);
                    }
                    merchant_combogrid.combogrid('setValue', garage.mc_id);
                    merchant_combogrid.combogrid('setText', garage.mc_name);
                    $.each(garage.services, function(i, n){
                        garage_cu_window.find('[name=service][value=' + n.id + ']')[0].checked = true;
                    });
                    garage_cu_window.find('.address-btn').css('color', 'red');
                    garage_cu_window.find('img.thumbnail').attr('src', garage.thumbnail).show();
                    garage_cu_window.find('img.img').attr('src', garage.img).show();
                    var opts = garage_cu_window.dialog('options');
                    opts.title = '编辑修理厂';
                    opts.iconCls = 'iconfa-wrench';
                    garage_cu_window.dialog(opts).dialog('center').dialog('open');
                }
            });
        });

        //删除修理厂按钮点击事件
        $(document).on('click', '.garage-del-btn', function(event){
            var garage_id = $(this).attr('data-id');
            $.messager.confirm('确认删除', '该操作无法撤回确认删除?', function(is_ok){
                if (!is_ok) return;
                del_garage(garage_id).done(function(resp){
                    if (resp.success)
                    {
                        garage_grid.datagrid('reload');
                    }
                });
            });
        });

        //地图选取回调事件
        window.addEventListener('message', address_select_listener);

        function address_select_listener(event){
            var loc = event.data;
            if (loc && loc.module == 'locationPicker')
            {
                //console.log('location', loc);
                $('.garage_cu_window [name="lat"]').val(loc.latlng.lat);
                $('.garage_cu_window [name="lng"]').val(loc.latlng.lng);
                $('.garage_cu_window [name="address"]').val(loc.poiaddress + '(' + loc.poiname + ')');
                $('.garage_cu_window .address-btn').css('color', 'red');
            }
        }

        /**
         * 函数相关
         */

        //添加修理厂
        function save_garage(data)
        {
            var url = '/garage/garage.json';
            var method = 'POST';
            var op = '添加';
            if (data.id)
            {
                url = '/garage/garage/' + data.id + '.json';
                method = 'PUT';
                op = '修改';
            }

            return $.ajax({
                url: url,
                method: method,
                data: {data: data},
                global: true
            }).done(function(resp){
                if(resp.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '修理厂' + op +'成功'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '修理厂' + op + '失败'
                    });
                }
            });
        }

        //删除修理厂
        function del_garage(garage_id)
        {
            return $.ajax({
                url: '/garage/garage/' + garage_id + '.json',
                method: 'DELETE',
                global: true
            }).done(function(resp){
                if (resp.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '修理厂删除成功'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '修理厂删除失败'
                    });
                }
            });
        }

        /**
         * 图片上传
         * @param type_name
         * @param file_name
         * @param file
         * @returns {*}
         */
        function img_upload(file_name, file)
        {
            var data = new FormData();
            data.append(file_name, file);
            return $.ajax({
                url: '/upload/garage_img.json',
                data: data,
                method: 'POST',
                processData: false,
                contentType: false
            }).done(function(resp){
                if (resp.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '文件上传成功'
                    });
                }
            });
        }

        //页面离开事件
        this.on_leave = function(){
            //销毁combogrid
            merchant_combogrid.combogrid('destroy');
            //销毁窗口
            garage_cu_window.window('destroy');
            garage_address_window.window('destroy');

            //清除动态绑定事件
            $(document).off('click', '.garage-edit-btn');
            $(document).off('click', '.garage-notice-btn');
            $(document).off('click', '.garage-gain-btn');
            $(document).off('click', '.garage-del-btn');
            $(document).off('click', '.garage-pay-btn');
            $(document).off('click', '.garage-order-detail-btn');

            //清楚地理位置选取事件绑定
            window.removeEventListener('message', address_select_listener);
        };
    };

</script>
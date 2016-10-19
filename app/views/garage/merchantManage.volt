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
<div class="row-fluid" id="garage_merchant">
    <div class="span12">
        <h4 class="widgettitle">修理厂商家信息管理页面</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="garage_merchant_grid_tb" class="filter-form">
                    <div class="row-fluid">
                        <div class="span3">
                            <span class="label">名称</span>
                            <input name="name" type="text" class="input-large"/>
                        </div>
                        <div class="span3">
                            <span class="label">电话</span>
                            <input name="tel" type="text" class="input-medium"/>
                        </div>
                        <div class="span4">
                            <span class="label">地址</span>
                            <input type="text" name="address">
                        </div>
                        <div class="span2">
                            <button class="btn btn-primary" id="garage_merchant_search_btn">
                                <i class="iconfa-search"></i>
                                查找
                            </button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="btn-group">
                            <button class="btn btn-balance garage_merchant-add-btn"><i class="iconfa-plus"></i>添加</button>
                        </div>
                    </div>
                </div>
                <div id="garage_merchant_grid">
                </div>
            </div>
        </div>
        <div class="garage_merchant_cu_window">
            <div class="row-fluid">
                <input type="hidden" name="id">
            </div>
            <div class="row-fliud">
                <span class="label">名称</span>
                <input type="text" name="name">
            </div>
            <div class="flow-fluid">
                <span class="label">地址</span>
                <input type="text" name="address">
            </div>
            <div class="row-fluid">
                <span class="label">电话</span>
                <input type="tel" name="tel">
            </div>
            <div class="row-fluid">
                <div class="span5">
                    <span class="label">营业执照</span>
                    <input type="hidden" name="business_license_img">
                    <input style="display:none" type="file" name="business_license_file" accept="image/jpeg;image/png">
                    <button class="file-proxy" target=".garage_merchant_cu_window [name=business_license_file]">选择图片</button>
                </div>
                <div class="span6">
                    <img class="business_license" style="display:none;width:99%">
                </div>
            </div>
            <div class="row-fluid">
                <div class="span5">
                    <span class="label">负责人身份证</span>
                    <input type="hidden" name="owner_id_card_img">
                    <input style="display:none" type="file" name="owner_id_card_file" accept="image/jpeg;image/png">
                    <button class="file-proxy" target=".garage_merchant_cu_window [name=owner_id_card_file]">选择图片</button>
                </div>
                <div class="span6">
                    <img class="owner_id_card" style="display:none;width: 99%">
                </div>
            </div>
        </div>
        {#<div id="garage_merchant_detail_window"></div>#}
    </div>
</div>

<script type="text/javascript">

    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

                //修理厂表格
        var garage_merchant_grid = $('#garage_merchant_grid').datagrid({
                    url: '/garage/merchant/list.json',
                    title: '商家列表',
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
                    toolbar: '#garage_merchant_grid_tb',
                    frozenColumns: [[
                        {field:'id',title:'操作',width:'15%',align:'center', formatter: function(value, row, index){
                            var delete_btn = '<button class="btn btn-danger garage_merchant-del-btn" title="删除" data-id="' + row.id + '"><i class="iconfa-trash"></i></button>';
                            var edit_btn = '<button class="btn btn-warning garage_merchant-edit-btn" title="编辑" data-id="' + row.id + '"><i class="iconfa-edit"></i></button>';

                            return '<div class="btn-group">' + delete_btn + edit_btn + '</div>';
                        }}
                    ]],
                    columns:[[
                        {field:'name', title:'名称', width:'15%', align:'center'},
                        {field:'tel', title:'电话', width:'15%', align:'center'},
                        {field:'address', title:'地址', width:'40%', align:'center'},
                        {field:'business_license_img', title:'营业执照', width:'10%', align:'center', formatter: function(value, row, index){
                            if (!value) return;
                            return '<i class="tip-img iconfa-picture" style="cursor: pointer" data-src="' + value + '"/>';
                        }},
                        {field:'owner_id_card_img', title:'负责人身份证', width:'10%', align:'center', formatter: function(value, row, index){
                            if (!value) return;
                            return '<i class="tip-img iconfa-picture" style="cursor: pointer" data-src="' + value + '"/>';
                        }}
                    ]]
                });

        /*   窗口    */

        //修理厂数据添加或编辑窗口
        var garage_merchant_cu_window = $('#garage_merchant .garage_merchant_cu_window').dialog({
            title: '添加商家',
            iconCls: 'icon-plus',
            width: 400,
            height: 420,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onBeforeClose: function(){
                garage_merchant_cu_window.find('[name=name], [name=address], [name=tel], [name=business_license_img], [name=owner_id_card_img]').val('');
                garage_merchant_cu_window.find('input:checkbox').removeAttr('checked');
                garage_merchant_cu_window.find('.business_license, .owner_id_card').hide();
                garage_merchant_cu_window.find('.address-btn').css('color', '#ccc');
            },
            buttons: [
                {
                    text: '确定',
                    handler: function(event){
                        event.preventDefault();
                        var garage_merchant = {};
                        var id = $('.garage_merchant_cu_window [name="id"]').val();
                        garage_merchant.id = id || null;
                        garage_merchant.name = $('.garage_merchant_cu_window [name="name"]').val();
                        garage_merchant.tel = $('.garage_merchant_cu_window [name="tel"]').val();
                        garage_merchant.address = $('.garage_merchant_cu_window [name="address"]').val();
                        garage_merchant.lat = $('.garage_merchant_cu_window [name="lat"]').val();
                        garage_merchant.lng = $('.garage_merchant_cu_window [name="lng"]').val();
                        garage_merchant.business_license_img = $('.garage_merchant_cu_window [name=business_license_img]').val();
                        garage_merchant.owner_id_card_img = $('.garage_merchant_cu_window [name=owner_id_card_img]').val();
                        //验证
                        var messages = [];

                        if (!garage_merchant.tel)
                        {
                            messages.push('没有填写电话');
                        }

                        if (!garage_merchant.name)
                        {
                            messages.push('没有填写名称');
                        }

                        if (!garage_merchant.address)
                        {
                            messages.push('没有填写地址');
                        }

                        if (messages.length > 0)
                        {
                            $.messager.alert('验证不通过', '提交失败,可能以下原因:<br><p style="color: orangered">' + messages.join('<br>')  + '</p>');
                            return false;
                        }

                        save_garage_merchant(garage_merchant).done(function(resp){
                            if (resp.success)
                            {
                                garage_merchant_grid.datagrid('reload');
                                garage_merchant_cu_window.dialog('close');
                            }
                        });
                    }
                },
                {
                    text: '取消',
                    handler: function(event){
                        garage_merchant_cu_window.dialog('close');
                    }
                }
            ]
        });

        /**
         * 事件相关
         */
        var filter_form = $('#garage_merchant .filter-form');
        //查找按钮点击事件
        $('#garage_merchant_search_btn').click(function(event){
            var criteria = {};
            filter_form.find('[name]').each(function(i, n){
                var field_name = $(n).attr('name');
                var value = $(n).val();
                criteria[field_name] = value;
            });
            garage_merchant_grid.datagrid('load', {criteria: criteria});
        });


        //修理厂添加按钮点击事件
        /*$(document).on('click', '.garage_merchant-add-btn', function(event){
         var data = {};
         $('').each(function(i, n){

         });
         save_garage_merchant({
         user_id: user_id,
         activity_id: activity_id
         }, function(){
         garage_merchant_grid.datagrid('reload');
         });
         });*/

        //修理厂添加按钮点击事件
        $('.garage_merchant-add-btn').click(function(event){
            var opts = garage_merchant_cu_window.dialog('options');
            opts.title = '添加修理厂';
            opts.iconCls = 'iconfa-plus';
            garage_merchant_cu_window.dialog(opts).dialog('center').dialog('open');
        });

        $('.garage_merchant_cu_window .file-proxy').click(function(event){
            var target_selector = $(this).attr('target');
            $(target_selector).click();
        });

        //营业执照文件选择器改变事件
        $('.garage_merchant_cu_window [name=business_license_file]').change(function(event){
            var file = event.target.files[0];
            var file_key = 'business_license';
            img_upload(file_key, file).done(function(resp){
                if (resp.success)
                {
                    $('.garage_merchant_cu_window [name=business_license_img]').val(resp.data[file_key]);
                    var file_reader = new FileReader();
                    file_reader.readAsDataURL(file);
                    file_reader.onload = function(event){
                        $('.garage_merchant_cu_window img.business_license').fadeIn().attr('src', event.target.result);
                    };
                }
            });
        });

        //文件选择器改变事件
        $('.garage_merchant_cu_window [name=owner_id_card_file]').change(function(event){
            var file = event.target.files[0];
            var file_key = 'owner_id_card_img';
            img_upload(file_key, file).done(function(resp){
                if (resp.success)
                {
                    $('.garage_merchant_cu_window [name=owner_id_card_img]').val(resp.data[file_key]);
                    var file_reader = new FileReader();
                    file_reader.readAsDataURL(file);
                    file_reader.onload = function(event){
                        $('.garage_merchant_cu_window img.owner_id_card').fadeIn().attr('src', event.target.result);
                    };
                }
            });
        });

        //修理厂编辑按钮点击事件
        $(document).on('click', '.garage_merchant-edit-btn', function(event){
            var garage_merchant_id = $(this).attr('data-id');
            var index = garage_merchant_grid.datagrid('getRowIndex', garage_merchant_id);
            var garage_merchant = garage_merchant_grid.datagrid('getData').rows[index];

            for (var key in garage_merchant)
            {
                garage_merchant_cu_window.find('[name=' + key + ']').val(garage_merchant[key]);
            }
            garage_merchant_cu_window.find('img.business_license').attr('src', garage_merchant.business_license_img).show();
            garage_merchant_cu_window.find('img.owner_id_card').attr('src', garage_merchant.owner_id_card_img).show();
            var opts = garage_merchant_cu_window.dialog('options');
            opts.title = '编辑商家';
            opts.iconCls = 'iconfa-wrench';
            garage_merchant_cu_window.dialog(opts).dialog('center').dialog('open');
        });

        //删除修理厂按钮点击事件
        $(document).on('click', '.garage_merchant-del-btn', function(event){
            var garage_merchant_id = $(this).attr('data-id');
            $.messager.confirm('确认删除', '该操作无法撤回确认删除?', function(is_ok){
                if (!is_ok) return;
                del_garage_merchant(garage_merchant_id).done(function(resp){
                    if (resp.success)
                    {
                        garage_merchant_grid.datagrid('reload');
                    }
                });
            });
        });

        //图片图标鼠标划过事件
        $(document).on('mouseover', '.tip-img', function(event){
            var img_src = $(this).attr('data-src');
            $(this).tooltip({
                content: '<img style="width: 300px" src="' + img_src + '"/>'
            }).tooltip('show');
        });

        //图片图标鼠标划出事件
        $(document).on('mouseout', '.tip-img', function(event){
            $(this).tooltip('destroy');
        });


        /**
         * 函数相关
         */

        //添加修理厂商家
        function save_garage_merchant(data)
        {
            var url = '/garage/merchant.json';
            var method = 'POST';
            var op = '添加';
            if (data.id)
            {
                url = '/garage/merchant/' + data.id + '.json';
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

        //删除修理厂商家
        function del_garage_merchant(garage_merchant_id)
        {
            return $.ajax({
                url: '/garage/merchant/' + garage_merchant_id + '.json',
                method: 'DELETE',
                global: true
            }).done(function(resp){
                if (resp.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '商家删除成功'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '商家删除失败'
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
                url: '/upload/garage_merchant_img.json',
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
            //销毁窗口
            garage_merchant_cu_window.dialog('destroy');

            //清除动态绑定事件
            $(document).off('click', '.garage_merchant-edit-btn');
            $(document).off('click', '.garage_merchant-notice-btn');
            $(document).off('click', '.garage_merchant-gain-btn');
            $(document).off('click', '.garage_merchant-del-btn');
            $(document).off('click', '.garage_merchant-pay-btn');
            $(document).off('click', '.garage_merchant-order-detail-btn');
            $(document).off('mouseover', '.tip-img');
        };
    };

</script>
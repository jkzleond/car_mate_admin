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
    .table td, .table th {
        text-align: center;
        vertical-align: middle;
    }

</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">挪车业务车主管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="move_car_co_grid_tb">
                    <div class="row-fluid" id="move_car_co_search_bar">
                        <div class="row-fluid">
                            <div class="span3">
                                <span class="label">用户名</span>
                                <input type="text" name="user_id" class="input-medium" />
                            </div>
                            <div class="span3">
                                <span class="label">手机号</span>
                                <input type="text" name="phone" class="input-small" />
                            </div>
                            <div class="span3">
                                <span class="label">车牌号</span>
                                <input type="text" name="hphm" class="input-mini" />
                            </div>
                            <div class="span3">
                                <button class="btn btn-primary" id="move_car_co_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="move_car_co_grid"></div>
            </div>
            <div id="move_car_co_detail_window"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */

        //数据表格
        var move_car_co_grid = $('#move_car_co_grid').datagrid({
            url: '/move_car/car_owners.json',
            title: '车主列表',
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
            toolbar: '#move_car_co_grid_tb',
            idField: 'id',
            columns:[[
                {field:'hphm', title:'车牌号', width:'12%', align:'center'},
                {field:'user_id', title:'用户名', width:'12%', align:'center'},
                {field:'phone', title:'联系电话', width:'12%', align:'center'},
                {field:'call_count', title:'被叫次数', width:'12%', align:'center'},
                {field:'not_link_count', title:'被叫接听次数', width:'12%', align:'center', formatter: function(value, row, index){
                    return row.call_count - value;
                }},
                {field:'not_owner_count', title:'被反馈不是车主次数', width:'12%', align:'center'},

                {field:'source', title:'数据来源', width: '10%', align:'center', formatter: function(value, row, index){
                    if(value == 'cm')
                    {
                        return '用户输入';
                    }
                    else
                    {
                        return '交管数据';
                    }
                }},
                {field: 'id', title: '操作', width: '15%', align: 'center', formatter: function(value, row, index){
                    var info_btn_html = '<button class="btn btn-info move_car-car_owner-detail-btn" data-id="'+ value + '" data-source="' + row.source + '">话单</button>';
                    var disable_btn_html = '<button class="btn btn-danger move_car-car_owner-disable-btn" data-id="' + value + '" data-source="' + row.source + '">禁用</button>';
                    var enable_btn_html = '<button class="btn btn-primary move_car-car_owner-enable-btn" data-id="' + value + '" data-source="' + row.source + '">启用</button>';

                    if(row.state == 1)
                    {
                        return info_btn_html + disable_btn_html;
                    }
                    else if(row.state == 2)
                    {
                        return info_btn_html + enable_btn_html;
                    }
                    else
                    {
                        return info_btn_html;
                    }

                }}
            ]]
        });

        //窗口
        var move_car_co_detail_window = $('#move_car_co_detail_window').window({
            title: '挪车业务车主相关话单',
            iconCls: 'icon-info-sign',
            width: '90%',
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            onOpen: function(){
                $(this).window('resize', {
                    width: 'auto',
                    height: 'auto'
                });
                $(this).window('center');
            },
            onLoad: function(){

                var resize_width = 'auto';
                var resize_height = 'auto';

                var $panel = $(this).window('panel');
                var panel_width = $panel.outerWidth();
                var panel_height = $panel.outerHeight();
                var client_width = window.innerWidth;
                var client_height = window.innerHeight;

                if(client_width < panel_width)
                {
                    resize_width = Math.round(client_width * 0.8);
                }

                if(client_height < panel_height)
                {
                    resize_height = Math.round(client_height * 0.8);
                }

                $(this).window('resize', {
                    width: resize_width,
                    height: resize_height
                });

                $(this).window('center');

                $(document).trigger('pageLoad:illegalOrderDetail');
            },
            onBeforeClose: function(){
                $(document).trigger('pageLeave:illegalOrderDetail');
            }
        });

        /**
         * 事件相关
         */
        //查找按钮点击事件
        $('#move_car_co_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#move_car_co_search_bar [name="user_id"]').val();
            criteria.phone = $('#move_car_co_search_bar [name="phone"]').val();
            criteria.hphm = $('#move_car_co_search_bar [name="hphm"]').val();
            move_car_co_grid.datagrid('load',{criteria: criteria});
        });

        //挪车业务话单按钮点击事件
        $(document).on('click', '.move_car-car_owner-detail-btn', function(event){
            var car_owner_id = $(this).attr('data-id');
            var car_owner_source = $(this).attr('data-source');
            var opt = move_car_co_detail_window.window('options');
            opt.href = '/move_car/car_owner/' + car_owner_source + '/' + car_owner_id + '/call_record';
            move_car_co_detail_window
                .window(opt)
                .window('setTitle', '车主话单')
                .window('open');
        });

        //挪车业务车主禁用按钮点击事件
        $(document).on('click', '.move_car-car_owner-disable-btn', function(event){
            var car_owner_id = $(this).attr('data-id');
            var car_owner_source = $(this).attr('data-source');
            CarOwner.disable(car_owner_source, car_owner_id).done(function(data){
                if(data.success)
                {
                    move_car_co_grid.datagrid('reload');
                }
            });
        });

        //挪车业务车主启用按钮点击事件
        $(document).on('click', '.move_car-car_owner-enable-btn', function(event){
            var car_owner_id = $(this).attr('data-id');
            var car_owner_source = $(this).attr('data-source');
            CarOwner.enable(car_owner_source, car_owner_id).done(function(data){
                if(data.success)
                {
                    move_car_co_grid.datagrid('reload');
                }
            });
        });

        //数据相关
        var CarOwner = {
            updateState: function(source, id, state){
                return $.ajax({
                    url: '/move_car/car_owner/' + source + '/' + id + '.json',
                    method: 'PUT',
                    data: {criteria: {state: state}},
                    dataType: 'json',
                    global: true
                });
            },
            enable: function(source, id){
                return this.updateState(source, id, 1).done(function(data){
                    if(data.success)
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '车主信息启用成功'
                        });
                    }
                    else
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '车主信息启用失败'
                        });
                    }
                });
            },
            disable: function(source, id){
                return this.updateState(source, id, 2).done(function(data){
                    if(data.success)
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '车主信息禁用成功'
                        });
                    }
                    else
                    {
                        $.messager.show({
                            title: '系统消息',
                            msg: '车主信息禁用失败'
                        });
                    }
                });
            }
        };

        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            move_car_co_detail_window.window('destroy');

            //清除动态绑定事件
            $(document).off('click', '.move_car-car_owner-detail-btn');
            $(document).off('click', '.move_car-car_owner-disable-btn');
            $(document).off('click', '.move_car-car_owner-enable-btn');
        };
    };
</script>
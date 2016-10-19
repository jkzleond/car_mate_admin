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
<div class="row-fluid" id="driver_info_page">
    <div class="span12">
        <h4 class="widgettitle">违章代缴驾驶员信息管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="driver_info_grid_tb">
                    <div class="row-fluid" id="driver_info_search_bar">
                        <div class="row-fluid">
                            <div class="span3">
                                <span class="label">用户名(ID)</span>
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
                                <button class="btn btn-primary" id="driver_info_search_btn"><i class="iconfa-search"></i>查找</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="driver_info_grid"></div>
            </div>
            <div id="driver_info_update_window">
                <div id="driver_info_update_form">
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">驾驶证号</span>
                            <input type="hidden" name="info_id">
                            <input type="hidden" name="user_id">
                            <input type="hidden" name="hphm">
                            <input type="text" name="license_no">
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">档案编号</span>
                            <input type="text" name="archive_no">
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">发动机号</span>
                            <input type="hidden" name="old_engine_no">
                            <input type="text" name="engine_no">
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">车架号</span>
                            <input type="text" name="frame_no">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */

        //数据表格
        var driver_info_grid = $('#driver_info_grid').datagrid({
            url: '/illegal/driverInfoList.json',
            title: '驾驶员信息列表',
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
            pageList:[10,50,100,150,200],
            showFooter: true,
            toolbar: '#driver_info_grid_tb',
            idField: 'id',
            columns:[[
                //{field:'id', title:'用户名(ID)', width:'15%', align:'center'},
                {field:'user_id', title:'用户名(ID)', width:'15%', align:'center'},
                {field:'user_name', title:'姓名', width:'6%', align:'center'},
                {field:'phone', title:'手机号', width:'10%', align:'center'},
                {field:'license_no', title:'驾驶证号', width:'15%', align:'center'},
                {field:'archive_no', title:'档案编号', width:'10%', align:'center'},
                {field:'hphm', title:'车牌号', width:'7%', align:'center'},
                {field:'hpzl', title:'号牌种类', width:'7%', align:'center'},
                {field:'engine_no', title:'发动机号', width:'7%', align:'center'},
                {field:'frame_no', title:'车架号', width:'15%', align:'center'},
                {field: 'id', title: '操作', width: '10%', align: 'center', formatter: function(value, row, index){
                    var update_btn_html = '<button class="btn btn-warning driver-info-update-btn" data-id="'+ value +'" title="编辑"><i class="iconfa-edit"></i></button>';
                    return update_btn_html;
                }}
            ]]
        });

        //窗口
        var driver_info_update_window = $('#driver_info_update_window').dialog({
            title: '驾驶员信息编辑',
            iconCls: 'icon-wrench',
            width: 240,
            height: 'auto',
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade',
            buttons: [
                {
                    text: '编辑',
                    handler: function(){
                        var info_id = $('#driver_info_update_window [name="info_id"]').val();
                        var user_id = $('#driver_info_update_window [name="user_id"]').val();
                        var hphm = $('#driver_info_update_window [name="hphm"]').val();
                        var license_no = $('#driver_info_update_window [name="license_no"]').val();
                        var archive_no = $('#driver_info_update_window [name="archive_no"]').val();
                        var old_engine_no = $('#driver_info_update_window [name="old_engine_no"]').val();
                        var engine_no = $('#driver_info_update_window [name="engine_no"]').val();
                        var frame_no = $('#driver_info_update_window [name="frame_no"]').val();
                        
                        $.ajax({
                            url: '/illegal/driverInfo/' + info_id + '.json',
                            method: 'PUT',
                            data: {
                                data: {
                                    license_no: license_no, 
                                    archive_no: archive_no,
                                    engine_no: engine_no,
                                    frame_no: frame_no
                                },
                                criteria: {
                                    engine_no: old_engine_no,
                                    user_id: user_id,
                                    hphm: hphm
                                }
                            },
                            dataType: 'json',
                            global: true
                        }).done(function(data){
                            if(!data.success)
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '驾驶员信息更新失败'
                                });
                            }
                            else
                            {
                                $.messager.show({
                                    title: '系统消息',
                                    msg: '驾驶员信息更新成功'
                                });
                                driver_info_grid.datagrid('load');
                            }
                        });
                        driver_info_update_window.dialog('close');
                    }
                },
                {
                    text: '取消',
                    handler: function(){
                        driver_info_update_window.dialog('close');
                    }
                }
            ],
            onClose: function(){
                $(this).find('[name]').val('');//窗口关闭后清除表单
            }
        });



        /**
         * 事件相关
         */
        //查找按钮点击事件
        $('#driver_info_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#driver_info_search_bar [name="user_id"]').val();
            criteria.phone = $('#driver_info_search_bar [name="phone"]').val();
            criteria.hphm = $('#driver_info_search_bar [name="hphm"]').val();
            driver_info_grid.datagrid('load',{criteria: criteria});
        });

        //违章代缴处理按钮点击事件
        $('#driver_info_page').on('click', '.driver-info-update-btn', function(event){

            var selected_info = driver_info_grid.datagrid('getSelected');

            var info_id = $(this).attr('data-id');
            driver_info_update_window.find('[name=info_id]').val(info_id);
            driver_info_update_window.find('[name=user_id]').val(selected_info.user_id);
            driver_info_update_window.find('[name=hphm]').val(selected_info.hphm);
            driver_info_update_window.find('[name=license_no]').val(selected_info.license_no);
            driver_info_update_window.find('[name=archive_no]').val(selected_info.archive_no);
            driver_info_update_window.find('[name=old_engine_no]').val(selected_info.engine_no);
            driver_info_update_window.find('[name=engine_no]').val(selected_info.engine_no);
            driver_info_update_window.find('[name=frame_no]').val(selected_info.frame_no);

            driver_info_update_window.dialog('open');
        });


        /**
         * 页面离开时事件
         */
        CarMate.page.on_leave = function(){
            //销毁窗口
            driver_info_update_window.dialog('destroy');

            //清除动态绑定事件
            // $(document).off('click', '.driver-info-update-btn');
        };
    };
</script>
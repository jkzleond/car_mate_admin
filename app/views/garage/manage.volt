<style type="text/css" rel="stylesheet">
    div.datagrid * {
        vertical-align: middle;
    }
    .cursor-pointer {
        cursor: pointer;
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
                        <div class="span4">
                            <span class="label">名称</span>
                            <input name="name" type="text" class="input-small"/>
                        </div>
                        <div class="span4">
                            <span class="label">电话</span>
                            <input name="tel" type="text" class="input-medium"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span4">
                            <span class="label">服务</span>
                            <select name="state" class="input-small">
                                <option value="">-请选择状态-</option>
                                {% for service in services %}
                                <option value="{{service.id}}">{{service.name}}</option>
                                {% endfor %}
                            </select>
                        </div>
                        <div class="span4">
                            <span class="label">排序字段</span>
                            <select name="order_by" class="input-medium">
                                <option value="appraise">评价</option>
                                <option value="reservation_count">人气</option>
                            </select>
                        </div>
                        <div class="span4">
                            <button class="btn btn-primary" id="garage_search_btn">
                                <i class="iconfa-search"></i>
                                查找
                            </button>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="btn-group">
                            <button class="btn btn-blance garage-add-btn"><i class="iconfa-plus"></i>添加</button>
                        </div>
                    </div>
                </div>
                <div id="garage_grid">
                </div>
            </div>
        </div>
        <div class="garage_cu_window">
            <div class="row-fliud">
                <span class="label">商家</span>
                <select name="mc_id"></select>
            </div>
            <div class="row-fliud">
                <span class="label">名称</span>
                <input type="text" name="name">
            </div>
            <div class="row-fluid">
                <fieldset class="well well-samll span12">
                    <legend>服务</legend>
                    {% for service in services %}
                    <div class="row-fluid">
                        <input type="checkbox" name="service" value="{{service.id}}">
                        <span>{{service.name}}</span>
                    </div>
                    {% endfor %}
                </fieldset>
            </div>
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
            title: '活动参与用户列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 'auto',
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
                {field:'address', title:'地址', width:'15%', align:'center'}
            ]]
        });

        /*   窗口    */

        //编辑窗口
        /*var user_window = $('#garage_window').window({
         title: '活动参与用户编辑',
         iconCls: 'icon-edit',
         width: 315,
         height: 525,
         closed: true,
         shadow: false,
         modal: true,
         openAnimation: 'fade'
         });*/


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
//            var old_option = user_grid.datagrid('options');
//            old_option.queryParams = {criteria: criteria};
            //重新配置选项后加载数据,为了改变的columns option 能够生效
//            user_grid.datagrid(old_option);
            garage_grid.datagrid('reload', {criteria: criteria});
        });

        //全选/全不选按钮点击事件
        $('#garage_select_all_btn').click(function(evetn){
            var is_select_all = $(this).data('is_select_all');
            if(is_select_all)
            {
                garage_grid.datagrid('unselectAll');
                //记录全选状态
                $(this).data('is_select_all', false);
            }
            else
            {
                garage_grid.datagrid('selectAll');
                //记录全选状态
                $(this).data('is_select_all', true);
            }
        });

        //批量领取
        $('#garage_batch_gain_btn').click(function(event){
            var url = '/activityUserGain';
            batch_process('领取', url);
        });

        //修理厂添加按钮点击事件
        $(document).on('click', '.garage-add-btn', function(event){
            var data = {};
            $('').each(function(i, n){

            });
            save_garage({
                user_id: user_id,
                activity_id: activity_id
            }, function(){
                garage_grid.datagrid('reload');
            });
        });

        //修理厂删除按钮点击事件
        $(document).on('click', '.garage-edit-btn', function(event){
            var user_id = $(this).attr('data-user_id');
            var activity_id = $(this).attr('data-activity_id');
            gain({
                user_id: user_id,
                activity_id: activity_id
            }, function(){
                garage_grid.datagrid('reload');
            });
        });

        //删除参与用户信息
        $(document).on('click', '.garage-del-btn', function(event){
            var user_id = $(this).attr('data-user_id');
            var activity_id = $(this).attr('data-activity_id');
            del(activity_id, user_id, function(){
                garage_grid.datagrid('reload');
            });
        });

        /**
         * 函数相关
         */

        function export_data(data, callback)
        {
            $.ajax({
                url: '',
                data: data,
                method: 'get',
                global: true
            }).done(function(resp){

            });
        }

        //批量处理
        function batch_process(op, url)
        {
            var selections = user_grid.datagrid('getSelections');
            if(selections.length == 0)
            {
                $.messager.alert('没有选择记录', '请至少选择一条记录', 'icon-warning-sign');
                return;
            }

            var ids = '';
            var len = selections.length;
            for(var i = 0; i < len; i++)
            {
                ids += selections[i].id + ',';
            }

            ids = ids.substring(0, ids.length - 1);

            var target_url = url + '/' + ids + '.json';

            $.messager.confirm('是否确定提交', '提交后无法撤消,是否确定要进行批量[' + op + ']操作', function(is_ok){
                if(is_ok)
                {
                    $.ajax({
                        url: target_url,
                        method: 'PUT',
                        data: {},
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(!data.success) return;
                        //提交成功后,再次点击搜索按钮,以刷新显示的数据
                        $('#garage_search_btn').click();
                    });
                }
            });
        }

        //单个用户处理
        function single_process(op, url)
        {
            $.messager.confirm('是否确定提交', '提交后无法撤消,是否确定要进行[' + op + ']操作', function(is_ok){
                if(is_ok)
                {
                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: {},
                        dataType: 'json',
                        global: true
                    }).done(function(data){
                        if(!data.success) return;
                        //提交成功后,再次点击搜索按钮,以刷新显示的数据
                        $('#garage_search_btn').click();
                    });
                }
            });
        }

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

        //领取
        function gain(data, callback)
        {
            $.messager.confirm('确认', '确定对该用户的奖品已领取?', function(is_ok){
                if (is_ok)
                {
                    $.ajax({
                        url: '/gygd/stadium/activity/user/gain.json',
                        method: 'PUT',
                        data: {data: data},
                        global: true
                    }).done(function(resp){
                        if (resp.success)
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '用户领取标记成功'
                            });

                            if (typeof callback == 'function')
                            {
                                callback();
                            }
                        }
                        else
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '用户领取标记失败'
                            });
                        }
                    });
                }
            });
        }

        /**
         * 删除参与用户信息
         * @param activity_id
         * @param user_id
         * @param callback
         */
        function del(activity_id, user_id, callback)
        {
            $.messager.confirm('确认', '确定对删除该参与用户信息', function(is_ok){
                if (is_ok)
                {
                    $.ajax({
                        url: '/gygd/stadium/activity/' + activity_id +'/users/' + user_id + '.json',
                        method: 'DELETE',
                        global: true
                    }).done(function(resp){
                        if (resp.success)
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '删除该参与用户信息成功'
                            });

                            if (typeof callback == 'function')
                            {
                                callback();
                            }
                        }
                        else
                        {
                            $.messager.show({
                                title: '系统消息',
                                msg: '删除该参与用户信息失败'
                            });
                        }
                    });
                }
            });
        }

        //页面离开事件
        this.on_leave = function(){
            //销毁窗口
            $('#garage_winodw').window('destroy');

            //清除动态绑定事件
            $(document).off('click', '.garage-edit-btn');
            $(document).off('click', '.garage-notice-btn');
            $(document).off('click', '.garage-gain-btn');
            $(document).off('click', '.garage-del-btn');
            $(document).off('click', '.garage-pay-btn');
            $(document).off('click', '.garage-order-detail-btn');
        };
    };

</script>
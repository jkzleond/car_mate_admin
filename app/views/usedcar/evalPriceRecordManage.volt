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
<div class="row-fluid" id="eval_price_record">
    <div class="span12">
        <h4 class="widgettitle">估价记录信息管理页面</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="eval_price_record_grid_tb" class="filter-form">
                    <div class="row-fluid">
                        <div class="span4" data-rel-info="idno">
                            <span class="label">用户名</span>
                            <input name="user_id" type="text" class="input-large"/>
                        </div>
                        <div class="span4">
                            <span class="label">姓名</span>
                            <input name="user_name" type="text" class="input-large"/>
                        </div>
                        <div class="span4">
                            <span class="label">电话</span>
                            <input name="phone" type="text" class="input-medium"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span4">
                            <span class="label">时间段</span>
                            <input type="text" name="start_date" class="input-medium">
                            -
                            <input type="text" name="end_date"  class="input-medium">
                        </div>
                        <div class="span3">
                            <span class="label">预约状态</span>
                            <select name="status" class="input-medium">
                                <option value="">全部</option>
                                <option value="0">未预约</option>
                                <option value="1">已预约</option>
                                <option value="2">已处理</option>
                            </select>
                        </div>
                        <div class="span2">
                            <button class="btn btn-primary" id="eval_price_record_search_btn">
                                <i class="iconfa-search"></i>
                                查找
                            </button>
                        </div>
                    </div>
                </div>
                <div id="eval_price_record_grid">
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
        var start_datebox = $('#eval_price_record_grid_tb [name="start_date"]').datebox({width: 100, editable: false});
        var end_datebox = $('#eval_price_record_grid_tb [name="end_date"]').datebox({width: 100, editable: false});

        //估价记录表格
        var eval_price_record_grid = $('#eval_price_record_grid').datagrid({
                    url: '/used_car/eval_price_record.json',
                    title: '估价记录列表',
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
                    toolbar: '#eval_price_record_grid_tb',
                    frozenColumns: [[
                        {field:'id',title:'操作',width:'15%',align:'center', formatter: function(value, row, index){
//                            var delete_btn = '<button class="btn btn-danger eval_price_record-del-btn" title="删除" data-id="' + row.id + '"><i class="iconfa-trash"></i></button>';
//                            var edit_btn = '<button class="btn btn-warning eval_price_record-edit-btn" title="编辑" data-id="' + row.id + '"><i class="iconfa-edit"></i></button>';
                            var process_btn = '<button class="btn btn-primary eval_price_record-process-btn" data-id="' + row.id + '">处理</button>';
                            var remark_btn = '<button class="btn btn-warning eval_price_record-remark-btn" data-id="' + row.id + '">备注<buton>';

                            if (row.status != '2')
                            {
                                return '<div class="btn-group">' + process_btn + remark_btn + '</div>';
                            }
                            else {
                                return '<div class="btn-group">' + remark_btn + '</div>'
                            }
                        }},
                        {field:'status', title:'状态', width:'10%', align:'center', formatter: function(value, row, index){
                            switch (value)
                            {
                                case '0':
                                    return '未预约';
                                case '1':
                                    return '已预约';
                                case '2':
                                    return '已处理';
                            }
                        }}
                    ]],
                    columns:[[
                        {field:'user_id', title: '用户名', width: '15%', align: 'center'},
                        {field:'user_name', title:'姓名', width:'10%', align:'center'},
                        {field:'phone', title:'电话', width:'15%', align:'center'},
                        {field:'brand_name', title:'品牌型号', width:'15%', align:'center', formatter: function(value, row, index){
                            return row.series_name + row.spec_name;
                        }},
                        {field:'first_reg_time', title:'上牌时间', width:'10%', align:'center'},
                        {field:'mile_age', title:'里程数(万公里)', width:'10%', align:'center'},
                        {field:'mid_price', title:'建议售价(万元)', width:'10%', align:'center'},
                        {field:'create_date', title:'时间', width:'10%', align:'center'},
                        {field:'des', title:'备注', width:'20%', align:'center'}
                    ]]
                });


        /* 窗口 */
        /**
         * 事件相关
         */
        var filter_form = $('#eval_price_record .filter-form');
        //查找按钮点击事件
        $('#eval_price_record_search_btn').click(function(event){
            var criteria = {};
            filter_form.find('[name]').each(function(i, n){
                var field_name = $(n).attr('name');
                var value = $(n).val();
                criteria[field_name] = value;
            });
            var start_date = start_datebox.datebox('getValue');
            criteria['start_date'] = start_date;
            var end_date = end_datebox.datebox('getValue');
            criteria['end_date'] = end_date;
            eval_price_record_grid.datagrid('load', {criteria: criteria});
        });

        //估价记录编辑按钮点击事件
        $(document).on('click', '.eval_price_record-process-btn', function(event){
            var eval_price_record_id = $(this).attr('data-id');
            $.messager.confirm('确定处理', '此操作不呢撤回,确定将该记录设置成已处理状态?', function(is_ok){
                if (!is_ok) return;
                // status: 2 -- 已处理
                update_eval_price_record(eval_price_record_id, {status: 2})
                        .then(function(resp){
                            if(resp.success)
                            {
                                eval_price_record_grid.datagrid('reload');
                            }
                        });
            });
        });

        //估价记录编辑按钮点击事件
        $(document).on('click', '.eval_price_record-remark-btn', function(event){
            var eval_price_record_id = $(this).attr('data-id');
            $.messager.prompt('填写备注', '备注内容', function(content){
                if (!content) return;
                update_eval_price_record(eval_price_record_id, {des: content})
                        .then(function(resp){
                            if(resp.success)
                            {
                                eval_price_record_grid.datagrid('reload');
                            }
                        });
            });
        });

        //删除估价记录按钮点击事件
        $(document).on('click', '.eval_price_record-del-btn', function(event){
            var eval_price_record_id = $(this).attr('data-id');
            $.messager.confirm('确认删除', '该操作无法撤回确认删除?', function(is_ok){
                if (!is_ok) return;
                del_eval_price_record(eval_price_record_id).done(function(resp){
                    if (resp.success)
                    {
                        eval_price_record_grid.datagrid('reload');
                    }
                });
            });
        });

        /**
         * 函数相关
         */

        //更新估价记录
        function update_eval_price_record(eval_price_record_id, data)
        {
            return $.ajax({
                url: '/used_car/eval_price_record/' + eval_price_record_id + '.json',
                method: 'PUT',
                data: {data: data},
                dataType: 'json',
                global: true
            }).done(function(resp){
                if (resp.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '估价记录更新成功'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '估价记录更新失败'
                    });
                }
            });
        }

        //删除估价记录
        function del_eval_price_record(eval_price_record_id)
        {
            return $.ajax({
                url: '/eval_price_record/eval_price_record/' + eval_price_record_id + '.json',
                method: 'DELETE',
                global: true
            }).done(function(resp){
                if (resp.success)
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '估价记录删除成功'
                    });
                }
                else
                {
                    $.messager.show({
                        title: '系统消息',
                        msg: '估价记录删除失败'
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
                url: '/upload/eval_price_record_img.json',
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
            //销毁时间控件
            start_datebox.datebox('destroy');
            end_datebox.datebox('destroy');
            //清除动态绑定事件
            $(document).off('click', '.eval_price_record-process-btn');
            $(document).off('click', '.eval_price_record-remark-btn');
            $(document).off('click', '.eval_price_record-del-btn');
            $(document).off('click', '.eval_price_record-order-detail-btn');
        };
    };

</script>
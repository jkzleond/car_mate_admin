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
</style>
<div class="row-fluid">
    <div class="span12">
        <div class="row-fluid">
            <div id="award_grid_tb">
                <div class="row-fluid">
                    <div class="span4">
                        <span class="label">时间段</span>
                        <select name="period">
                            <option value="">全天</option>
                            {% for period in period_list %}
                            <option value="{{ period['id'] }}">{{ period['start_time'] }}-{{ period['end_time'] }}</option>
                            {% endfor %}
                        </select>
                    </div>
                    <div class="span4">
                        <button id="award_add_btn" class="btn btn-primary hidden-none"><i class="iconfa-plus"></i>添加奖品</button>
                    </div>
                </div>
            </div>
            <div id="award_grid"></div>
        </div>
        <div id="award_cu_window">
            <form id="award_cu_form" action="">
                <div class="row-fluid">
                    <div class="span6">
                        <span class="label">名称</span>
                        <input type="hidden" name="id"/>
                        <input type="text" name="name"/>
                    </div>
                    <div class="span6">
                        <span class="label">说明</span>
                        <input type="text" name="des"/>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span4">
                        <span class="label">数量</span>
                        <input id="award_numbox" type="text" name="num"/>
                    </div>
                    <div class="span4">
                        <span class="label">概率(万分之一)</span>
                        <input id="award_rate_numbox" type="text" name="rate"/>
                    </div>
                    <div class="span4">
                        <span class="label">单日中奖额度</span>
                        <input id="award_day_limit_numbox" type="text" name="day_limit"/>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <span class="label">图片</span>
                        <input id="award_pic_file" type="file" name="picf"/>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span3">
                        <img id="award_pic" src="" alt="" class="hidden-none"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.viewManager.onLoad = function(container){
        var opt = container.window('options');
        opt.iconCls = 'iconfa-info-sign';
        opt.title = '奖品管理';
        container.window(opt);
        /**
         *控件相关
         */
        //活动选择框
        var activity_select = $('#award_activity_list').combogrid({
            url: '/awardActivityList.json',
            title: '活动列表',
            width: 300,
            idField: 'id',
            textField: 'name',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            onChange: function(new_value, old_value){
                var aid = new_value;
                $('#award_add_btn').show();
                award_grid.datagrid('load',{
                    aid: new_value
                });
                return new_value;
            },
            columns:[[
                {field:'name', title:'活动名称', width:'90%', align:'center'}
            ]]
        });

        //奖品表格
        var award_grid = $('#award_grid').datagrid({
            url: '/awardList.json',
            title: '奖品列表',
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
            toolbar: '#award_grid_tb',
            columns:[[
                {field:'id', title:'ID', width:'5%', align:'center'},
                {field:'name', title:'名称', width:'10%', align:'center'},
                {field:'num', title:'数量', width:'5%', align:'center'},
                {field:'rate', title:'概率(万分之一)', width:'8%', align:'center'},
                {field:'winnum', title:'已抽数量', width:'5%', align:'center'},
                {field:'createDate',title:'创建时间',width:'8%',align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var time = value.replace(/\s+/g, '/');
                    time = time.replace(/:\d+(AM|PM)/, ' $1');
                    return CarMate.utils.date('Y-m-d H:i:s', time);
                }},
                {field:'lastWinDate',title:'最近抽中',width:'8%',align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var time = value.replace(/\s+/g, '/');
                    time = time.replace(/:\d+(AM|PM)/, ' $1');
                    return CarMate.utils.date('Y-m-d H:i:s', time);
                }},
                {field:'pic', title:'图片', width:'18%', align:'center', formatter: function(value, row, index){
                    if(!value) return '无';
                    return '<img  src="data:image/png;base64,' + value + '" alt="奖品图片"/>';
                }},
                {field:'des', title:'说明', width:'5%', align:'center'},
                {field:'state',title:'当前状态',width:'5%',align:'center', formatter: function(value, row, index){
                    if(value === 0 || value === '0')
                    {
                        return '<span class="label label-success">未领完</span>';
                    }
                    else if(value == 1)
                    {
                        return '<span class="label label-important">已领完</span>';
                    }
                }},
                {field:'dayLimit',title:'单日中奖额度',width:'8%',align:'center'},
                {field:'dayNum',title:'今日已中',width:'5%',align:'center'},
                {field:'aid',title:'操作',width:'10%',align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    if(row.state != 2)
                    {
                        return '<div class="btn-group"><button class="btn btn-mini btn-warning award-edit-btn" data-id="' + row.id + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger award-del-btn" data-id="' + row.id + '"><i class="icon-trash"></i></button></div>';
                    }
                }}
            ]]
        });

        //窗口
        var award_cu_window = $('#award_cu_window').dialog({
            title: '奖品添加',
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
                        var waiting = $(this).data('waiting');
                        if(waiting) return;

                        var win_state = award_cu_window.data('state');

                        var name = $('#award_cu_form [name="name"]').val();
                        var des = $('#award_cu_form [name="des"]').val();
                        var pic_data = $('#award_pic').attr('src').match(/base64,(.*)/)[1];
                        var num = award_numbox.numberspinner('getValue');
                        var rate = award_rate_numbox.numberspinner('getValue');
                        var day_limit = award_day_limit_numbox.numberspinner('getValue');

                        var award = new Award();

                        award.set('name', name);
                        award.set('des', des);
                        award.set('num', num);
                        award.set('rate', rate);
                        award.set('day_limit', day_limit);
                        award.set('pic_data', pic_data);

                        if(win_state == 'create')
                        {
                            var aid = activity_select.combobox('getValue');
                            award.set('aid', aid);
                            award.create();
                        }
                        else if(win_state == 'update' )
                        {
                            var id = $('#award_cu_form [name="id"]').val();
                            award.set('id', id);
                            award.update();
                        }

                        Award.commit();

                        $(this).data('waiting', true);
                        $('#award_cu_window').dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#award_cu_window').dialog('close');
                    }
                }
            ]
        });

        //数字输入框
        var award_numbox = $('#award_numbox').numberspinner({
            width: 80,
            height: 30,
            value: 0
        });
        var award_rate_numbox = $('#award_rate_numbox').numberspinner({
            width: 80,
            height: 30,
            value: 0,
            max: 10000,
            suffix: '‱'
        });
        var award_day_limit_numbox = $('#award_day_limit_numbox').numberspinner({
            width: 80,
            height: 30,
            value: 0
        });

        /**
         * 事件相关
         **/

        //添加奖品按钮点击事件
        $('#award_add_btn').click(function(event){

            $('#award_cu_form [name="name"]').val('');
            $('#award_cu_form [name="des"]').val('');
            $('#award_cu_form [name="picf"]').val('');

            $('#award_pic').attr('src', '');

            award_numbox.numberspinner('reset');
            award_rate_numbox.numberspinner('reset');
            award_day_limit_numbox.numberspinner('reset');

            award_cu_window.data('state', 'create');

            var opt = award_cu_window.dialog('options');
            opt.title = '添加奖品';
            opt.iconCls = 'icon-plus';
            opt.buttons[0].text = '添加';

            award_cu_window.dialog(opt).dialog('open').dialog('center');
        });

        //奖品图片选择时事件
        $('#award_pic_file').change(function(event){
            var file = this.files[0];
            var f_reader = new FileReader();
            f_reader.onload = function(event){
                var img_url = event.target.result;
                $('#award_pic').attr('src', img_url).hide().fadeIn(1000);
            };

            f_reader.readAsDataURL(file);
        });

        //编辑按钮事件
        $(document).on('click', '.award-edit-btn', function(event){
            var award = award_grid.datagrid('getSelected');

            $('#award_cu_form [name="id"]').val(award.id);
            $('#award_cu_form [name="name"]').val(award.name);
            $('#award_cu_form [name="des"]').val(award.des);
            //$('#award_cu_form [name="picf"]').val('');
            $('#award_pic').attr('src', 'data:image/png;base64,' + award.pic).hide().fadeIn(1000);

            award_numbox.numberspinner('setValue', award.num);
            award_rate_numbox.numberspinner('setValue', award.rate);
            award_day_limit_numbox.numberspinner('setValue', award.dayLimit);

            award_cu_window.data('state', 'update');

            var opt = award_cu_window.dialog('options');
            opt.title = '编辑奖品';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '编辑';

            award_cu_window.dialog(opt).dialog('open').dialog('center');
        });

        //删除按钮事件
        $(document).on('click', '.award-del-btn', function(event){
            var id = $(this).attr('data-id');
            $.messager.confirm('是否删除', '删除该奖品的同时将删除所有相关的中奖记录,是否继续删除该奖品?', function(is_ok){
                if(is_ok)
                {
                    var award = new Award();
                    award.set('id', id);
                    award.delete();
                    Award.commit();
                }
            });
        });

        /**
         *数据相关
         **/

        var Award = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){

                    if(action == 'update' || action == 'create') return '/award.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            });
                            var ids_str = ids.join('-');
                            return '/award/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/award/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        Award.on('created', function(event, data){
            if(!data.success) return;
            $(this).data('waiting', false);
            $.messager.show({
                title: '系统消息',
                msg: '奖品添加成功'
            });
            award_grid.datagrid('load');
        });

        Award.on('updated', function(event, data){
            if(!data.success) return;
            $(this).data('waiting', false);
            $.messager.show({
                title: '系统消息',
                msg: '奖品更新成功'
            });
            award_grid.datagrid('reload');
        });

        Award.on('deleted', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '奖品删除成功'
            });
            award_grid.datagrid('reload');
        });

        /**
         * 页面离开时事件,用于清理资源
         */
        this.onLeave = function(){
            console.log('leave');
            //销毁窗口
            activity_select.combogrid('destroy');
            award_cu_window.dialog('destroy');

            //清除动态绑定事件
            $(document).off('click', '.award-edit-btn');
            $(document).off('click', '.award-del-btn');
        };

    };
</script>


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
        <h4 class="widgettitle">活动管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div id="activity_grid"></div>
            </div>

            <!-- 发布/编辑本地惠窗口 -->
            <div id="activity_cu_window" class="relative">
                <form id="activity_cu_form" class="well well-small" action="" method="post">
                    <div class="row-fluid">
                        <div class="span7">
                            <input name="id" type="hidden" class="hidden-none"/>
                            <label class="label label-important" for="activity_name">活动名称</label>
                            <input id="activity_name" name="name" type="text" class="input-xxlarge required" />
                        </div>
                        <div class="span2">
                            <label class="label" for="activity_type">类型</label>
                            <select id="activity_type" name="type_id" class="input-small">
                                {% for type in type_list %}
                                <option value="{{ type.id }}">{{ type.name }}</option>
                                {% endfor%}
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <span class="label">图片</span>
                            <input id="activity_pic_file" type="file" name="picf" value="" accept="image/jpeg, image/png, image/gif" />
                        </div>
                        <div class="span2">
                            <img id="activity_img" name="pic_data" src="" />
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">内容</span>
                            <textarea id="activity_contents_editor" name="contents" style="width:60%; height:200px"></textarea>
                        </div>
                    </div>
                    <div class="row-fluid" style="margin-top: 10px">
                        <div class="span5">
                            <label class="label">报名开始时间</label>
                            <input name="sign_start" id="activity_sign_start" class="datetimebox" type="text" />
                        </div>
                        <div class="span5">
                            <label class="label">报名结束时间</label>
                            <input name="sign_end" id="activity_sign_end" class="datetimebox" type="text" />
                        </div>
                    </div>
                    <div class="row-fluid hidden-none" id="activity_trip_line">
                        <div class="span12">
                            <span class="label">行程线路</span>
                            <textarea name="trip_line" class="input-large span8"></textarea>
                        </div>
                    </div>
                    <div class="row-fluid hidden-none" id="activity_award">
                        <div class="span5">
                            <label class="label">抽奖开始时间</label>
                            <input name="award_start" id="activity_award_start" class="datetimebox" type="text" tag="抽奖开始时间" />
                        </div>
                        <div class="span5">
                            <label class="label">抽奖结束时间</label>
                            <input name="award_end" id="activity_award_end" class="datetimebox" type="text" tag="抽奖结束时间" />
                        </div>
                        <div class="span2 activity-state" style="display:none">
                            <label class="label" for="activity_award_state">抽奖状态</label>
                            <select id="activity_award_state" name="award_state" class="input-small">
                                <option value="0">未启动</option>
                                <option value="1">进行中</option>
                                <option value="2">已过期</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <label class="label">需要付款</label>
                            <select id="activity_need_pay" name="need_pay" class="input-mini">
                                <option value="0" checked>否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid hidden-none pay" >
                        <fieldset class="well well-small">
                            <legend>付款金额</legend>
                            <div class="row-fluid">
                                <label class="label">付款金额(老本活动用)：</label>
                                <input type="text" name="deposit" value="0" tag="付款金额">
                            </div>
                            <div class="row-fluid">
                                <div class="span4">
                                    <label class="label">付款项目：</label>
                                    <button id="activity_add_pay_item"><i class="iconfa-plus"></i>添加</button>
                                </div>
                                <div id="activity_pay_item_container" class="span8">

                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="row-fluid hidden-none pay" style="display: none">
                        <fieldset class="well well-small">
                            <legend>付款方式</legend>
                            <div class="controls controls-row">
                                <div class="span6">
                                    <label for="CASH" class="checkbox">
                                        <input type="checkbox" name="pay_type" value="CASH" id="CASH"/>线下现金支付
                                    </label>
                                </div>
                                <div class="span6">
                                    <label for="POS" class="checkbox">
                                        <input type="checkbox" name="pay_type" value="POS" id="POS"/>线下POS机刷卡
                                    </label>
                                </div>
                            </div>
                            <div class="controls controls-row">
                                <div class="span6">
                                    <label for="ONLINE" class="checkbox">
                                        <input type="checkbox" name="pay_type" value="ONLINE" id="ONLINE"/>支付宝在线支付
                                    </label>
                                </div>
                                <div class="span6">
                                    <label for="TRANSFER" class="checkbox">
                                        <input type="checkbox" name="pay_type" value="TRANSFER" id="TRANSFER"/>支付宝转账
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="row-fluid">
                        <div class="span7">
                            <label class="label">相关链接</label>
                            <input id="url" name="url" type="text" class="input-xxlarge"/>
                        </div>
                        <div class="span3">
                            <label class="label">自动生成链接</label>
                            <select id="activity_auto_start" name="auto_start" class="input-mini">
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                        <div class="span2">
                            <label class="label">分组ID</label>
                            <input id="activity_group_column" name="group_column" type="text" tag="分组ID" class="input-mini"/>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span3">
                            <label class="label label-important">开始时间</label>
                            <input name="start_date" id="activity_start_date" type="text" class="input-medium datetimebox" />
                        </div>
                        <div class="span3">
                            <label class="label label-important">结束时间</label>
                            <input name="end_date" id="activity_end_date" type="text" class="input-medium datetimebox" />
                        </div>
                        <div class="span2">
                            <label class="label">需要签到</label>
                            <select id="activity_need_check_in" name="need_check_in" class="input-mini">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                        <div class="span2">
                            <label class="label">需要通知</label>
                            <select id="activity_need_notice" name="need_notice" class="input-mini">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                        <div class="span2 activity-state" style="display:none">
                            <label class="label" for="activity_state">活动状态</label>
                            <select id="activity_state" name="state" class="input-small">
                                <option value="0">未启动</option>
                                <option value="1">进行中</option>
                                <option value="2">已过期</option>
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <fieldset class="well well-small span6">
                            <legend>登记信息</legend>
                            <div class="row-fluid">
                                <label for="activity_info1" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info1" value="uname"/>真实姓名
                                </label>
                                <label for="activity_info2" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info2" value="phone"/>电话/手机
                                </label>
                                <label for="activity_info3" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info3" value="sex"/>性别
                                </label>
                                <label for="activity_info4" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info4" value="address"/>地址
                                </label>
                            </div>
                            <div class="row-fluid">
                                <label for="activity_info5" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info5" value="idcardno"/>身份证号
                                </label>
                                <label for="activity_info6" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info6" value="pca"/>省市区
                                </label>
                                <label for="activity_info7" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info7" value="sinaWeibo"/>新浪微博账号
                                </label>
                                <label for="activity_info8" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info8" value="weixin"/>微信账号
                                </label>
                            </div>
                            <div class="row-fluid">
                                <label for="activity_info9" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info9" value="hphm"/>号牌
                                </label>
                                <label for="activity_info10" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info10" value="people"/>人数
                                </label>
                                <label for="activity_info11" class="checkbox span3">
                                    <input type="checkbox" name="info" id="activity_info11" value="qqNum"/>QQ号(选填)
                                </label>
                            </div>
                            <div class="row-fluid">
                                <label for="activity_info12" class="checkbox span3">
                                    <input type="checkbox" name="info" value="auto" id="activity_info12" />其他信息
                                </label>
                                <input type="text" name="option" class="input-medium"/>
                            </div>
                            <div class="row-fluid">
                                <label for="activity_info13" class="checkbox inline">
                                    <input type="checkbox" name="info" value="select" id="activity_info13" />下拉列表
                                </label>
                            </div>
                        </fieldset>

                        <fieldset id="activity_has_select" class="span6 well well-small" style="display: none" >
                            <legend>下拉列表设置</legend>
                            <label class="label">显示名称：</label>
                            <input type="text" name="sel_name" value=""/><br/>
                            <div id="activity_sel_ops">
                                <div class="row-fluid sel-option">
                                    <div class="span6">
                                        <span class="badge badge-info ">1</span><input type="text" name="sel_option" style="width:200px;"/>
                                    </div>
                                    <div class="span6">
                                        <span class="label label-important">简称</span><input type="text" name="sel_short_name" class="required-info" style="width:70px;"/>
                                        <span class="label">金额</span><input type="text" name="sel_deposit" style="width:50px;"/>
                                    </div>
                                </div>
                                <div class="row-fluid sel-option">
                                    <div class="span6">
                                        <span class="badge badge-info">2</span><input type="text" name="sel_option" style="width:200px;"/>
                                    </div>
                                    <div class="span6">
                                        <span class="label label-important">简称</span><input type="text" name="sel_short_name" class="required-info" style="width:70px;"/>
                                        <span class="label">金额</span><input type="text" name="sel_deposit" style="width:50px;"/>
                                    </div>
                                </div>
                            </div>
                            <button id="activity_add_sel_option" class="btn btn-small"><i class="icon-plus"></i>添加选项</button>
                        </fieldset>
                    </div>
                </form>
            </div>

            <!--   付款信息窗口         -->
            <div id="activity_pay_window">
                <table id="activity_pay_grid"></table>
            </div>
            <!--   签到信息窗口  -->
            <div id="activity_check_in_window">
                <table id="activity_check_in_grid"></table>
                <div id="activity_check_in_tb" style="padding:2px 5px;">
                    <span class="label">按用户ID搜索</span>
                    <input type="text" name="buyer_name" id=""/>
                    <button class="btn btn-primary" id="activity_check_in_search_btn"><i class="icon-search"></i></button>
                </div>
            </div>
            <!--   预览窗口   -->
            <div id="activity_preview_window">
                <iframe src="" frameborder="0" style="width: 300px; height: 480px"></iframe>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){
        /**
         * 控件相关
         */

        //创建ckeditor
        var editor = CKEDITOR.replace( 'activity_contents_editor', {
            enterMode: CKEDITOR.ENTER_P,
            height: 270,
            removePlugins : 'save'
            //filebrowserImageUploadUrl : 'ckUploadImage?command=QuickUpload&type=Images'
        });
        var finder_path = "{{ url('/js/ckfinder/') }}";
        //集成ckfinder
        CKFinder.setupCKEditor(editor, finder_path);

        /******窗口*********/
        //活动添加窗口
        $('#activity_cu_window').dialog({
            title: '活动添加',
            iconCls: 'icon-plus',
            width: 1100,
            height: 600,
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

                        //验证
                        $('.required').validatebox({required: true, message: '字段为必填字段！'});

                        var is_all_valid = true;

                        $('.required').each(function(i, n){
                            var is_valid = $(this).validatebox('isValid');
                            is_all_valid = is_all_valid && is_valid;
                        });

                        is_all_valid = is_all_valid && start_date.datetimebox('isValid');
                        is_all_valid = is_all_valid && end_date.datetimebox('isValid');

                        if(!is_all_valid) return;



                        var win_state = $('#activity_cu_window').data('state');

                        var activity = new Activity();

                        var name = $('#activity_cu_window [name="name"]').val();
                        var type_id = $('#activity_cu_window [name="type_id"]').val();
                        var award_start = $('#activity_cu_window [name="award_start"]').val();
                        var award_end = $('#activity_cu_window [name="award_end"]').val();
                        var need_pay = $('#activity_cu_window [name="need_pay"]').val();

                        var pay_items = [];

                        if(need_pay != false)
                        {
                            $('#activity_cu_window .activity-pay-item').each(function(i, n){

                                var pay_item = {};
                                pay_item.name = $(n).find('[name="pay_item_name"]').val();
                                pay_item.price = $(n).find('[name="pay_item_price"]').val();

                                if(win_state == 'update')
                                {
                                    pay_item.id = $(n).find('[name="pay_item_id"]').val();
                                }

                                pay_items.push(pay_item);
                            });
                        }

                        var pay_types = [];
                        $('#activity_cu_window [name="pay_type"]:checked').each(function(i, n){
                            pay_types.push($(n).val());
                        });

                        var pic_data = null;
                        if($('#activity_pic_file').val())
                        {
                            pic_data = $('#activity_img').attr('src').match(/base64,(.*)/)[1];
                        }

                        var contents = editor.getData(); //$('#activity_cu_window [name="contents"]').val();

                        var deposit = $('#activity_cu_window [name="deposit"]').val();
                        var url = $('#activity_cu_window [name="url"]').val();
                        var auto_start = $('#activity_cu_window [name="auto_start"]').val();
                        var group_column = $('#activity_cu_window [name="group_column"]').val();
                        var _start_date = $('#activity_cu_window [name="start_date"]').val();
                        var _end_date = $('#activity_cu_window [name="end_date"]').val();
                        var sign_start_date = sign_start.datetimebox('getValue');
                        var sign_end_date = sign_end.datetimebox('getValue');
                        var trip_line = $('#activity_cu_window [name="trip_line"]').val();
                        var need_check_in = $('#activity_cu_window [name="need_check_in"]').val();
                        var need_notice = $('#activity_cu_window [name="need_notice"]').val();
                        var state = $('#activity_cu_window [name="state"]').val();

                        var info = [];

                        var option = $('#activity_cu_window [name="option"]').val();

                        $('#activity_cu_window [name="info"]:checked').each(function(i, n){
                            info.push($(n).val());
                        });

                        //如果选中下拉列表选项就要增加验证
                        if(info.indexOf('select') != -1)
                        {
                            $('.required-info').validatebox({required: true, message: '字段为必填字段！'});


                            $('.required-info').each(function(i, n){
                                var is_valid = $(this).validatebox('isValid')
                                is_all_valid = is_all_valid && is_valid;
                            });

                            if(!is_all_valid) return;
                        }

                        activity.set('name', name);
                        activity.set('type_id', type_id);
                        activity.set('award_start', award_start);
                        activity.set('award_end', award_end);
                        activity.set('need_pay', need_pay);
                        activity.set('pay_items', pay_items); //付款项目
                        activity.set('pay_types', pay_types);

                        activity.set('pic_data', pic_data);
                        activity.set('contents', contents);

                        activity.set('deposit', deposit);
                        activity.set('url', url);
                        activity.set('auto_start', auto_start);
                        activity.set('group_column', group_column);
                        activity.set('start_date', _start_date);
                        activity.set('end_date', _end_date);
                        activity.set('sign_start_date', sign_start_date);
                        activity.set('sign_end_date', sign_end_date);
                        activity.set('trip_line', trip_line);
                        activity.set('need_check_in', need_check_in);
                        activity.set('need_notice', need_notice);
                        activity.set('state', state);
                        activity.set('info', info);

                        if(info.indexOf('auto') != -1)
                        {
                            activity.set('option', option);
                        }

                        if(info.indexOf('select') != -1)
                        {
                            var sel_name = $('#activity_cu_form [name="sel_name"]').val();

                            var sel_options = [];
                            var sel_short_names = [];
                            var sel_deposits = [];

                            $('#activity_cu_form .sel-option').each(function(i, n){
                                sel_options.push($(n).find('[name="sel_option"]').val());
                                sel_short_names.push($(n).find('[name="sel_short_name"]').val());
                                sel_deposits.push($(n).find('[name="sel_deposit"]').val());
                            });
                            activity.set('sel_name', sel_name);
                            activity.set('sel_options', sel_options);
                            activity.set('sel_short_names', sel_short_names);
                            activity.set('sel_deposits', sel_deposits);
                        }

                        if(win_state == 'create')
                        {
                            activity.create();
                        }
                        else if(win_state == 'update')
                        {
                            var id = $('#activity_cu_form [name="id"]').val();
                            activity.set('id', id);
                            activity.update();
                        }

                        Activity.commit();
                        $(this).data('waiting', true);
                        $('#activity_cu_window').dialog('close', true);
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        $('#activity_cu_window').dialog('close', true);
                    }
                }
            ]
        });

        //活动付款信息窗口
        $('#activity_pay_window').window({
            title: '活动支付信息',
            iconCls: 'icon-info-sign',
            width: '80%',
            height: 550,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //活动签到信息窗口
        $('#activity_check_in_window').window({
            title: '活动签到信息',
            iconCls: 'icon-info-sign',
            width: '80%',
            height: 550,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        //预览窗口
        $('#activity_preview_window').window({
            title: '活动预览',
            iconCls: 'icon-eye-open',
            width: 315,
            height: 525,
            closed: true,
            shadow: false,
            modal: true,
            openAnimation: 'fade'
        });

        /******表格********/

        //活动表格

        $('#activity_grid').datagrid({
            url: '/activityList.json',
            title: '活动列表',
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
            view: $.fn.datagrid.defaults.detailview,
            //pageList:[50,100,150,200],
            loadFilter: function(data){
                //因为两个单元格同事需要id,为避免字段冲突智能复制该字段
                if(data.count == 0) return data;
                var pass_rows = [];

                var rows = data.rows;

                var cur_row = {};

                for(var i = 0; i < rows.length; i++)
                {
                    var row = rows[i];

                    if(cur_row.id != row.id)
                    {
                        cur_row = row;
                        cur_row.pay_items = [];
                        pass_rows.push(cur_row);
                    }

                    if(row.pay_item_id)
                    {
                        cur_row.pay_items.push({
                            id: row.pay_item_id,
                            name: row.pay_item_name,
                            price: row.pay_item_price
                        });
                    }
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
                    $('#activity_cu_form :text').val('');
                    $('#activity_cu_form select :first-child').attr('selected', true);
                    $('#activity_cu_form select').change();
                    $('#activity_cu_form :checkbox:checked').attr('checked', false);
                    $('#activity_cu_form :checkbox').change();

                    $('#activity_pay_item_container').empty();

                    $('.activity-state').hide();
                    $('#activity_cu_form .sel-add').remove();

                    $('#activity_pic_file').val('');
                    $('#activity_img').attr('src', '').hide();
                    editor.setData('');

                    $('#activity_cu_form [name="trip_line"]').val('');

                    //设置窗口状态,并打开
                    $('#activity_cu_window').data('state', 'create');

                    var opt = $('#activity_cu_window').dialog('options');
                    opt.title = '添加活动';
                    opt.iconCls = 'icon-plus';
                    opt.buttons[0].text = '添加';
                    $('#activity_cu_window').dialog(opt).dialog('open', true).dialog('center');

                }
            }],
            detailFormatter:function(index,row){
                return '<div class="ddv" style="padding:5px 0"></div>';
            },
            onExpandRow: function(index, row){
                var ddv = $(this).datagrid('getRowDetail',index).find('div.ddv');
                ddv.panel({
                    height:'auto',
                    border:false,
                    cache:false,
                    href: "{{ url('/activityDetail/') }}" + row.id,
                    onLoad:function(){
                        $('#activity_grid').datagrid('fixDetailRowHeight',index);
                    }
                });
                $('#activity_grid').datagrid('fixDetailRowHeight',index);
            },
            frozenColumns: [[
                {field:'id',title:'操作',width:'12%',align:'center', formatter: function(value, row, index){
                    if(!value) return;
                    if(row.state != 2)
                    {
                        return '<div class="btn-group"><button title="预览" class="btn btn-mini btn-info activity-preview-btn" data-id="' + value + '"><i class="icon-eye-open"></i></button><button class="btn btn-mini btn-warning activity-update-btn" data-id="' + value + '"><i class="icon-edit"></i></button><button class="btn btn-mini btn-danger activity-del-btn" data-id="' + value + '"><i class="icon-trash"></i></button></div>';
                    }
                    else
                    {
                        return '<div class="btn-group"><button title="预览" class="btn btn-mini btn-info activity-preview-btn" data-id="' + value + '"><i class="icon-eye-open"></i></button><button class="btn btn-mini btn-warning activity-update-btn" data-id="' + value + '"><i class="icon-edit"></i></button></div>';
                    }

                }}
            ]],
            columns:[[
                {field:'name', title:'活动名称', width:'15%', align:'center'},
                {field:'rownum', title:'url(新)', width:'15%', align:'center', formatter: function(value, row, index){
                    if(row.typeId == '3')
                    {
                        return 'http://116.55.248.76:8092/?{url_param}#tour/detail/' + row.id;
                    }
                    else
                    {
                        return 'http://116.55.248.76:8092/?{url_param}#activity/detail/' + row.id;
                    }
                }},
                {field:'url',title:'相关链接',width:'20%',align:'center'},
                {field:'createDate',title:'创建时间',width:'9%',align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var time = value.replace(/\s+/g, '/');
                    time = time.replace(/:\d+(AM|PM)/, ' $1');
                    return CarMate.utils.date('Y-m-d H:i:s', time);
                }},
                {field:'startDate',title:'开始时间',width:'9%',align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var time = value.replace(/\s+/g, '/');
                    time = time.replace(/:\d+(AM|PM)/, ' $1');
                    return CarMate.utils.date('Y-m-d H:i:s', time);
                }},
                {field:'endDate',title:'结束时间',width:'9%',align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var time = value.replace(/\s+/g, '/');
                    time = time.replace(/:\d+(AM|PM)/, ' $1');
                    return CarMate.utils.date('Y-m-d H:i:s', time);
                }},
                {field:'state',title:'当前状态',width:'6%',align:'center', formatter: function(value, row, index){
                    if(value == 1)
                    {
                        return '进行中';
                    }
                    else if(value == 2)
                    {
                        return '已过期';
                    }
                    else
                    {
                        return '未启动';
                    }
                }},
                {field:'typeName',title:'类型',width:'6%',align:'center'},
                {field:'num',title:'参与人数',width:'6%',align:'center'},
                {field:'gainNum',title:'领取人数',width:'6%',align:'center'}
            ]]
        });

        //活动付款信息表格
        $('#activity_pay_grid').datagrid({
            url: '/activityPayList.json',
            title: '支付信息列表列表',
            iconCls: 'icon-list',
            width: '100%',
            height: 510,
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination: true,///分页
            pageSize: 10,///（每页记录数）
            pageNumber: 1,///（当前页码）
            columns: [[
                {field:'orderNo', title:'订单号', width:'15%', align:'center'},
                {field:'orderName', title:'商品名称', width:'15%', align:'center'},
                {field:'userId', title:'用户ID', width:'15%', align:'center'},
                {field:'buyerName', title:'用户名', width:'15%', align:'center'},
                {field:'money', title:'金额', width:'5%', align:'center'},
                {field:'state', title:'状态', width:'5%', align:'center', formatter: function(value, row, index){
                    if(value == 'TRADE_FINISHED')
                    {
                        return '已付款';
                    }
                    else
                    {
                        return '未付款';
                    }
                }},
                {field:'createTime', title:'创建时间', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var date_conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', date_conv);
                }},
                {field:'payTime', title:'支付时间', width:'10%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var date_conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', date_conv);
                }}
            ]]
        });

        //活动签到信息表格
        $('#activity_check_in_grid').datagrid({
            url: '/activityCheckList.json',
            title: '签到信息列表',
            iconCls: 'icon-list',
            toolbar: '#activity_check_in_tb',
            width: '100%',
            height: 510,
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            pagination:true,///分页
            pageSize:10,///（每页记录数）
            pageNumber:1,///（当前页码）
            view: $.fn.datagrid.defaults.groupview,
            groupField: 'userid',
            groupFormatter: function(value, rows){
                return value + ' 共签到 ' + rows.length + ' 次';
            },
            columns: [[
                {field:'id', title:'ID', width:'5%', align:'center'},
                {field:'userid', title:'用户ID', width:'15%', align:'center'},
                {field:'createDate', title:'时间', width:'20%', align:'center', formatter: function(value, row, index){
                    if(!value) return '';
                    var date_conv = CarMate.utils.date.mssqlToJs(value);
                    return CarMate.utils.date('Y-m-d H:i:s', date_conv);
                }},
                {field:'des', title:'说明', width:'20%', align:'center'},
                {field:'url', title:'链接', width:'30', align:'center'}
            ]]
        });

        //日期时间控件

        var award_start = $('#activity_cu_form [name="award_start"]').datetimebox({editable: false});
        var award_end = $('#activity_cu_form [name="award_end"]').datetimebox({editable: false});

        var sign_start = $('#activity_cu_form [name="sign_start"]').datetimebox({editable: false});
        var sign_end = $('#activity_cu_form [name="sign_end"]').datetimebox({editable: false});

        var start_date = $('#activity_cu_form [name="start_date"]').datetimebox({
            editable: false,
            required: true,
            message: '必填日期！'
        });
        var end_date = $('#activity_cu_form [name="end_date"]').datetimebox({
            editable: false,
            required: true,
            message: '必填日期！'
        });

        /**
         * 事件相关
         */

        //活动类型select change 事件
        $('#activity_type').change(function(event){
            $('#activity_award').hide(500);
            $('#activity_trip_line').hide(500);
            if($(this).val() == '2')
            {
                $('#activity_award').show(500);
            }
            else if($(this).val() == '3')
            {
                $('#activity_trip_line').show(500);
            }
        });

        //是否需要付款select change 事件
        $('#activity_need_pay').change(function(event){
            if($(this).val() == '1')
            {
                $('.pay').show(500);
            }
            else
            {
                $('.pay').hide(500);
            }
        });

        //添加付款项目按钮点击事件
        $('#activity_add_pay_item').click(function(event){
            var item_tpl = '<div class="row-fluid activity-pay-item">' +
                                '<div class="span5">' +
                                    '<lable class="label">款项名称</lable>' +
                                    '<input type="text" name="pay_item_name" class="input-medium"/>' +
                                '</div>' +
                                '<div class="span5">' +
                                    '<lable class="label">款项金额</lable>' +
                                    '<input type="text" name="pay_item_price" class="input-medium"/>' +
                                '</div>' +
                                '<div class="span1">' +
                                    '<button class="activity-remove-pay-item"><i class="iconfa-remove"></i></button>' +
                                '</div>' +
                            '</div>';

            var $item = $(item_tpl);
            $('#activity_pay_item_container').append($item);
            $item.find('[name="pay_item_price"]').numberbox({
                precision: 2,
                groupSeparator: ','
            });

            return false;
        });

        //删除付款项目按钮点击事件
        $(document).on('click', '.activity-remove-pay-item', function(){

            $(this).parents('.activity-pay-item').remove();

            return false;
        });

        //[下拉列表]复选框 change 事件
        $('#activity_info13').change(function(event){
            if($(this).attr('checked'))
            {
                $('#activity_has_select').show(500);
            }
            else
            {
                $('#activity_has_select').hide(500);
            }
        });

        //活动图片选择框改变事件
        $('#activity_pic_file').change(function(event){
            var file = this.files[0];

            var reader = new FileReader();
            reader.addEventListener('load', function(event){
                var src = event.target.result;
                $('#activity_img').attr('src', src).fadeIn();
            });
            reader.readAsDataURL(file);
        });

        //编辑按钮点击事件
        $(document).on('click', '.activity-update-btn', function(event){

            var row = $('#activity_grid').datagrid('getSelected');

            //先清空文本框
            $('#activity_cu_form :text').val('');
            //清除sel-add
            $('.sel-add').remove();
            //显示状态选择列表
            $('.activity-state').show();

            $('#activity_cu_form [name="id"]').val(row.id);
            $('#activity_cu_form [name="name"]').val(row.name);

            $('#activity_cu_form [name="type_id"]').val(row.typeId);
            $('#activity_cu_form [name="type_id"]').change();

            $('#activity_pic_file').val('');
            $('#activity_img').attr('src', 'data:image/png;base64,' + row.picData).show();
            editor.setData(row.contents); //$('#activity_cu_form [name="contents"]').val(row.contents);

            $('#activity_cu_form [name="trip_line"]').text(row.tripLine || '');

            var sstime = row.signStartDate ? CarMate.utils.date.mssqlToJs(row.signStartDate) : '';
            var setime = row.signEndDate ? CarMate.utils.date.mssqlToJs(row.signEndDate) : '';

            sstime = sstime ? CarMate.utils.date('Y-m-d H:i:s', sstime) : '';
            setime = setime ? CarMate.utils.date('Y-m-d H:i:s', setime) : '';

            sign_start.datetimebox('setValue', sstime);
            sign_end.datetimebox('setValue', setime);


            var astime = CarMate.utils.date.mssqlToJs(row.awardStart);
            var aetime = CarMate.utils.date.mssqlToJs(row.awardEnd);
            award_start.datetimebox('setValue', CarMate.utils.date('Y-m-d H:i:s', astime));
            award_end.datetimebox('setValue', CarMate.utils.date('Y-m-d H:i:s', aetime));
            $('#activity_cu_form [name="award_state"]').val(row.awardState);

            $('#activity_cu_form [name="need_pay"]').val(row.needPay);
            $('#activity_cu_form [name="need_pay"]').change();

            $('#activity_pay_item_container').empty();

            var len = row.pay_items.length;

            var pay_item_tpl = '<div class="row-fluid activity-pay-item">' +
                '<div class="span5">' +
                '<lable class="label">款项名称</lable>' +
                    '<input type="hidden" name="pay_item_id" value="<:id>"/>' +
                '<input type="text" name="pay_item_name" class="input-medium" value="<:name>"/>' +
                '</div>' +
                '<div class="span5">' +
                '<lable class="label">款项金额</lable>' +
                '<input type="text" name="pay_item_price" class="input-medium" value="<:price>"/>' +
                '</div>' +
                '<div class="span1">' +
                '<button class="activity-remove-pay-item"><i class="iconfa-remove"></i></button>' +
                '</div>' +
                '</div>';

            for(var i = 0; i < len; i++)
            {
                var pay_item = row.pay_items[i];
                var item_html = pay_item_tpl.replace(/<:([^<:>]*)>/g, function($0, $1, match_pos){
                    return pay_item[$1];
                });
                $('#activity_pay_item_container').append(item_html);
            }

            $('#activity_pay_item_container [name="pay_item_price"]').numberbox({
                precision: 2,
                groupSeparator: ','
            });


            var pay_types = row.payTypes ? row.payTypes.split(', ') : [];

            $('#activity_cu_window [name="pay_type"]').each(function(i, n){
                if( pay_types.indexOf( $(this).val() ) == -1 )
                {
                    $(this).attr('checked', false);
                }
                else
                {
                    $(this).attr('checked', true);
                }
            });

            $('#activity_cu_form [name="deposit"]').val(row.deposit);
            $('#activity_cu_form [name="url"]').val(row.url);
            $('#activity_cu_form [name="auto_start"]').val(row.autoStart);
            $('#activity_cu_form [name="group_column"]').val(row.groupColumn);
            var sdate = CarMate.utils.date.mssqlToJs(row.startDate);
            var edate = CarMate.utils.date.mssqlToJs(row.endDate);
            start_date.datetimebox('setValue', CarMate.utils.date('Y-m-d H:i:s', sdate));
            end_date.datetimebox('setValue', CarMate.utils.date('Y-m-d H:i:s', edate));
            $('#activity_cu_form [name="state"]').val(row.state);

            $('#activity_cu_form [name="need_check_in"]').val(row.needCheckIn);
            $('#activity_cu_form [name="need_notice"]').val(row.needNotice);

            var info = row.infos ? row.infos.split(', ') : [];

            $('#activity_cu_window [name="info"]').each(function(i, n){
                if( info.indexOf( $(this).val() ) == -1 )
                {
                    $(this).attr('checked', false);
                }
                else
                {
                    $(this).attr('checked', true);

                    if( $(this).val() == 'select' )
                    {
                        $('#activity_cu_form [name="sel_name"]').val(row.sname);

                        var sel_options = row.optionList ? row.optionList.split(', ') : [];
                        var sel_short_names = row.shortNames ? row.shortNames.split(', ') : [];
                        var sel_deposits = row.depositList ? row.depositList.split(', ') : [];

                        var len = sel_options.length;

                        for(var i = 0; i < len; i++)
                        {
                            if(i < 2)
                            {
                                var option_container = $('#activity_cu_form .sel-option').eq(i);
                                option_container.find('[name=sel_option]').val(sel_options[i]);
                                option_container.find('[name=sel_short_name]').val(sel_short_names[i]);
                                option_container.find('[name=sel_deposit]').val(sel_deposits[i]);
                            }
                            else
                            {
                                $('#activity_sel_ops').append(
                                    '<div class="row-fluid sel-option sel-add">' +
                                    '<div class="span6">' +
                                    '<span class="badge badge-info">' + (i+1) +'</span><input type="text" name="sel_option" style="width:200px;" value="' + sel_options[i] +'"/>' +
                                    '</div>' +
                                    '<div class="span6">' +
                                    '<span class="label label-important">简称</span><input type="text" name="sel_short_name" style="width:70px;" value="' + sel_short_names[i] +'"/>' +
                                    '<span class="label">金额</span><input type="text" name="sel_deposit" style="width:50px;" value="' + sel_deposits[i] + '"/>' +
                                    '</div>' +
                                    '</div>'
                                );
                            }
                        }
                    }
                }

                if( $(this).val() == 'select' )
                {
                    $(this).change();
                }
            });

            $('#activity_cu_form [name="option"]').val(row.option);

            //改变窗口状态
            $('#activity_cu_window').data('state', 'update');

            var opt = $('#activity_cu_window').dialog('options');
            opt.title = '修改活动';
            opt.iconCls = 'icon-wrench';
            opt.buttons[0].text = '修改';
            $('#activity_cu_window').dialog(opt).dialog('open', true).dialog('center');

        });

        //删除活动按钮点击事件
        $(document).on('click', '.activity-del-btn', function(event){
            var id = $(this).attr('data-id');

            $.messager.confirm('删除活动', '是否删除活动', function(result){
                if(result)
                {
                    var activity = new Activity();
                    activity.set('id', id);
                    activity.delete();
                    Activity.commit();
                }
            });
        });

        //签到信息查看按钮点击事件
        $(document).on('click', '.check-user-view', function(event){
            var aid = $(this).attr('data-id');
            $('#activity_check_in_window').window('open').window('center');
            $('#activity_check_in_grid').datagrid('reload', {
                aid: aid
            });
        });

        //活动预览按钮点击事件
        $(document).on('click', '.activity-preview-btn', function(event){
            var activity = $('#activity_grid').datagrid('getSelected');
            var url = activity.url.replace('{loginname}', 'SYSTEM_ACCOUNT');
            $('#activity_preview_window iframe').attr('src', url);
            $('#activity_preview_window').window('open').window('center');
        });

        //签到信息查看按钮点击事件
        $(document).on('click', '.pay-user-view', function(event){
            var aid = $(this).attr('data-id');
            $('#activity_pay_window').window('open').window('center');
            $('#activity_pay_grid').datagrid('load', {
                aid: aid
            });
        });

        //添加选项按钮点击事件
        $('#activity_add_sel_option').click(function(event){
            event.preventDefault();
            var option_num = $('.sel-option').length + 1;
            $('#activity_sel_ops').append(
                '<div class="row-fluid sel-option sel-add">' +
                    '<div class="span6">' +
                        '<span class="badge badge-info">' + option_num +'</span><input type="text" name="sel_option" style="width:200px;"/>' +
                    '</div>' +
                    '<div class="span6">' +
                        '<span class="label label-important">简称</span><input type="text" name="sel_short_name"  class="required-info" style="width:70px;"/>' +
                        '<span class="label">金额</span><input type="text" name="sel_deposit" style="width:50px;"/>' +
                    '</div>' +
                '</div>'
            );
            return false;
        });

        //用户签到信息搜索按钮点击事件
        $('#activity_check_in_search_btn').click(function(event){
            var user_id = $('#activity_check_in_tb [name="buyer_name"]').val();
            $('#activity_check_in_grid').datagrid('load', {user_id: user_id});
        });

        /**
         * 数据相关
         */

        //活动模型
        var Activity = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){
                    if(action == 'update' || action == 'create') return '/activity.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            })
                            var ids_str = ids.join('-');
                            return '/activity/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/activity/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        Activity.on('created', function(event, data){
            if(!data.success)
            {
                $.messager.show({title: '系统消息', msg: '活动添加失败'});
                return;
            }
            $.messager.show({title: '系统消息', msg: '活动添加成功'});
            $('#activity_grid').datagrid('reload');
        });

        Activity.on('deleted', function(event, data){
            if(!data.success)
            {
                $.messager.show({title: '系统消息', msg: '活动删除失败'});
                return;
            }
            $.messager.show({title: '系统消息', msg: '活动删除成功'});
            $('#activity_grid').datagrid('reload');
        });

        Activity.on('updated', function(event, data){
            if(!data.success)
            {
                $.messager.show({title: '系统消息', msg: '活动更新失败'});
                return;
            }
            $.messager.show({title: '系统消息', msg: '活动更新成功'});
            $('#activity_grid').datagrid('reload');
        });
    };

    CarMate.page.on_leave = function(){
        //销毁窗口
        $('#activity_cu_window').dialog('destroy');
        $('#activity_pay_window').window('destroy');
        $('#activity_check_in_window').window('destroy');
        $('#activity_preview_window').window('destroy');

        //清除动态绑定事件
        $(document).off('click', '.activity-update-btn');
        $(document).off('click', '.activity-del-btn');
        $(document).off('click', '.activity-detail-btn');
        $(document).off('click', '.check-user-view');
        $(document).off('click', '.pay-user-view');
        $(document).off('click', '.activity-remove-pay-item');
    };
</script>

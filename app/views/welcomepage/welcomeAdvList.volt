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

    #welcome_adv_pagination.pagination table td {
        vertical-align: middle;
    }

    div.welcome-adv-pic-container {
        width: 18%;
        position: relative;
        float: left;
        margin: 0.1%;
    }

    .welcome-adv-pic-container img{
        width:100%;
    }

    div.welcome-adv-pic-toolbar-top{
        width: 100%;
        position: absolute;
        top: 0px;
        left: 0px;
    }

    div.welcome-adv-pic-toolbar-bottom{
        width: 100%;
        position: absolute;
        bottom: 0px;
        left: 0px;
    }
    .welcome-adv-pic-tool{
        font-size: 20px;
        margin-left: 2px;
        float: right;
        cursor: pointer;
    }
    .welcome-adv-pic-tool:hover{
        color: deepskyblue;
        text-decoration: none;
    }
</style>
<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">开屏广告管理</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <div class="span12">
                    <div id="welcome_adv_pagination"></div>
                </div>
            </div>
            <div class="row-fluid">
                <div id="welcome_adv_grid"></div>
            </div>
            <div id="welcome_adv_cu_window">
                <form id="welcome_adv_cu_form" action="">
                    <div class="row-fluid">
                        <div class="span12">
                            <input name="id" type="hidden"/>
                            <span class="label">所在省份</span>
                            <select name="province_id">
                                {% for province in province_list %}
                                <option value="{{ province.id }}">{{ province.name }}</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">相关网址</span>
                            <input type="text" name="url"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">定时</span>
                            <input type="checkbox" id="welcome_adv_is_clock_checkbox">
                        </div>
                    </div>
                    <div class="row-fluid welcom-adv-clock-options" style="display:none">
                        <div class="span12">
                            <fieldset class="well well-small">
                                <legend>定时选项</legend>
                                <div class="row-fluid">
                                    <span class="label">定时类型</span>
                                    <select name="clock_type" class="input-medium">
                                        <option value="1" selected>一次</option>
                                        <option value="2">每年重复</option>
                                        <option value="3">每月重复</option>
                                        <option value="4">每天重复</option>
                                    </select>
                                </div>
                                <div id="welcome_adv_one_time_options" class="row-fluid">
                                    <div class="span6">
                                        <span class="label">开始时间</span>
                                        <input type="text" name="start_time" class="input-medium">
                                    </div>
                                    <div class="span6">
                                        <span class="label">结束时间</span>
                                        <input type="text" name="end_time" class="input-medium">
                                    </div>
                                </div>
                                <div id="welcome_adv_repeat_options" class="row-fluid" style="display:none">
                                    <div class="row-fluid">
                                        <div class="span3 welcome-adv-repeat-month">
                                            <select name="repeat_month" class="input-mini">
                                                <option value="01">1</option>
                                                <option value="02">2</option>
                                                <option value="03">3</option>
                                                <option value="04">4</option>
                                                <option value="05">5</option>
                                                <option value="06">6</option>
                                                <option value="07">7</option>
                                                <option value="08">8</option>
                                                <option value="09">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                            <span>月</span>    
                                        </div>
                                        <div class="span3 welcome-adv-repeat-day">
                                            <select name="repeat_day" class="input-mini">
                                                <option value="01">1</option>
                                                <option value="02">2</option>
                                                <option value="03">3</option>
                                                <option value="04">4</option>
                                                <option value="05">5</option>
                                                <option value="06">6</option>
                                                <option value="07">7</option>
                                                <option value="08">8</option>
                                                <option value="09">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                                <option value="24">24</option>
                                                <option value="25">25</option>
                                                <option value="26">26</option>
                                                <option value="27">27</option>
                                                <option value="28">28</option>
                                                <option value="29" class="month-n2-day">29</option>
                                                <option value="30" class="month-n2-day">30</option>
                                                <option value="31" class="month-big-day month-n2-day">31</option>
                                            </select>
                                            <span>日</span>    
                                        </div>
                                        <div class="span3 welcome-adv-repeat-hour">
                                            <select name="repeat_hour" class="input-mini">
                                                <option value="00">0</option>
                                                <option value="01">1</option>
                                                <option value="02">2</option>
                                                <option value="03">3</option>
                                                <option value="04">4</option>
                                                <option value="05">5</option>
                                                <option value="06">6</option>
                                                <option value="07">7</option>
                                                <option value="08">8</option>
                                                <option value="09">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                            </select>
                                            <span>时</span>    
                                        </div>
                                        <div class="span3 welcome-adv-repeat-minute">
                                            <select name="repeat_minute" class="input-mini">
                                                <option value="00">0</option>
                                                <option value="01">1</option>
                                                <option value="02">2</option>
                                                <option value="03">3</option>
                                                <option value="04">4</option>
                                                <option value="05">5</option>
                                                <option value="06">6</option>
                                                <option value="07">7</option>
                                                <option value="08">8</option>
                                                <option value="09">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                                <option value="13">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="23">23</option>
                                                <option value="24">24</option>
                                                <option value="25">25</option>
                                                <option value="26">26</option>
                                                <option value="27">27</option>
                                                <option value="28">28</option>
                                                <option value="29">29</option>
                                                <option value="30">30</option>
                                                <option value="31">31</option>
                                                <option value="32">32</option>
                                                <option value="33">33</option>
                                                <option value="34">34</option>
                                                <option value="35">35</option>
                                                <option value="36">36</option>
                                                <option value="37">37</option>
                                                <option value="38">38</option>
                                                <option value="39">39</option>
                                                <option value="40">40</option>
                                                <option value="41">41</option>
                                                <option value="42">42</option>
                                                <option value="43">43</option>
                                                <option value="44">44</option>
                                                <option value="45">45</option>
                                                <option value="46">46</option>
                                                <option value="47">47</option>
                                                <option value="48">48</option>
                                                <option value="49">49</option>
                                                <option value="50">50</option>
                                                <option value="51">51</option>
                                                <option value="52">52</option>
                                                <option value="53">53</option>
                                                <option value="54">54</option>
                                                <option value="55">55</option>
                                                <option value="56">56</option>
                                                <option value="57">57</option>
                                                <option value="58">58</option>
                                                <option value="59">59</option>
                                            </select>
                                            <span>分</span>
                                        </div>    
                                    </div>
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <span>持续</span>
                                            <input type="text" name="duration_days" class="input-mini">
                                            <span>天</span>
                                            <input type="text" name="duration_hours" class="input-mini">
                                            <span>小时</span>
                                            <input type="text" name="duration_minutes" class="input-mini">
                                            <span>分钟</span>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <span class="label">图片</span>
                            <input id="welcome_adv_pic_file" type="file" name="picf"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span3"></div>
                        <div class="span6">
                            <img id="welcome_adv_pic" src="" alt="" class="hidden-none"/>
                        </div>
                        <div class="span3"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){


        /**
         * 控件相关
         */

        var img_base_url = "{{ url('/welcomeAdvPic') }}";

        //日期事件控件
        var start_time_box = $('#welcome_adv_cu_form [name="start_time"]').datetimebox({
            editable: false
        });
        var end_time_box = $('#welcome_adv_cu_form [name="end_time"]').datetimebox({
            
        });

        //数字控件
        var duration_days_number_box = $('#welcome_adv_cu_form [name="duration_days"]').numberbox({
            min: 0,
            value: 0
        });
        var duration_hours_number_box = $('#welcome_adv_cu_form [name="duration_hours"]').numberbox({
            min: 0,
            value: 0
        });
        var duration_minutes_number_box = $('#welcome_adv_cu_form [name="duration_minutes"]').numberbox({
            min: 0,
            value: 0
        });

        var welcome_adv_grid = $('#welcome_adv_grid');

        //分页器
        var welcome_adv_pagination = $('#welcome_adv_pagination').pagination({
            pageSize: 5,
            pageList: [5, 10, 20],
            buttons: [
                {
                    text: '添加',
                    iconCls: 'icon-plus',
                    handler: function(event){
                        $('#welcome_adv_cu_form [name="id"]').val('');
                        $('#welcome_adv_cu_form [name="province_id"]').val('');
                        $('#welcome_adv_cu_form [name="url"]').val('');
                        $('#welcome_adv_cu_form [name="picf"]').val('');

                        //复位定时选项
                        $('#welcome_adv_is_clock_checkbox').prop('checked', false).change();
                        $('#welcome_adv_cu_form [name="repeat_month"]').val('01');
                        $('#welcome_adv_cu_form [name="repeat_day"]').val('01');
                        $('#welcome_adv_cu_form [name="repeat_hour"]').val('00');
                        $('#welcome_adv_cu_form [name="repeat_minute"]').val('00');

                        duration_days_number_box.numberbox('setValue', 0);
                        duration_hours_number_box.numberbox('setValue', 0);
                        duration_minutes_number_box.numberbox('setValue', 0);

                        $('#welcome_adv_pic').attr('src', '').hide();

                        welcome_adv_cu_dialog.data('state', 'create');
                        var opt = welcome_adv_cu_dialog.dialog('options');
                        opt.title = '添加开屏广告';
                        opt.iconCls = 'icon-plus';
                        opt.buttons[0].text = '添加';
                        welcome_adv_cu_dialog.dialog(opt).dialog('open').dialog('center');
                    }
                }
            ],
            onSelectPage: function(page_num, page_size){
                getAdvList(page_num, page_size, renderAdvGrid);
            }
        });



        //窗口
        var welcome_adv_cu_dialog = $('#welcome_adv_cu_window').dialog({
            title: '开屏广告添加',
            iconCls: 'icon-plus',
            width: '400',
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
                        var waiting = welcome_adv_cu_dialog.data('waiting');
                        if(waiting) return;

                        var win_state = welcome_adv_cu_dialog.data('state');

                        var province_id = $('#welcome_adv_cu_form [name="province_id"]').val();
                        var url = $('#welcome_adv_cu_form [name="url"]').val();
                        var picf = $('#welcome_adv_cu_form [name="picf"]').val();
                        var pic_data = null;

                        if(picf)
                        {
                            pic_data = $('#welcome_adv_pic').attr('src').match(/base64,(.*)/)[1];
                        }

                        var welcome_adv = new WelcomeAdv();

                        welcome_adv.set('province_id', province_id);
                        welcome_adv.set('url', url);
                        welcome_adv.set('pic_data', pic_data);

                        var is_clock = $('#welcome_adv_is_clock_checkbox').prop('checked');

                        if(is_clock)
                        {
                            $('.welcom-adv-clock-options [name]:visible').each(function(i, n){
                                var key = $(n).attr('name');
                                var value = $(n).val();
                                welcome_adv.set(key, value);
                            });

                            if(welcome_adv.get('clock_type') == 1)
                            {
                                welcome_adv.set('start_time', start_time_box.datetimebox('getValue'));
                                welcome_adv.set('end_time', end_time_box.datetimebox('getValue'));
                            }
                        }

                        if(win_state == 'create')
                        {
                            welcome_adv.create();
                        }
                        else if(win_state == 'update')
                        {
                            var id = $('#welcome_adv_cu_form [name="id"]').val();
                            welcome_adv.set('id', id);
                            welcome_adv.update();
                        }

                        WelcomeAdv.commit({complete: function(data){
                            welcome_adv_cu_dialog.data('waiting', false);
                        }});

                        welcome_adv_cu_dialog.data('waiting', true);
                        welcome_adv_cu_dialog.dialog('close');
                    }
                },
                {
                    text: '取消',
                    iconCls: 'icon-remove',
                    handler: function(){
                        welcome_adv_cu_dialog.dialog('close');
                    }
                }
            ]
        });

        /**
         * 事件相关
         */
        
        //定时复选框改变事件
        $('#welcome_adv_is_clock_checkbox').change(function(event){
            if( $(this).prop('checked') )
            {
                $('.welcom-adv-clock-options').show();
            }
            else
            {
                $('.welcom-adv-clock-options').hide();
            }
        });

        //定时类型改变事件
        $('#welcome_adv_cu_form [name="clock_type"]').change(function(event){
            var clock_type = $(this).val();
            if(clock_type == 1)
            {
                $('#welcome_adv_one_time_options').show();
                $('#welcome_adv_repeat_options').hide();

            }
            else
            {
                $('#welcome_adv_repeat_options').show();
                $('#welcome_adv_one_time_options').hide();
            }

            if(clock_type == 2)
            {
                $('.welcome-adv-repeat-month').show();
                $('.welcome-adv-repeat-day').show();
                $('.welcome-adv-repeat-hour').show();
                $('.welcome-adv-repeat-minute').show();
            }
            else if(clock_type == 3)
            {
                $('.welcome-adv-repeat-month').hide();
                $('.welcome-adv-repeat-day').show();
                $('.welcome-adv-repeat-hour').show();
                $('.welcome-adv-repeat-minute').show();
            }
            else if(clock_type == 4)
            {
                $('.welcome-adv-repeat-month').hide();
                $('.welcome-adv-repeat-day').hide();
                $('.welcome-adv-repeat-hour').show();
                $('.welcome-adv-repeat-minute').show();
            }
        });

        //月重复选择框改变事件
        $('#welcome_adv_cu_form [name="repeat_month"]').change(function(event){
            var month = $(this).val();

            if(month == '02')
            {
                $('.month-n2-day').hide();
            }
            else
            {
                $('.month-n2-day').show();    
            }

            if(['01', '03', '05', '07', '08', '10', '12'].indexOf(month) >= 0) //大月
            {
                $('.month-big-day').show();
            }
            else //小月
            {
                $('.month-big-day').hide();
            }

            //修正天选择框
            if( !$('#welcome_adv_cu_form [name="repeat_day"]').find('option:selected').is(':visible') )
            {
                $('#welcome_adv_cu_form [name="repeat_day"]').val('01');
            }
        });

        //图片选择器改变事件
        $('#welcome_adv_pic_file').change(function(event){
            var file = this.files[0];
            if(file.size > 512000)
            {
                $.messager.show({
                    title: '系统消息',
                    msg: '上传图片必须小于500Kb'
                });
                return;
            }
            var reader = new FileReader();
            reader.addEventListener('load', function(event){
                var img_src = event.target.result;
                $('#welcome_adv_pic').attr('src', img_src).fadeIn();
            });
            reader.readAsDataURL(file);
        });

        //开屏广告使用
        $(document).on('click', '.welcome-adv-enable-btn', function(event){
            var id = $(this).attr('data-id');
            $.ajax({
                url: '/welcomeAdvEnable/' + id + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '开屏广告成功使用'
                });
                welcome_adv_pagination.pagination('select');
            });
        });

        //开屏广告弃用
        $(document).on('click', '.welcome-adv-disable-btn', function(event){
            var id = $(this).attr('data-id');
            $.ajax({
                url: '/welcomeAdvDisable/' + id + '.json',
                method: 'PUT',
                dataType: 'json',
                global: true
            }).done(function(data){
                if(!data.success) return;
                $.messager.show({
                    title: '系统消息',
                    msg: '开屏广告成功弃用'
                });
                welcome_adv_pagination.pagination('select');
            });
        });

        //开屏广告编辑按钮点击事件
        $(document).on('click', '.welcome-adv-edit-btn', function(event){
            var index = $(this).attr('data-index');
            var adv = welcome_adv_grid.data('rows')[index];

            $('#welcome_adv_cu_form [name="id"]').val(adv.id);
            $('#welcome_adv_cu_form [name="province_id"]').val(adv.provinceId);
            $('#welcome_adv_cu_form [name="url"]').val(adv.url);
            $('#welcome_adv_cu_form [name="picf"]').val('');
            $('#welcome_adv_pic').attr('src', img_base_url + '/' + adv.id + '?' + Date.now()).show();

            if(adv.clockType)
            {
                $('#welcome_adv_is_clock_checkbox').prop('checked', true).change();
                $('#welcome_adv_cu_form [name="clock_type"]').val(adv.clockType).change();

                if(adv.clockType == 1)
                {
                    start_time_box.datetimebox('setValue', adv.startTime);
                    end_time_box.datetimebox('setValue', adv.endTime);
                }
                else 
                {
                    //填写重复时间
                    var datetime_parts = adv.repeatTime.split(' ');
                    var date_parts = null;
                    var time_parts = null;
                    var month_part = null;
                    var day_part = null;
                    var hour_part = null;
                    var minute_part = null;

                    if(adv.clockType == 2)
                    {
                        date_parts = datetime_parts[0].split('-');
                        time_parts = datetime_parts[1].split(':');
                        month_part = date_parts[0];
                        day_part = date_parts[1];
                        hour_part = time_parts[0];
                        minute_part = time_parts[1];
                    }
                    else if(adv.clockType == 3)
                    {
                        date_parts = datetime_parts[0].split('-');
                        time_parts = datetime_parts[1].split(':');
                        day_part = date_parts[0];
                        hour_part = time_parts[0];
                        minute_part = time_parts[1];
                    }
                    else if(adv.clockType == 4)
                    {
                        time_parts = datetime_parts[0].split(':');
                        hour_part = time_parts[0];
                        minute_part = time_parts[1];
                    }

                    $('#welcome_adv_cu_form [name=repeat_month]').val(month_part || '01');
                    $('#welcome_adv_cu_form [name=repeat_day]').val(day_part || '01');
                    $('#welcome_adv_cu_form [name=repeat_hour]').val(hour_part || '00');
                    $('#welcome_adv_cu_form [name=repeat_minute]').val(minute_part || '00');

                    //填写持续时间
                    var duration = adv.duration || 0;
                    var duration_days = Math.floor(duration / (24 * 60));
                    duration = duration % (24 * 60);

                    var duration_hours = Math.floor(duration / 60);
                    duration = duration % 60;

                    var duration_minutes = duration;
                    duration_days_number_box.numberbox('setValue', duration_days);
                    duration_hours_number_box.numberbox('setValue', duration_hours);
                    duration_minutes_number_box.numberbox('setValue', duration_minutes);
                }
            }
            else
            {
                $('#welcome_adv_is_clock_checkbox').prop('checked', false).change();   
            }

            welcome_adv_cu_dialog.data('state', 'update');
            var opt = welcome_adv_cu_dialog.dialog('options');
            opt.title = '编辑开屏广告';
            opt.iconCls = 'icon-edit';
            opt.buttons[0].text = '编辑';
            welcome_adv_cu_dialog.dialog(opt).dialog('open').dialog('center');
        });

        //开屏广告删除按钮点击事件
        $(document).on('click', '.welcome-adv-del-btn', function(event){
            var id = $(this).attr('data-id');
            $.messager.confirm('删除开屏广告', '该操作无法撤消,是否继续', function(is_ok){
                if(!is_ok) return;
                var adv = new WelcomeAdv();
                adv.set('id', id);
                adv.delete();
                WelcomeAdv.commit();
            });
        });

        /**
         * 数据相关
         */

        var WelcomeAdv = CarMate.Model.extend({
            __class_props__: {
                buildUrl: function(condition, action){

                    if(action == 'update' || action == 'create') return '/welcomeAdv.json';
                    if(action == 'delete')
                    {
                        if(condition instanceof Array)
                        {
                            var ids = [];
                            $.each(condition, function(i, c){
                                ids.push(c.id);
                            });
                            var ids_str = ids.join('-');
                            return '/welcomeAdv/' + ids_str + '.json';
                        }
                        else
                        {
                            return '/welcomeAdv/' + condition.id + '.json';
                        }
                    }
                }
            }
        });

        WelcomeAdv.on('created', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '开屏广告添加成功'
            });
            welcome_adv_pagination.pagination('select', 1);
        });

        WelcomeAdv.on('updated', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '开屏广告更新成功'
            });
            welcome_adv_pagination.pagination('select');
        });

        WelcomeAdv.on('deleted', function(event, data){
            if(!data.success) return;
            $.messager.show({
                title: '系统消息',
                msg: '开屏广告删除成功'
            });
            welcome_adv_pagination.pagination('select');
        });


        function getAdvList(page_num, page_size, callback)
        {
            $.ajax({
                url: '/welcomeAdvList.json',
                method: 'POST',
                data: {
                    page: page_num,
                    rows: page_size
                },
                dataType: 'json',
                global: true
            }).done(function(data){
                welcome_adv_pagination.pagination('loaded');
                var opt = welcome_adv_pagination.pagination('options');
                opt.total = data.total;
                welcome_adv_pagination.pagination(opt);
                welcome_adv_grid.data('rows', data.rows);
                callback.call(this, data);
            });
            welcome_adv_pagination.pagination('loading');
        }

        function renderAdvGrid(data)
        {
            var rows = data.rows;
            var total = data.total;
            var count = data.count;

            var len = rows.length;
            $('#welcome_adv_grid').empty();
            for(var i = 0; i < len; i++)
            {
                var row = rows[i];
                //var pic = row.pic;
                var src = img_base_url + '/' + row.id + '?' + Date.now();

                var tools_top = '';
                var tools_bottom = '';

                tools_top += '<i class="iconfa-trash welcome-adv-pic-tool welcome-adv-del-btn" data-id="' + row.id +'"></i>';

                tools_bottom += '<i>' + row.provinceName + '</i>';
                tools_bottom += '<i class="iconfa-edit welcome-adv-pic-tool welcome-adv-edit-btn" data-index="' + i + '"></i>';

                if(row.isState == 1)
                {
                    tools_bottom += '<i title="弃用" class="iconfa-star welcome-adv-pic-tool welcome-adv-disable-btn" data-id="' + row.id +'"></i>';
                }
                else
                {
                    tools_bottom += '<i title="使用" class="iconfa-star-empty welcome-adv-pic-tool welcome-adv-enable-btn" data-id="' + row.id +'"></i>'
                }


                if(row.clockType)
                {
                    var clock_type_des = null;
                    if(row.clockType == 1)
                    {
                        clock_type_des = row.startTime + '至' + (row.endTime || 'forever');
                    }
                    else if(row.clockType == 2)
                    {
                        clock_type_des = '每年' + row.repeatTime;
                    }
                    else if(row.clockType == 3)
                    {
                        clock_type_des = '每月' + row.repeatTime;
                    }
                    else if(row.clockType)
                    {
                        clock_type_des = '每天' + row.repeatTime;
                    }

                    var duration_des = '';
                    if(row.clockType > 1 && row.duration)
                    {
                        var duration = row.duration || 0;
                        var duration_days = Math.floor(duration / (24 * 60));
                        duration = duration % (24 * 60);
                        if(duration_days)
                        {
                            duration_des += duration_days + '天';
                        }

                        var duration_hours = Math.floor(duration / 60);
                        duration = duration % 60;
                        if(duration_hours)
                        {
                            duration_des += duration_hours + '小时';
                        }

                        if(duration)
                        {
                            duration_des += duration + '分钟';
                        }

                        if(duration_des)
                        {
                            duration_des = '持续' + duration_des;
                        }
                    }

                    tools_bottom += '<i title="' + clock_type_des + duration_des +'" class="iconfa-time welcome-adv-pic-tool" data-id="' + row.id + '">';
                }

                $('#welcome_adv_grid').append(
                    '<div class="welcome-adv-pic-container well well-small">' +
                        '<div class="welcome-adv-pic-toolbar-top">' +
                            tools_top +
                        '</div>' +
                        '<img src="' + src + '" />' +
                        '<div class="welcome-adv-pic-toolbar-bottom">' +
                            tools_bottom +
                        '</div>' +
                    '</div>'
                );

            }
        }

        //初始化广告列表
        getAdvList(1, 5, renderAdvGrid);

        /**
         * 页面离开时事件,用于清理资源
         */
        this.on_leave = function(){

            //销毁窗口
            welcome_adv_cu_dialog.dialog('destroy');

            //清除动态绑定事件
            $(document).off('click', '.welcome-adv-enable-btn');
            $(document).off('click', '.welcome-adv-disable-btn');
            $(document).off('click', '.welcome-adv-edit-btn');
            $(document).off('click', '.welcome-adv-del-btn');
        };

    };
</script>


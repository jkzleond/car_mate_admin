<style type="text/css">
    .easyui-datebox {
        width:120px;
    }
</style>

<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">查询统计-时间段</h4>
        <div class="widgetcontent nopadding">
            <form id="act_statistics_form" class="form-inline well" >

                <fieldset class="pull-left span5">

                    <span class="label">时间</span>
                    <input type="text" class="easyui-datebox" name="start_date" value="{{ default_start_time }}"/>
                    <span>至</span>
                    <input type="text" class="easyui-datebox" name="end_date" value="{{ default_end_time }}" />
                </fieldset>
                <fieldset class="pull-left span2">
                    <span class="label">分组方式</span>
                    <input type="radio" name="group_type" value="hours" /><span>按时间点</span>
                    <input type="radio" name="group_type" value="days" checked /><span>按天</span>
                    <input type="radio" name="group_type" value="months" /><span>按月</span>
                </fieldset>
                <fieldset class="pull-left span2">
                    <span class="label">统计内容</span>
                    <select name="statistics_type" style="width: 100px;" id="statistics_type">
                        <option value="All">全部</option>
                        <option value="Member">用户操作</option>
                        <option value="FXCQuery">违章查询</option>
                        <option value="LocalFavour">本地惠</option>
                        <option value="NewIndex">新首页</option>
                        <option value="Insurance">保险系统</option>
                        <option value="Interaction">路况</option>
                        <option value="Talk">车友互动</option>
                        <option value="Location">周边信息</option>
                        <option value="Friend">好友</option>
                        <option value="HotList">排行榜</option>
                        <option value="Activity">活动</option>
                    </select>
                </fieldset>
                <fieldset class="pull-left span2">
                    <span class="label">图形</span>
                    <select id="chart_type" name="chart_type" class="span8">
                        <option value="spline">线状图</option>
                        <option value="column">柱状图</option>
                    </select>
                </fieldset>
                <button id="finder_btn" class="btn btn-primary pull-right span1">确定</button>
            </form>
            <div class="charts">
                <div id="charts_container"></div>
            </div>
            <table width="100%" >
                <tr>
                    <td width="39%">
                        <div id="pie_charts_container"></div>
                    </td>
                    <td width="60%">
                        <div style="padding:10px;">
                            <table id="act_total_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                                <thead>

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table id="act_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                            <thead>

                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function($){

        /**
         * 控件相关
         */
        //定义日期控件
        var start_date_box = $('[name=start_date].easyui-datebox').datebox({
            required: true
        });

        var end_date_box = $('[name=end_date].easyui-datebox').datebox({
            required: true
        });

        $('.datebox input').keydown(function(event){
            event.preventDefault();
            return false;
        });

        //定义charts控件
        var options = {
            chart: {
                type: $('#chart_type').val(),
                height: 300,
                backgroundColor: 'none',
                zoomType: 'xy'
            },
            title: {
                text: '操作统计'
            },
            subtitle: {
                text: '按天'
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                title: {
                    text: '操作数'
                },
                labels: {
                    formatter: function () {
                        return Math.floor(this.value/1000) + 'k';
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: []
        };

        $('#charts_container').highcharts(options);

        var charts = $('#charts_container').highcharts();

        //定义pie_charts控件,用于显示访问总量统计

        var pie_options = {
            chart: {
                width:400,
                height:200,
                backgroundColor: 'none',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: ''
            },
            tooltip: {
                //pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                useHTML: true,
                formatter: function(){
                    return '<b>'+ this.point.name +'</b></br> '+'操作:'+this.point.y + "<br/>" + "百分比:" +this.percentage.toFixed(2) +' %';
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: []
        };
        $('#pie_charts_container').highcharts(pie_options);
        var pie_charts = $('#pie_charts_container').highcharts();//查询到统计数据后的响应


        //查询按钮事件

        var act_xhr = null;
        var act_total_xhr = null;

        $("#finder_btn").click(function(event){
            event.preventDefault();
            var condition = {};
            condition.start_date = start_date_box.datebox('getValue');
            condition.end_date = end_date_box.datebox('getValue');
            condition.group_type = $('#query_statistics_form [name=group_type]:checked').val();
            condition.statistics_type = $('#statistics_type').val();
            //先取消正在进行的ajax
            if(act_xhr) act_xhr.abort();
            if(act_total_xhr) act_total_xhr.abort();
            act_xhr = ActStatistics.find(condition);
            act_total_xhr = ActTotalStatistics.find(condition);
            return false;
        });

        //图表类型选择框事件
        $('#chart_type').change(function(event){
            var old_options = charts.options;
            old_options.chart.type = $(this).val();
            //销毁旧的charts
            charts.destroy();
            //用修改过的options创建新的charts
            charts = new Highcharts.Chart(old_options);
        });

        /**
         * 数据相关
         */

        //定义ActStatistics(访问)模型类
        var ActStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/actStatistics',
                buildUrl: function(condition){
                    if(!condition)
                    {
                        return this.baseUrl;
                    }
                    else
                    {
                        var start_date = condition.start_date || '';
                        var end_date = condition.end_date || '';
                        var group_type = condition.group_type || 'days';
                        var statistics_type = condition.statistics_type || 'All';
                        return this.baseUrl + '/' + start_date + '/' + end_date + '/' + group_type + '/' + statistics_type + '.json';
                    }
                }
            }
        });

        //查询到统计数据后的响应
        ActStatistics.on('found',function(event, models){

            var data = ActStatistics.raw_data.rows;
            var statistics_type = $('#statistics_type').val();
            switch(statistics_type)
            {
                case 'Member':
                    renderMemberData(data);
                    break;
                case 'FXCQuery':
                    renderFXCQueryData(data);
                    break;
                case 'LocalFavour':
                    renderLocalFavourData(data);
                    break;
                case 'Interaction':
                    renderInteractionData(data);
                    break;
                case 'Talk':
                    renderTalkData(data);
                    break;
                case 'Location':
                    renderLocationData(data);
                    break;
                case 'Insurance':
                    renderInsuranceData(data);
                    break;
                case 'NewIndex':
                    renderNewIndexData(data);
                    break;
                case 'Friend':
                    renderFriendData(data);
                    break;
                case 'HotList':
                    renderHotListData(data);
                    break;
                case 'Activity':
                    renderActivityData(data);
                    break;
                default:
                    renderAllData(data);
                    break;
            }
        });

        //未查询到数据的响应
        ActStatistics.on('notFound',function(){
            charts.hideLoading();
        });

        //查询数据之前
        ActStatistics.on('beforeFind', function(){
            charts.showLoading();
        });

        //定义ActTotalStatistics模型
        var ActTotalStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/actTotalStatistics',
                buildUrl: function(condition){
                    if(!condition)
                    {
                        return this.baseUrl;
                    }
                    else
                    {
                        var start_date = condition.start_date || '';
                        var end_date = condition.end_date || '';
                        var statistics_type = condition.statistics_type || 'all';
                        return this.baseUrl + '/' + start_date + '/' + end_date + '/' + statistics_type + '.json';
                    }
                }
            }
        });

        //查询到访问总量统计数据后的响应
        ActTotalStatistics.on('found',function(event, models){
            var data = ActTotalStatistics.raw_data.rows;
            var statistics_type = $('#statistics_type').val();

            switch(statistics_type)
            {
                case 'Member':
                    renderMemberTotalData(data);
                    break;
                case 'FXCQuery':
                    renderFXCQueryTotalData(data);
                    break;
                case 'LocalFavour':
                    renderLocalFavourTotalData(data);
                    break;
                case 'Interaction':
                    renderInteractionTotalData(data);
                    break;
                case 'Talk':
                    renderTalkTotalData(data);
                    break;
                case 'Location':
                    renderLocationTotalData(data);
                    break;
                case 'Insurance':
                    renderInsuranceTotalData(data);
                    break;
                case 'NewIndex':
                    renderNewIndexTotalData(data);
                    break;
                case 'Friend':
                    renderFriendTotalData(data);
                    break;
                case 'HotList':
                    renderHotListTotalData(data);
                    break;
                case 'Activity':
                    renderActivityTotalData(data);
                    break;
                default:
                    renderAllTotalData(data);
                    break;
            }

        });
        //未查询到数据的响应
        ActTotalStatistics.on('notFound',function(){
            pie_charts.hideLoading();
        });
        //查询数据之前
        ActTotalStatistics.on('beforeFind', function(){
            pie_charts.showLoading();
        });


        //初始加载数据
        var condition = {};
        condition.start_date = start_date_box.datebox('getValue');
        condition.end_date = end_date_box.datebox('getValue');
        condition.group_type = $('#query_statistics_form [name=group_type]:checked').val();            act_xhr = ActStatistics.find(condition);
        act_total_xhr = ActTotalStatistics.find(condition);


        //离开(切换)页面的回调,离开时取消正在进行ajax
        CarMate.page.on_leave = function(){
            act_xhr.abort();
            act_total_xhr.abort();
        };

        //渲染数据相关函数

        function renderMemberData(models)
        {
            var len = models.length;
            var categories = [];
            var register_data = [];
            var login_data = [];
            var feedback_data = [];
            var iosversion_data = [];
            var password_data = [];
            var userinfo_data = [];
            var update_push_data = [];
            var get_push_data = [];
            var hphm_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>注册</th>' +
                '<th>登录</th>' +
                '<th>获取版本</th>' +
                '<th>用户反馈</th>' +
                '<th>修改个人信息</th>' +
                '<th>密码相关操作</th>' +
                '<th>更新推送</th>' +
                '<th>获取推送</th>' +
                '<th>号牌获取用户</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                register_data.push(Number(model.register));
                login_data.push(Number(model.login));
                feedback_data.push(Number(model.feedback));
                iosversion_data.push(Number(model.iosversion));
                password_data.push(Number(model.password));
                userinfo_data.push(Number(model.userinfo));
                update_push_data.push(Number(model.updatePush));
                get_push_data.push(Number(model.getPush));
                hphm_data.push(Number(model.hphm));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.register+"</td>" +
                    "<td>"+model.login+"</td>" +
                    "<td>"+model.iosversion+"</td>" +
                    "<td>"+model.feedback+"</td>" +
                    "<td>"+model.userinfo+"</td>" +
                    "<td>"+model.password+"</td>" +
                    "<td>"+ model.updatePush +"</td>" +
                    "<td>"+ model.getPush +"</td>" +
                    "<td>"+ model.hphm +"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '注册', data: register_data},
                {name: '登录', data: login_data},
                {name: '获取版本', data: iosversion_data},
                {name: '用户反馈', data: feedback_data},
                {name: '修改个人信息', data: userinfo_data},
                {name: '密码相关操作', data: password_data},
                {name: '更新推送', data: update_push_data},
                {name: '获取推送', data: get_push_data},
                {name: '号牌获取用户', data: hphm_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderFXCQueryData(models){
            var len = models.length;
            var categories = [];
            var wflist_data = [];
            var addcar_data = [];
            var getcheckcode_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>违章查询</th>' +
                '<th>同步车辆信息</th>' +
                '<th>获取验证码</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                wflist_data.push(Number(model.wflist));
                addcar_data.push(Number(model.addcar));
                getcheckcode_data.push(Number(model.getcheckcode));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.wflist+"</td>" +
                    "<td>"+model.addcar+"</td>" +
                    "<td>"+model.getcheckcode+"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '违法查询', data: wflist_data},
                {name: '同步车辆信息', data: addcar_data},
                {name: '获取验证码', data: getcheckcode_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderLocalFavourData(models){
            var len = models.length;
            var categories = [];
            var comment_data = [];
            var list_data = [];
            var locallist_data = [];
            var content_data = [];
            var detail_data = [];
            var add_favor_data = [];
            var visit_index_data = [];
            var save_exchange_data = [];
            var item_content_data = [];
            var item_exchange_data = [];
            var item_list_data = [];
            var item_search_data = [];
            var user_transaction_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                    '<th>日期</th>' +
                    '<th>获取评论列表</th>' +
                    '<th>本地惠列表查询</th>' +
                    '<th>获取本地惠列表</th>' +
                    '<th>本地惠详细查询</th>' +
                    '<th>本地惠详细</th>' +
                    '<th>本地惠投稿</th>' +
                    '<th>本地惠首页</th>' +
                    '<th>保存商品兑换</th>' +
                    '<th>商品详情</th>' +
                    '<th>商品兑换</th>' +
                    '<th>访问商城</th>' +
                    '<th>查询商品</th>' +
                    '<th>交易详情</th>' +
                    '<th>总操作</th>' +
                    '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                comment_data.push(Number(model.comment));
                list_data.push(Number(model.list));
                locallist_data.push(Number(model.locallist));
                content_data.push(Number(model.content));
                detail_data.push(Number(model.detail));
                add_favor_data.push(Number(model.addFavour));
                visit_index_data.push(Number(model.visitIndex));
                save_exchange_data.push(Number(model.saveExchange));
                item_content_data.push(Number(model.itemContent));
                item_exchange_data.push(Number(model.itemExchange));
                item_list_data.push(Number(model.itemList));
                item_search_data.push(Number(model.itemSearch));
                user_transaction_data.push(Number(model.userTransaction));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.comment+"</td>" +
                    "<td>"+model.list+"</td>" +
                    "<td>"+model.locallist+"</td>" +
                    "<td>"+model.content+"</td>" +
                    "<td>"+model.detail+"</td>" +
                    "<td>"+model.addFavour+"</td>" +
                    "<td>"+ model.visitIndex +"</td>" +
                    "<td>"+ model.saveExchange +"</td>" +
                    "<td>"+ model.itemContent +"</td>" +
                    "<td>"+ model.itemExchange +"</td>" +
                    "<td>"+ model.itemList +"</td>" +
                    "<td>"+ model.itemSearch +"</td>" +
                    "<td>"+ model.userTransaction +"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '获取评论列表', data: comment_data},
                {name: '本地惠列表查询', data: list_data},
                {name: '获取本地惠列表', data: locallist_data},
                {name: '本地惠详细查询', data: content_data},
                {name: '本地惠详细', data: detail_data},
                {name: '本地惠投稿', data: add_favor_data},
                {name: '本地惠首页', data: visit_index_data},
                {name: '保存商品兑换', data: save_exchange_data},
                {name: '商品详情', data: item_content_data},
                {name: '商品兑换', data: item_exchange_data},
                {name: '商城首页', data: item_list_data},
                {name: '查询商品', data: item_search_data},
                {name: '交易详情', data: user_transaction_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderInteractionData(models){
            var len = models.length;
            var categories = [];
            var add_comment_data = [];
            var get_list_data = [];
            var add_data = [];
            var search_data = [];
            var comment_list_data = [];
            var traffic_log_write_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>添加评论</th>' +
                '<th>微博路况列表</th>' +
                '<th>添加话题</th>' +
                '<th>话题公告搜索</th>' +
                '<th>互动评论列表</th>' +
                '<th>实时路况</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                add_comment_data.push(Number(model.addcomment));
                get_list_data.push(Number(model.getlist));
                add_data.push(Number(model.add));
                search_data.push(Number(model.search));
                comment_list_data.push(Number(model.commentlist));
                traffic_log_write_data.push(Number(model.TrafficLogWrite));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.addcomment+"</td>" +
                    "<td>"+model.getlist+"</td>" +
                    "<td>"+model.add+"</td>" +
                    "<td>"+model.search+"</td>" +
                    "<td>"+model.commentlist+"</td>" +
                    "<td>"+model.TrafficLogWrite+"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '添加评论', data: add_comment_data},
                {name: '微博路况列表', data: get_list_data},
                {name: '添加话题', data: add_data},
                {name: '话题公告搜索', data: search_data},
                {name: '互动评论列表', data: comment_list_data},
                {name: '实时路况', data: traffic_log_write_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderTalkData(models){
            var len = models.length;
            var categories = [];
            var add_talk_data = [];
            var get_file_stream_data = [];
            var get_talk_replay_data = [];
            var add_talk_replay_data = [];
            var get_talk_data = [];
            var detail_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>添加话题</th>' +
                '<th>添加声音图片</th>' +
                '<th>获取话题回复</th>' +
                '<th>添加话题回复</th>' +
                '<th>获取话题列表</th>' +
                '<th>车友互动详细</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                add_talk_data.push(Number(model.addtalk));
                add_talk_replay_data.push(Number(model.addtalkreply));
                get_file_stream_data.push(Number(model.getfilestream));
                get_talk_replay_data.push(Number(model.gettalkreply));
                get_talk_data.push(Number(model.gettalk));
                detail_data.push(Number(model.detail));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.addtalk+"</td>" +
                    "<td>"+model.getfilestream+"</td>" +
                    "<td>"+model.gettalkreply+"</td>" +
                    "<td>"+model.addtalkreply+"</td>" +
                    "<td>"+model.gettalk+"</td>" +
                    "<td>"+model.detail+"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '添加话题', data: add_talk_data},
                {name: '添加声音图片', data: get_file_stream_data},
                {name: '获取话题回复', data: get_talk_replay_data},
                {name: '添加话题回复', data: add_talk_replay_data},
                {name: '获取话题列表', data: get_talk_data},
                {name: '车优惠互动详细', data: detail_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderLocationData(models){
            var len = models.length;
            var categories = [];
            var get_list_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>获取周边坐标</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                get_list_data.push(Number(model.getlist));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.getlist+"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '获取周边坐标', data: get_list_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderInsuranceData(models){
            var len = models.length;
            var categories = [];
            var home_data = [];
            var param_data = [];
            var exact_data = [];
            var result_data = [];
            var history_data = [];
            var buy_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>保险首页</th>' +
                '<th>添加保险信息</th>' +
                '<th>提交精算请求</th>' +
                '<th>查看详细表单</th>' +
                '<th>历史保单列表</th>' +
                '<th>购买保险</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                home_data.push(Number(model.home));
                param_data.push(Number(model.param));
                exact_data.push(Number(model.exact));
                result_data.push(Number(model.result));
                history_data.push(Number(model.history));
                buy_data.push(Number(model.buy));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.home+"</td>" +
                    "<td>"+model.param+"</td>" +
                    "<td>"+model.exact+"</td>" +
                    "<td>"+model.result+"</td>" +
                    "<td>"+model.history+"</td>" +
                    "<td>"+model.buy+"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '保险首页', data: home_data},
                {name: '添加保险信息', data: param_data},
                {name: '提交精算请求', data: exact_data},
                {name: '查看详细保单', data: result_data},
                {name: '历史保单列表', data: history_data},
                {name: '购买保险', data: buy_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderNewIndexData(models){
            var len = models.length;
            var categories = [];
            var get_index_data = [];
            var get_weather_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>首页3.0</th>' +
                '<th>天气油价</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                get_index_data.push(Number(model.getIndex));
                get_weather_data.push(Number(model.getWeather));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.getIndex+"</td>" +
                    "<td>"+model.getWeather+"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '首页3.0', data: get_index_data},
                {name: '天气油价', data: get_weather_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderFriendData(models){
            var len = models.length;
            var categories = [];
            var addu_data = [];
            var list_data = [];
            var del_data = [];
            var add_black_data = [];
            var black_user_data = [];
            var del_black_data = [];
            var verify_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>添加好友</th>' +
                '<th>好友列表</th>' +
                '<th>删除好友</th>' +
                '<th>添加黑名单</th>' +
                '<th>黑名单列表</th>' +
                '<th>删除黑名单</th>' +
                '<th>好友验证</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                addu_data.push(Number(model.addu));
                list_data.push(Number(model.list));
                del_data.push(Number(model.del));
                add_black_data.push(Number(model.addBlack));
                black_user_data.push(Number(model.blackUser));
                del_black_data.push(Number(model.delBlack));
                verify_data.push(Number(model.verify));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.addu+"</td>" +
                    "<td>"+model.list+"</td>" +
                    "<td>"+model.del+"</td>" +
                    "<td>"+model.addBlack+"</td>" +
                    "<td>"+model.blackUser+"</td>" +
                    "<td>"+model.delBlack+"</td>" +
                    "<td>"+ model.verify +"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '添加好友', data: addu_data},
                {name: '好友列表', data: list_data},
                {name: '删除好友', data: del_data},
                {name: '添加黑名单', data: add_black_data},
                {name: '黑名单列表', data: black_user_data},
                {name: '删除黑名单', data: del_black_data},
                {name: '好友验证', data: verify_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderHotListData(models){
            var len = models.length;
            var categories = [];
            var hot_type_data = [];
            var get_hot_detail_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>排行版类型</th>' +
                '<th>排行版详细</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                hot_type_data.push(Number(model.hotType));
                get_hot_detail_data.push(Number(model.getHotDetail));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.hotType+"</td>" +
                    "<td>"+model.getHotDetail+"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '排行版类型', data: hot_type_data},
                {name: '排行版详细', data: get_hot_detail_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderActivityData(models){
            var len = models.length;
            var categories = [];
            var save_gain_data = [];
            var save_user_data = [];
            var user_info_data = [];
            var vote_info_data = [];
            var random_award_data = [];
            var check_in_data = [];
            var save_vote_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>保存抽奖结果</th>' +
                '<th>保存用户信息</th>' +
                '<th>获取投票信息</th>' +
                '<th>获取用户信息</th>' +
                '<th>随机抽奖结果</th>' +
                '<th>用户签到</th>' +
                '<th>保存投票</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                save_gain_data.push(Number(model.savGain));
                save_user_data.push(Number(model.saveUser));
                user_info_data.push(Number(model.userInfo));
                vote_info_data.push(Number(model.voteInfo));
                random_award_data.push(Number(model.randomAward));
                check_in_data.push(Number(model.checkIn));
                save_vote_data.push(Number(model.saveVote));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.saveGain+"</td>" +
                    "<td>"+model.saveUser+"</td>" +
                    "<td>"+model.voteInfo+"</td>" +
                    "<td>"+model.userInfo+"</td>" +
                    "<td>"+model.randomAward+"</td>" +
                    "<td>"+model.checkIn+"</td>" +
                    "<td>"+ model.saveVote +"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '保存用户信息', data: save_user_data},
                {name: '获取投票信息', data: user_info_data},
                {name: '获取用户信息', data: vote_info_data},
                {name: '随机抽奖结果', data: random_award_data},
                {name: '用户签到', data: check_in_data},
                {name: '保存投票', data: save_vote_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }

        function renderAllData(models){
            var len = models.length;
            var categories = [];
            var member_data = [];
            var fxc_query_data = [];
            var local_favor_data = [];
            var new_index_data = [];
            var insurance_data = [];
            var interaction_data = [];
            var talk_data = [];
            var location_data = [];
            var friend_data = [];
            var other_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //渲染thead
            $("#act_table thead").empty().append(
                '<tr>' +
                '<th>日期</th>' +
                '<th>用户操作</th>' +
                '<th>违章查询</th>' +
                '<th>本地惠</th>' +
                '<th>新首页</th>' +
                '<th>保险</th>' +
                '<th>路况</th>' +
                '<th>车友互动</th>' +
                '<th>周边信息</th>' +
                '<th>好友</th>' +
                '<th>其他</th>' +
                '<th>总操作</th>' +
                '<th>操作环比</th>' +
                '</tr>'
            );

            //插入新数据前先把旧数据清空
            $("#act_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                member_data.push(Number(model.member));
                fxc_query_data.push(Number(model.fxcQuery));
                local_favor_data.push(Number(model.localFavour));
                new_index_data.push(Number(model.newIndex));
                insurance_data.push(Number(model.insurance));
                interaction_data.push(Number(model.interaction));
                talk_data.push(Number(model.talk));
                location_data.push(Number(model.location));
                friend_data.push(Number(model.friend));
                other_data.push(Number(model.other));
                sum_data.push(Number(model.sum));
                //填充数据表格
                var sum = model.sum;
                if(i === 0){
                    pre_sum = sum/2;
                }
                var growth_rate;
                if(pre_sum === 0&&sum != 0){
                    pre_sum = sum/2;
                }
                if(pre_sum === 0 && sum === 0){
                    growth_rate = "0.00%";
                }else{
                    growth_rate = (((sum/pre_sum) - 1)*100).toFixed(2)+'%';
                }
                pre_sum = sum;

                $("#act_table tbody").append(
                    "<tr>" +
                    "<td>"+model.date+"</td>" +
                    "<td>"+model.member+"</td>" +
                    "<td>"+model.fxcQuery+"</td>" +
                    "<td>"+model.localFavour+"</td>" +
                    "<td>"+model.newIndex+"</td>" +
                    "<td>"+model.insurance+"</td>" +
                    "<td>"+model.interaction+"</td>" +
                    "<td>"+ model.talk +"</td>" +
                    "<td>"+ model.location +"</td>" +
                    "<td>"+ model.friend +"</td>" +
                    "<td>"+ model.other +"</td>" +
                    "<td>"+ model.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '用户操作', data: member_data},
                {name: '违章查询', data: fxc_query_data},
                {name: '本地惠', data: local_favor_data},
                {name: '新首页', data: new_index_data},
                {name: '保险', data: insurance_data},
                {name: '路况', data: interaction_data},
                {name: '车友互动', data: talk_data},
                {name: '周边信息', data: location_data},
                {name: '好友', data: friend_data},
                {name: '其他', data: other_data},
                {name: '总操作', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.title.text = $('#query_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();
        }


        //渲染总数

        function renderMemberTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['注册', Number(model.register)]);
            series_data.push(['登录', Number(model.login)]);
            series_data.push(['获取版本', Number(model.iosversion)]);
            series_data.push(['用户反馈', Number(model.feedback)]);
            series_data.push(['修改个人信息', Number(model.userinfo)]);
            series_data.push(['密码相关操作', Number(model.password)]);
            series_data.push(['更新推送', Number(model.updatePush)]);
            series_data.push(['获取推送', Number(model.getPush)]);
            series_data.push(['号牌获取用户', Number(model.hphm)]);


            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>注册</th>' +
                '<th>登录</th>' +
                '<th>获取版本</th>' +
                '<th>用户反馈</th>' +
                '<th>修改个人信息</th>' +
                '<th>密码相关操作</th>' +
                '<th>更新推送</th>' +
                '<th>获取推送</th>' +
                '<th>号牌获取用户</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.register+"</td>" +
                "<td>"+model.login+"</td>" +
                "<td>"+model.iosversion+"</td>" +
                "<td>"+model.feedback+"</td>" +
                "<td>"+model.userinfo+"</td>" +
                "<td>"+model.password+"</td>" +
                "<td>"+model.updatePush+"</td>" +
                "<td>"+model.getPush+"</td>" +
                "<td>"+model.hphm+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderFXCQueryTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];

            series_data.push(['违章查询', Number(model.wflist)]);
            series_data.push(['同步车辆信息', Number(model.addcar)]);
            series_data.push(['获取验证码', Number(model.getcheckcode)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                    '<th>违章查询</th>' +
                    '<th>同步车辆信息</th>' +
                    '<th>获取验证码</th>' +
                    '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.wflist+"</td>" +
                "<td>"+model.addcar+"</td>" +
                "<td>"+model.getcheckcode+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderLocalFavourTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['获取评论列表', Number(model.comment)]);
            series_data.push(['本地惠列表查询', Number(model.list)]);
            series_data.push(['获取本地惠列表', Number(model.locallist)]);
            series_data.push(['本地惠详细查询', Number(model.content)]);
            series_data.push(['本地惠详细', Number(model.detail)]);
            series_data.push(['本地惠投稿', Number(model.addFavour)]);
            series_data.push(['本地惠首页', Number(model.visitIndex)]);
            series_data.push(['保存商品兑换', Number(model.saveExchange)]);
            series_data.push(['商品详情', Number(model.itemContent)]);
            series_data.push(['商品兑换', Number(model.itemExchange)]);
            series_data.push(['商城首页', Number(model.itemList)]);
            series_data.push(['查询商品', Number(model.itemSearch)]);
            series_data.push(['交易详情', Number(model.userTransaction)]);


            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];


            console.log(new_options);

            pie_charts.hideLoading();
            //pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>获取评论列表</th>' +
                '<th>本地惠列表查询</th>' +
                '<th>获取本地惠列表</th>' +
                '<th>本地惠详细查询</th>' +
                '<th>本地惠详细</th>' +
                '<th>本地惠投稿</th>' +
                '<th>本地惠首页</th>' +
                '<th>保存商品兑换</th>' +
                '<th>商品详情</th>' +
                '<th>商品兑换</th>' +
                '<th>商城首页</th>' +
                '<th>查询商品</th>' +
                '<th>交易详情</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.comment+"</td>" +
                "<td>"+model.list+"</td>" +
                "<td>"+model.locallist+"</td>" +
                "<td>"+model.content+"</td>" +
                "<td>"+model.detail+"</td>" +
                "<td>"+model.addFavour+"</td>" +
                "<td>"+model.visitIndex+"</td>" +
                "<td>"+model.saveExchange+"</td>" +
                "<td>"+model.itemContent+"</td>" +
                "<td>"+model.itemList+"</td>" +
                "<td>"+model.itemSearch+"</td>" +
                "<td>"+model.userTransaction+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderInteractionTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['添加评论', Number(model.addcomment)]);
            series_data.push(['微博路况列表', Number(model.getlist)]);
            series_data.push(['添加话题', Number(model.add)]);
            series_data.push(['话题公告搜索', Number(model.search)]);
            series_data.push(['互动评论列表', Number(model.commentlist)]);
            series_data.push(['实时路况', Number(model.TrafficLogWrite)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>添加评论</th>' +
                '<th>微博路宽列表</th>' +
                '<th>添加话题</th>' +
                '<th>话题公告搜索</th>' +
                '<th>互动评论列表</th>' +
                '<th>实时路况</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.addcomment+"</td>" +
                "<td>"+model.getlist+"</td>" +
                "<td>"+model.add+"</td>" +
                "<td>"+model.search+"</td>" +
                "<td>"+model.commentlist+"</td>" +
                "<td>"+model.TrafficLogWrite+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderTalkTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['添加话题', Number(model.addtalk)]);
            series_data.push(['添加声音图片', Number(model.getfilestream)]);
            series_data.push(['获取话题回复', Number(model.gettalkreply)]);
            series_data.push(['添加话题回复', Number(model.addtalkreply)]);
            series_data.push(['获取话题列表', Number(model.gettalk)]);
            series_data.push(['车友互动详细', Number(model.detail)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>添加话题</th>' +
                '<th>添加声音图片</th>' +
                '<th>获取话题回复</th>' +
                '<th>添加话题回复</th>' +
                '<th>获取话题列表</th>' +
                '<th>车友互动详细</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.addtalk+"</td>" +
                "<td>"+model.getfilestream+"</td>" +
                "<td>"+model.gettalkreply+"</td>" +
                "<td>"+model.addtalkreply+"</td>" +
                "<td>"+model.gettalk+"</td>" +
                "<td>"+model.detail+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderLocationTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['获取周边坐标', Number(model.getlist)]);


            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>获取周边坐标</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.getlist+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderInsuranceTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['保险首页', Number(model.home)]);
            series_data.push(['添加保险信息', Number(model.param)]);
            series_data.push(['提交精算请求', Number(model.exact)]);
            series_data.push(['查看详细保单', Number(model.result)]);
            series_data.push(['历史保单列表', Number(model.history)]);
            series_data.push(['购买保险', Number(model.buy)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>保险首页</th>' +
                '<th>添加保险信息</th>' +
                '<th>提交精算请求</th>' +
                '<th>查看详细保单</th>' +
                '<th>历史保单列表</th>' +
                '<th>购买保险</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.home+"</td>" +
                "<td>"+model.param+"</td>" +
                "<td>"+model.exact+"</td>" +
                "<td>"+model.result+"</td>" +
                "<td>"+model.history+"</td>" +
                "<td>"+model.buy+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderNewIndexTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['首页3.0', Number(model.getIndex)]);
            series_data.push(['天气油价', Number(model.getWeather)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>首页3.0</th>' +
                '<th>天气油价</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.getIndex+"</td>" +
                "<td>"+model.getWeather+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderFriendTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['添加好友', Number(model.addu)]);
            series_data.push(['好友列表', Number(model.list)]);
            series_data.push(['删除好友', Number(model.del)]);
            series_data.push(['添加黑名单', Number(model.addBlack)]);
            series_data.push(['黑名单列表', Number(model.blackUser)]);
            series_data.push(['删除黑名单', Number(model.delBlack)]);
            series_data.push(['好友验证', Number(model.verify)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>添加好友</th>' +
                '<th>好友列表</th>' +
                '<th>删除好友</th>' +
                '<th>添加黑名单</th>' +
                '<th>黑名单列表</th>' +
                '<th>删除黑名单</th>' +
                '<th>好友验证</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.addu+"</td>" +
                "<td>"+model.list+"</td>" +
                "<td>"+model.del+"</td>" +
                "<td>"+model.addBlack+"</td>" +
                "<td>"+model.blackUser+"</td>" +
                "<td>"+model.delBlack+"</td>" +
                "<td>"+model.verify+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderHotListTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['排行版类型', Number(model.hotType)]);
            series_data.push(['排行版详细', Number(model.getHotDetail)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>排行榜类型</th>' +
                '<th>排行榜详细</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.hotType+"</td>" +
                "<td>"+model.getHotDetail+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderActivityTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['保存抽奖结果', Number(model.saveGain)]);
            series_data.push(['保存用户信息', Number(model.saveUser)]);
            series_data.push(['获取投票信息', Number(model.voteInfo)]);
            series_data.push(['获取用户信息', Number(model.userInfo)]);
            series_data.push(['随机抽奖结果', Number(model.randomAward)]);
            series_data.push(['用户签到', Number(model.checkIn)]);
            series_data.push(['保存投票', Number(model.saveVote)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>保存抽奖结果</th>' +
                '<th>保存用户信息</th>' +
                '<th>获取投票信息</th>' +
                '<th>获取用户信息</th>' +
                '<th>随机抽奖结果</th>' +
                '<th>用户签到</th>' +
                '<th>保存投票</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.saveGain+"</td>" +
                "<td>"+model.saveUser+"</td>" +
                "<td>"+model.voteInfo+"</td>" +
                "<td>"+model.userInfo+"</td>" +
                "<td>"+model.randomAward+"</td>" +
                "<td>"+model.checkIn+"</td>" +
                "<td>"+model.saveVote+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

        function renderAllTotalData(models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            series_data.push(['用户操作', Number(model.member)]);
            series_data.push(['违章查询', Number(model.fxcQuery)]);
            series_data.push(['本地惠', Number(model.localFavour)]);
            series_data.push(['新首页', Number(model.newIndex)]);
            series_data.push(['保险', Number(model.insurance)]);
            series_data.push(['路况', Number(model.interaction)]);
            series_data.push(['车友互动', Number(model.talk)]);
            series_data.push(['周边信息', Number(model.location)]);
            series_data.push(['好友', Number(model.friend)]);
            series_data.push(['其他', Number(model.other)]);


            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '操作:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            pie_charts.hideLoading();
            pie_charts.destroy();

            $('#pie_charts_container').highcharts(new_options);

            pie_charts = $('#pie_charts_container').highcharts();

            $('#act_total_table thead').empty().append(
                '<tr>' +
                '<th>用户操作</th>' +
                '<th>违章查询</th>' +
                '<th>本地惠</th>' +
                '<th>新首页</th>' +
                '<th>保险</th>' +
                '<th>路况</th>' +
                '<th>车友互动</th>' +
                '<th>周边信息</th>' +
                '<th>好友</th>' +
                '<th>其他</th>' +
                '<th>总操作</th>' +
                '</tr>'
            );

            //填充数据表格
            $("#act_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.member+"</td>" +
                "<td>"+model.fxcQuery+"</td>" +
                "<td>"+model.localFavour+"</td>" +
                "<td>"+model.newIndex+"</td>" +
                "<td>"+model.insurance+"</td>" +
                "<td>"+model.interaction+"</td>" +
                "<td>"+model.talk+"</td>" +
                "<td>"+model.location+"</td>" +
                "<td>"+model.friend+"</td>" +
                "<td>"+model.other+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        }

    })(jQuery);
</script>
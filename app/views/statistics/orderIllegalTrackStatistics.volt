<style type="text/css">
    .easyui-datebox {
        width:120px;
    }
    div.datagrid * {
        vertical-align: middle;
    }
</style>

<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">查询统计-时间段</h4>
        <div class="widgetcontent nopadding">
            <div class="row-fluid">
                <form id="order_illegal_track_statistics_form" class="form-inline well" >
                    <div class="row-fluid">
                        <fieldset class="pull-left span5">
                            <span class="label">时间</span>
                            <input type="text" class="easyui-datebox" name="start_date" value="{{ default_start_time }}"/>
                            <span>至</span>
                            <input type="text" class="easyui-datebox" name="end_date" value="{{ default_end_time }}" />
                        </fieldset>
                        <fieldset class="pull-left span4">
                            <span class="label">分组方式</span>
                            <input type="radio" name="group_type" value="hours" /><span>按时间点</span>
                            <input type="radio" name="group_type" value="days" checked /><span>按天</span>
                            <input type="radio" name="group_type" value="months" /><span>按月</span>
                        </fieldset>
                        <fieldset class="pull-left span2">
                            <span class="label">图形</span>
                            <select id="chart_type" name="chart_type" class="span8">
                                <option value="spline">线状图</option>
                                <option value="column">柱状图</option>
                            </select>
                        </fieldset>
                    </div>
                </form>    
            </div>
            <div class="row-fluid">
                <div id="order_illegal_user_grid_tb">
                    <div class="row-fluid" id="order_illegal_user_search_bar">
                        <div class="row-fluid">
                            <div class="span4">
                                <span class="label">用户名</span>
                                <input type="text" name="user_id" class="input-medium" />
                            </div>
                            <div class="span3">
                                <span class="label">支付订单数</span>
                                <input type="text" name="pay_order_num" class="input-mini" />
                            </div>
                            <div class="span2">
                                <button class="btn btn-primary" id="order_illegal_user_search_btn"><i class="iconfa-search"></i>查找用户</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="order_illegal_user_grid">  
                </div>    
            </div>
            <div class="charts">
                <div id="charts_container"></div>
                <div id="charts_container2"></div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <div id="pie_charts"></div>
                    <div id="pie_charts2"></div>
                </div>
                <div class="span8">
                    <table id="user_total_table" width="96%" class="table" cellpadding="0" cellspacing="1">
                        <thead>
                        <tr><th>成交单数</th><th>支付宝单数</th><th>微信单数</th><th>线下支付单数</th><th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th></tr>
                        </thead>
                        <tbody>
                        
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <table id="user_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                        <thead>
                        <tr><th>日期</th><th>成交单数</th><th>支付宝</th><th>微信</th><th>线下</th><th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th><th>总违章条数</th><th>手续费合计</th><th>实际收入合计</th><th>订单环比</th></tr>
                        </thead>
                        <tbody>
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    //页面加载时事件
    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */
        //定义日期控件
        var start_date_box = $('[name=start_date].easyui-datebox').datebox({
            required: true,
            editable: false
        });

        var end_date_box = $('[name=end_date].easyui-datebox').datebox({
            required: true,
            editable: false
        });

        //数字控件
        var pay_order_num_box = $('#order_illegal_user_search_bar [name=pay_order_num]').numberbox({
            min: 0
        });

        //已提交过订单用户表格
        var order_illegal_user_grid = $('#order_illegal_user_grid').datagrid({
            url: '/illegal/orderUserList.json',
            title: '违章代缴用户列表',
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
            toolbar: '#order_illegal_user_grid_tb',
            columns: [[
                {field:'user_id', title:'用户名', width:'20%', align:'center'},
                {field:'create_date', title:'注册时间', width:'20%', align:'center'},
                {field:'user_name', title:'用户姓名', width:'20%', align:'center'},
                {field:'phone', title:'手机号', width:'20%', align:'center'},
                {field:'order_num', title:'提交订单数', width:'10%', align:'center'},
                {field:'pay_order_num', title:'支付订单数', width:'10%', align:'center'},
                {field:'processed_illegal_num', title:'处理条目数', width:'10%', align:'center'},
                {field:'rownum', title:'操作', width:'10%', align:'center', formatter:function(value, row, index){
                    return '<button class="btn btn-info illegal-track-statistics-btn" data-user-id="' + row.user_id + '" title="统计"><i class="iconfa-bar-chart"></i></button>';
                }}
            ]]
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
                text: '违章代缴业务订单数统计'
            },
            subtitle: {
                text: '按天'
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                title: {
                    text: '订单数'
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
            series: [
                {
                    id: 'alipay',
                    name: '支付宝',
                    data: []
                },
                {
                    id: 'wxpay',
                    name: '微信',
                    data: []
                },
                {
                    id: 'offline',
                    name: '线下支付',
                    data: []
                },
                {
                    id: 'android',
                    name: 'android',
                    data: []
                },
                {
                    id: 'iPhone',
                    name: 'iPhone',
                    data: []
                },
                {
                    id: 'iPad',
                    name: 'iPad',
                    data: []
                },
                {
                    id: 'iPodTouch',
                    name: 'iPodTouch',
                    data: []
                },
                {
                    id: 'windowsPhone',
                    name: 'WindowsPhone',
                    data: []
                },
                {
                    id: 'other',
                    name: '其他',
                    data: []
                },
                {   id: 'sum',
                    name: '总成交数',
                    data: []
                }]
        };

        var options2 = {
            chart: {
                type: $('#chart_type').val(),
                height: 300,
                backgroundColor: 'none',
                zoomType: 'xy'
            },
            title: {
                text: '违章代缴业务实际收入统计'
            },
            subtitle: {
                text: '按天'
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                title: {
                    text: '订单数'
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
            series: [
                {
                    id: 'realIncome',
                    name: '实际收入合计',
                    data: []
                },
                {
                    id: 'alipayRealIncome',
                    name: '支付宝实际收入合计',
                    data: []
                },
                {
                    id: 'wxpayRealIncome',
                    name: '微信支付实际收入合计',
                    data: []
                },
                {
                    id: 'offlineRealIncome',
                    name: '线下支付实际收入合计',
                    data: []
                }
            ]
        };

        $('#charts_container').highcharts(options);
        $('#charts_container2').highcharts(options2);

        var charts = $('#charts_container').highcharts();
        var charts2 = $('#charts_container2').highcharts();

        //定义pie_charts控件,用于显示访问总量统计

        var pie_options = {
            chart: {
                width:400,
                height:250,
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
                    return '<b>'+ this.point.name +'</b></br> '+'订单数:'+this.point.y + "<br/>" + "百分比:" +this.percentage.toFixed(2) +' %';
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
            series: [{
                id: 'total',
                type: 'pie',
                name: '客户端所占比例',
                data: [

                ]
            }]
        };

        var pie_options2 = {
            chart: {
                width:400,
                height:250,
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
                    return '<b>'+ this.point.name +'</b></br> '+'订单数:'+this.point.y + "<br/>" + "百分比:" +this.percentage.toFixed(2) +' %';
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
            series: [{
                id: 'total',
                type: 'pie',
                name: '支付方式所占比例',
                data: [

                ]
            }]
        };

        $('#pie_charts').highcharts(pie_options);
        $('#pie_charts2').highcharts(pie_options2);
        var pie_charts = $('#pie_charts').highcharts();//查询到统计数据后的响应
        var pie_charts2 = $('#pie_charts2').highcharts();//查询到统计数据后的响应

        //查找按钮点击事件
        $('#order_illegal_user_search_btn').click(function(event){
            var criteria = {};
            criteria.user_id = $('#order_illegal_user_search_bar [name="user_id"]').val();
            criteria.pay_order_num = $('#order_illegal_user_search_bar [name="pay_order_num"]').val();
            order_illegal_user_grid.datagrid('load',{criteria: criteria});
        });

        //统计按钮事件
        $(document).on('click',".illegal-track-statistics-btn",function(event){
            event.preventDefault();
            var condition = {};
            condition.user_id = $(this).attr('data-user-id');
            condition.start_date = start_date_box.datebox('getValue');
            condition.end_date = end_date_box.datebox('getValue');
            condition.group_type = pay_order_num_box.numberbox('getValue');
            OrderIllegalTrackStatistics.find(condition);
            OrderIllegalTrackTotalStatistics.find(condition);
            return false;
        });

        //图表类型选择框事件
        $('#chart_type').change(function(event){
            var old_options = charts.options;
            old_options.chart.type = $(this).val();
            var old_series = charts.series;
            var s_options = [];
            $(old_series).each(function(i, s){
                s_options.push(s.options);
            });
            old_options.series = s_options;
            //销毁旧的charts
            charts.destroy();
            $('#charts_container').highcharts(old_options);
            charts = $('#charts_container').highcharts();
        });

        /**
         * 数据相关
         */

        //定义OrderIllegalTrackStatistics(访问)模型类
        var OrderIllegalTrackStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/orderIllegalTrackStatistics',
                buildUrl: function(condition){
                    if(!condition)
                    {
                        return this.baseUrl;
                    }
                    else
                    {
                        var user_id = condition.user_id || '';
                        var start_date = condition.start_date || '';
                        var end_date = condition.end_date || '';
                        var group_type = condition.group_type || 'days';
                        return this.baseUrl + '/' + user_id + '/' + start_date + '/' + end_date + '/' + group_type + '.json';
                    }
                }
            }
        });

        //查询到统计数据后的响应
        OrderIllegalTrackStatistics.on('found',function(event, models){
            var len = models.length;
            var categories = [];
            var alipay_data = [];
            var wxpay_data = [];
            var offline_data = [];
            var android_data = [];
            var iphone_data = [];
            var ipad_data = [];
            var ipod_touch_data = [];
            var window_phone_data = [];
            var other_data = [];
            var sum_data = [];
            var realIncome_data = [];
            var alipayRealIncome_data = [];
            var wxpayRealIncome_data = [];
            var offlineRealIncome_data = [];

            var pre_sum = 0;

            //插入新数据前先把旧数据清空
            $("#user_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var model = models[i];
                categories.push(model.date);
                alipay_data.push(Number(model.alipayNum));
                wxpay_data.push(Number(model.wxpayNum));
                offline_data.push(Number(model.offlineNum));
                android_data.push(Number(model.android));
                iphone_data.push(Number(model.iPhone));
                ipad_data.push(Number(model.iPad));
                ipod_touch_data.push(Number(model.iPodTouch));
                window_phone_data.push(Number(model.windowsPhone));
                other_data.push(Number(model.other));
                sum_data.push(Number(model.orderNum));
                realIncome_data.push(Number(model.realIncome));
                alipayRealIncome_data.push(Number(model.alipayRealIncome));
                wxpayRealIncome_data.push(Number(model.wxpayRealIncome));
                offlineRealIncome_data.push(Number(model.offlineRealIncome));

                //填充数据表格
                var sum = Number(model.orderNum);
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

                $("#user_table tbody").append(
                    "<tr>" +
                    "<td>" + model.date + "</td>" +
                    "<td>" + model.orderNum + "</td>" +
                    "<td>" + model.alipayNum + "</td>" +
                    "<td>" + model.wxpayNum + "</td>" +
                    "<td>" + model.offlineNum + "</td>" +
                    "<td>" + model.android + "</td>" +
                    "<td>" + model.iPhone + "</td>" +
                    "<td>" + model.iPad + "</td>" +
                    "<td>" + model.iPodTouch + "</td>" +
                    "<td>" + model.windowsPhone + "</td>" +
                    "<td>" + model.other + "</td>" +
                    "<td>" + model.illegalNum + "</td>" +
                    "<td>" + model.poundage + "</td>" +
                    "<td>" + model.realIncome + "</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            charts.get('alipay').setData(alipay_data, false);
            charts.get('wxpay').setData(wxpay_data, false);
            charts.get('offline').setData(offline_data, false);
            charts.get('android').setData(android_data, false);
            charts.get('iPhone').setData(iphone_data, false);
            charts.get('iPad').setData(ipad_data, false);
            charts.get('iPodTouch').setData(ipod_touch_data, false);
            charts.get('windowsPhone').setData(window_phone_data, false);
            charts.get('other').setData(other_data, false);
            charts.get('sum').setData(sum_data, false);
            //设置x轴类别
            charts.xAxis[0].setCategories(categories, false);
            charts.setTitle(null, {text: $('#order_illegal_track_statistics_form [name=group_type]:checked + span').text()});
            charts.hideLoading();
            charts.redraw();

            charts2.get('realIncome').setData(realIncome_data, false);
            charts2.get('alipayRealIncome').setData(alipayRealIncome_data, false);
            charts2.get('wxpayRealIncome').setData(wxpayRealIncome_data, false);
            charts2.get('offlineRealIncome').setData(offlineRealIncome_data, false);
            charts2.xAxis[0].setCategories(categories, false);
            charts2.setTitle(null, {text: $('#order_illegal_track_statistics_form [name=group_type]:checked + span').text()});
            charts2.hideLoading();
            charts2.redraw();

        });
        //未查询到数据的响应
        OrderIllegalTrackStatistics.on('notFound',function(){
            charts.hideLoading();
        });
        //查询数据之前
        OrderIllegalTrackStatistics.on('beforeFind', function(){
            charts.showLoading();
        });

        //定义OrderIllegalTrackTotalStatistics模型
        var OrderIllegalTrackTotalStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/orderIllegalTrackTotalStatistics',
                buildUrl: function(condition){
                    if(!condition)
                    {
                        return this.baseUrl;
                    }
                    else
                    {
                        var user_id = condition.user_id || '';
                        var start_date = condition.start_date || '';
                        var end_date = condition.end_date || '';
                        return this.baseUrl + '/' + user_id + '/' + start_date + '/' + end_date + '.json';
                    }
                }
            }
        });

        //查询到访问总量统计数据后的响应
        OrderIllegalTrackTotalStatistics.on('found',function(event, models){
            //设置pie_charts
            var model = models[0];
            var series_data = [];
            var series_data2 = [];
            series_data.push(['android', Number(model.android)]);
            series_data.push(['iPhone', Number(model.iPhone)]);
            series_data.push(['iPad', Number(model.iPad)]);
            series_data.push(['iPodTouch', Number(model.iPodTouch)]);
            series_data.push(['WindowsPhone', Number(model.windowsPhone)]);
            series_data.push(['其他', Number(model.other)]);

            pie_charts.get('total').setData(series_data, false);
            pie_charts.hideLoading();
            pie_charts.redraw();

            series_data2.push(['alipay', Number(model.alipayNum)]);
            series_data2.push(['wxpay', Number(model.wxpayNum)]);
            series_data2.push(['offline', Number(model.offlineNum)]);

            pie_charts2.get('total').setData(series_data2, false);
            pie_charts2.hideLoading();
            pie_charts2.redraw();

            //填充数据表格
            $("#user_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.orderNum+"</td>" +
                "<td>"+model.alipayNum+"</td>" +
                "<td>"+model.wxpayNum+"</td>" +
                "<td>"+model.offlineNum+"</td>" +
                "<td>"+model.android+"</td>" +
                "<td>"+model.iPhone+"</td>" +
                "<td>"+model.iPad+"</td>" +
                "<td>"+model.iPodTouch+"</td>" +
                "<td>"+model.windowsPhone+"</td>" +
                "<td>"+model.other+"</td>" +
                "</tr>"
            );
        });

        //未查询到数据的响应
        OrderIllegalTrackTotalStatistics.on('notFound',function(){
            pie_charts.hideLoading();
        });
        //查询数据之前
        OrderIllegalTrackTotalStatistics.on('beforeFind', function(){
            pie_charts.showLoading();
        });

        //页面离开时事件
        CarMate.page.on_leave = function(){
            $(document).off('click',".illegal-track-statistics-btn");
        };
    };
</script>
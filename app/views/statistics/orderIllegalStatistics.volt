<style type="text/css">
    .easyui-datebox {
        width:120px;
    }
</style>

<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">查询统计-时间段</h4>
        <div class="widgetcontent nopadding">
            <form id="user_statistics_form" class="form-inline well" >

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
                <button id="finder_btn" class="btn btn-primary pull-right span1">确定</button>
            </form>
            <div class="charts">
                <div id="charts_container"></div>
                <div id="charts_container2"></div>
            </div>
            <table width="100%" >
                <tr>
                    <td width="39%" id="totalContainer">
                        <div id="pie_charts"></div>
                        <div id="pie_charts2"></div>
                    </td>
                    <td width="60%">
                        <div style="padding:10px;">
                            <table id="user_total_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                                <thead>
                                <tr><th>成交单数</th><th>支付宝单数</th><th>微信单数</th><th>线下支付单数</th><th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th></tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table id="user_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                            <thead>
                            <tr><th>日期</th><th>成交单数</th><th>支付宝</th><th>微信</th><th>线下</th><th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th><th>总违章条数</th><th>手续费合计</th><th>实际收入合计</th><th>订单环比</th></tr>
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


        //查询按钮事件
        $("#finder_btn").click(function(event){
            event.preventDefault();
            var condition = {};
            condition.start_date = start_date_box.datebox('getValue');
            condition.end_date = end_date_box.datebox('getValue');
            condition.group_type = $('#user_statistics_form [name=group_type]:checked').val();
            OrderIllegalStatistics.find(condition);
            OrderIllegalTotalStatistics.find(condition);
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

        //定义OrderIllegalStatistics(访问)模型类
        var OrderIllegalStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/orderIllegalStatistics',
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
                        return this.baseUrl + '/' + start_date + '/' + end_date + '/' + group_type + '.json';
                    }
                }
            }
        });

        //查询到统计数据后的响应
        OrderIllegalStatistics.on('found',function(event, models){
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
            charts.setTitle(null, {text: $('#user_statistics_form [name=group_type]:checked + span').text()});
            charts.hideLoading();
            charts.redraw();

            charts2.get('realIncome').setData(realIncome_data, false);
            charts2.xAxis[0].setCategories(categories, false);
            charts2.setTitle(null, {text: $('#user_statistics_form [name=group_type]:checked + span').text()});
            charts2.hideLoading();
            charts2.redraw();

        });
        //未查询到数据的响应
        OrderIllegalStatistics.on('notFound',function(){
            charts.hideLoading();
        });
        //查询数据之前
        OrderIllegalStatistics.on('beforeFind', function(){
            charts.showLoading();
        });

        //定义OrderIllegalTotalStatistics模型
        var OrderIllegalTotalStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/orderIllegalTotalStatistics',
                buildUrl: function(condition){
                    if(!condition)
                    {
                        return this.baseUrl;
                    }
                    else
                    {
                        var start_date = condition.start_date || '';
                        var end_date = condition.end_date || '';
                        return this.baseUrl + '/' + start_date + '/' + end_date + '.json';
                    }
                }
            }
        });

        //查询到访问总量统计数据后的响应
        OrderIllegalTotalStatistics.on('found',function(event, models){
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
        OrderIllegalTotalStatistics.on('notFound',function(){
            pie_charts.hideLoading();
        });
        //查询数据之前
        OrderIllegalTotalStatistics.on('beforeFind', function(){
            pie_charts.showLoading();
        });

        //初始加载数据
        var condition = {};
        condition.start_date = start_date_box.datebox('getValue');
        condition.end_date = end_date_box.datebox('getValue');
        condition.group_type = $('#user_statistics_form [name=group_type]:checked').val();
        OrderIllegalStatistics.find(condition);
        OrderIllegalTotalStatistics.find(condition);

    })(jQuery);
</script>
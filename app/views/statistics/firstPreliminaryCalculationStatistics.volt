<style type="text/css">
    .easyui-datebox {
        width:120px;
    }
</style>

<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">保险系统行为统计(每日新增用户)</h4>
        <div class="widgetcontent nopadding">
            <form id="fpc_statistics_form" class="form-inline well" >

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
            </div>
            <table width="100%" >
                <tr>
                    <td width="39%" id="totalContainer">
                        <div id="pie_charts_container"></div>
                    </td>
                    <td width="60%">
                        <div style="padding:10px;">
                            <table id="fpc_total_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                                <thead>
                                <tr><th>app</th><th>新增</th></tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table id="fpc_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                            <thead>
                                <tr><th>日期</th><th>app</th><th>总新增</th><th>新增环比</th></tr>
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
                text: '保险行为统计(每日新增用户)'
            },
            subtitle: {
                text: '按天'
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                title: {
                    text: '新增用户'
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
                    return '<b>'+ this.point.name +'</b></br> '+'来源:'+this.point.y + "<br/>" + "百分比:" +this.percentage.toFixed(2) +' %';
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
        $("#finder_btn").click(function(event){
            event.preventDefault();
            var condition = {};
            condition.start_date = start_date_box.datebox('getValue');
            condition.end_date = end_date_box.datebox('getValue');
            condition.group_type = $('#fpc_statistics_form [name=group_type]:checked').val();
            FPCStatistics.find(condition);
            FPCTotalStatistics.find(condition);
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

        //定义FPCStatistics(访问)模型类
        var FPCStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/firstPreliminaryCalculationStatistics',
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
        FPCStatistics.on('found',function(event, models){
            var data = FPCStatistics.raw_data.rows;
            var len = data.length;
            var categories = [];
            var app_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //插入新数据前先把旧数据清空
            $("#fpc_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var item = data[i];
                categories.push(item.date);
                app_data.push(Number(item.app));
                sum_data.push(Number(item.sum));

                //填充数据表格
                var sum = item.sum;
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

                $("#fpc_table tbody").append(
                    "<tr>" +
                    "<td>"+item.date+"</td>" +
                    "<td>"+item.app+"</td>" +
                    "<td>"+ item.sum +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: 'app', data: app_data},
                {name: '总新增', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.subtitle.text = $('#fpc_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();

        });
        //未查询到数据的响应
        FPCStatistics.on('notFound',function(){
            charts.hideLoading();
        });
        //查询数据之前
        FPCStatistics.on('beforeFind', function(){
            charts.showLoading();
        });

        //定义FPCTotalStatistics模型
        var FPCTotalStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/firstPreliminaryCalculationTotalStatistics',
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
        FPCTotalStatistics.on('found',function(event, models){
            //设置pie_charts
            var item = FPCTotalStatistics.raw_data.rows[0];
            var series_data = []
            series_data.push(['app', Number(item.app)]);


            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '来源:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
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


            //填充数据表格
            $("#fpc_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+item.app+"</td>" +
                "<td>"+item.sum+"</td>" +
                "</tr>"
            );
        });
        //未查询到数据的响应
        FPCTotalStatistics.on('notFound',function(){
            pie_charts.hideLoading();
        });
        //查询数据之前
        FPCTotalStatistics.on('beforeFind', function(){
            pie_charts.showLoading();
        });


        //初始加载数据
        var condition = {};
        condition.start_date = start_date_box.datebox('getValue');
        condition.end_date = end_date_box.datebox('getValue');
        condition.group_type = $('#fpc_statistics_form [name=group_type]:checked').val();
        FPCStatistics.find(condition);
        FPCTotalStatistics.find(condition);

    })(jQuery);
</script>
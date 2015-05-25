<style type="text/css">
    .easyui-datebox {
        width:120px;
    }
</style>

<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">用户活跃度统计</h4>
        <div class="widgetcontent nopadding">
            <form id="user_activity_statistics_form" class="form-inline well" >

                <fieldset class="pull-left span5">

                    <span class="label">时间</span>
                    <input type="text" class="easyui-datebox" name="start_date" value="<?php echo $default_start_time; ?>"/>
                    <span>至</span>
                    <input type="text" class="easyui-datebox" name="end_date" value="<?php echo $default_end_time; ?>" />
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
            <table id="user_activity_table" width="100%" class="table" cellpadding="0" cellspacing="1">
                <thead>
                    <tr><th>日期</th><th>总活跃</th><th>总访问</th><th>人均使用次数</th><th>活跃环比</th></tr>
                </thead>
                <tbody>

                </tbody>
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
                text: '访问统计'
            },
            subtitle: {
                text: '按天'
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                title: {
                    text: '访问人数'
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


        //查询按钮事件
        $("#finder_btn").click(function(event){
            event.preventDefault();
            var condition = {};
            condition.start_date = start_date_box.datebox('getValue');
            condition.end_date = end_date_box.datebox('getValue');
            condition.group_type = $('#user_activity_statistics_form [name=group_type]:checked').val();
            UserActivityStatistics.find(condition);
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

        //定义UserActivityStatistics(访问)模型类
        var UserActivityStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/userActivityStatistics',
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
        UserActivityStatistics.on('found',function(event, models){
            var data = UserActivityStatistics.raw_data.rows;
            var len = data.length;
            var categories = [];
            var user_rate_data = [];
            var user_data = [];
            var sum_data = [];

            var pre_sum = 0;

            //插入新数据前先把旧数据清空
            $("#query_table tbody").empty();

            for(var i = 0; i < len; i++)
            {
                var item = data[i];
                categories.push(item.date);
                user_rate_data.push(Number(item.userRate));
                user_data.push(Number(item.user));
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

                $("#user_activity_table tbody").append(
                    "<tr>" +
                    "<td>"+item.date+"</td>" +
                    "<td>"+item.userRate+"</td>" +
                    "<td>"+item.sum+"</td>" +
                    "<td>"+ item.user +"</td>" +
                    "<td>"+ growth_rate +"</td>" +
                    "</tr>"
                );
            }

            var new_options = $.extend(options, {});
            new_options.series = [
                {name: '人均使用次数', data: user_rate_data},
                {name: '活跃用户', data: user_data},
                {name: '总访问', data: sum_data}
            ];
            //设置x轴类别
            new_options.xAxis.categories = categories;
            new_options.subtitle.text = $('#user_activity_statistics_form [name=group_type]:checked + span').text();
            charts.hideLoading();
            charts.destroy();
            $('#charts_container').highcharts(new_options);
            charts = $('#charts_container').highcharts();

        });
        //未查询到数据的响应
        UserActivityStatistics.on('notFound',function(){
            charts.hideLoading();
        });
        //查询数据之前
        UserActivityStatistics.on('beforeFind', function(){
            charts.showLoading();
        });


        //初始加载数据
        var condition = {};
        condition.start_date = start_date_box.datebox('getValue');
        condition.end_date = end_date_box.datebox('getValue');
        condition.group_type = $('#user_activity_statistics_form [name=group_type]:checked').val();
        UserActivityStatistics.find(condition);

    })(jQuery);
</script>

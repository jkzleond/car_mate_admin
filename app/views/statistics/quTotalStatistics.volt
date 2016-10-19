<style type="text/css">
    .easyui-datebox {
        width:120px;
    }
</style>

<div class="row-fluid">
    <div class="span6">
        <h4 class="widgettitle">总访问统计</h4>
        <div class="widgetcontent nopadding">
            <div id="query_total_charts"></div>
            <div>
                <table id="query_total_table" class="table">
                    <thead>
                    <tr>
                        <th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th><th>总访问</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="span6">
        <h4 class="widgettitle">用户注册统计</h4>
        <div class="widgetcontent nopadding">
            <div id="user_total_charts"></div>
            <div>
                <table id="user_total_table" class="table">
                    <thead>
                        <tr>
                            <th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th><th>总注册</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row-fluid">
    <h4 class="widgettitle">省份用户排名</h4>
    <div class="widgetcontent nopadding">
        <table id="province_user_total_table" class="table">
            <thead>
            <tr><th>序号</th><th>省份</th><th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th><th>用户总数</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <h4 class="widgettitle">用户版本统计</h4>
    <div class="widgetcontent nopadding">
        <table id="user_client_version_table" class="table">
            <thead>
            <tr><th>版本</th><th>Android</th><th>iPhone</th><th>iPad</th><th>iPodTouch</th><th>Windows Phone</th><th>其他</th><th>用户总数</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    (function($){

        //定义pie_charts控件,用于显示访问总量统计

        var pie_options = {
            chart: {
                width:500,
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
                    return '<b>'+ this.point.name +'</b></br> '+'访问:'+this.point.y + "<br/>" + "百分比:" +this.percentage.toFixed(2) +' %';
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
        //创建两个pie charts
        $('#query_total_charts').highcharts(pie_options);
        $('#user_total_charts').highcharts(pie_options);
        var query_charts = $('#query_total_charts').highcharts();//查询到统计数据后的响应
        var user_charts = $('#user_total_charts').highcharts()

        /**
         * 数据相关
         */

        //定义QueryTotalStatistics模型
        var QueryTotalStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/queryTotalStatistics',
                buildUrl: function(condition){
                    if(!condition)
                    {
                        return this.baseUrl;
                    }
                    else
                    {
                        var start_date = condition.start_date || 'origin';
                        var end_date = condition.end_date || 'now';
                        return this.baseUrl + '/' + start_date + '/' + end_date + '.json';
                    }
                }
            }
        });

        //查询到访问总量统计数据后的响应
        QueryTotalStatistics.on('found',function(event, models){
            //设置pie_charts
            var model = models[0];
            var series_data = []
            series_data.push(['android', Number(model.android)]);
            series_data.push(['iPhone', Number(model.iPhone)]);
            series_data.push(['iPad', Number(model.iPad)]);
            series_data.push(['iPodTouch', Number(model.iPodTouch)]);
            series_data.push(['WindowsPhone', Number(model.windowsPhone)]);
            series_data.push(['其他', Number(model.other)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '访问:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            query_charts.hideLoading();
            query_charts.destroy()

            $('#query_total_charts').highcharts(new_options);

            query_charts = $('#query_total_charts').highcharts();

            //填充数据表格
            $("#query_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.android+"</td>" +
                "<td>"+model.iPhone+"</td>" +
                "<td>"+model.iPad+"</td>" +
                "<td>"+model.iPodTouch+"</td>" +
                "<td>"+model.windowsPhone+"</td>" +
                "<td>"+model.other+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        });
        //未查询到数据的响应
        QueryTotalStatistics.on('notFound',function(){
            query_charts.hideLoading();
        });
        //查询数据之前
        QueryTotalStatistics.on('beforeFind', function(){
            query_charts.showLoading();
        });

        //定义UserTotalStatistics模型
        var UserTotalStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/userTotalStatistics',
                buildUrl: function(condition){
                    if(!condition)
                    {
                        return this.baseUrl;
                    }
                    else
                    {
                        var start_date = condition.start_date || 'origin';
                        var end_date = condition.end_date || 'now';
                        return this.baseUrl + '/' + start_date + '/' + end_date + '.json';
                    }
                }
            }
        });

        //查询到访问总量统计数据后的响应
        UserTotalStatistics.on('found',function(event, models){
            //设置pie_charts
            var model = models[0];
            var series_data = []
            series_data.push(['android', Number(model.android)]);
            series_data.push(['iPhone', Number(model.iPhone)]);
            series_data.push(['iPad', Number(model.iPad)]);
            series_data.push(['iPodTouch', Number(model.iPodTouch)]);
            series_data.push(['WindowsPhone', Number(model.windowsPhone)]);
            series_data.push(['其他', Number(model.other)]);

            var new_options = $.extend(pie_options, {});

            new_options.tooltip = {
                useHtml: true,
                formatter: function(){
                    return '<b>' + this.point.name + '</b><br/> ' + '注册:' + this.point.y + '<br/>百分比:' + this.percentage.toFixed(2) + '%';
                }
            };

            var series = {
                type: 'pie',
                data: series_data
            }

            new_options.series = [series];

            user_charts.hideLoading();
            user_charts.destroy()

            $('#user_total_charts').highcharts(new_options);

            user_charts = $('#user_total_charts').highcharts();;

            //填充数据表格
            $("#user_total_table tbody").empty().append(
                "<tr>" +
                "<td>"+model.android+"</td>" +
                "<td>"+model.iPhone+"</td>" +
                "<td>"+model.iPad+"</td>" +
                "<td>"+model.iPodTouch+"</td>" +
                "<td>"+model.windowsPhone+"</td>" +
                "<td>"+model.other+"</td>" +
                "<td>"+model.sum+"</td>" +
                "</tr>"
            );
        });
        //未查询到数据的响应
        UserTotalStatistics.on('notFound',function(){
            user_charts.hideLoading();
        });
        //查询数据之前
        UserTotalStatistics.on('beforeFind', function(){
            user_charts.showLoading();
        });

        //定义ProvinceUserStatistics模型
        var ProvinceUserStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/provinceUserStatistics',
                buildUrl: function(condition){
                    return this.baseUrl + '.json';

                }
            }
        });

        //查询到访问总量统计数据后的响应
        ProvinceUserStatistics.on('found',function(event, models){
            //设置pie_charts
            var data = ProvinceUserStatistics.raw_data.rows;

            //填充数据表格
            $(data).each(function(index, item){
                $("#province_user_total_table tbody").append(
                    "<tr>" +
                    "<td>"+(index + 1)+"</td>" +
                    "<td>"+item.provinceName+"</td>" +
                    "<td>"+item.android+"</td>" +
                    "<td>"+item.iPhone+"</td>" +
                    "<td>"+item.iPad+"</td>" +
                    "<td>"+item.iPodTouch+"</td>" +
                    "<td>"+item.windowsPhone+"</td>" +
                    "<td>"+item.other+"</td>" +
                    "<td>"+item.totalCount+"</td>" +
                    "</tr>"
                );
            });

        });
        //未查询到数据的响应
        ProvinceUserStatistics.on('notFound',function(){
            $("#province_user_total_table tbody").append('<tr><td colspan="9">暂无数据</td></tr>');
        });
        //查询数据之前
        ProvinceUserStatistics.on('beforeFind', function(){
            $("#province_user_total_table tbody").empty();
        });


        //定义ClientVersionStatistics模型
        var ClientVersionStatistics = CarMate.Model.extend({
            __class_props__:{
                baseUrl: '/statistics/clientVersionStatistics',
                buildUrl: function(condition){
                    return this.baseUrl + '.json';
                }
            }
        });

        //查询到访问总量统计数据后的响应
        ClientVersionStatistics.on('found',function(event, models){
            //设置pie_charts
            var data = ClientVersionStatistics.raw_data.rows;

            //填充数据表格
            $(data).each(function(index, item){
                $("#user_client_version_table tbody").append(
                    "<tr>" +
                    "<td>"+item.clientVersion+"</td>" +
                    "<td>"+item.android+"</td>" +
                    "<td>"+item.iPhone+"</td>" +
                    "<td>"+item.iPad+"</td>" +
                    "<td>"+item.iPodTouch+"</td>" +
                    "<td>"+item.windowsPhone+"</td>" +
                    "<td>"+item.other+"</td>" +
                    "<td>"+item.totalCount+"</td>" +
                    "</tr>"
                );
            });
        });
        //未查询到数据的响应
        ClientVersionStatistics.on('notFound',function(){
            $("#user_client_version_table tbody").append('<tr><td colspan="9">暂无数据</td></tr>');
        });
        //查询数据之前
        ClientVersionStatistics.on('beforeFind', function(){
            $("#user_client_version_table tbody").empty();
        });

        //初始加载数据
        QueryTotalStatistics.find({start_date: 'origin', end_date: 'now'});
        UserTotalStatistics.find({start_date: 'origin', end_date: 'now'});
        ProvinceUserStatistics.find();
        ClientVersionStatistics.find();
    })(jQuery);
</script>
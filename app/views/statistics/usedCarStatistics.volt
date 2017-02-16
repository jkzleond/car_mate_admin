<style type="text/css">
    .easyui-datebox {
        width:120px;
    }
    .table {
        padding: 0px;
    }
    /*.narrow {*/
    /*padding: 0px !important;*/
    /*}*/
    .table.center, .table tr .center {
        text-align: center;
    }
    .table th.left, .table td.left {
        text-align: left;
    }

</style>

<div class="row-fluid">
    <div class="span12">
        <h4 class="widgettitle">挪车业务统计</h4>
        <div class="widgetcontent nopadding">
            <table class="table center">
                <tr>
                    <th>进入页面人数</th>
                    <th>进入页面次数</th>
                    <th>在线估值次数</th>
                    <th>在线估值人数</th>
                    <th>详细估值次数</th>
                    <th>详细估值人数</th>
                </tr>
                <tr id="used_car_status_statistics_value_container" style="font-size: 24px; font-weight: bold;">

                </tr>
            </table>
            <div id="used_car_brand_statistics_grid"></div>
            <table class="table center">
                <tr>
                    <th>x<10万</th>
                    <th>10<=x<1万</th>
                    <th>15<=x<20万</th>
                    <th>20<=x<25万</th>
                    <th>25<=x<30万</th>
                    <th>x<=30万</th>
                </tr>
                <tr id="used_car_price_count_value_container" style="font-size: 24px; font-weight: bold;"></tr>

            <table class="table">
                <tr>
                    <th class="left">车辆上牌时间统计</th>
                </tr>
                <tr>
                    <td>
                        <div class="row-fluid">
                            <div class="span2"></div>
                            <div class="span4">
                                <span class="label">日期</span>
                                <input name="first_reg_time_start_date" type="text" class="input input-small easyui-datebox" value="{{ start_date }}" />
                                -
                                <input name="first_reg_time_end_date" type="text" class="input input-small easyui-datebox" value="{{ end_date }}" />
                            </div>
                            <div class="span2">
                                <button class="btn btn-primary pull-right used-car-first-reg-time-statistics-btn" title="统计"><i class="iconfa-bar-chart"></i></button>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div id="used_car_first_reg_time_chart_container"></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    CarMate.page.on_loaded = function(){

        /**
         * 控件相关
         */
                //定义日期控件
        var trend_start_date_box = $('[name=trend_start_date]').datebox({
                    required: true
                });

        var trend_end_date_box = $('[name=trend_end_date]').datebox({
            required: true
        });

        var charge_trend_start_date_box = $('[name=charge_trend_start_date]').datebox({
            required: true
        });

        var charge_trend_end_date_box = $('[name=charge_trend_end_date]').datebox({
            required: true
        });

        var first_reg_time_start_date_box = $('[name=first_reg_time_start_date]').datebox({
            required: true
        });

        var first_reg_time_end_date_box = $('[name=first_reg_time_end_date]').datebox({
            required: true
        });


        $('.datebox input').keydown(function(event){
            event.preventDefault();
            return false;
        });

        //定义charts控件
        var first_reg_time_chart_options = {
            chart: {
                type: $('#chart_type').val(),
                height: 300,
                backgroundColor: 'none',
                zoomType: 'xy'
            },
            title: {
                text: '上牌时间统计'
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                title: {
                    text: '上牌数'
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
                    id: 'new_car_count',
                    name: '上牌数量',
                    data: []
                }
            ]
        };

        $('#used_car_first_reg_time_chart_container').highcharts(first_reg_time_chart_options);
        var first_reg_time_chart = $('#used_car_first_reg_time_chart_container').highcharts();

        //意见表格
        var used_car_brand_statistics_grid = $('#used_car_brand_statistics_grid').datagrid({
            title: '品牌统计(估过价)',
            iconCls: 'icon-list',
            width: '100%',
            height: '280',
            fitColumns: true,
            singleSelect: true,
            nowrap: false,///设置为true，当数据长度超出列宽时将会自动截取
            striped: true,///显示条纹
            //pagination:true,///分页
            pageSize:20,///（每页记录数）
            //pageNumber:1,///（当前页码）
            //pageList:[50,100,150,200],
            idField: 'brand_name',
            columns:[[
                {field:'brand_name', title:'品牌名称', width:'50%', align:'center'},
                {field:'car_count', title:'车辆数', width:'50%', align:'center'}
            ]]
        });


        /*事件相关*/

        //查询按钮事件
        $("#finder_btn").click(function(event){
            event.preventDefault();
            var condition = {};
            condition.start_date = start_date_box.datebox('getValue');
            condition.end_date = end_date_box.datebox('getValue');
            condition.group_type = $('#used_car_statistics_form [name=group_type]:checked').val();
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

        //挪车业务趋势,使用量列表统计按钮点击事件
        $('.tend-use-count-statistics-btn').click(function(event){
            var start_date = trend_start_date_box.datebox('getValue');
            var end_date = trend_end_date_box.datebox('getValue');

            UsedCarStatistics.getTrendUseCountListStatistics(start_date, end_date).done(function(resp){
                if(resp.success)
                {
                    $('#used_car_trend_use_count_list_container').empty().append(used_car_trend_use_count_list_tpl(resp.list));
                }
            });
        });

        //挪车业务收费趋势,使用量列表统计按钮点击事件
        $('.charge_trend-use-count-statistics-btn').click(function(event){
            var start_date = charge_trend_start_date_box.datebox('getValue');
            var end_date = charge_trend_end_date_box.datebox('getValue');

            UsedCarStatistics.getChargeTrendUseCountListStatistics(start_date, end_date).done(function(resp){
                if(resp.success)
                {
                    $('#used_car_charge_trend_use_count_list_container').empty().append(used_car_charge_trend_use_count_list_tpl(resp.list));
                }
            });
        });

        //挪车业务操作统计按钮点击事件
        $('.used-car-first-reg-time-statistics-btn').click(function(event){
            var start_date = first_reg_time_start_date_box.datebox('getValue');
            var end_date = first_reg_time_end_date_box.datebox('getValue');
            var grain = $('[name="first_reg_time_grain"]:checked').val();

            first_reg_time_chart.showLoading();
            UsedCarStatistics.getFirstRegTimeStatistics(start_date, end_date, grain).done(function(resp){
                if(resp.success)
                {
                    var categories = [];
                    var series_data = {};
                    $.each(resp.list, function(index, item){
                        categories.push(item.date);
                        $.each(item, function(key, value){
                            if(key == 'date') return;
                            series_data[key] = series_data[key] || [];
                            series_data[key].push(Number(value));
                        });
                    });
                    $.each(series_data, function(series_id, series_data){
                        if(first_reg_time_chart.get(series_id))
                        {
                            first_reg_time_chart.get(series_id).setData(series_data, false);
                        }
                    });
                    first_reg_time_chart.xAxis[0].setCategories(categories, false);
                    first_reg_time_chart.redraw();
                    first_reg_time_chart.hideLoading();
                }
            });
        });

        /**
         * 数据相关
         */

        var UsedCarStatistics = {
            getStatusStatistics: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/used_car/getUsedCarStatus.json',
                    dataType: 'json',
                    processData: false,
                    global: true
                });
            },
            getBrandCarCountStatistics: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/used_car/getUsedCarBrandCarCountStatistics.json',
                    dataType: 'json',
                    processData: false,
                    global: true
                });
            },
            getPriceCountStatistics: function(start_date, end_date){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/used_car/getUsedCarPriceCountStatistics.json',
                    dataType: 'json',
                    contentType: 'application/json',
                    global: true
                });
            },
            getFeedback: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/used_car/getUsedCarFeedback.json',
                    dataType: 'json',
                    global: true
                });
            },
            getChargeTrendDWTStatistics: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/used_car/getUsedCarChargeTrendDWT.json',
                    dataType: 'json',
                    processData: false,
                    global: true
                });
            },
            getChargeTrendUseCountListStatistics: function(start_date, end_date){
                return $.ajax({
                    method: 'post',
                    data: JSON.stringify({start_date: start_date, end_date: end_date}),
                    url: '/statistics/used_car/getUsedCarChargeTrendUseCountList.json',
                    dataType: 'json',
                    contentType: 'application/json',
                    global: true
                });
            },
            getFirstRegTimeStatistics: function(start_date, end_date, grain){
                return $.ajax({
                    method: 'post',
                    data: JSON.stringify({start_date: start_date, end_date: end_date}),
                    url: '/statistics/used_car/getUsedCarFirstRegTimeStatistics.json',
                    dataType: 'json',
                    contentType: 'application/json',
                    global: true
                });
            }
        };

        var used_car_status_statistics_value_tpl = _.template($('#used_car_status_statistics_value_tpl').html(), {variable: 'data'});
        var used_car_price_count_value_tpl = _.template($('#used_car_price_count_value_tpl').html(), {variable: 'data'});
        var used_car_trend_use_count_list_tpl = _.template($('#used_car_trend_use_count_list_tpl').html(), {variable: 'list'});
        var used_car_charge_price_count_value_tpl = _.template($('#used_car_charge_price_count_value_tpl').html(), {variable: 'data'});
        var used_car_charge_trend_use_count_list_tpl = _.template($('#used_car_charge_trend_use_count_list_tpl').html(), {variable: 'list'});

        UsedCarStatistics.getStatusStatistics().done(function(resp){
            if(resp.success)
            {
                $('#used_car_status_statistics_value_container').append(used_car_status_statistics_value_tpl(resp.list[0]));
            }
        });

        UsedCarStatistics.getBrandCarCountStatistics().done(function(resp){
            if(resp.success)
            {
                used_car_brand_statistics_grid.datagrid('loadData', resp.list);
            }
        });


        UsedCarStatistics.getPriceCountStatistics().done(function(resp){
            if(resp.success)
            {
                $('#used_car_price_count_value_container').append(used_car_price_count_value_tpl(resp.list[0]));
            }
        });

        UsedCarStatistics.getFirstRegTimeStatistics(first_reg_time_start_date_box.get('getValue'), first_reg_time_end_date_box.get('getValue'), 'day').done(function(resp){
            if(resp.success)
            {
                var categories = [];
                var series_data = {};
                $.each(resp.list, function(index, item){
                    categories.push(item.date);
                    $.each(item, function(key, value){
                        if(key == 'date') return;
                        series_data[key] = series_data[key] || [];
                        series_data[key].push(Number(value));
                    });
                });
                $.each(series_data, function(series_id, series_data){
                    if(first_reg_time_chart.get(series_id))
                    {
                        first_reg_time_chart.get(series_id).setData(series_data, false);
                    }
                });
                first_reg_time_chart.xAxis[0].setCategories(categories, false);
                first_reg_time_chart.redraw();
            }
        });

        CarMate.page.on_leave = function(){

        };
    };
</script>
<script type="text/html" id="used_car_status_statistics_value_tpl">
    <td><%=data.login_app_total%></td>
    <td><%=data.login_app_user_total%></td>
    <td><%=data.eval_price_total%></td>
    <td><%=data.eval_price_user_total%></td>
    <td><%=data.eval_price_detail_total%></td>
    <td><%=data.eval_price_detial_user_total%></td>
</script>

<script type="text/html" id="used_car_price_count_value_tpl">
    <td><%=data['x-10']%></td>
    <td><%=data['10-x-15']%></td>
    <td><%=data['15-x-20']%></td>
    <td><%=data['20-x-25']%></td>
    <td><%=data['25-x-30']%></td>
    <td><%=data['30-x']%></td>
</script>
<script type="text/html" id="used_car_trend_use_count_list_tpl">
    <%
        var len = list.length;
        for(var i = 0; i < len; i++){
    %>
        <tr>
            <td><%=list[i].date%></td>
            <td><%=list[i].day_use_count%></td>
            <td><%=list[i].day_user_count%></td>
            <td><%=list[i].day_new_user_count%></td>
            <td><%=list[i].day_add_car_count%></td>
            <td><%=list[i].day_appeal_count%></td>
        </tr>
    <%
        }
    %>
</script>
<script type="text/html" id="used_car_charge_price_count_value_tpl">
    <td><%=data.today_fee_count%></td>
    <td><%=data.today_pay_count%></td>
    <td><%=data.today_pay_user_count%></td>
    <td><%=data.week_fee_count%></td>
    <td><%=data.week_pay_count%></td>
    <td><%=data.week_pay_user_count%></td>
    <td><%=data.total_fee_count%></td>
    <td><%=data.total_pay_count%></td>
    <td><%=data.total_pay_user_count%></td>
</script>
<script type="text/html" id="used_car_charge_trend_use_count_list_tpl">
    <%
    var len = list.length;
    for(var i = 0; i < len; i++){
    %>
    <tr>
        <td><%=list[i].date%></td>
        <td><%=list[i].day_fee_count%></td>
        <td><%=list[i].day_use_count%></td>
        <td><%=list[i].day_pay_count%></td>
        <td><%=list[i].day_user_count%></td>
        <td><%=list[i].day_call_time%></td>
        <td><%=list[i].day_call_bill%></td>
    </tr>
    <%
    }
    %>
</script>
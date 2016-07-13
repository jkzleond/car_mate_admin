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
                    <th>新增申诉</th>
                    <th>总通话时长</th>
                    <th>总通话费用</th>
                    <th>总充值余额</th>
                    <th>支付宝收款</th>
                    <th>微信收款</th>
                </tr>
                <tr id="move_car_status_statistics_value_container" style="font-size: 40px; font-weight: bold;">

                </tr>
            </table>
            <table class="table">
                <tr>
                    <th class="left">挪车服务趋势</th>
                </tr>
                <tr>
                    <td class="narrow">
                        <table class="table center">
                            <tr>
                                <th>今日使用次数</th>
                                <th>今日使用人数</th>
                                <th>今日绑定车辆数</th>
                                <th>本周使用次数</th>
                                <th>本周使用人数</th>
                                <th>本周绑定车辆数</th>
                                <th>总使用次数</th>
                                <th>总使用人数</th>
                                <th>总绑定车辆数</th>
                            </tr>
                            <tr id="move_car_trend_dwt_value_container" style="font-size: 32px; font-weight: bold;"></tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="narrow">
                        <table class="table">
                            <tr>
                                <th class="span6">使用量详细列表</th>
                                <td class="span6">
                                    <div class="row-fluid">
                                        <div class="span9 pull-right">
                                            <span class="label">日期</span>
                                            <input name="trend_start_date" type="text" class="input input-small easyui-datebox" value="{{ start_date }}" />
                                            -
                                            <input name="trend_end_date" type="text" class="input input-small easyui-datebox" value="{{ end_date }}" />
                                            <button class="btn btn-primary pull-right tend-use-count-statistics-btn" title="统计"><i class="iconfa-bar-chart"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="narrow">

                                    <div class="row-fluid" style="max-height:400px; overflow-y: auto; overflow-x: hidden">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>日期</th>
                                                    <th>日使用次数</th>
                                                    <th>日使用人数</th>
                                                    <th>日新增使用人数</th>
                                                    <th>日绑定车数</th>
                                                    <th>日申诉数</th>
                                                </tr>
                                            </thead>
                                            <tbody id="move_car_trend_use_count_list_container">

                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table class="table">
                <tr>
                    <th class="left">挪车服务操作统计</th>
                </tr>
                <tr>
                    <td>
                        <div class="row-fluid">
                            <div class="span2"></div>
                            <div class="span4">
                                <span class="label">时间粒度</span>
                                <input type="radio" name="op_count_grain" value="hour">小时
                                <input type="radio" name="op_count_grain" value="day" checked>天
                                <input type="radio" name="op_count_grain" value="week">周
                                <input type="radio" name="op_count_grain" value="month">月
                                <input type="radio" name="op_count_grain" value="year">年
                            </div>
                            <div class="span4">
                                <span class="label">日期</span>
                                <input name="op_count_start_date" type="text" class="input input-small easyui-datebox" value="{{ start_date }}" />
                                -
                                <input name="op_count_end_date" type="text" class="input input-small easyui-datebox" value="{{ end_date }}" />
                            </div>
                            <div class="span2">
                                <button class="btn btn-primary pull-right move-car-op-count-statistics-btn" title="统计"><i class="iconfa-bar-chart"></i></button>
                            </div>
                        </div>
                        <div class="row-fluid">
                            <div id="move_car_op_count_chart_container"></div>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="table">
                <tr><th colspan="2">挪车提醒反馈统计</th></tr>
                <tr>
                    <th style="width: 50%">车主来挪车了吗</th>
                    <td>
                        <div id="charts_container1"></div>
                    </td>
                </tr>
                <tr>
                    <th style="width: 50%">在使用过程中遇到的问题</th>
                    <td>
                        <div id="charts_container2"></div>
                    </td>
                </tr>
                <tr>
                    <th style="width: 50%">此项服务收费10元您还会使用该服务吗</th>
                    <td>
                        <div id="charts_container3"></div>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">意见</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="move_car_advise_grid"></div>
                    </td>
                </tr>
            </table>
            <table class="table">
                <tr>
                    <th class="left">挪车服务收费趋势</th>
                </tr>
                <tr>
                    <td class="narrow">
                        <table class="table center">
                            <tr>
                                <th>今日收款金额</th>
                                <th>今日付款次数</th>
                                <th>今日付款人数</th>
                                <th>本周收款金额</th>
                                <th>本周付款次数</th>
                                <th>本周付款人数</th>
                                <th>总使收款金额</th>
                                <th>总使付款次数</th>
                                <th>总绑付款人数</th>
                            </tr>
                            <tr id="move_car_charge_trend_dwt_value_container" style="font-size: 32px; font-weight: bold;"></tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="narrow">
                        <table class="table">
                            <tr>
                                <th class="span6">使用量详细列表</th>
                                <td class="span6">
                                    <div class="row-fluid">
                                        <div class="span9 pull-right">
                                            <span class="label">日期</span>
                                            <input name="charge_trend_start_date" type="text" class="input input-small easyui-datebox" value="{{ start_date }}" />
                                            -
                                            <input name="charge_trend_end_date" type="text" class="input input-small easyui-datebox" value="{{ end_date }}" />
                                            <button class="btn btn-primary pull-right charge_trend-use-count-statistics-btn" title="统计"><i class="iconfa-bar-chart"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="narrow">

                                    <div class="row-fluid" style="max-height:400px; overflow-y: auto; overflow-x: hidden">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>日期</th>
                                                <th>日收款金额</th>
                                                <th>日使用次数</th>
                                                <th>日付款次数</th>
                                                <th>日付款人数</th>
                                                <th>日通话时长</th>
                                                <th>日通话费用</th>
                                            </tr>
                                            </thead>
                                            <tbody id="move_car_charge_trend_use_count_list_container">

                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
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

        var op_count_start_date_box = $('[name=op_count_start_date]').datebox({
            required: true
        });

        var op_count_end_date_box = $('[name=op_count_end_date]').datebox({
            required: true
        });


        $('.datebox input').keydown(function(event){
            event.preventDefault();
            return false;
        });

        //定义charts控件
        var options = {
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
                    return '<b>'+ this.point.name +'</b></br>' + "百分比:" +this.percentage.toFixed(2) +' %';
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
                id: 'problem',
                type: 'pie',
                name: '问题',
                data: [

                ]
            }]
        };

        var options2 = {
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
                    return '<b>'+ this.point.name +'</b></br>' + "百分比:" +this.percentage.toFixed(2) +' %';
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
                id: 'problem',
                type: 'pie',
                name: '问题',
                data: [

                ]
            }]
        };

        var options3 = {
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
                    return '<b>'+ this.point.name +'</b></br>' + "百分比:" +this.percentage.toFixed(2) +' %';
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
                id: 'problem',
                type: 'pie',
                name: '问题',
                data: [

                ]
            }]
        };

        $('#charts_container1').highcharts(options);
        var pie_chart1 = $('#charts_container1').highcharts();
        $('#charts_container2').highcharts(options2);
        var pie_chart2 = $('#charts_container2').highcharts();
        $('#charts_container3').highcharts(options3);
        var pie_chart3 = $('#charts_container3').highcharts();


        var op_count_chart_options = {
            chart: {
                type: $('#chart_type').val(),
                height: 300,
                backgroundColor: 'none',
                zoomType: 'xy'
            },
            title: {
                text: '挪车业务操作统计'
            },
            xAxis: {
                categories: []
            },
            yAxis: {
                title: {
                    text: '操作数'
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
                    id: 'submit_order',
                    name: '提交订单',
                    data: []
                },
                {
                    id: 'order_pay',
                    name: '订单支付',
                    data: []
                },
                {
                    id: 'select_ticket',
                    name: '选择票券',
                    data: []
                },
                {
                    id: 'notify',
                    name: '通知车主',
                    data: []
                },
                {
                    id: 'notify_again',
                    name: '再次通知车主',
                    data: []
                },
                {
                    id: 'feedback',
                    name: '反馈页面',
                    data: []
                },
                {
                    id: 'submit_ticket',
                    name: '提交反馈',
                    data: []
                },
                {
                    id: 'car_list',
                    name: '查看车辆',
                    data: []
                },
                {
                    id: 'modify_car',
                    name: '编辑车辆',
                    data: []
                },
                {
                    id: 'delete_car',
                    name: '删除车辆',
                    data: []
                },
                {
                    id: 'check_available_tickets',
                    name: '查看可用票券',
                    data : []
                },
                {
                    id: 'check_expired_tickets',
                    name: '查看过期票券',
                    data : []
                },
                {
                    id: 'modify_phone',
                    name: '修改手机号',
                    data: []
                },
                {
                    id: 'wx_share',
                    name: '微信分享',
                    data: []
                }
            ]
        };

        $('#move_car_op_count_chart_container').highcharts(op_count_chart_options);
        var op_count_chart = $('#move_car_op_count_chart_container').highcharts();

        //意见表格
        var move_car_advise_grid = $('#move_car_advise_grid').datagrid({
            url: '/move_car/advise.json',
            title: '意见列表',
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
            toolbar: '#move_car_co_grid_tb',
            idField: 'id',
            columns:[[
                {field:'create_date', title:'填写时间', width:'16%', align:'center'},
                {field:'user_id', title:'用户名', width:'16%', align:'center'},
                {field:'hphm', title:'车牌号', width:'16%', align:'center'},
                {field:'phone', title:'联系电话', width:'16%', align:'center'},
                {field:'other', title:'其他问题', width:'16%', align:'center'},
                {field:'advise', title:'意见建议', width:'16%', align:'center'}
            ]]
        });


        /*事件相关*/

        //查询按钮事件
        $("#finder_btn").click(function(event){
            event.preventDefault();
            var condition = {};
            condition.start_date = start_date_box.datebox('getValue');
            condition.end_date = end_date_box.datebox('getValue');
            condition.group_type = $('#move_car_statistics_form [name=group_type]:checked').val();
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

            MoveCarStatistics.getTrendUseCountListStatistics(start_date, end_date).done(function(resp){
                if(resp.success)
                {
                    $('#move_car_trend_use_count_list_container').empty().append(move_car_trend_use_count_list_tpl(resp.list));
                }
            });
        });

        //挪车业务收费趋势,使用量列表统计按钮点击事件
        $('.charge_trend-use-count-statistics-btn').click(function(event){
            var start_date = charge_trend_start_date_box.datebox('getValue');
            var end_date = charge_trend_end_date_box.datebox('getValue');

            MoveCarStatistics.getChargeTrendUseCountListStatistics(start_date, end_date).done(function(resp){
                if(resp.success)
                {
                    $('#move_car_charge_trend_use_count_list_container').empty().append(move_car_charge_trend_use_count_list_tpl(resp.list));
                }
            });
        });

        //挪车业务操作统计按钮点击时间
        $('.move-car-op-count-statistics-btn').click(function(event){
            var start_date = op_count_start_date_box.datebox('getValue');
            var end_date = op_count_end_date_box.datebox('getValue');
            var grain = $('[name="op_count_grain"]:checked').val();

            op_count_chart.showLoading();
            MoveCarStatistics.getOpCountStatistics(start_date, end_date, grain).done(function(resp){
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
                        if(op_count_chart.get(series_id))
                        {
                            op_count_chart.get(series_id).setData(series_data, false);
                        }
                    });
                    op_count_chart.xAxis[0].setCategories(categories, false);
                    op_count_chart.redraw();
                    op_count_chart.hideLoading();
                }
            });
        });

        /**
         * 数据相关
         */

        var MoveCarStatistics = {
            getStatusStatistics: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/move_car/getMoveCarStatus.json',
                    dataType: 'json',
                    processData: false,
                    global: true
                });
            },
            getTrendDWTStatistics: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/move_car/getMoveCarTrendDWT.json',
                    dataType: 'json',
                    processData: false,
                    global: true
                });
            },
            getTrendUseCountListStatistics: function(start_date, end_date){
                return $.ajax({
                    method: 'post',
                    data: JSON.stringify({start_date: start_date, end_date: end_date}),
                    url: '/statistics/move_car/getMoveCarTrendUseCountList.json',
                    dataType: 'json',
                    contentType: 'application/json',
                    global: true
                });
            },
            getFeedback: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/move_car/getMoveCarFeedback.json',
                    dataType: 'json',
                    global: true
                });
            },
            getChargeTrendDWTStatistics: function(){
                return $.ajax({
                    method: 'get',
                    url: '/statistics/move_car/getMoveCarChargeTrendDWT.json',
                    dataType: 'json',
                    processData: false,
                    global: true
                });
            },
            getChargeTrendUseCountListStatistics: function(start_date, end_date){
                return $.ajax({
                    method: 'post',
                    data: JSON.stringify({start_date: start_date, end_date: end_date}),
                    url: '/statistics/move_car/getMoveCarChargeTrendUseCountList.json',
                    dataType: 'json',
                    contentType: 'application/json',
                    global: true
                });
            },
            getOpCountStatistics: function(start_date, end_date, grain){
                return $.ajax({
                    method: 'post',
                    data: JSON.stringify({start_date: start_date, end_date: end_date, grain: grain}),
                    url: '/statistics/move_car/getMoveCarUserOpCount.json',
                    dataType: 'json',
                    contentType: 'application/json',
                    global: true
                });
            }
        };

        var move_car_status_statistics_value_tpl = _.template($('#move_car_status_statistics_value_tpl').html(), {variable: 'data'});
        var move_car_trend_dwt_value_tpl = _.template($('#move_car_trend_dwt_value_tpl').html(), {variable: 'data'});
        var move_car_trend_use_count_list_tpl = _.template($('#move_car_trend_use_count_list_tpl').html(), {variable: 'list'});
        var move_car_charge_trend_dwt_value_tpl = _.template($('#move_car_charge_trend_dwt_value_tpl').html(), {variable: 'data'});
        var move_car_charge_trend_use_count_list_tpl = _.template($('#move_car_charge_trend_use_count_list_tpl').html(), {variable: 'list'});

        MoveCarStatistics.getStatusStatistics().done(function(resp){
            if(resp.success)
            {
                $('#move_car_status_statistics_value_container').append(move_car_status_statistics_value_tpl(resp.list[0]));
            }
        });

        MoveCarStatistics.getTrendDWTStatistics().done(function(resp){
            if(resp.success)
            {
                $('#move_car_trend_dwt_value_container').append(move_car_trend_dwt_value_tpl(resp.list[0]));
            }
        });


        MoveCarStatistics.getTrendUseCountListStatistics(trend_start_date_box.datebox('getValue'), trend_end_date_box.datebox('getValue')).done(function(resp){
            if(resp.success)
            {
                $('#move_car_trend_use_count_list_container').empty().append(move_car_trend_use_count_list_tpl(resp.list));
            }
        });

        MoveCarStatistics.getFeedback().done(function(resp){
            if(resp.success)
            {
                var data = resp.list[0];
                pie_chart1.get('problem').setData([
                    ['是', Number(data.success_count)],
                    ['否', Number(data.fail_count)]
                ]);
                pie_chart2.get('problem').setData([
                    ['车主不来挪车', Number(data.czbl)],
                    ['车主来的有些晚', Number(data.czlw)],
                    ['没有遇到问题', Number(data.success_count)],
                    ['其他', Number(data.other)]
                ]);
                pie_chart3.get('problem').setData([
                    ['愿意', Number(data.q2_1)],
                    ['再便宜点可以接受', Number(data.q2_2)],
                    ['收费就不用了', Number(data.q2_3)]
                ]);
            }
        });

        MoveCarStatistics.getChargeTrendDWTStatistics().done(function(resp){
            if(resp.success)
            {
                $('#move_car_charge_trend_dwt_value_container').append(move_car_charge_trend_dwt_value_tpl(resp.list[0]));
            }
        });

        MoveCarStatistics.getChargeTrendUseCountListStatistics(trend_start_date_box.datebox('getValue'), trend_end_date_box.datebox('getValue')).done(function(resp){
            if(resp.success)
            {
                $('#move_car_charge_trend_use_count_list_container').empty().append(move_car_charge_trend_use_count_list_tpl(resp.list));
            }
        });

        MoveCarStatistics.getOpCountStatistics(op_count_start_date_box.get('getValue'), op_count_end_date_box.get('getValue'), 'day').done(function(resp){
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
                    if(op_count_chart.get(series_id))
                    {
                        op_count_chart.get(series_id).setData(series_data, false);
                    }
                });
                op_count_chart.xAxis[0].setCategories(categories, false);
                op_count_chart.redraw();
            }
        });

        CarMate.page.on_leave = function(){

        };
    };
</script>
<script type="text/html" id="move_car_status_statistics_value_tpl">
    <td><%=data.new_appeal%></td>
    <td><%=data.total_call_time%></td>
    <td><%=data.total_call_bill%></td>
    <td><%=data.total_recharge%></td>
    <td><%=data.total_alipay%></td>
    <td><%=data.total_wxpay%></td>
</script>

<script type="text/html" id="move_car_trend_dwt_value_tpl">
    <td><%=data.today_use_count%></td>
    <td><%=data.today_user_count%></td>
    <td><%=data.today_add_car_count%></td>
    <td><%=data.week_use_count%></td>
    <td><%=data.week_user_count%></td>
    <td><%=data.week_add_car_count%></td>
    <td><%=data.total_use_count%></td>
    <td><%=data.total_user_count%></td>
    <td><%=data.total_add_car_count%></td>
</script>
<script type="text/html" id="move_car_trend_use_count_list_tpl">
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
<script type="text/html" id="move_car_charge_trend_dwt_value_tpl">
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
<script type="text/html" id="move_car_charge_trend_use_count_list_tpl">
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
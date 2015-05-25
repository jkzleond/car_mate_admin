<div class="row-fluid">
    <div id="dashboard-left" class="span8">

        <h5 class="subtitle">系统信息</h5>
        <ul class="shortcuts">
            <li class="events">
                <a href="">
                    <span class="shortcuts-icon iconsi-event"></span>
                    <span class="shortcuts-label"><?php echo date('Y-m-d'); ?></span>
                </a>
            </li>
            <li class="archive">
                <a href="">
                    <span class="shortcuts-icon iconsi-archive"></span>
                    <span class="shortcuts-label">违章查询验证码获取： <?php if ($code_status == '200') { ?>良好<?php } else { ?>异常<?php } ?> </span>
                </a>
            </li>
            <li class="help">
                <a href="">
                    <span class="shortcuts-icon iconsi-help"></span>
                    <span class="shortcuts-label">首页访问状态： <?php if ($main_status == '200') { ?>良好<?php } else { ?>异常<?php } ?> </span>
                </a>
            </li>
        </ul>

        <br />

        <h5 class="subtitle">用户数统计</h5><br />
        <div id="chartplace" style="height:300px;"></div>
        <div class="divider30"></div>
        <br />

        <h4 class="widgettitle"><span class="icon-comment icon-white"></span> 微博更新状况 </h4>
        <div class="widgetcontent nopadding">
            <table width="100%" style="text-align:center;;font-size:14px;line-height:30px;" border="0" cellpadding="0" cellspacing="1" bgcolor="#cccccc">
                <tr>
                    <td bgcolor="#333333" style="color:#ffffff">省份</td>
                    <td bgcolor="#333333" style="color:#ffffff">微博更新状况</td>
                </tr>
                <?php foreach ($interactions as $interaction) { ?>
                <tr>
                    <td bgcolor="#FFFFFF" ><?php echo $interaction->province_name; ?></td>
                    <td bgcolor="#FFFFFF">微博超过一小时没更新，最近更新时间为:<?php echo date('Y-m-d H:i:s', strtotime($interaction->publish_time)); ?></td>
                </tr>
                <?php } ?>
            </table>









        </div>

        <br />


    </div><!--span8-->

    <div id="dashboard-right" style="margin-top:37px;" class="span4">
        <h4 class="widgettitle">违章更新时间 </h4>
        <div class="widgetcontent nopadding">
            <table width="100%" style="text-align:center;;font-size:14px;line-height:30px;" border="0" cellpadding="0" cellspacing="1" bgcolor="#cccccc">
                <tr>
                    <td bgcolor="#333333" style="color:#ffffff">省份</td>
                    <td bgcolor="#333333" style="color:#ffffff">省份简称</td>
                    <td bgcolor="#333333" style="color:#ffffff">违章更新时间</td>
                </tr>
                <?php foreach ($violations as $violation) { ?>
                <tr>
                    <td bgcolor="#FFFFFF" ><?php echo $violation->province_name; ?></td>
                    <td bgcolor="#FFFFFF"><?php echo $violation->province; ?></td>
                    <td bgcolor="#FFFFFF">
                        <?php if (empty($violation->max_time)) { ?>

                        <?php } else { ?>
                            <?php echo date('Y-m-d H:i:s', strtotime($violation->max_time)); ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <!--tabbedwidget-->
            <br />
        </div><!--span4-->
    </div><!--row-fluid-->

    <div class="footer">
        <div class="footer-left">
            <span></span>
        </div>
        <div class="footer-right">
            <span></span>
        </div>
    </div><!--footer-->

    <script type="text/javascript">
        jQuery(document).ready(function() {

            // simple chart
            var android = [];
            var iPhone = [];
            var iPad = [];
            var iPodTouch = [];
            var windowsPhone = [];
            var other = [];

            function showTooltip(x, y, contents) {
                jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css({
                    position: 'absolute',
                    display: 'none',
                    top: y + 5,
                    left: x + 5
                }).appendTo("body").fadeIn(1000);
            }


            jQuery("#chartplace").bind("plotclick", function (event, pos, item) {
                if (item) {
                    jQuery("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
                    plot.highlight(item.series, item.datapoint);
                }
            });


            //datepicker
            jQuery('#datepicker').datepicker();

            // tabbed widget
            jQuery('.tabbedwidget').tabs();

            /**
             * 获取今日用户注册量
             */
            var today = new Date();
            var year = String(today.getFullYear());
            var month = String(today.getMonth() + 1);
            var day = String(today.getDate());

            month = month.length < 2 ? '0' + month : month;
            day = day.length < 2 ? '0' + day : day;

            var today_str = year + '-' + month + '-' + day;

            /*jQuery.get('/statistics/userStatistics/' + today_str + '/' + today_str + '/hours.json', null, 'json')

             });*/

            jQuery.ajax({
                url: '/statistics/userStatistics/' + today_str + '/' + today_str + '/hours.json',
                dataType: 'json',
                method: 'GET',
                global: true
            }).done(function (data) {
                var rows = data.rows;
                var x_ticks = [];
                $.each(rows, function (index, item) {
                    x_ticks.push([index, item.date + '点']);
                    android.push([index, item.android]);
                    iPhone.push([index, item.iPhone]);
                    iPad.push([index, item.iPad]);
                    iPodTouch.push([index, item.iPodTouch]);
                    windowsPhone.push([index, item.windowsPhone]);
                    other.push([index, item.other]);
                });

                var plot = jQuery.plot(jQuery("#chartplace"),
                    [
                        {data: android, label: "andriod", color: "#6fad04"},
                        {data: iPhone, label: "iPhone", color: "#ad6f04"},
                        {data: iPad, label: "iPad", color: "#c06"},
                        {data: iPodTouch, label: "iPodTouch", color: "#0c6"},
                        {data: windowsPhone, label: "WindowsPhone", color: "#06c"},
                        {data: other, label: "其他", color: "#6f04ad"}
                    ], {
                        series: {
                            lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.05}, {opacity: 0.15}]}},
                            points: {show: true}
                        },
                        legend: {position: 'nw'},
                        grid: {hoverable: true, clickable: true, borderColor: '#666', borderWidth: 2, labelMargin: 10},
                        xaxis: {ticks: x_ticks},
                        yaxis: {ticks: 3}
                    });

                var previousPoint = null;
                jQuery("#chartplace").bind("plothover", function (event, pos, item) {
                    jQuery("#x").text(pos.x);
                    jQuery("#y").text(pos.y);

                    if (item) {
                        if (previousPoint != item.dataIndex) {
                            previousPoint = item.dataIndex;

                            jQuery("#tooltip").remove();
                            var x = item.datapoint[0],
                                y = item.datapoint[1];

                            showTooltip(item.pageX, item.pageY,
                                item.series.label + " 在: " + x + " 点注册数 " + y);
                        }

                    } else {
                        jQuery("#tooltip").remove();
                        previousPoint = null;
                    }
                });
            });
        });
    </script>
<!DOCTYPE html>
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>车友惠| </title>
        

        <link rel="stylesheet" href="<?php echo $this->url->get('/css/style.default.css'); ?>" type="text/css" />
        <script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-easyui/jquery.min.js'); ?>"></script>

<!--        <script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-1.9.1.min.js'); ?>"></script>-->
        <script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-migrate-1.1.1.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-ui-1.9.2.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo $this->url->get('/js/modernizr.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo $this->url->get('/js/bootstrap.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo $this->url->get('/js/jquery.cookie.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo $this->url->get('/js/custom.js'); ?>"></script>
        
<link rel="stylesheet" href="<?php echo $this->url->get('/css/responsive-tables.css'); ?>">
<script type="text/javascript" src="<?php echo $this->url->get('/js/jquery.uniform.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/flot/jquery.flot.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/flot/jquery.flot.resize.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/responsive-tables.js'); ?>"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo $this->url->get('/js/excanvas.min.js'); ?>"></script><![endif]-->

<link rel="stylesheet" type="text/css" href="<?php echo $this->url->get('/js/jquery-easyui/themes/metro/easyui.css'); ?>">
<!--<link rel="stylesheet" type="text/css" href="<?php echo $this->url->get('/js/jquery-easyui/themes/icon.css'); ?>">-->

<script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-easyui/jquery.easyui.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-easyui/plugins/datagrid_detail.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-easyui/plugins/datagrid_groupview.js'); ?>"></script>

<script type="text/javascript" src="<?php echo $this->url->get('/js/jquery-easyui/locale/easyui-lang-zh_CN.js'); ?>"></script>

<script type="text/javascript" src="<?php echo $this->url->get('/js/highcharts/highcharts.js'); ?>"></script>

<!-- CKEdidtor -->
<script type="text/javascript" src="<?php echo $this->url->get('/js/ckfinder/ckfinder.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/ckeditor/ckeditor.js'); ?>"></script>


	</head>
	<body>
        

<div class="mainwrapper">

    <div class="header">
        <div class="logo">
            <a href=""><img src="<?php echo $this->url->get('/images/logo.png'); ?>" alt=""   /></a>
        </div>
        <div class="headerinner">
            <ul class="headmenu">
                <li class="odd">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="count">4</span>
                        <span class="head-icon head-message"></span>
                        <span class="headmenu-label">消息</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-header">Messages</li>
                        <li><a href=""><span class="icon-envelope"></span> New message from <strong>Jack</strong> <small class="muted"> - 19 hours ago</small></a></li>
                        <li><a href=""><span class="icon-envelope"></span> New message from <strong>Daniel</strong> <small class="muted"> - 2 days ago</small></a></li>
                        <li><a href=""><span class="icon-envelope"></span> New message from <strong>Jane</strong> <small class="muted"> - 3 days ago</small></a></li>
                        <li><a href=""><span class="icon-envelope"></span> New message from <strong>Tanya</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li><a href=""><span class="icon-envelope"></span> New message from <strong>Lee</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li class="viewmore"><a href="messages.html">View More Messages</a></li>
                    </ul>
                </li>
                <li>
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                        <span class="count">10</span>
                        <span class="head-icon head-users"></span>
                        <span class="headmenu-label">用户</span>
                    </a>
                    <ul class="dropdown-menu newusers">
                        <li class="nav-header">New Users</li>
                        <li>
                            <a href="">
                                <img src="<?php echo $this->url->get('/images/photos/thumb1.png'); ?>" alt="" class="userthumb" />
                                <strong>Draniem Daamul</strong>
                                <small>April 20, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="<?php echo $this->url->get('/images/photos/thumb2.png'); ?>" alt="" class="userthumb" />
                                <strong>Shamcey Sindilmaca</strong>
                                <small>April 19, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="<?php echo $this->url->get('/images/photos/thumb3.png'); ?>" alt="" class="userthumb" />
                                <strong>Nusja Paul Nawancali</strong>
                                <small>April 19, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="<?php echo $this->url->get('/images/photos/thumb4.png'); ?>" alt="" class="userthumb" />
                                <strong>Rose Cerona</strong>
                                <small>April 18, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="<?php echo $this->url->get('/images/photos/thumb5.png'); ?>" alt="" class="userthumb" />
                                <strong>John Doe</strong>
                                <small>April 16, 2013</small>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="odd">
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#">
                        <span class="count">1</span>
                        <span class="head-icon head-bar"></span>
                        <span class="headmenu-label">统计</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-header">Statistics</li>
                        <li><a href=""><span class="icon-align-left"></span> New Reports from <strong>Products</strong> <small class="muted"> - 19 hours ago</small></a></li>
                        <li><a href=""><span class="icon-align-left"></span> New Statistics from <strong>Users</strong> <small class="muted"> - 2 days ago</small></a></li>
                        <li><a href=""><span class="icon-align-left"></span> New Statistics from <strong>Comments</strong> <small class="muted"> - 3 days ago</small></a></li>
                        <li><a href=""><span class="icon-align-left"></span> Most Popular in <strong>Products</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li><a href=""><span class="icon-align-left"></span> Most Viewed in <strong>Blog</strong> <small class="muted"> - 1 week ago</small></a></li>
                        <li class="viewmore"><a href="charts.html">View More Statistics</a></li>
                    </ul>
                </li>
                <li class="right">
                    <div class="userloggedinfo">
                        <img src="<?php echo $this->url->get('/images/photos/thumb1.png'); ?>" alt="" />
                        <div class="userinfo">
                            <h5>
                                代用名
                                <small>
                                    <?php echo $user->user_id; ?>
                                </small>
                            </h5>
                            <ul>
                                <li><a href="#">用户信息</a></li>
                                <li><a href="">修改密码</a></li>
                                <li><a href="/logout">退出</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul><!--headmenu-->
        </div>
    </div>

    <div class="leftpanel">

        <div class="leftmenu">
            <ul class="nav nav-tabs nav-stacked">
                <li class="nav-header">导航</li>
                <li class="active"><a href=""><span class="iconfa-laptop"></span>功能菜单</a></li>
                <li class="dropdown" id="menu_to_main"><a href=""><span class="iconfa-hand-up"></span> 首页</a>
                    <ul>
                        <li><a href="/status">查看链接</a></li>
                        <li><a href="/ranks">APP排行</a></li>

                    </ul>
                </li>

                <li class="dropdown"><a href=""><span class="iconfa-pencil"></span> 统计</a>
                    <ul>
                        <?php if ($this->isIncluded('queryStatistics', $auth)) { ?>
                        <li><a href="/statistics/queryStatistics">访问统计</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('userStatistics', $auth)) { ?>
                        <li><a href="/statistics/userStatistics">注册用户统计</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('actStatistics', $auth)) { ?>
                        <li><a href="/statistics/actStatistics">操作统计</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('totalStatistics', $auth)) { ?>
                        <li><a href="/statistics/quTotalStatistics">总访问/用户统计</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('userActivity', $auth)) { ?>
                        <li><a href="/statistics/userActivityStatistics">用户活跃度统计</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('insuranceActStatistics', $auth)) { ?>
                        <li><a href="/statistics/firstPreliminaryCalculationStatistics">每日新增用户数(保险)</a></li>
                        <li><a href="/statistics/insuranceActStatistics">保险系统行为统计</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-briefcase"></span> 保险系统</a>
                    <ul>
                        <?php if ($this->isIncluded('insurance', $auth)) { ?>
                        <li><a href="/insuranceList">保险列表</a></li>
                        <li><a href="/insuranceCompany">保险公司管理</a></li>
                        <?php } ?>

                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-th-list"></span>本地惠</a>
                    <ul>
                        <?php if ($this->isIncluded('localFavourPush', $auth)) { ?>
                        <li><a href="/localFavourPush">本地惠发布</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('localFavourIndex', $auth)) { ?>
                        <li><a href="/localFavourAdvList">首页推广</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('localFavourSub', $auth)) { ?>
                        <li><a href="/localFavourSubList">本地惠稿件管理</a></li>
                        <li><a href="/localFavourScrollAd">滚动广告管理</a></li>
                        <li><a href="">汽车信息校对</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-picture"></span>活动营销</a>
                    <ul>
                        <?php if ($this->isIncluded('activityMng', $auth)) { ?>
                        <li><a href="/activityList">活动管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('activityUserMng', $auth)) { ?>
                        <li><a href="/activityUser">参与用户列表</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('awardListMng', $auth)) { ?>
                        <li><a href="/awardList">抽奖奖品</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('awardGainMng', $auth)) { ?>
                        <li><a href="/awardGainManage">中奖管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('voteListMng', $auth)) { ?>
                        <li><a href="/voteList">投票管理</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-book"></span>积分商城</a>
                    <ul>
                        <?php if ($this->isIncluded('itemMng', $auth)) { ?>
                        <li><a href="/itemManage">商品管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('transactionList', $auth)) { ?>
                        <li><a href="/transactionList">流水记录</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('dealList', $auth)) { ?>
                        <li><a href="/dealManage">订单管理</a></li>
                        <?php } ?>
                    </ul>
                </li>



                <li class="dropdown"><a href=""><span  class="iconfa-signal"></span>系统</a>
                    <ul>
                        <?php if ($this->isIncluded('welAdv', $auth)) { ?>
                        <li class="dropdown"><a href="/welcomeAdvList">开屏广告管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('notice', $auth)) { ?>
                        <li class="dropdown"><a href="/noticeManage">公告管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('oilPrice', $auth)) { ?>
                        <li class="dropdown"><a href="">油价管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('talkInfo', $auth)) { ?>
                        <li class="dropdown"><a href="">车友互动</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('interaction', $auth)) { ?>
                        <li class="dropdown"><a href="">路况管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('iosPush', $auth)) { ?>
                        <li class="dropdown"><a href="">消息推送</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('feedBack', $auth)) { ?>
                        <li class="dropdown"><a href="">意见反馈</a></li>
                        <li class="dropdown"><a href="/appException">异常信息</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('logQuery', $auth)) { ?>
                        <li class="dropdown"><a href="">查看日志</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('userManager', $auth)) { ?>
                        <li class="dropdown"><a href="">注册用户管理</a></li>
                        <?php } ?>
                        <?php if ($this->isIncluded('baiduMap', $auth)) { ?>
                        <li class="dropdown"><a href="">查看地图</a></li>
                        <li class="dropdown"><a href="">周边信息审核</a></li>
                        <?php } ?>
                    </ul>
                </li>

                <?php if ($this->isIncluded('adminUserManager', $auth)) { ?>
                <li class="dropdown"><a href=""><span class="iconfa-th-list"></span>后台管理</a>
                    <ul>
                        <li><a href="">管理员</a></li>
                    </ul>
                </li>
                <?php } ?>




            </ul>
        </div><!--leftmenu-->

    </div><!-- leftpanel -->

    <div class="rightpanel">

        <ul class="breadcrumbs">
            <li><a href="dashboard.html"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
            <li>首页</li>

        </ul>

        <div class="pageheader">
            <form id="change_province_form" action="" method="post" class="searchbar">
                当前城市：
                <select name="province_id">
                    <?php foreach ($province_list as $province) { ?>
                        <?php if ($this->isIncluded($province->id, $city_auth)) { ?>
                            <option value="<?php echo $province->id; ?>" <?php if ($province->id == $user->province_id) { ?>selected<?php } ?> ><?php echo $province->name; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </form>
            <div class="pageicon"><span class="iconfa-laptop"></span></div>
            <div class="pagetitle">
                <h5>
                    <?php if (empty($user->nick_name)) { ?>
                        <?php echo $user->u_name; ?>
                    <?php } else { ?>
                        <?php echo $user->nick_name; ?>
                    <?php } ?>
                </h5>
                <h1>欢迎您</h1>
            </div>
        </div><!--pageheader-->

        <div id="main_content" class="maincontent" style="position:relative; min-height:400px;">
            <div id="main_page_loading" class="loading hide" style="position:absolute;height:25px; width:200px; text-align: center; top: 0px; left: 0px; z-index:10">
                <div>
                    <img src="<?php echo $this->url->get('/images/loading.gif'); ?>" style="margin:auto; width:25px; height:25px"/>
                </div>
                <span>正在加载页面...</span>
            </div>
            <div id="main_page_container" class="maincontentinner container-fluid">
            </div><!--maincontentinner-->
        </div><!--maincontent-->

    </div><!--rightpanel-->

</div><!--mainwrapper-->
<script type="text/javascript" src="<?php echo $this->url->get('/js/car_mate/core.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/datejs/core.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/datejs/date.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $this->url->get('/js/datejs/parser.js'); ?>"></script>
<script type="text/javascript">
    CarMate.page.container = '#main_page_container';
    CarMate.page.on_loading = function(){
        var parent_width = $('#main_content').width();
        var parent_height = $('#main_content').height();
        var loading_width = $('#main_page_loading').width();
        var loading_height = $('#main_page_loading').height();
        $('#main_page_loading').css({left: (parent_width - loading_width)/2, top: (parent_height - loading_height)/2});
        //$('#main_page_loading').top(height/2);
        $('#main_page_loading').show();
    }
    CarMate.page.on_complete = function(){
        $('#main_page_loading').fadeOut(1000);
        $('#main_page_container').hide();
        $('#main_page_container').fadeIn(1000);
    }

    /**
     * ajax global setting
     */
    //base url setting

    var base_url = "<?php echo $this->url->get(''); ?>";/**
     *
     */

    $.ajaxPrefilter(function(options){
        options.url = base_url + options.url;
    });

</script>
<script type="text/javascript">

    (function($){
        /**
         * 菜单点击事件
         */
        $('.leftmenu .dropdown li a').click(function(event){
            event.preventDefault();
            var url = $(this).attr('href');
            if(url)
            {
                CarMate.page.load(url);
            }
            return false;
        });

        CarMate.bind('page:loaded', function(){
            console.log('page:loaded');
        });
        /**
         * 自动点击首页第一个菜单(初始页)
         */

        CarMate.page.load('/status');

        /**
         * 省份切换事件
         */
        $('#change_province_form [name=province_id]').change(function(event){
            $.post('/adminuser/changeProvince',{'province_id': $(this).val()},function(){
                CarMate.page.reload();
            });
        });

        //global ajax setting

        $.ajaxSetup({
            global: true,
            statusCode: {
                302: function(xhr){
                    var data = JSON.parse(xhr.responseText);
                    window.location.href = data.redirect;
                }
            }
        });




    })(jQuery)

</script>
    
	</body>
</html>
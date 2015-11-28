{% extends "base.volt" %}
{% block head_assets %}
{{super()}}
<link rel="stylesheet" href="{{ url('/css/responsive-tables.css') }}">
<script type="text/javascript" src="{{ url('/js/jquery.uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/flot/jquery.flot.min.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/flot/jquery.flot.resize.min.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/responsive-tables.js') }}"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="{{  url('/js/excanvas.min.js') }}"></script><![endif]-->

<link rel="stylesheet" type="text/css" href="{{ url('/js/jquery-easyui/themes/metro/easyui.css') }}">
<!--<link rel="stylesheet" type="text/css" href="{{ url('/js/jquery-easyui/themes/icon.css') }}">-->

<script type="text/javascript" src="{{ url('/js/jquery-easyui/jquery.easyui.min.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/jquery-easyui/plugins/datagrid_detail.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/jquery-easyui/plugins/datagrid_groupview.js') }}"></script>

<script type="text/javascript" src="{{ url('/js/jquery-easyui/locale/easyui-lang-zh_CN.js') }}"></script>

<script type="text/javascript" src="{{ url('/js/highcharts/highcharts.js') }}"></script>

<!-- CKEdidtor -->
<script type="text/javascript" src="{{ url('/js/ckfinder/ckfinder.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
    
</style>

{% endblock %}


{% block content %}

<div class="mainwrapper pin-container">

    <div class="header">
        <div class="logo">
            <a href=""><img src="{{ url('/images/logo.png') }}" alt=""   /></a>
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
                                <img src="{{ url('/images/photos/thumb1.png') }}" alt="" class="userthumb" />
                                <strong>Draniem Daamul</strong>
                                <small>April 20, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="{{  url('/images/photos/thumb2.png') }}" alt="" class="userthumb" />
                                <strong>Shamcey Sindilmaca</strong>
                                <small>April 19, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="{{  url('/images/photos/thumb3.png') }}" alt="" class="userthumb" />
                                <strong>Nusja Paul Nawancali</strong>
                                <small>April 19, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="{{  url('/images/photos/thumb4.png') }}" alt="" class="userthumb" />
                                <strong>Rose Cerona</strong>
                                <small>April 18, 2013</small>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <img src="{{  url('/images/photos/thumb5.png') }}" alt="" class="userthumb" />
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
                        <img src="{{  url('/images/photos/thumb1.png') }}" alt="" />
                        <div class="userinfo">
                            <h5>
                                代用名
                                <small>
                                    {{ user.user_id}}
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

        <div class="leftmenu pin-menu">
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
                        {% if 'queryStatistics' in auth %}
                        <li><a href="/statistics/queryStatistics">访问统计</a></li>
                        {% endif %}
                        {% if 'userStatistics' in auth %}
                        <li><a href="/statistics/userStatistics">注册用户统计</a></li>
                        {% endif%}
                        {% if 'actStatistics' in auth %}
                        <li><a href="/statistics/actStatistics">操作统计</a></li>
                        {% endif %}
                        {% if 'totalStatistics' in auth%}
                        <li><a href="/statistics/quTotalStatistics">总访问/用户统计</a></li>
                        {% endif %}
                        {% if 'userActivity' in auth%}
                        <li><a href="/statistics/userActivityStatistics">用户活跃度统计</a></li>
                        {% endif %}
                        {% if 'insuranceActStatistics' in auth %}
                        <li><a href="/statistics/firstPreliminaryCalculationStatistics">每日新增用户数(保险)</a></li>
                        <li><a href="/statistics/insuranceActStatistics">保险系统行为统计</a></li>
                        {% endif %}
                        {% if 'totalStatistics' in auth%}
                        <li><a href="/statistics/orderIllegalStatistics">违章代缴业务订单统计</a></li>
                        <li><a href="/statistics/orderIllegalUserStatistics">违章代缴业务用户统计</a></li>
                        <li><a href="/statistics/orderIllegalTrackStatistics">违章代缴业务追踪统计</a></li>
                        {% endif %}
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-briefcase"></span> 保险系统</a>
                    <ul>
                        {% if 'insurance' in auth %}
                        <li><a href="/insuranceList">车险列表</a></li>
                        <li><a href="/insuranceCompany">保险公司管理</a></li>
                        <li><a href="/insuranceType">险种管理</a></li>
                        <li><a href="/insuranceNewInfoManage">保险订单管理(全险种)</a></li>
                        <li><a href="/insuranceReservation">保险预约管理</a></li>
                        {% endif %}

                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-th-list"></span>本地惠</a>
                    <ul>
                        {% if 'localFavourPush' in auth %}
                        <li><a href="/localFavourPush">本地惠发布</a></li>
                        {% endif %}
                        {% if 'localFavourIndex' in auth %}
                        <li><a href="/localFavourAdvList">首页推广</a></li>
                        {% endif %}
                        {% if 'localFavourSub' in auth %}
                        <li><a href="/localFavourSubList">本地惠稿件管理</a></li>
                        <li><a href="/localFavourScrollAd">滚动广告管理</a></li>
                        <li><a href="">汽车信息校对</a></li>
                        {% endif %}
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-picture"></span>活动营销</a>
                    <ul>
                        {% if 'activityMng' in auth %}
                        <li><a href="/activityList">活动管理</a></li>
                        {% endif %}
                        {% if 'activityUserMng' in auth %}
                        <li><a href="/activityUser">参与用户列表</a></li>
                        {% endif %}
                        {% if 'awardListMng' in auth %}
                        <li><a href="/awardList">抽奖奖品</a></li>
                        {% endif %}
                        {% if 'awardGainMng' in auth %}
                        <li><a href="/awardGainManage">中奖管理</a></li>
                        {% endif %}
                        {% if 'voteListMng' in auth %}
                        <li><a href="/voteList">投票管理</a></li>
                        {% endif %}
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-book"></span>积分商城</a>
                    <ul>
                        {% if 'itemMng' in auth %}
                        <li><a href="/itemManage">商品管理</a></li>
                        {% endif %}
                        {% if 'transactionList' in auth %}
                        <li><a href="/transactionList">流水记录</a></li>
                        {% endif %}
                        {% if 'dealList' in auth %}
                        <li><a href="/dealManage">订单管理</a></li>
                        {% endif %}
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span class="iconfa-truck"></span>违章代缴</a>
                    <ul>
                        {% if 'dealList' in auth %}
                        <li><a href="/illegal/orderMng">订单管理</a></li>
                        <li><a href="/illegal/driverInfoMng">驾驶员信息</a></li>
                        <li><a href="/illegal/transactionList">流水列表</a></li>
                        {% endif %}
                    </ul>
                </li>
                <li class="dropdown"><a href=""><span  class="iconfa-signal"></span>系统</a>
                    <ul>
                        {% if 'welAdv' in auth %}
                        <li class="dropdown"><a href="/welcomeAdvList">开屏广告管理</a></li>
                        <li class="dropdown"><a href="/carService/manage">汽车服务内容</a></li>
                        {% endif %}
                        {% if 'notice' in auth %}
                        <li class="dropdown"><a href="/noticeManage">公告管理</a></li>
                        {% endif %}
                        {% if 'oilPrice' in auth %}
                        <li class="dropdown"><a href="">油价管理</a></li>
                        {% endif %}
                        {% if 'talkInfo' in auth %}
                        <li class="dropdown"><a href="">车友互动</a></li>
                        {% endif %}
                        {% if 'interaction' in auth %}
                        <li class="dropdown"><a href="">路况管理</a></li>
                        {% endif %}
                        {% if 'iosPush' in auth %}
                        <li class="dropdown"><a href="/push_message">消息推送</a></li>
                        {% endif %}
                        {% if 'feedBack' in auth %}
                        <li class="dropdown"><a href="">意见反馈</a></li>
                        <li class="dropdown"><a href="/appException">异常信息</a></li>
                        {% endif %}
                        {% if 'logQuery' in auth %}
                        <li class="dropdown"><a href="">查看日志</a></li>
                        {% endif %}
                        {% if 'userManager' in auth %}
                        <li class="dropdown"><a href="">注册用户管理</a></li>
                        {% endif %}
                        {% if 'baiduMap' in auth %}
                        <li class="dropdown"><a href="">查看地图</a></li>
                        <li class="dropdown"><a href="">周边信息审核</a></li>
                        {% endif %}
                    </ul>
                </li>
                {% if 'adminUserManager' in auth %}
                <li class="dropdown"><a href=""><span class="iconfa-th-list"></span>后台管理</a>
                    <ul>
                        <li><a href="">管理员</a></li>
                    </ul>
                </li>
                {% endif %}
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
                    {% for province in province_list %}
                        {% if province.id in city_auth %}
                            <option value="{{ province.id }}" {% if province.id == user.province_id %}selected{% endif %} >{{ province.name }}</option>
                        {% endif %}
                    {% endfor %}
                </select>
            </form>
            <div class="pageicon"><span class="iconfa-laptop"></span></div>
            <div class="pagetitle">
                <h5>
                    {% if user.nick_name is empty %}
                        {{ user.u_name }}
                    {% else %}
                        {{ user.nick_name }}
                    {% endif %}
                </h5>
                <h1>欢迎您</h1>
            </div>
        </div><!--pageheader-->

        <div id="main_content" class="maincontent" style="position:relative; min-height:400px;">
            <div id="main_page_loading" class="loading hide" style="position:absolute;height:25px; width:200px; text-align: center; top: 0px; left: 0px; z-index:10">
                <div>
                    <img src="{{  url('/images/loading.gif') }}" style="margin:auto; width:25px; height:25px"/>
                </div>
                <span>正在加载页面...</span>
            </div>
            <div id="main_page_container" class="maincontentinner container-fluid">
            </div><!--maincontentinner-->
        </div><!--maincontent-->

    </div><!--rightpanel-->

</div><!--mainwrapper-->
<script type="text/javascript" src="{{ url('/js/car_mate/core.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/datejs/core.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/datejs/date.js') }}"></script>
<script type="text/javascript" src="{{ url('/js/datejs/parser.js') }}"></script>
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

    var base_url = "{{ url('') }}";/**
     *
     */

    $.ajaxPrefilter(function(options){
        options.url = base_url + options.url;
    });

    //判断访问的是否fake后台端口
    if(location.port == '8094')
    {
        //为ajax获取的json数据类型添加data filter，filter接受的是原始字符串
        $.ajaxPrefilter('json', function(options){
            if(options.url.indexOf('/statistics') == 0)
            {
                options.dataFilter = function(data_str){
                    var data_object = JSON.parse(data_str);
                    if(!data_object.rows || data_object.rows.length < 1) return data_str;
                  
                    $.each(data_object.rows, function(index, item){
                        for(var prop in item)
                        { 
                          if(prop == 'id' || prop == 'date' || prop == 'clientVersion' || /(_id|Id)$/.test(prop) || !/^(\d+|\d+\.?\d+)$/.test(item[prop])) continue;

                          item[prop] = Math.round(item[prop]*7.5);
                        }   
                    });
                    return JSON.stringify(data_object);
                };
            }
            else
            {
                options.dataFilter = function(data_str){
                    var data_object = JSON.parse(data_str);
                    if(!data_object.rows || data_object.rows.length < 1)
                    {
                        return data_str;
                    }
                    else if(data_object.total)
                    {
                        data_object.total = Math.round(data_object.total*7.5);
                    }
                   return JSON.stringify(data_object);
                };    
            }    
        });
    }

    CarMate.viewManager = {
        _windows: {},
        _curUrl: null,
        _viewObjMap: {},
        onLoad: null,
        open: function(url){
            var win = this._windows[url];
            if(win)
            {
                win.window('open');
                return;
            }
            var win_id = Date.now();
            $('body').append('<div id="' + win_id + '" data-view-url="' + url + '"><div>');
            win = $('#'+win_id).window({
                width: '80%',
                height: '80%',
                closed: true,
                shadow: false,
                modal: false,
                openAnimation: 'fade',
                onOpen: function(){
                    $(this).window('center');
                },
                onLoad: function(){
                    var opt = $(this).window('options');
                    var resize_width = opt.width;
                    var resize_height = opt.height;

                    var $panel = $(this).window('panel');
                    var panel_width = $panel.outerWidth();
                    var panel_height = $panel.outerHeight();
                    var client_width = window.innerWidth;
                    var client_height = window.innerHeight;

                    if(client_width < panel_width)
                    {
                        resize_width = Math.round(client_width * 0.8);
                    }

                    if(client_height < panel_height)
                    {
                        resize_height = Math.round(client_height * 0.8);
                    }

                    $(this).window('resize', {
                        width: resize_width,
                        height: resize_height
                    });


                    if(typeof CarMate.viewManager.onLoad == 'function')
                    {
                        CarMate.viewManager.onLoad($(this));
                    }

                    if(typeof CarMate.viewManager.onLeave == 'function')
                    {
                        var onLeave = CarMate.viewManager.onLeave;
                        var opt = $(this).window('options');
                        opt.onBeforeClose = function(){
                            onLeave.call($(this));
                        };
                        $(this).window(opt);
                    }

                    $(this).window('center').window('open', true);
                },
                onClose: function(){
                    $(this).window('destroy');
                    CarMate.viewManager.dispose(url);
                }
            });
            this._windows[url] = win;
            win.window('refresh', url);
        },
        dispose: function(url)
        {
            delete this._windows[url];
        }
    };
</script>
<script type="text/javascript">

    (function($){
        /**
         * 固定菜单栏
         */
        $('.pin-menu').pin({
            containerSelector: '.pin-container'
        });

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
    {% endblock %}
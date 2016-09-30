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

    <div class="leftpanel">

        <div class="leftmenu pin-menu">
            <ul class="nav nav-tabs nav-stacked">
                <li class="nav-header">导航</li>
                <li class="active"><a href=""><span class="iconfa-laptop"></span>功能菜单</a></li>
                {% if 'gygd' in auth %}
                <li class="dropdown"><a href=""><span class="iconfa-picture"></span>古韵官渡</a>
                    <ul>
                        {% if 'stadium' in auth %}
                        <li><a href="/gygd/stadium">体育惠民一本通抽奖</a></li>
                        {% endif %}
                        {% if 'museum' in auth %}
                        <li><a href="/gygd/museum">博物馆集戳有奖</a></li>
                        {% endif %}
                    </ul>
                </li>
                {% endif %}
            </ul>
        </div><!--leftmenu-->

    </div><!-- leftpanel -->

    <div class="rightpanel">

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
        {% if 'stadium' in auth %}
            CarMate.page.load('/gygd/stadium');
        {% elseif 'museum' in auth %}
            CarMate.page.load('/gygd/stadium');
        {% endif %}

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
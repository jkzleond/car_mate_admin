{% extends "base.volt" %}

{% block head_assets %}
    {{ super() }}
    <link rel="stylesheet" href="/css/style.shinyblue.css" type="text/css" />
    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/supersized.css">
<!--    <script src="/assets/js/jquery-1.8.2.min.js" ></script>-->

    <style>
        body {
            background-image: url("{{ url('/assets/img/b3.jpg')}}");
        }
    </style>

    <script src="/assets/js/supersized.3.2.7.min.js" ></script>
    <script src="/assets/js/supersized-init.js" ></script>

    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#login').submit(function(){
                var u = jQuery('#username').val();
                var p = jQuery('#password').val();
                if(u == '' && p == '') {
                    jQuery('.login-alert').fadeIn();
                    return false;
                }
            });
            $('#login input').focus(function(){
                $('.login-alert').fadeOut();
            });
        });
    </script>
{% endblock%}

{% block content %}
<!--    <body class="loginpagex">-->

    <div class="loginpanel" style="left: 50%; top: 30%">
        <div class="loginpanelinner">
            <div class="logox animate0 bounceIn"><img src="images/logo1.png" alt="" /></div>
            <form id="login" action="/login" method="post">
                <div class="inputwrapper login-alert">
                    <div class="alert alert-error">用户名或密码不能为空</div>
                </div>
                {% if flashSession.has('error') %}
                <div class="alert alert-error">
                    {{ flashSession.output() }}
                </div>
                {% endif %}
                <div class="inputwrapper animate1 bounceIn">
                    <input type="text" name="username" id="username" placeholder="输入用户名" value="{% if user_id is defined %}{{ user_id }}{% endif %}" />
                </div>
                <div class="inputwrapper animate2 bounceIn">
                    <input type="password" name="password" id="password" placeholder="输入密码" value="{% if password is defined %}{{ password }}{% endif %}" />
                </div>
                <div class="inputwrapper1 animate4 bounceIn">
                    <input type="code" name="code" id="code" placeholder="验证码" />
                    <div style="float:right"><img id="validate_code" src="/validatecode" style="width: 80px; height: 27px"></div>
                </div>


                <div class="inputwrapper animate3 bounceIn">
                    <button name="submit" style="font-family:微软雅黑;">登录</button>
                </div>


                <div class="inputwrapper animate5 bounceIn">
                    <label><input type="checkbox" class="remember" name="remember" value="1" {% if remember is defined %} checked {% endif %}/> 记住密码</label>
                </div>

            </form>
        </div><!--loginpanelinner-->
    </div><!--loginpanel-->

    <div class="loginfooter">
        <p></p>
    </div>
    <script type="text/javascript">
        (function(){
            //点击验证码图片更换验证码
            $('#validate_code').click(function(){
                this.src = '/validatecode?' + Date.now();
            });
        })()
    </script>
{% endblock %}
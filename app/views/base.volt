<!DOCTYPE html>
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>车友惠|{% block title %} {% endblock %}</title>
        {% block head_assets %}
        <link rel="stylesheet" href="{{ url('/css/style.default.css') }}" type="text/css" />
        <script type="text/javascript" src="{{ url('/js/jquery-easyui/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/jquery.pin.js') }}"></script>

<!--        <script type="text/javascript" src="{{ url('/js/jquery-1.9.1.min.js') }}"></script>-->
        <script type="text/javascript" src="{{ url('/js/jquery-migrate-1.1.1.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/jquery-ui-1.9.2.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/modernizr.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/jquery.cookie.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/custom.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/underscore.js') }}"></script>
        <script type="text/javascript" src="{{ url('/js/iScroll/iscroll.js') }}"></script>

        {% endblock %}
	</head>
	<body>
        {% block content %}

        {% endblock %}
	</body>
</html>
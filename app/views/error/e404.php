<!DOCTYPE html>
<html>
<head>
<title>{% Lang::get('system.error.e404-title') %}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--

html {
	height: 100%;
	width: 100%;
}

body {
	margin: 0px;
	height: 100%;
	width: 100%;
	background: #F4F5F0;
}

.main {
	width: 100%;
	height: 100%;
	display: table;
}

.info {
	display: table-cell;
	vertical-align: middle;
	text-align: center;
}

.layer {
	width: 440px;
	text-align: center;
	display: inline-block;
	font-family: verdana, arial, sans-serif;
	color: #222222;
}

.content {
	margin: 0px 0px 7px 0px;
	overflow: hidden;
}

.content img {
	display: inline-block;
	margin: 0px 0px 10px 0px;
}

.content h1 {
	color: #DD6666;
	font-size: 56px;
	margin: 0px;
}

.content p {
	font-size: 16px;
	line-height: 32px;
	margin: 12px 0px 12px 0px;
}

.footer {
	color: #888888;
	font-size: 13px;
}

.footer a {
	color: #888888;
	text-decoration: underline;
}

.footer a {
	text-decoration: underline;
}

-->
</style>
</head>
<body> 
  
<div class="main">
	
	<div class="info">
	
		<div class="layer">
	
			<div class="content">
			
				<img src="/img/logobig.png">
				
				<h1>{% Lang::get('system.error.e404-header') %}</h1>
				<p>
					{% if(is_null($message)) %}
						{% Lang::get('system.error.e404-content') %}
					{% else %}
						{% $message %}
					{% endif %}
				</p>
				
			</div>
			
			<div class="footer">
				{% Lang::get('system.error.e404-footer') %}
				<a href="{% Request::site() %}">{% Request::site() %}</a>
			</div>
			
		</div>
		
	</div>

</div>
  
</body>
</html>
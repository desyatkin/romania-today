<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name='yandex-verification' content='65d29bfe2194b660' />
	<meta name="description" content=""></meta>
	<link rel="shortcut icon" href="/favicon.ico" /> 
	<script src="//code.jquery.com/jquery.js"></script>
	


	
    <script type="text/javascript" src="/js/ui/jquery.ui.core.js"></script>
    <script type="text/javascript" src="/js/ui/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="/js/ui/jquery.ui.tabs.js"></script>
    <script src="/js/functions.js" type="text/javascript"></script>

    <link rel="stylesheet" type="text/css" href="/css/slider.css" />
    <link href="/css/krutilka.css" rel="stylesheet" type="text/css" media="screen" /> 
    <link rel="stylesheet" type="text/css" href="/css/all.css">
    <script type="text/javascript">
		$(document).ready(function(){
			$("#featured").tabs();
		});
	</script>
</head>
<body>
	<div id="wbody">
		@include('site.top')
		<div id="wrapper">
		<div id="mainBlockTop">
    		<div class="block top_main_bl">
        		@include('helpers.mainNewsSlider')
    		</div> <!-- /block top_main_bl -->
		    <div class="clear"></div>
		</div> <!-- /mainBlockTop -->
			@yield('content')
			@include('site.sidebar')
			@include('site.footer')
		</div>
		

	</div>
</body>
</html>
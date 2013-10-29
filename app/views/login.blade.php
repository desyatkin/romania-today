<!DOCTYPE html>
<html>
  <head>
    <title>Система администрирования PolishNews.ru</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Bootstrap --}}
    <link href="/css/bootstrap.css" rel="stylesheet" media="screen">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
    <style>body { padding-top: 85px; overflow-y: scroll;}</style>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
  </head>
  <body> 

    <div class="container">
    	<form class="form-horizontal" role="form" action="/login" method="POST">
		  <div class="form-group">
		    <label for="inputEmail1"  class="col-lg-2 control-label">Логин</label>
		    <div class="col-lg-10">
		      <input type="text" class="form-control" name="email" id="inputEmail1" placeholder="login">
		    </div>
		  </div>
		  <div class="form-group">
		    <label for="inputPassword1" class="col-lg-2 control-label">Пароль</label>
		    <div class="col-lg-10">
		      <input type="password" name="password" class="form-control" id="inputPassword1" placeholder="password">
		    </div>
		  </div>
		  <div class="form-group">
		    <div class="col-lg-offset-2 col-lg-10">
		      <button type="submit" class="btn btn-default">Вход</button>
		    </div>
		  </div>
		</form>
    </div>
        
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.js"></script>
  </body>
</html>
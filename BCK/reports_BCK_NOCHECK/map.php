<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Comtible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!--<link rel="icon" href="../../favicon.ico"> -->

    <title>Trips Report</title>



    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">

	<!-- Bootstrap additionl components -->
	<link rel="stylesheet" href="css/scaffolding.less">

	<!-- Bootstrap additionl components -->
	<link rel="stylesheet" href="css/navbar.less">
    <link rel="stylesheet" href="css/bootstrap-slider.css">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="css/main.css" media="screen"/>

    <!-- Map Main CSS -->
	<link rel="stylesheet" type="text/css" href="css/map.main.css" media="screen"/>

	<!-- DC CSS -->
	<link rel="stylesheet" href="http://openlayers.org/en/v3.11.2/css/ol.css" type="text/css">

    <!-- DatePicker3 CSS -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker3.min.css" media="screen"/>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || alert("Non � stata caricata la risorsa jQuery!");</script>

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="http://getbootstrap.com/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

	<!-- OpenLayers [JS] -->
    <script src="http://openlayers.org/en/v3.11.2/build/ol.js" type="text/javascript"></script>

	<!-- DatePicker [JS] -->
    <script src="js/bootstrap-datepicker.min.js" type="text/javascript"></script>

    <!-- Slider [JS] -->
    <script src="js/bootstrap-slider.js" type="text/javascript"></script>

	</head>

	<body data-spy="scroll">

	<!-- Fixed navbar -->
	<nav class="navbar navbar-default" id="top-page">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Reports</a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li ><a href="trips.php">Trips</a></li>
					<li class="active"><a href="map.php">Map</a></li>
					<li><a href="routes.php">Routes</a></li>
					<li><a href="live.php">Live</a></li>   
					<!--<li><a href="#contact">Contact</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li class="dropdown-header">Nav header</li>
							<li><a href="#">Separated link</a></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
					</li>-->
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<!-- Begin page content -->
	<div class="container-fluid bs-docs-container">
		<div class="page-header">
			<h1>Trips HeatMap</h1>
		</div>
        <div class="row">
			<div class="col-xs-12 col-sm-5 col-md-5">
				<button type="button" class="btn btn-default" id="pan-to-milan">Pan to Milan</button>
				<button type="button" class="btn btn-default" id="pan-to-florence">Pan to Florence</button>
				<button type="button" class="btn btn-default" id="change-begend">Change to Ending Location</button>
                <input id="weight" data-slider-id='weightSlider' type="text" data-slider-min="0" data-slider-max="10" data-slider-step="1" data-slider-value="4"/>
			</div>
		  	<div class="col-xs-12 col-sm-7 col-md-7">
				<div class="col-xs-4 col-md-2">
					<span class="date-label">Time Range:</span>
				</div>
				<div class="col-xs-10 col-md-6">
	            	<div class="input-daterange input-group" id="datepicker">
					    <input type="text" class="input-sm form-control" id="start" name="start" />
					    <span class="input-group-addon">to</span>
					    <input type="text" class="input-sm form-control" id="end" name="end" />
					</div>

				</div>
                <div class="col-xs-3 col-md-3" id="element-counter">
						<div class="col-xs-6 col-md-4">
							<span class="date-label">Tot:</span>
						</div>
						<div class="col-xs-10 col-md-6">
			            	<input type="text" class="input-sm form-control" placeholder="" disabled>
						</div>
				</div>
			</div>
		</div>
		<br>
		<div class="row-fluid">
    		<div id="map" class="map"><div id="info"></div></div>
		</div>
	</div>

	<footer class="footer">
		<div class="container">
			<p class="text-muted">&reg;Omniaevo S.r.l. 2015</p>
		</div>
	</footer>


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>


	<script type="text/ecmascript" src="js/map.main.js"></script>
</body>
</html>

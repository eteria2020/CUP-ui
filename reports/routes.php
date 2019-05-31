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

	<?php
	    $car_plate='';

	    if (isset($_REQUEST['id'])) $car_plate=$_REQUEST['id'];
	    if (isset($_REQUEST['car_plate'])) $car_plate=$_REQUEST['car_plate'];

	?>


	<link rel="stylesheet" type="text/css" href="css/routes.main.css" />


	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">


	<!-- DateTimePicker CSS -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.min.css" media="screen"/>

	<link rel="stylesheet" href="css/navbar.less">
    <link rel="stylesheet" href="css/bootstrap-slider.css">



    <!-- Libs [JS] -->
	<script type="text/ecmascript" src="js/libs/libs.js"></script>
	<script type="text/ecmascript" src="js/libs/libs_adds.js"></script>                                    

	<!-- Bootstrap Compoments [JS] -->
   	<script src="js/libs/bootstrap-transition.js" type="text/javascript"></script>
	<script src="js/libs/bootstrap-collapse.js" type="text/javascript"></script>

    <!-- DateTimePicker [JS] -->
	<script src="js/libs/bootstrap-datetimepicker.min.js" type="text/javascript"></script>

	<!-- Slider [JS] -->
    <script src="js/libs/bootstrap-slider.js" type="text/javascript"></script>

	<meta name="generator" content="Journey2web" />
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
					<li><a href="trips.php">Trips</a></li>
					<li><a href="map.php">Map</a></li>
					<li class="active"><a href="routes.php">Routes</a></li>
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
	<div class="container col-md-12">
		<div class="row mainrow">
			<div class="col-md-12">
					<div class="btn-toolbar" role="toolbar">
                        <div class="col-md-2 labeldate">
						   	<span>Verranno caricati le </span>
						</div>
						<div class="col-md-4 dataslider">
							<input id="ex6" type="text" data-slider-min="50" data-slider-max="300" data-slider-step="25" data-slider-value="100"/>
							<span id="ex6CurrentSliderValLabel"><span id="ex6SliderVal">100 corse prima di</span></span>
						</div>
                        <div class="col-md-3">
							<div class='input-group date' id='datetimepicker1'>
			                    <input type='text' class="form-control" />
			                    <span class="input-group-addon">
			                        <span class="glyphicon glyphicon-calendar"></span>
			                    </span>
			                </div>
						</div>
						<div class="col-md-2">
						<button id="dataupdate" type="button" class="btn btn-default" aria-label="Left Align">
							Aggiorna Dati
						  <span class=" glyphicon glyphicon-refresh" aria-hidden="true"></span>
						</button>
						</div>
					</div>
				<div class="clearfix visible-xs-block"></div>
			</div>
	        <div class="col-md-10 leftbar">
				<div id="map" class="map"></div>

			</div>
	        <div class="col-md-2 rightbar">
            	<div id="steps" class="side   panel panel-default">
					<div class="panel-heading">
				    	<h3 class="panel-title">Trips </h3>
			  		</div>
					<ol class="list-group panel-body" id="trips"></ol>
				</div>
			</div>
		</div>
    </div>
	<footer class="footer">
		<div class="container">
			<p class="text-muted">&reg;Omniaevo S.r.l. 2015 | Corse Caricate: <span id="trips"></span> | Punti Record Caricati: <span id="points"></span>
		</p>
		</div>
	</footer>
</body>
	<script type="text/javascript" src="js/routes.main.min.js"></script>
</html>
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

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="css/main.css" media="screen"/>

	<!-- DC CSS -->
	<link rel="stylesheet" type="text/css" href="css/dc.css" media="screen"/>

	<!-- Color Brewer -->
    <link rel="stylesheet" href="css/colorbrewer.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || alert("Non è stata caricata la risorsa jQuery!");</script>

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="http://getbootstrap.com/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

	<!-- Crossfilters [JS] -->
	<script type="text/ecmascript" src="js/crossfilter.js"></script>
	<!-- D3 -->
	<script type="text/ecmascript" src="js/d3.js"></script>
	<!-- DC -->
	<script type="text/ecmascript" src="js/dc.js"></script>

	</head>

	<body>

	<!-- Fixed navbar -->
	<nav class="navbar navbar-default navbar-fixed-top">
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
					<li class="active"><a href="trips.php">Trips</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
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
					</li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<!-- Begin page content -->
	<div class="container">
		<div class="page-header">
			<h1>Trips Report</h1>
		</div>

		<p class="lead">
			<div id='data-count' style="height: 300px">
			<!--  <span class='filter-count'></span> selected out of <span class='total-count'></span> records -->
	    	</div>
		</p>
		
		<br>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				Intervallo  (ultimi 28 giorni). Dal <span id="dateFrom"></span> Al <span id="dateTo"></span>
			</div>
			<div class="panel-body">
				<div id="days-chart">
					<a class="reset" href="javascript:daysChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
					<div class="clearfix"></div>
				</div>
		  </div>
		</div>


		<div class="panel panel-default">
			<div class="panel-heading">
				Corse per giorno della settimana
			</div>
			<div class="panel-body">
				<div id="day-of-week-chart">
					<a class="reset" href="javascript:dayOfWeekChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>


		<div class="panel panel-default">
			<div class="panel-heading">
				Corse per durata (minuti)
			</div>
            <div class="panel-body">
            	<div id="duration-chart">
                    <span class="reset" style="display: none;">intervallo: <span class="filter"></span></span>
					<a class="reset" href="javascript:durationChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
			        <div class="clearfix"></div>
			    </div>
			</div>
		</div>

        <div class="panel panel-default">
			<div class="panel-heading">
            	Corse ora di inizio
			</div>
			<div class="panel-body">
			    <div id="beginning-hour-chart">
			        <span class="reset" style="display: none;">intervallo: <span class="filter"></span></span>
			        <a class="reset" href="javascript:hoursChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
			        <div class="clearfix"></div>
			    </div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						Suddivisione per citt&agrave;
		            </div>
					<div class="panel-body">
						<div id="city-chart">
					        <span class="reset" style="display: none;">range: <span class="filter"></span></span>
					        <a class="reset" href="javascript:sexChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
					        <div class="clearfix"></div>
					    </div>
		            </div>
				</div>
        	</div>
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						Suddivisione per sesso
		            </div>
					<div class="panel-body">
					    <div id="gender-chart">
					        <span class="reset" style="display: none;">range: <span class="filter"></span></span>
					        <a class="reset" href="javascript:sexChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
					        <div class="clearfix"></div>
					    </div>
					</div>
				</div>
            </div>
		</div>

        <div class="panel panel-default">
			<div class="panel-heading">
				Suddivisione per quartiere
            </div>
			<div class="panel-body">
				<div id="area-chart">
					<span class="reset" style="display: none;">range: <span class="filter"></span></span>
					<a class="reset" href="javascript:nilChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
					<div class="clearfix"></div>
				</div>
            </div>
		</div>

		<div style='clear:both;'></div>
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


	<script type="text/ecmascript" src="js/main.js"></script>
</body>
</html>

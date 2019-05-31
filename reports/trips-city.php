<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">                        
	<?php if(!isset($_GET['city'])) die("Non e' stato possibile verificare la citta' selezionata!"); ?>
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

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="css/main.css" media="screen"/>

	<!-- DC CSS -->
	<link rel="stylesheet" type="text/css" href="css/dc.css" media="screen"/>

	<!-- Color Brewer -->
    <link rel="stylesheet" href="css/colorbrewer.css">



    <!-- Libs [JS] -->
	<script type="text/ecmascript" src="js/libs/libs.js"></script>

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="http://getbootstrap.com/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


	<script>var fleetId = <?php echo $_GET['city']; ?></script>

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
					<li><a href="routes.php">Routes</a></li>
					<li><a href="live.php">Live</a></li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<!-- Begin page content -->
	<div class="container bs-docs-container">
    		<div class="page-header">
				<h1>Trips Report</h1>
			</div>

		<div class="row">
			<div class="col-md-9" role="main">

				<p class="lead">
					<div id='data-count'>
					<!--  <span class='filter-count'></span> selected out of <span class='total-count'></span> records -->
			    	</div>
				</p>
				
				<br>
				
				<div class="panel panel-default  affix-top" id="data-range">
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

   				<div class="panel panel-default" id="day-of-week">
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

				<div class="panel panel-default" id="duration">
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
		
		        <div class="panel panel-default" id="beginning-hour">
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

				<div class="row" id="bio-data">
					<div class="col-xs-12 col-sm-6 col-md-6">
                     	<div class="panel panel-default" id="customer-age">
							<div class="panel-heading">
								Suddivisione per Categoria di Et&agrave;
				            </div>
							<div class="panel-body">
							    <div id="age-chart">
							        <span class="reset" style="display: none;">range: <span class="filter"></span></span>
							        <a class="reset" href="javascript:sexChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
							        <div class="clearfix"></div>
								</div>
                                <div class="chart-label ">
			                        <span class="glyphicon glyphicon-stop" aria-hidden="true"></span> = 45-54&nbsp;&nbsp;&nbsp;
									<span class="glyphicon glyphicon-stop" aria-hidden="true"></span> = 55-64&nbsp;&nbsp;&nbsp;
									<span class="glyphicon glyphicon-stop" aria-hidden="true"></span> = Over 65
								</div>
							</div>
						</div>
		        	</div>
					<div class=" col-xs-12 col-sm-6 col-md-6">
						<div class="panel panel-default" id="customer-gender">
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

				<div class="panel panel-default" id="area-map">
					<div class="panel-heading">
		            	Corse ora di inizio
					</div>
					<div class="panel-body">
					    <div id="area-map-chart" class="area-chart">
					        <span class="reset" style="display: none;">intervallo: <span class="filter"></span></span>
					        <a class="reset" href="javascript:hoursChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
					        <div class="clearfix"></div>
					    </div>
					</div>
				</div>

				<div class="panel panel-default" id="area-list">
					<div class="panel-heading">
		            	Corse ora di inizio
					</div>
					<div class="panel-body">
					    <div id="area-list-chart">
					        <span class="reset" style="display: none;">intervallo: <span class="filter"></span></span>
					        <a class="reset" href="javascript:hoursChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
					        <div class="clearfix"></div>
					    </div>
					</div>
				</div>

				<div style='clear:both;'></div>
			</div>
			<div class="col-md-3" role="complementary">
				<nav class="bs-docs-sidebar hidden-print hidden-xs hidden-sm affix">
					<ul class="nav bs-docs-sidenav"  data-spy="affix" data-offset-top="100" data-offset-bottom="0">
						<li><a href="#data-range">Intervallo Dati</a></li>
						<li><a href="#day-of-week">Giorno della Settimana</a></li>
						<li><a href="#duration">Durata della Corsa</a></li>
						<li><a href="#beginning-hour">Ora di inizio Corsa</a></li>
						<li><a href="#customer-age">Fascia di Et&agrave;</a></li>
                        <li><a href="#customer-gender">Sesso</a></li>
						<li><a href="#area-map">Quartiere</a></li>
					</ul>
				</nav>
			</div>
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


	<script type="text/ecmascript" src="js/trips.city.main.min.js"></script>
</body>
</html>

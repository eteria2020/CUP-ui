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


	<link rel="stylesheet" type="text/css" href="include/style.css" />


	   <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">


	   <!-- DatePicker3 CSS -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker3.min.css" media="screen"/>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
	<script type="text/javascript" src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>
	<script type="text/javascript" src="include/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="include/jquery.scrollTo.js"></script>
	<script type="text/javascript" src="include/journey.js"></script>

	<!-- DatePicker [JS] -->
	   <script src="js/bootstrap-datepicker.min.js" type="text/javascript"></script>

	<script>

	    var car_plate = '<?php echo $car_plate;?>'

	    function loadTracks(features) {

	    layerTracks.removeAllFeatures();

	    var url =  "log-data-test.php?car_plate="+car_plate;
	    console.log(url);

	    $.getJSON( url , function( data ) {
	          var items = [];
	          var trips = [];
	          $.each( data, function( key, val ) {
	            console.log(val);
	            if (val._id>-1){
                    var duration =  Math.round((val.end_trip.sec - val.begin_trip.sec)/60);

                    if (duration > 3 && val._id != 0){
						trips.push(val._id);

						console.log(val);

		                var begin_trip 	= new Date(val.begin_trip.sec*1000);
	                    var end_trip	= new Date(val.end_trip.sec*1000);


		                var date = begin_trip.getDate() + '/' + begin_trip.getMonth() + '/' + begin_trip.getFullYear() + "  " + begin_trip.getHours() + ":" + begin_trip.getMinutes();


		                items.push('<li href="#" class="list-group-item way" id="' + val._id + '">'+
										'<h4 class="list-group-item-heading">ID: '+ val._id +'</h4>'+
										'<p class="list-group-item-text">'+
	                                        date + '(' + duration + 'min : ' + val.points +')'+
										'</p>'+
									'</li>');

					}
	            }

	          });
	             //console.log("http://core.sharengo.it/ui/log-data.php?id_trip="+trips[1] );
	            //newTrack(features, ""+trips[1], "", "http://core.sharengo.it/ui/log-data.php?id_trip="+trips[1]);
	          $("#trips").html(items.join( "" ));
	          	newTrack(features, "-1", "", "http://core.sharengo.it/ui/log-data-test.php?id_trip=-1&car_plate="+car_plate);
	          	addHover();
	          	layerTracks.addFeatures(features);
	      });

		//newTrack(features, "way_J10_vers_albine", "", "http://core.sharengo.it/ui/log-data.php?id_trip=4410");
	    }

		// register all waypoints
		function loadPoints(features) {
		    /*
			newPoint(features, "place_J00_pradelles", 2.43917391185, 43.4026609097);

			newPoint(features, "place_J10_vers_albine", 2.533133, 43.459583);

			newPoint(features, "place_J20_vers_rouairoux", 2.553165, 43.509887);

			newPoint(features, "place_J30_vers_angles", 2.565228, 43.561835);

			newPoint(features, "place_J40_vers_brassac", 2.492323, 43.63078);

			newPoint(features, "place_J50_vers_st_affrique", 2.892018, 43.949875);

			newPoint(features, "place_J60_vers_st_rome_de_cernon", 2.967788, 44.015868);

			newPoint(features, "place_J80_vers_becours", 3.038662, 44.23184);
		    */

		}


	   </script>

	<link rel="stylesheet" type="text/css" href="include/jquery.fancybox-1.3.4.css" />

	<meta name="generator" content="Journey2web" />
</head>
<body>

	<div id="steps" class="side   panel panel-default">
		<div class="panel-heading">
	    	<h3 class="panel-title">Trips </h3>
  		</div>
		<ol class="list-group panel-body" id="trips"></ol>
	</div>

	<div id="content">
		<div id="map"></div>
	</div>


	<footer class="footer">
		<div class="container">
			<p class="text-muted">&reg;Omniaevo S.r.l. 2015</p>
		</div>
	</footer>
</body>


</html>

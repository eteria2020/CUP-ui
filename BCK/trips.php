<html xmlns="http://www.w3.org/1999/xhtml">
<?php
    $car_plate='';

    if (isset($_REQUEST['id'])) $car_plate=$_REQUEST['id'];
    if (isset($_REQUEST['car_plate'])) $car_plate=$_REQUEST['car_plate'];

?>

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>Trips map view</title>

	<link rel="stylesheet" type="text/css" href="include/style.css" />

	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
	<script type="text/javascript" src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>
	<script type="text/javascript" src="include/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="include/jquery.scrollTo.js"></script>
	<script type="text/javascript" src="include/journey.js"></script>
	<script>

    var car_plate = '<?php echo $car_plate;?>'

    function loadTracks(features) {

    layerTracks.removeAllFeatures();

    var url =  "log-data.php?car_plate="+car_plate;
    console.log(url);

    $.getJSON( url , function( data ) {
          var items = [];
          var trips = [];
          $.each( data, function( key, val ) {
            console.log(val);
            if (val._id>-1){
                trips.push(val._id);
                var begin_trip = new Date(val.begin_trip.sec*1000);
                console.log(begin_trip);
                var date = begin_trip.getDate() + '/' + begin_trip.getMonth() + '/' + begin_trip.getFullYear() + "  " + begin_trip.getHours() + ":" + begin_trip.getMinutes();

                var duration =  Math.round((val.end_trip.sec - val.begin_trip.sec)/60);
                items.push( "<li class='way' id='" + val._id + "'><h3>" + val._id + "</h3><p> "+ date + "(" + duration + "min : " + val.points +") </p></li>" );
            }

          });
             //console.log("http://core.sharengo.it/ui/log-data.php?id_trip="+trips[1] );
            //newTrack(features, ""+trips[1], "", "http://core.sharengo.it/ui/log-data.php?id_trip="+trips[1]);
          $("#trips").html(items.join( "" ));
          newTrack(features, "-1", "", "http://core.sharengo.it/ui/log-data.php?id_trip=-1&car_plate="+car_plate);
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



<div id="steps" class="side">
	<h2>Trips :</h2>
	<ol id="trips">



    </ol>
</div>

<div id="content">

	<div id="map"></div>

</div>

</body>
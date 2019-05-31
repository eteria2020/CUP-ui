$.ajaxSetup({
        async: true,
        cache : false,
        timeout: 30000,		// set to 10s
         error: function (msg) { alert('error : ' + msg.d); }
    });

// Define map objects properties
var track_style = new OpenLayers.Style({strokeColor: "green", strokeWidth: 5, strokeOpacity: 0.5});
var track_style_h = {strokeColor: "blue", strokeWidth: 6, strokeOpacity: 0.8};

// Extended Styles for thumbnails
var pictureStyleMap = new OpenLayers.Style(
{
	externalGraphic: "${thumb}",
	graphicWidth: 20, graphicHeight: 20, graphicYOffset: -10, graphicXOffset: -10,
	backgroundGraphic: "${galleryBorder}",
	backgroundWidth: 24, backgroundHeight: 24,
	backgroundXOffset: -10, backgroundYOffset: -10
	}, {
	context: {
		thumb: function(feature) {
				return (feature.cluster)? feature.cluster[0].attributes.thumb: feature.attributes.thumb;
			},
		galleryBorder: function(feature) {
				return (feature.cluster)? 'include/img/gallery.png': "";
			}
		}
});

var bigStyleMap = {
	graphicWidth: 40, graphicHeight: 40, graphicYOffset: -20, graphicXOffset: -20,
	backgroundWidth: 48, backgroundHeight: 48,
	backgroundXOffset: -20, backgroundYOffset: -20
};

// Style for Waypoints
var placeStyleMap = new OpenLayers.Style(
{
	externalGraphic: "include/img/sign-big.png",
	graphicWidth: 18, graphicHeight: 26, graphicYOffset: -26, graphicXOffset: -9
});

var placeBigStyleMap = {
	graphicWidth: 34, graphicHeight: 48, graphicYOffset: -48, graphicXOffset: -17
};


// Distance in pixels on the map to consider pictures in the same gallery
var clusterDistance = 20;
// Todo : sort pictures in galleries by time

// Define global objects
var map; //complex object of type OpenLayers.Map
var tracks = new Object;
var bounds = new OpenLayers.Bounds();
var placeFeatureIds = new Object;
var trackFeatureIds = new Object;
var highlightCtrl;
var featuresTrack =[];

// Define track constructor
function newTrack(features, id, name, gpx) {
	try {
		var xhr = new OpenLayers.Request.XMLHttpRequest();

		xhr.open("GET", gpx, false);
		xhr.setRequestHeader('Content-Type', 'text/xml');

		xhr.send("");
		xmlDoc = xhr.responseXML;
		if (!xmlDoc) {
			// alert("xhr.responseXML is null");
			// Always null except with firefox loaded from local files…
			var parser = new DOMParser();
			var xmlDoc = parser.parseFromString(xhr.responseText, "text/xml");
			// see http://stackoverflow.com/questions/3781387/responsexml-always-null
		}
	}

	catch(err)
	{
		txt = "XMLHttpRequest error: " + err.message + "\n\n";
		alert(txt);
		return
	}

	try {
		var f = new OpenLayers.Format.GPX();
		// We suppose there is only one track on the gpx file
		var tracks = f.read(xmlDoc);
	} catch(err) {
		txt = "GPX reading error: " + err.message + "\n\n";
		alert(txt);
		return
	}

	var points = 0;

	for(var idx in tracks) {
		var trackFeature = tracks[idx];

		trackFeature.geometry.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
		bounds.extend(trackFeature.geometry.getBounds());
		trackFeature.attributes.id = trackFeature.attributes.name;
		trackFeatureIds[trackFeature.attributes.name] = trackFeature.id;
		features.push(trackFeature);

		points += trackFeature.geometry.components.length;
	}


	// Check Errors
    $.each(featuresTrack, function(key,val)
	{
		console.log(val);
	});

	map.zoomToExtent(bounds);


	console.log("Lenght: " +tracks.length);
    console.log("Points: " + points );

	$("span#points").text(points);
	$("span#trips").text(tracks.length);


	// Scrivo il numero di elementi caricati:
}

// Define waypoint constructor


function newPoint(features, id, lon, lat) {
	var lonLat = new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	var point = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);

	var iconFeature = new OpenLayers.Feature.Vector(point, {id: id});

	bounds.extend(lonLat);

	features.push(iconFeature);

	placeFeatureIds[id] = iconFeature.id;

}

// Define picture constructor

function newPicture(features, lon, lat, thumb, pict, title) {
	var lonLat = new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
	var point = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);

	var iconFeature = new OpenLayers.Feature.Vector(point, {title: title? title: "", thumb: thumb, pict: pict});
	bounds.extend(lonLat);

	features.push(iconFeature);

}


var urlDate = "",
	urlLimit =	100,
    urlTemplate1 ="",
    urlTemplate2 ="",
    urlTemplate3 ="";

$(document).ready(function()
	{
    	doneResizing();

		// DateTime Picker
		$('#datetimepicker1').datetimepicker({
		 	sideBySide: true,
			maxDate:	Date(),
			defaultDate: Date(),
			format: 'YYYY-MM-DD HH:mm:ss'
		});

	    urlDate 		= 	$("div.date input").val();
	    urlTemplate1	=  	'data/routes-data.php?date={{date}}&limit={{limit}}&k=70F2F21227ECA0FA0A60336CF9809053D18EA65A67575E646376E61A570F5A4B';
		urlTemplate2	=  	'data/routes-data.php?id_trip=-1&date={{date}}&limit={{limit}}&k=70F2F21227ECA0FA0A60336CF9809053D18EA65A67575E646376E61A570F5A4B';
		urlTemplate3	=  	'data/routes-data.php?id_trip={{id_trip}}&k=70F2F21227ECA0FA0A60336CF9809053D18EA65A67575E646376E61A570F5A4B';

        // Create The Slider
		$("#ex6").slider();

		// Listen to Slider Change Value (Ther's also the on("slide" bind, that listen
		// only the slide action, not also the click on a specific section of the
		// slidebar.
		$("#ex6").on("change", function(slideEvt) {
			console.log(slideEvt);
			$("#ex6SliderVal").text(slideEvt.value.newValue+" corse prima di");

			// Also update the urlLimit value
			urlLimit = slideEvt.value.newValue;
		});

		// Build the map widget
		OpenLayers.ImgPath = "http://js.mapbox.com/theme/dark/";
		map = new OpenLayers.Map ({
			div: 'map',
			controls:[
					new OpenLayers.Control.Navigation(),
					new OpenLayers.Control.PanZoomBar()
					//new OpenLayers.Control.LayerSwitcher(),
					//new OpenLayers.Control.Attribution()
			],
			maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
			maxResolution: 156543.0399,
			numZoomLevels: 19,
			units: 'm',
			projection: new OpenLayers.Projection("EPSG:900913"),
			displayProjection: new OpenLayers.Projection("EPSG:4326")
		});

		// Define the main map layer

		// Here we use a predefined layer that will be kept up to date with URL changes
		layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
		map.addLayer(layerMapnik);
		/*layerTilesAtHome = new OpenLayers.Layer.OSM.Osmarender("Osmarender");
    map.addLayer(layerTilesAtHome);
    layerCycleMap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");
    map.addLayer(layerCycleMap);*/


		var strategy = new OpenLayers.Strategy.Cluster({distance: clusterDistance, threshold: 2})

		layerPictures = new OpenLayers.Layer.Vector("Pictures",
			{
				// projection: "EPSG:4326",
			strategies:[strategy],
			styleMap: new OpenLayers.StyleMap(
					{
					"default": pictureStyleMap,
					"temporary": bigStyleMap,
					"select": {}
					}
				)
			}
		);

		layerPlaces = new OpenLayers.Layer.Vector("Places",
			{
				// projection: "EPSG:4326",
			styleMap: new OpenLayers.StyleMap(
					{
					"default": placeStyleMap,
					"temporary": placeBigStyleMap
					}
				)
			}
		);

		layerTracks = new OpenLayers.Layer.Vector("Tracks",
			{
				// projection: "EPSG:4326",
			styleMap: new OpenLayers.StyleMap(
					{
					"default": track_style,
					"temporary": track_style_h
					}
				)
			}
		);
  	}
);

$(window).load(function(){
		loadTracks(featuresTrack);

		map.addLayer(layerTracks);
		layerTracks.addFeatures(featuresTrack);

		var featuresPlace =[]
		loadPoints(featuresPlace);

		map.addLayer(layerPlaces);
		layerPlaces.addFeatures(featuresPlace);

		/*
  var featuresPicture= []
  loadPictures(featuresPicture);

  map.addLayer(layerPictures);
  layerPictures.addFeatures(featuresPicture);
  */


		highlightCtrl = new OpenLayers.Control.SelectFeature([layerPlaces, layerTracks],
			{
			hover: true,
			highlightOnly: true,
			renderIntent: "temporary",
			eventListeners: {
				featurehighlighted: function(e) {
						if (e.feature.attributes.id) {
							element = $("#" + e.feature.attributes.id);
							if (!element.hasClass("list-group-item-success")) {
								element.addClass("list-group-item-success");
								element.parent().scrollTo(element, 800);
							}
						}
					},
				featureunhighlighted: function(e) {
						if (e.feature.attributes.id) {
							element = $("#" + e.feature.attributes.id);
							element.removeClass("list-group-item-success");
							element.parent().stop();
						}
					}
				}
			}
		);
		map.addControl(highlightCtrl);
		highlightCtrl.activate();


		// add hover function to highlight way points
		$(".place").hover(
			function () {
				var f = layerPlaces.getFeatureById(placeFeatureIds[$(this).attr("id")]);
				$(this).addClass("list-group-item-success");
				highlightCtrl.highlight(f);
			},
			function () {
				var f = layerPlaces.getFeatureById(placeFeatureIds[$(this).attr("id")]);
				highlightCtrl.unhighlight(f);
				$(this).removeClass("list-group-item-success");
			}
		);



		// Zoom to the extent of a track when clicking on a descriptio

	}
);

var allowedZoom = true;

function addHover() {
	// Add hover function to highlight tracks
	$(".way").hover(
		function () {


			var f = layerTracks.getFeatureById(trackFeatureIds[$(this).attr("id")]);

       	 	if(!f){
            	$(this).addClass("list-group-item-danger");
       	 	}else{
				$(this).addClass("list-group-item-success");
				highlightCtrl.highlight(f);
            }
		},
		function () {
			var f = layerTracks.getFeatureById(trackFeatureIds[$(this).attr("id")]);
			highlightCtrl.unhighlight(f);
			$(this).removeClass("list-group-item-success");
		}
	);

	$(".way").click(
		function() {
			if (allowedZoom) {
				var f = layerTracks.getFeatureById(trackFeatureIds[$(this).attr("id")]);
				map.zoomToExtent(f.geometry.getBounds());
			}
		}
	);

	// dirty trick not to zoom when clicking on a link inside a description
	$("a").hover(
		function() {allowedZoom = false;},
		function() {allowedZoom = true;}
	);

}



function loadTracks(features) {

	layerTracks.removeAllFeatures();

	var url =  urlTemplate1.replace('{{date}}', urlDate).replace('{{limit}}', urlLimit);

	$.getJSON(url, function(data)
		{
			//console.log("DATA:");
        	//console.log(data);

			var items =[];
			var trips =[];
			
			
			
			$.each(data, function(key, val)
				{
					if (val._id>-1) {
						var duration = Math.round((val.end_trip.sec - val.begin_trip.sec)/60);

						//if (duration > 3 && val._id != 0) {
							trips.push(val._id);

							var date = moment(val.begin_trip.sec*1000).format("DD/MM/YYYY HH:mm");

							items.push('<li href="#" class="list-group-item way" id="' + val._id + '">'+
								'<h5 class="list-group-item-heading">'+ date +' <b>'+ val.VIN+'</b></h5>'+
								'<p class="list-group-item-text">'+
								 val._id + ' ' + duration + 'min (' + val.points +')'+
								'</p>'+
								'</li>');
							
							console.log(urlTemplate3.replace('{{id_trip}}', val._id));
							
							newTrack(features, "-1", "", urlTemplate3.replace('{{id_trip}}', val._id));
							addHover();

						//}
					}

				}
			);
			//console.log("http://core.sharengo.it/ui/log-data.php?id_trip="+trips[1] );
			//newTrack(features, ""+trips[1], "", "http://core.sharengo.it/ui/log-data.php?id_trip="+trips[1]);

			
			$("#trips").html(items.join(""));
			
							layerTracks.addFeatures(features)
/*
			$("#trips").html(items.join(""));
			newTrack(features, "-1", "", urlTemplate2.replace('{{date}}', urlDate).replace('{{limit}}', urlLimit) );
			addHover();
			layerTracks.addFeatures(features);*/
		}
	);

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





// Resize only if the window.resize is done
var id;
$(window).resize(function() {
    clearTimeout(id);
    id = setTimeout(doneResizing, 500);

});


function doneResizing(){
	var newHeight 				= $("body").height();
    $(".row.mainrow").css("height", newHeight -150); //-110);
    $(".map").css("height", newHeight -150);
}

// Bind dataUpdate Action
$("button#dataupdate").click(function(){
	updateTracks();
});


function updateTracks(){
	// Update the date value
	urlDate 		= 	$("div.date input").val();
	
	// Reload the tracks
	loadTracks(featuresTrack);
}



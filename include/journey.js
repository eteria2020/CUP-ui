/**
 * include/js/journey.js  -   Main javascript library
 *
 * Copyright © 2011, Florian Birée <florian@biree.name>
 *
 * This file is a part of journey2web.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 **/

// Define map objects properties
var track_style = new OpenLayers.Style({strokeColor: "green", strokeWidth: 5, strokeOpacity: 0.5});
var track_style_h = {strokeColor: "red", strokeWidth: 6, strokeOpacity: 0.8};

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
var featuresTrack =[]

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


	for(var idx in tracks) {
		var trackFeature = tracks[idx];
		console.log(trackFeature);
		trackFeature.geometry.transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
		bounds.extend(trackFeature.geometry.getBounds());
		trackFeature.attributes.id = trackFeature.attributes.name;
		trackFeatureIds[trackFeature.attributes.name] = trackFeature.id;
		features.push(trackFeature);

	}

	map.zoomToExtent(bounds);

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


$(document).ready(function()
	{

		// Build the map widget

		OpenLayers.ImgPath = "http://js.mapbox.com/theme/dark/";
		map = new OpenLayers.Map ("map",
			{
			controls:[
					new OpenLayers.Control.Navigation(),
					new OpenLayers.Control.PanZoomBar(),
					//new OpenLayers.Control.LayerSwitcher(),
					//new OpenLayers.Control.Attribution()
				],
			maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
			maxResolution: 156543.0399,
			numZoomLevels: 19,
			units: 'm',
			projection: new OpenLayers.Projection("EPSG:900913"),
			displayProjection: new OpenLayers.Projection("EPSG:4326")
			}
		);

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
				console.log("OVER!");

			},
			function () {
				var f = layerPlaces.getFeatureById(placeFeatureIds[$(this).attr("id")]);
				highlightCtrl.unhighlight(f);
				$(this).removeClass("list-group-item-success");
			}
		);

		// fancy profiles
		$("a.hdiff").fancybox(
			{
				//'orig'            : $(this),
			'padding': 0,
			'transitionIn': 'elastic',
			'transitionOut': 'elastic'
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

/*
var fsource = new ol.source.Vector({
extractStyles: false,
projection: 'EPSG:3857',
url: 'trips-geo.php',
format: new ol.format.GeoJSON()
});
*/






/*********************** ESEMPIO CAMBIO DINAMICO FILTRI DI CARICAMENTO DATI *****************/


/*
var fsource = new ol.source.Vector({
extractStyles: false,
projection: 'EPSG:3857',
url: 'trips-geo.php',
format: new ol.format.GeoJSON()
});
*/

 /*
var vectorSource = new ol.source.Vector({
	extractStyles: false,
	projection: 'EPSG:3857',
	url: urlTemplate,
	format: new ol.format.GeoJSON()
}) */

 var fill = new ol.style.Fill({
   color: '#EDE431'
 });
 var stroke = new ol.style.Stroke({
   color: '#FF0000',
   width: 1.25
 });


var iconStyle = new ol.style.Style({
     image: new ol.style.Circle({
         fill: fill,
         stroke: stroke,
         radius: 7
     }),
     fill: fill,
     stroke: stroke
   })


var urlTemplate = 'data/cars-geo-data.php';

var vectorSource = new ol.source.Vector({
	extractStyles: false,
	projection: 'EPSG:3857',
    /*
	loader: function(extent, resolution, projection) {
		var url = urlTemplate;

		$.ajax(url).then(function(response) {
            var format = new ol.format.GeoJSON();
            var features = format.readFeatures(response,
            {featureProjection: projection});
            vectorSource.addFeatures(features);
			// Determino il numero di elementi caricati
			$("#element-counter input").val(features.length);
        });
	},*/
	format: new ol.format.GeoJSON()
});




var vector = new ol.layer.Vector(
	{
	source:vectorSource,
    style : iconStyle
	}
);

var view = new ol.View({
  // the view's initial state
  center: ol.proj.transform([9.185, 45.465], 'EPSG:4326', 'EPSG:3857'),
  zoom: 12
});



var raster = new ol.layer.Tile(
	{
	source: new ol.source.Stamen(
			{
			layer: 'toner'
			}
		)
	}
);

var OSM = new ol.layer.Tile(
	{
	source: new ol.source.OSM()
	}
);

var map = new ol.Map(
	{
	layers:[OSM, vector],
	target: 'map',
	view: view,
	eventListeners:
		{"zoomend": zoomChanged}

	}
);


map.on("moveend", zoomChanged);


var lastZoom;
function zoomChanged()
{
	zoom = map.getView().getZoom();
	if (lastZoom!=zoom)
	{
		console.log(zoom);
		lastZoom = zoom;

	}
}


var info = $('#info');
info.tooltip({
  animation: false,
  trigger: 'manual'
});

var displayFeatureInfo = function(pixel) {
  info.css({
    left: pixel[0] + 'px',
    top: (pixel[1] - 5) + 'px'
  });
  var feature = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
    return feature;
  });
  if (feature) {
    info.tooltip('hide')
        .attr('data-original-title', feature.get('plate'))
        .tooltip('fixTitle')
        .tooltip('show');
  } else {
    info.tooltip('hide');
  }
};

map.on('pointermove', function(evt) {
  if (evt.dragging) {
    info.tooltip('hide');
    return;
  }
  displayFeatureInfo(map.getEventPixel(evt.originalEvent));
});

map.on('click', function(evt) {
  displayFeatureInfo(evt.pixel);
});





// MDL CODE
var city ={
	milan 		: ol.proj.fromLonLat([9.1858849, 45.4654005]),
    florence 	: ol.proj.fromLonLat([11.2497741, 43.769539])
};



$("#pan-to-milan").click(function()
{
	var pan = ol.animation.pan(
	{
		duration: 2000,
		source: /** @type {ol.Coordinate} */ (view.getCenter())
	});
	map.beforeRender(pan);
	view.setCenter(city.milan);
	view.setZoom(12);
});

$("#pan-to-florence").click(function()
{
	var pan = ol.animation.pan(
	{
		duration: 2000,
		source: /** @type {ol.Coordinate} */ (view.getCenter())
	});
	map.beforeRender(pan);
	view.setCenter(city.florence);
	view.setZoom(12);
});



// MDL CODE
var city ={
	milan 		: ol.proj.fromLonLat([9.1858849, 45.4654005]),
    florence 	: ol.proj.fromLonLat([11.2497741, 43.769539])
};


function vectorSourceRefresh() {
    console.log("a");

    $.ajax(urlTemplate).then(function(response) {
            var format = new ol.format.GeoJSON();
            var features = format.readFeatures(response,  {featureProjection: 'EPSG:3857'});

            for (var i=0; i<features.length; i++) {
              features[i].setId(features[i].get('plate'));
              var ft = vectorSource.getFeatureById(features[i].getId());
              if (ft) {
                ft.setGeometry(features[i].getGeometry());
              } else {
                vectorSource.addFeature(features[i]);
                console.log("Add: "+features[i].get('plate'));
              }
            }


            // Determino il numero di elementi caricati
            $("#element-counter input").val(features.length);
            console.log(features.length);
            vectorSource.changed();
    });
}


setInterval( vectorSourceRefresh, 2500);


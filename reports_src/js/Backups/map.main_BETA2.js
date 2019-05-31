
/*
var fsource = new ol.source.Vector({
extractStyles: false,
projection: 'EPSG:3857',
url: 'trips-geo.php',
format: new ol.format.GeoJSON()
});
*/






/*********************** ESEMPIO CAMBIO DINAMICO FILTRI DI CARICAMENTO DATI *****************/
var cqlFilter = 'ID=355';

var urlTemplate = 'data/trips-geo-data.php?'+
    'CQL_FILTER={{cqlFilter}}';

/*
var fsource = new ol.source.Vector({
extractStyles: false,
projection: 'EPSG:3857',
url: 'trips-geo.php',
format: new ol.format.GeoJSON()
});
*/


var vectorSource = new ol.source.Vector({
	extractStyles: false,
	projection: 'EPSG:3857',
	url: urlTemplate,
	format: new ol.format.GeoJSON()
})



var vector = new ol.layer.Heatmap(
	{
	source:vectorSource,
	radius: 12
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
		vector.setRadius(zoom*1.2);
		console.log(zoom);
		lastZoom = zoom;
	}
}


var cnt = 0;
function animate()
{
	feats = fsource.getFeatures();
	console.log(feats[cnt]);
	vector.getSource().addFeature(feats[cnt]);
	cnt++;

}


//setInterval(animate,300);




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



function changeFilter() {
  cqlFilter = 'ID=455';
  vectorSource.clear(true);
}

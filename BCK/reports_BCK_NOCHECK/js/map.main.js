
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

var today	 	= new Date();
var aMonthAgo 	= new Date();
aMonthAgo.setMonth(today.getMonth() - 1);

var todayFormatted 		= today.getFullYear() + '-' + today.getMonth() + '-' + today.getDate();
var aMonthAgoFormatted 	= aMonthAgo.getFullYear() + '-' + aMonthAgo.getMonth() + '-' + aMonthAgo.getDate();

// Setting Up the star date to a month ago.
$("#datepicker #end")	.val( todayFormatted );
$("#datepicker #start")	.val( aMonthAgoFormatted );

var params = {
	dateFrom 	: aMonthAgoFormatted,
	dateTo		: todayFormatted,
	begend		: 0, 	// 0 ==> Beginning Hour ||  1 ==> Ending Hour
    weight      : 0.192,
    base_weight : 0.4
}

var urlTemplate = 'data/trips-geo-data.php?'+
    'dateFrom={{dateFrom}}&'+
	'dateTo={{dateTo}}&'+
	'begend={{begend}}';

var vectorSource = new ol.source.Vector({
	extractStyles: false,
	projection: 'EPSG:3857',
	loader: function(extent, resolution, projection) {
		var url = urlTemplate
			.replace('{{dateFrom}}', params.dateFrom)
			.replace('{{dateTo}}', params.dateTo)
			.replace('{{begend}}', params.begend);
		$.ajax(url).then(function(response) {
            var format = new ol.format.GeoJSON();
            var features = format.readFeatures(response,
            {featureProjection: projection});
            vectorSource.addFeatures(features);

			// Determino il numero di elementi caricati
			$("#element-counter input").val(features.length);
        });
	},
	format: new ol.format.GeoJSON()
});


// This functions will change the source loader params to pass to the url
// so we can make a specific ajax call
function changeFilterDateFrom(dateFrom) {
  params.dateFrom = dateFrom;
  vectorSource.clear(true);
}
function changeFilterDateTo(dateTo){
   params.dateTo = dateTo;
  vectorSource.clear(true);
}
function changeFilterBegEnd(begend){
   params.begend = begend;
  vectorSource.clear(true);
}


var vector = new ol.layer.Heatmap(
	{
	source:vectorSource,
	radius: 12,
    opacity: 0.7,
    blur: 14,
    weight: function(f) {
      return  params.base_weight + params.weight;
    }
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
		vector.setRadius(zoom*1.0);
        params.weight =  (0.4*zoom/25);
		console.log(zoom);
		lastZoom = zoom;

	}
}


console.log(vector);

var cnt = 0;
function animate()
{
    /*
	feats = fsource.getFeatures();
	console.log(feats[cnt]);
	vector.getSource().addFeature(feats[cnt]);
    */

    params.weight = 0.1*cnt;
    console.log(params.weight);
    vector.getSource().changed();
    //map.renderSync();
	cnt++;
    if (cnt>10) cnt=0;

}


//setInterval(animate,100);




// MDL CODE
var city ={
	milan 		: ol.proj.fromLonLat([9.1858849, 45.4654005]),
    florence 	: ol.proj.fromLonLat([11.2497741, 43.769539])
};


            

$('#weight').slider({
	formatter: function(value) {
        params.base_weight = value/10;
        vector.getSource().changed();
      	return '' + value/10;
	}
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

$("#change-begend").click(function()
{
	$(this).text(function(i, text){
    	return text === "Change to Ending Location" ? "Change to Beginning Location" : "Change to Ending Location";
    })

	params.begend == 0 ? changeFilterBegEnd(1) : changeFilterBegEnd(0);
});

console.log(today);

$('.input-daterange').datepicker({
    format: "yyyy-mm-dd",
    language: "it",
    endDate:	today,
    orientation: "bottom auto",
    autoclose: true
});

$('.input-daterange').datepicker()
    .on("changeDate", function(e) {
        if (e.target.id == "start"){
            // id = start
			changeFilterDateFrom($(e.target).val());
        }else{
            // id = end
			changeFilterDateTo($(e.target).val());
        }
});
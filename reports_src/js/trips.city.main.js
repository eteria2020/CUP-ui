


// Global Vars Definition
var global = {
	// Obj containing the citys (from ajax call)
	city: {},
	params: {},

	// Charts definition
	charts: {
		day				: dc.barChart('#days-chart'),
		dayOfWeek		: dc.rowChart('#day-of-week-chart'),
		duration 		: dc.barChart('#duration-chart'),
		beginningHour	: dc.barChart('#beginning-hour-chart'),
		age				: dc.pieChart('#age-chart'),
		gender			: dc.pieChart('#gender-chart'),
		arealist		: dc.rowChart('#area-list-chart'),
		areamap			: dc.geoChoroplethChart("#area-map-chart")
	},

	// Set the timeout needed for the page resize bind function
	timeout: 0
};

// The magic!
$(document).ready(function(){

	getCityData();
    getCharts();

	// Print the DC.js version
	console.log("Version:"+dc.version);
	d3.selectAll('#version').text(dc.version);
});

// Window Resize Action Bind
$(window).resize(function() {
    clearTimeout(global.timeout);
    global.timeout = setTimeout(resizeCharts, 500);
});



/**
 *	This page make an AJAX request to the "trips-data.php" getting the "city" number,
 *	name, and id.
 *
 *	It also makes the Submenu and populate it.
 *
 */
function getCityData(){
	$.ajax({
		method: "GET",
		dataType: "json",
		url: 'data/trips-data.php',
		data: { action:"getCitys",k:"70F2F21227ECA0FA0A60336CF9809053D18EA65A67575E646376E61A570F5A4B"},
		success: function(d){
			$('#navbar li:eq(0)').after('<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">City Trips <span class="caret"></span></a><ul class="dropdown-menu"></ul></li>')

			global.city = d.city;

            $.each( global.city, function( key, value ) {
            	if(fleetId == value.fleet_id){	global.params = value.params;	}
				$("#navbar ul.dropdown-menu").append('<li><a href="trips-city.php?city=' + value.fleet_id + '">' + value.fleet_name + '</a></li>');
			});
		}
	});
}


function getCharts(){
	// Get the data records of the city (the fleetId MUST be specified in the trips-city page

	d3.csv('data/trips-data.php?action=getCityData&k=70F2F21227ECA0FA0A60336CF9809053D18EA65A67575E646376E61A570F5A4B&city='+fleetId, function (trips_record)
	{
		// Variables
		var	formatDate = d3.time.format('%Y-%m-%d %H:%M:%S'),
		    ddmin = null,
	    	ddmax = null,
			maxval = 0;

        //	 A little coercion, since the CSV is untyped.
		trips_record.forEach(function(d, i)
		{
			d.dd = formatDate.parse(d.time_beginning_parsed);
			if (ddmin==null || d.dd < ddmin) ddmin = d.dd;
	        if (ddmax==null || d.dd > ddmax) ddmax = d.dd;
		});

		// Create the crossfilter for the relevant dimensions and groups.
		var trips			= crossfilter(trips_record),
			all				= trips.groupAll(),

			// dc.barChart('#days-chart')
			date_beginning	= trips.dimension(function(d){return d.dd;}),
			days			= date_beginning.group(d3.time.day),

			// dc.rowChart('#day-of-week-chart')
			dayOfWeek		= trips.dimension(function(d){
				var name	= ['','Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
				return name[d.time_dow];
			}),
			dayOfWeeks		= dayOfWeek.group(),

			// dc.barChart('#beginning-hour-chart')
			beginningHour	= trips.dimension(function(d){return d.dd.getHours();}),
			beginningHours	= beginningHour.group(),

			// dc.barChart('#duration-chart')
			duration		= trips.dimension(function(d){return Math.min(d.time_total_minute,61);}),
			durations		= duration.group(),

			// dc.pieChart('#gender-chart')
			gender			= trips.dimension(function(d){return d.customer_gender;}),
			genders			= gender.group(),


			real_area		= trips.dimension(function(d){return +d.area_id;}),
			//real_areas		= real_area.group().reduceCount().value(),
            real_areas		= real_area.group().reduceCount(),

			// dc.rowChart('#area-chart')
			area			= trips.dimension(function(d){return [d.area_id,""+d.area_name];}),
			areas			= area.group(),


			// dc.pieChart('#age-chart')
	        age				= trips.dimension(function(d){
		   		if (18 <= d.customer_age && d.customer_age <= 24 )		return 0;
				else if(25 <= d.customer_age && d.customer_age <= 34 )	return 1;
				else if(35 <= d.customer_age && d.customer_age <= 44 )	return 2;
				else if(45 <= d.customer_age && d.customer_age <= 54 )	return 3;
				else if(55 <= d.customer_age && d.customer_age <= 64 )  return 4;
				else if( d.customer_age > 64 )							return 5;
			}),
		   	ages			= age.group()
		;



		// Get the urban areas
		d3.json("data/urban-areas-data.php?k=70F2F21227ECA0FA0A60336CF9809053D18EA65A67575E646376E61A570F5A4B&city="+fleetId, function (areasjson) {
            // Calculate the max value of record recursion
	   		maxval = real_areas.top(1)[0].value;

			date_beginning.filterAll();
			global.charts.day.width(900)
		        .margins({top: 30, left: 40, right: 10, bottom: 20})
	            .renderLabel(false)
	            .x(d3.time.scale().domain(d3.extent(trips_record, function(d) { return d.dd; })))
	            .xUnits(d3.time.days)
		        .height(250)
		        .gap(2)
		        .group(days)
		        .dimension(date_beginning)
		        .mouseZoomable(true)
		        .elasticY(true)
	            .xAxisPadding(1)
	            .centerBar(true)
	            .elasticX(true)
   				/*.x(d3.time.scale()
		            .domain([ddmin, ddmax])
		            .rangeRound([0, 10 * 10]))*/
		        .round(d3.time.month.round)
		        .renderHorizontalGridLines(true);
			global.charts.day.yAxis().ticks(2);

		    age.filterAll();
			global.charts.age.width(180)
		        .height(180)
		        .radius(80)
		        .innerRadius(30)
		        .dimension(age)
		        .group(ages)
				.label(function (d) {
					switch(d.key){
						case 0: return '18-24'; 	break;
						case 1: return '25-34';		break;
						case 2: return '35-44'; 	break;
						case 3: return '45-54';		break;
						case 4: return '55-65'; 	break;
						case 5: return 'Over 64';	break;
					}
		        });

		    gender.filterAll();
			global.charts.gender.width(180)
		        .height(180)
		        .radius(80)
		        .innerRadius(30)
		        .dimension(gender)
		        .group(genders)
				.label(function (d) {
					var lbl 	= d.key == "male" ? 'Uomini ' : 'Donne ',
						percent = 0;

		            if (global.charts.gender.hasFilter() && !global.charts.gender.hasFilter(d.key)){
		            	percent = 0;
		            }else{
				   		percent = (d.value / trips.groupAll().reduceCount().value() * 100);
					}

					lbl += percent.toFixed(2) + "%";

					return lbl;
		            //return d.key == "male" ? 'Uomini ' + Math.round((d.value*100)/(gender.top(Number.POSITIVE_INFINITY).length)) + "%": 'Donne ' + Math.round((d.value*100)/(gender.top(Number.POSITIVE_INFINITY).length)) + "%";
		        });


			dayOfWeek.filterAll();
			global.charts.dayOfWeek.width(400)
		        .height(250)
		        .margins({top: 20, left: 10, right: 10, bottom: 20})
		        .group(dayOfWeeks)
		        .dimension(dayOfWeek)
		        .elasticX(true)
		        .label(function (d) {
		            return d.key;
		        })
		        .title(function (d) {
		            return d.value;
		        });


			beginningHour.filterAll();
			global.charts.beginningHour.width(450)
		        .height(250)
		        .margins({top: 50, right: 10, bottom: 30, left: 50})
				.dimension(beginningHour)
		        .group(beginningHours)
				.elasticY(true)
				.centerBar(true)
				.mouseZoomable(true)
		    	.round(d3.time.hour.round)
				.x(
					d3.scale.linear()
					.domain([0, 24])
				)
				.renderHorizontalGridLines(true);
		    global.charts.beginningHour.xAxis().tickFormat(
		    function (v) {
				return v + 'h';
			});
		    global.charts.beginningHour.yAxis().ticks(5);

			duration.filterAll();
			global.charts.duration.width(450)
		        .height(250)
		        .margins({top: 50, right: 10, bottom: 30, left: 50})
				.dimension(duration)
		        .group(durations)
				.elasticY(true)
				.centerBar(true)
				.mouseZoomable(true)
		        .round(d3.time.minute.round)//dc.round.floor)
				.x(
					d3.scale.linear()
					.domain([0, 60])
					.rangeRound([0, 10 * 60])
				)
				.renderHorizontalGridLines(true);
		    global.charts.duration.xAxis().tickFormat(
			    function (v) {
					return v + 'm';
				});
		    global.charts.duration.yAxis().ticks(5);

			/**
			 * This functuon return a shade of a color,
			 */
			var getColor = d3.scale.linear().domain([0, maxval]).range(["yellow", "red"]);


            area.filterAll();

			var width = 500,
				height= 500;


            real_area.filterAll();
        	global.charts.areamap
				.width(width)
				.height(height)
				.dimension(real_area)
				.group(real_areas)

				.colorCalculator(function(d){ return d ? getColor(d) : '#ffffff';})

				.overlayGeoJson(areasjson.features, "quartiere", function (d) {
					return d.properties.to_char;
				})
				.title(function (d) {
					return "Area: " + d.key + "\nConcentrazione: "+d.value;
				})
				.projection(
					d3.geo.stereographic()
						.center([
                    		global.params.center.longitude,
							global.params.center.latitude
						])//[3.9,43.0])
						.scale(global.params.scale)
						.translate([
							width / global.params.translation.width ,
							height / global.params.translation.height
						])
				);

			var maxColor = getColor(maxval),
				minColor = getColor(0);

			$(".area-chart").append('<div><span style="float:left;color: '+maxColor+
				'">&nbsp;0</span><span style="float:right;color:'+minColor+
				'">'+maxval+'&nbsp;</span>'+
				'<div style="'+
				'height: 20px;'+
				'background: '+maxColor+';'+ /* For browsers that do not support gradients */
			    'background: -webkit-linear-gradient(left, '+minColor+' , '+maxColor+');'+ /* For Safari 5.1 to 6.0 */
			    'background: -o-linear-gradient(right, '+minColor+', '+maxColor+');'+ /* For Opera 11.1 to 12.0 */
			    'background: -moz-linear-gradient(right, '+minColor+', '+maxColor+');'+ /* For Firefox 3.6 to 15 */
			    'background: linear-gradient(to right, '+minColor+' , '+maxColor+');'+ /* Standard syntax (must be last) */
				'"></div></div>');


			global.charts.arealist.width(500)
		        .gap(1)
		        .margins({top: 10, left: 10, right: 10, bottom: 30})
		        .group(areas)
		        .dimension(area)
		        .ordering(function(d) { return -d.value })
		        .rowsCap(100)
		        .elasticX(true)

		        .label(function (d) {
		        	var e = d.key[0] > 0 ? d.key[0] : 0;

					/*
					$("#area-map-chart svg > g.layer0 > g.quartiere."+e+" > path").css("fill",getColor(d.value));
					$("#area-map-chart svg > g.layer0 > g.quartiere."+e+" > path").css("stroke",getColor(d.value));
                    $("#area-map-chart svg > g.layer0 > g.quartiere."+e+">  path").css("border","1px solid black");

                    var title = $("#area-map-chart svg > g.layer0 > g.quartiere."+e+" > title");
					console.log(title.text() + "  VALUE: "+d.value) ;
                    title.text(title.text()+d.value);
                              */
		            return d.key[1] ;
		        })
		        .title(function (d) {
		            return d.key + " : " + d.value;
		        });



             //

            $("svg").css("background-color","red");

			 dc.dataCount('#data-count')
		        .dimension(trips)
		        .group(all)
		        .html({
		            some:'<strong>%filter-count</strong> selected out of <strong>%total-count</strong> records' +
		                ' | <a href=\'javascript:dc.filterAll(); dc.renderAll();\'\'>Reset All</a>',
		            all:'All records selected. Please click on the graph to apply filters.'
		        });


	        //dc.renderAll();
	        //dc.redrawAll();

	        //console.log("1");

			// Graphics are loaded, so I resize the graphs
		    resizeCharts();

			// Recompose the chart structure (to adapt for Bootstrap)
			$("div.panel-body > div:not(.chart-label) >span, div.panel-body > div:not(.chart-label) > a").appendTo("div.panel-heading");

	        // Coloring the Age Pie Chart Legend
			$("div.panel .chart-label > span:nth-of-type(3)").css("color", $("div.panel#customer-age g.pie-slice._5 path").css("fill")) ;
			$("div.panel .chart-label > span:nth-of-type(2)").css("color", $("div.panel#customer-age g.pie-slice._4 path").css("fill")) ;
			$("div.panel .chart-label > span:nth-of-type(1)").css("color", $("div.panel#customer-age g.pie-slice._3 path").css("fill")) ;

			// Setting the correct svg width of the Map Chart
			// Doing this the chart is vertically centered
	        $(".area-list-chart").css("width",width+"px");

			//dc.renderAll();
		});
	});
}


/**
 *
 * This function resize the Charts, adapting it to the body width.
 *
 */
function resizeCharts(){
	var newWidth 				= $(".panel-body").width(),
		newRadiusChartsWidth	= $(".col-xs-12 .panel-body").width();


	global.charts.day.width(newWidth)
	.transitionDuration(0);

    global.charts.dayOfWeek.width(newWidth)
	.transitionDuration(0);

	global.charts.beginningHour.width(newWidth)
	.transitionDuration(0);

	global.charts.duration.width(newWidth)
	.transitionDuration(0);

	global.charts.arealist
		.width(newWidth)
		.height(global.charts.arealist.height() + 800)
		.transitionDuration(0);

	global.charts.age
		.width(newRadiusChartsWidth-5)
		.height(newRadiusChartsWidth-5)
		.radius((newRadiusChartsWidth/2.2)-20)
		.transitionDuration(0);

	global.charts.gender
		.width(newRadiusChartsWidth-5)
		.height(newRadiusChartsWidth-5)
		.radius((newRadiusChartsWidth/2.2)-20)
		.transitionDuration(0);

	// Render the charts
	dc.renderAll();
}

/**
 *
 *	This function print the specified filter
 *
 *  @param	filter	A filter (d3.dimension)
 *	@case	DEBUG
 */
function print_filter(filter){
	var f=eval(filter);
	if (typeof(f.length) != "undefined") {}else{}
	if (typeof(f.top) != "undefined") {f=f.top(Infinity);}else{}
	if (typeof(f.dimension) != "undefined") {f=f.dimension(function(d) { return "";}).top(Infinity);}else{}
	//console.log(filter+"("+f.length+") = "+JSON.stringify(f).replace("[","[\n\t").replace(/}\,/g,"},\n\t").replace("]","\n]"));
}
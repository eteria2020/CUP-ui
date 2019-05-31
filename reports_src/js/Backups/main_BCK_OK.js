
//Funzione di debug da usare per stampare l'output di un filtro.
function print_filter(filter){
	var f=eval(filter);
	if (typeof(f.length) != "undefined") {}else{}
	if (typeof(f.top) != "undefined") {f=f.top(Infinity);}else{}
	if (typeof(f.dimension) != "undefined") {f=f.dimension(function(d) { return "";}).top(Infinity);}else{}
	console.log(filter+"("+f.length+") = "+JSON.stringify(f).replace("[","[\n\t").replace(/}\,/g,"},\n\t").replace("]","\n]"));
}


//definizione dei grafici
var charts ={
		day				: dc.barChart('#days-chart'),
		dayOfWeek		: dc.rowChart('#day-of-week-chart'),
		duration 		: dc.barChart('#duration-chart'),
		beginningHour	: dc.barChart('#beginning-hour-chart'),
		city			: dc.pieChart('#city-chart'),
		age				: dc.pieChart('#age-chart'),
		gender			: dc.pieChart('#gender-chart'),
        area			: dc.rowChart('#area-chart')

		// IMPLEMENTA DIVISIONE PER CAP DI RESIDENZA
	};

//Download ed elaborazione dei dati
d3.csv('data/trips-data.php', function (trips_record)
{
	// Global d3 Vars
	var	formatDate = d3.time.format('%Y-%m-%d %H:%M:%S'),
	    ddmin = null,
    	ddmax = null;

	/* A nest operator, for grouping the trips
	var nestByDate = d3.nest()
	.key(function(d)
		{return d3.time.day(d.date);});
      */
	//	 A little coercion, since the CSV is untyped.
	trips_record.forEach(function(d, i)
	{
		d.dd = formatDate.parse(d.time_beginning_parsed);
		if (ddmin==null || d.dd < ddmin) ddmin = d.dd;
        if (ddmax==null || d.dd > ddmax) ddmax = d.dd;
	});

	// Adding the correct info to the page
	//$("#dateFrom").text(ddmin.getDay()() + '/' + (ddmin.getMonth() + 1) + '/' +  ddmin.getFullYear());
	//$("#dateTo").text(ddmax.getDay() + '/' + (ddmax.getMonth() + 1) + '/' +  ddmax.getFullYear());


	// Create the crossfilter for the relevant dimensions and groups.
	var trips			= crossfilter(trips_record),
		all				= trips.groupAll(),

		date_beginning	= trips.dimension(function(d){return d.dd;}),
		days			= date_beginning.group(d3.time.day),

		dayOfWeek		= trips.dimension(function(d){
			var name	= ['','Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
			return name[d.time_dow];
		}),
		dayOfWeeks		= dayOfWeek.group();

		beginningHour	= trips.dimension(function(d){return d.dd.getHours();}),
		beginningHours	= beginningHour.group(),

		duration		= trips.dimension(function(d){return Math.min(+d.time_total_minute,61);}),
		durations		= duration.group(),

		gender			= trips.dimension(function(d){return d.customer_gender;}),
		genders			= gender.group(),

		city			= trips.dimension(function(d){return d.fleet_id;}),
		citys			= city.group(),

	   	area			= trips.dimension(function(d){return [d.area_id,d.area_name,d.fleet_id];}),
	   	areas			= area.group(),

        age				= trips.dimension(  function(d){
										   		if (18 <= d.customer_age && d.customer_age <= 24 ){
										   			return 0;
										   		}
												else if(25 <= d.customer_age && d.customer_age <= 34 ){
									                return 1;
												}
												else if(35 <= d.customer_age && d.customer_age <= 44 ){
									                return 2;
												}
												else if(45 <= d.customer_age && d.customer_age <= 54 ){
									                return 3;
												}
												else if(55 <= d.customer_age && d.customer_age <= 64 ){
									                return 4;
												}else if( d.customer_age > 64 ){
									                return 5;
												}
										   	}),
	   	ages			= age.group()

		;

	date_beginning.filterAll();
	charts.day.width(900)
        .margins({top: 20, left: 40, right: 10, bottom: 20})
        .height(250)
        .gap(1)
        .group(days)
        .dimension(date_beginning)
        .mouseZoomable(true)
        .elasticY(true)
        .x(d3.time.scale()
            .domain([ddmin, ddmax])
            .rangeRound([0, 10 * 10]))
        .round(d3.time.month.round)
        .renderHorizontalGridLines(true);
	charts.day.yAxis().ticks(2);

    age.filterAll();
	charts.age.width(180)
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
	charts.gender.width(180)
        .height(180)
        .radius(80)
        .innerRadius(30)
        .dimension(gender)
        .group(genders)
		.label(function (d) {
			var lbl 	= d.key == "male" ? 'Uomini ' : 'Donne ',
				percent = 0;

            if (charts.gender.hasFilter() && !charts.gender.hasFilter(d.key)){
            	percent = 0;
            }else{
		   		percent = (d.value / trips.groupAll().reduceCount().value() * 100);
			}

			lbl += percent.toFixed(2) + "%";

			return lbl;
            //return d.key == "male" ? 'Uomini ' + Math.round((d.value*100)/(gender.top(Number.POSITIVE_INFINITY).length)) + "%": 'Donne ' + Math.round((d.value*100)/(gender.top(Number.POSITIVE_INFINITY).length)) + "%";
        });

	city.filterAll();
	charts.city.width(180)
        .height(180)
        .radius(80)
        .innerRadius(30)
        .dimension(city)
        .group(citys)
		.label(function (d) {
			switch(d.key){
				case '1': return 'Milano'; 	break;
				case '2': return 'Firenze';	break;
			}
        });

	dayOfWeek.filterAll();
	charts.dayOfWeek.width(400)
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
	charts.beginningHour.width(450)
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
    charts.beginningHour.xAxis().tickFormat(
    function (v) {
		return v + 'h';
	});
    charts.beginningHour.yAxis().ticks(5);


	duration.filterAll();
	charts.duration.width(450)
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
    charts.duration.xAxis().tickFormat(
    function (v) {
		return v + 'm';
	});
    charts.duration.yAxis().ticks(5);

	area.filterAll();
	charts.area.width(500)
        .gap(1)
        .margins({top: 10, left: 10, right: 10, bottom: 30})
        .group(areas)
        .dimension(area)
        .ordering(function(d) { return -d.value })
        .rowsCap(100)
        .elasticX(true)

        .label(function (d) {
            return d.key[2] == 1 ? 'MI ' + d.key[1]: 'FI ' +d.key[1] ;
        })
        .title(function (d) {
            return d.key + " : " + d.value;
        });





	dc.dataCount('#data-count')
        .dimension(trips)
        .group(all)
        .html({
            some:'<strong>%filter-count</strong> selected out of <strong>%total-count</strong> records' +
                ' | <a href=\'javascript:dc.filterAll(); dc.renderAll();\'\'>Reset All</a>',
            all:'All records selected. Please click on the graph to apply filters.'
        });


        dc.renderAll();
        //dc.redrawAll();

        console.log("1");

		// Graphics are loaded, so I resize the graphs
	    doneResizing();

		// Recompose the chart structure (to adapt for Bootstrap)
		$("div.panel-body > div:not(.chart-label) >span, div.panel-body > div:not(.chart-label) > a").appendTo("div.panel-heading");

        // Coloring the Age Pie Chart Legend
		$("div.panel .chart-label > span:nth-of-type(3)").css("color", $("div.panel#customer-age g.pie-slice._5 path").css("fill")) ;
		$("div.panel .chart-label > span:nth-of-type(2)").css("color", $("div.panel#customer-age g.pie-slice._4 path").css("fill")) ;
		$("div.panel .chart-label > span:nth-of-type(1)").css("color", $("div.panel#customer-age g.pie-slice._3 path").css("fill")) ;
});

console.log("Version:"+dc.version);
d3.selectAll('#version').text(dc.version);

// Resize only if the window.resize is done
var id;
$(window).resize(function() {
    clearTimeout(id);
    id = setTimeout(doneResizing, 500);

});


function doneResizing(){
	var newWidth 				= $(".panel-body").width(),
		newRadiusChartsWidth	= $(".col-xs-12 .panel-body").width();


	charts.day.width(newWidth)
	.transitionDuration(0);

    charts.dayOfWeek.width(newWidth)
	.transitionDuration(0);

	charts.beginningHour.width(newWidth)
	.transitionDuration(0);

	charts.duration.width(newWidth)
	.transitionDuration(0);

	charts.area
		.width(newWidth)
		.height(	charts.area.height() + 800)
		.transitionDuration(0);

	charts.city
		.width(newRadiusChartsWidth-5)
		.height(newRadiusChartsWidth-5)
		.radius((newRadiusChartsWidth/2.2)-20)
		.transitionDuration(0);

	charts.age
		.width(newRadiusChartsWidth-5)
		.height(newRadiusChartsWidth-5)
		.radius((newRadiusChartsWidth/2.2)-20)
		.transitionDuration(0);

	charts.gender
		.width(newRadiusChartsWidth-5)
		.height(newRadiusChartsWidth-5)
		.radius((newRadiusChartsWidth/2.2)-20)
		.transitionDuration(0);

	dc.renderAll();
	//charts.day.transitionDuration(750);
}


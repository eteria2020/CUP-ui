var fluctuation;
var fluctuationGroup;
function print_filter(filter){
	var f=eval(filter);
	if (typeof(f.length) != "undefined") {}else{}
	if (typeof(f.top) != "undefined") {f=f.top(Infinity);}else{}
	if (typeof(f.dimension) != "undefined") {f=f.dimension(function(d) { return "";}).top(Infinity);}else{}
	console.log(filter+"("+f.length+") = "+JSON.stringify(f).replace("[","[\n\t").replace(/}\,/g,"},\n\t").replace("]","\n]"));
}


//definizione dei grafici
var dayOfWeekChart = dc.rowChart('#day-of-week-chart');
var durationChart = dc.barChart('#duration-chart');
var daysChart = dc.barChart('#days-chart');
var hoursChart = dc.barChart('#hours-chart');
var sexChart = dc.pieChart('#sex-chart');


//Download ed elaborazione dei dati
d3.csv('http://core.sharengo.it/ui/reports/trips-data_BCK.php', function (data){

    var dateFormat = d3.time.format('%Y-%m-%d %H:%M:%S');
    var numberFormat = d3.format('.2f');

    var cs =0;
    data.forEach(function (d) {
        d.dow = +d.time_dow;
        d.sesso = +d.customer_gender;
        d.dd = dateFormat.parse(d.time_beginning_hour);
        cs++;
    });

    console.log(cs);

    console.log(data);

    var ndx = crossfilter(data);
    var all = ndx.groupAll();


    var days = ndx.dimension(function (d) {
        return d.time_beginning_day;
    });
    days.filterAll();
    daysGroup = days.group();


    var duration = ndx.dimension(function (d) {
        return d.time_total_minute;
        //return Math.min(+d.time_total_minute,61);
    });
    duration.filterAll();
    durationGroup = duration.group();

	/*
    length = ndx.dimension(function (d) {
        return d.km
    });
    length.filterAll();
    lengthGroup = length.group();
	*/

    time_beginning_hour = ndx.dimension(function (d) {
        return d.time_beginning_hour;
    });
    time_beginning_hour.filterAll();
    hoursGroup = time_beginning_hour.group();


    var sex = ndx.dimension(function (d) {
        return d.customer_gender;
    })
    sex.filterAll();
    var sexGroup = sex.group();


    var dayOfWeek = ndx.dimension(function (d) {
        var day = d.time_dow;
        var name = ['','Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
        return day + '.' + name[day];
    });
    dayOfWeek.filterAll();
    var dayOfWeekGroup = dayOfWeek.group();
    console.log(dayOfWeekGroup.size());


    daysChart.width(800)
        .margins({top: 20, left: 40, right: 10, bottom: 20})
        .height(250)
        .group(daysGroup)
        .dimension(days)
        .mouseZoomable(true)
        .elasticY(true)
        .x(d3.time.scale()
            .domain([new Date(2014, 5, 1), new Date(2015,4, 31)])
            .rangeRound([0, 10 * 90]))
        .round(d3.time.month.round)
        .renderHorizontalGridLines(true);
//        .renderArea(true);

        //daysChart.yAxis().ticks(5)



    dayOfWeekChart.width(300)
        .height(200)
        .margins({top: 20, left: 10, right: 10, bottom: 20})
        .group(dayOfWeekGroup)
        .dimension(dayOfWeek)

        .label(function (d) {
            return d.key.split('.')[1];
        })
        .title(function (d) {
            return "";//d.value;
        })



     durationChart.width(420)
        .height(200)
        .margins({top: 10, right: 50, bottom: 30, left: 40})
        .dimension(duration)
        .group(durationGroup)
        .mouseZoomable(true)
        .elasticY(true)
        .centerBar(true)
        .round(dc.round.floor)
        .x(d3.scale.linear().domain([0, 60]))
        .renderHorizontalGridLines(true)
        durationChart.xAxis().tickFormat(
        function (v) { return v + 'm'; });
        durationChart.yAxis().ticks(5);

     hoursChart.width(420)
        .height(200)
        .margins({top: 10, right: 50, bottom: 30, left: 40})
        .dimension(time_beginning_hour)
        .group(hoursGroup)
        .mouseZoomable(true)
        .elasticY(true)
        .centerBar(true)
        .round(dc.round.floor)
        .x(d3.scale.linear().domain([0, 24]))
        .renderHorizontalGridLines(true)
        hoursChart.xAxis().tickFormat(
        function (v) { return v + 'h'; });
        hoursChart.yAxis().ticks(5);


     sexChart.width(180)
        .height(180)
        .radius(80)
        .innerRadius(30)
        .dimension(sex)
        .group(sexGroup);



        dc.dataCount('#data-count')
        .dimension(ndx)
        .group(all)
        .html({
            some:'<strong>%filter-count</strong> selected out of <strong>%total-count</strong> records' +
                ' | <a href=\'javascript:dc.filterAll(); dc.renderAll();\'\'>Reset All</a>',
            all:'All records selected. Please click on the graph to apply filters.'
        });


        dc.renderAll();
        //dc.redrawAll();

        console.log("1");

});

console.log("Version:"+dc.version);
d3.selectAll('#version').text(dc.version);

// console.log("Version:"+dc.version);
//  d3.selectAll('#version').text(dc.version);
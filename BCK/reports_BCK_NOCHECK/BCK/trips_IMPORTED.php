<html>
<head>
<link rel="stylesheet" type="text/css" href="node_modules/dc/dc.css" media="screen"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<script src="node_modules/dc/jquery-2.1.3.min.js"></script>
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>-->
<script src="node_modules/dc/d3.min.js"></script>
<script src="node_modules/dc/crossfilter.js"></script>
<script src="node_modules/dc/dc.min.js"></script>
<script type="text/javascript" src="node_modules/dc/colorbrewer.js"></script>
 <style>
.dc-chart g.row text {fill: black;}.
 </style>

    <?php
    $date  = new DateTime();
    $interval = new DateInterval('P28D');
    // porta a venerdi
    $date->sub($interval);
    $start_day    =    $date->format('d-m-Y');

    $date  = new DateTime();
    $interval = new DateInterval('P1D');
    // porta a venerdi
    $date->sub($interval);
    $start_day2    =    $date->format('d-m-Y');

    ?>

</head>
<body>
<br>
<div class="container">

    <div id="version"></div>

    <div id="days-chart" style="height: 350px">
        <strong style="color: #000088;font-size: larger">Intervallo  (ultimi 28 giorni). &nbsp;&nbsp;Dal <?php echo $start_day . " &nbsp;al&nbsp; " . $start_day2 ." &nbsp;&nbsp;&nbsp;" ?> </strong>
        <a class="reset" href="javascript:daysChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
        <div class="clearfix"></div>
    </div>

    <div id="day-of-week-chart" style="height: 350px">
        <strong style="color: #000088;font-size: larger">Corse per giorno della settimana </strong>
        <a class="reset" href="javascript:dayOfWeekChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
        <div class="clearfix"></div>
    </div>
    <br>
    <div id="duration-chart" style="height: 350px">
        <strong style="color: #000088;font-size: larger">&nbsp;&nbsp;&nbsp;Corse per durata (minuti)</strong>
        <span class="reset" style="display: none;">intervallo: <span class="filter"></span></span>
        <a class="reset" href="javascript:durationChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
        <div class="clearfix"></div>
    </div>

    <div id="length-chart" style="height: 300px">
        <strong style="color: #000088;font-size: larger" >Corse per lunghezza (km)</strong>
        <span class="reset" style="display: none;">intervallo: <span class="filter"></span></span>
        <a class="reset" href="javascript:lengthChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
        <div class="clearfix"></div>
    </div>

    <div id="hours-chart" style="height: 350px">
        <strong style="color: #000088;font-size: larger" >&nbsp;&nbsp;&nbsp;Corse ora di inizio</strong>
        <span class="reset" style="display: none;">intervallo: <span class="filter"></span></span>
        <a class="reset" href="javascript:hoursChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
        <div class="clearfix"></div>
    </div>

    <div id="sex-chart" style="height: 300px">
        <strong style="color: #000088;font-size: larger" >Suddivisione per sesso</strong>
        <span class="reset" style="display: none;">range: <span class="filter"></span></span>
        <a class="reset" href="javascript:sexChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
        <div class="clearfix"></div>
    </div>

     <div id="nil-chart" style="height: 800px">
        <strong style="color: #000088;font-size: larger" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Suddivisione per quartiere</strong>
        <span class="reset" style="display: none;">range: <span class="filter"></span></span>
        <a class="reset" href="javascript:nilChart.filterAll();dc.redrawAll();" style="display: none;"> Azzera </a>
        <div class="clearfix"></div>
    </div>

    <div id='data-count' style="height: 300px">
      <!--  <span class='filter-count'></span> selected out of <span class='total-count'></span> records -->
    </div>
</div>
</body>

<script>

//Funzione di debug da usare per stampare l'output di un filtro.
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
  var lengthChart = dc.barChart('#length-chart');
  var daysChart = dc.barChart('#days-chart');
  var hoursChart = dc.barChart('#hours-chart');
  var sexChart = dc.pieChart('#sex-chart');
  var nilChart = dc.rowChart('#nil-chart');


//Download ed elaborazione dei dati
  d3.csv('http://www.twistcar.it/api/ui/fatture-data.php', function (data) {

    var dateFormat = d3.time.format('%Y-%m-%d %H:%M:%S');
    var numberFormat = d3.format('.2f');
    var ddmin = null;
    var ddmax = null;

    //Scorre tutti i dati per eventuali conversioni di formato
    var cs =0;
    data.pop();

    data.forEach(function (d) {
        //d.dow = +d.dow;
        //d.sesso = +d.sesso;
        d.dd = dateFormat.parse(d.ora_inizio);
        if (ddmin==null || d.dd < ddmin) ddmin = d.dd;
        if (ddmax==null || d.dd > ddmax) ddmax = d.dd;
        cs++;
    });

    console.log(cs);
    console.log(ddmin);
    console.log(ddmax);

    //Inizializza dati e resetta filtri
    var ndx = crossfilter(data);
    var all = ndx.groupAll();

    //Metrica per giorno
    var days = ndx.dimension(function(d)  {
      return d.dd;
    });
    var daysGroup = days.group(d3.time.day);

    //Metrica per durata corsa (se > di 60 viene conteggiato sempre come 61minuti)
    duration = ndx.dimension(function (d) {
        return Math.min(+d.minuti,61);
    });
    duration.filterAll();
    durationGroup = duration.group();

    //Metrica per lunghezza
    length = ndx.dimension(function (d) {
        return d.km
    });
    length.filterAll();
    lengthGroup = length.group();

    //Metrica per ora di inizio corsa
    hours = ndx.dimension(function (d) {
        return d.ora
    });
    hours.filterAll();
    hoursGroup = hours.group();

    //Metrica per sesso
    var sex = ndx.dimension(function (d) {
        return (d.sesso==0?'M':'F');
    })
    sex.filterAll();
    var sexGroup = sex.group();

    //Metrica per quartiere
    var nil = ndx.dimension(function (d) {
        return (d.nil);
    })
    nil.filterAll();
    var nilGroup = nil.group();
    console.log(nilGroup.size());

    //Metrica per giorno della settimana
    var dayOfWeek = ndx.dimension(function (d) {
        return d.giorno;
    });
    dayOfWeek.filterAll();
    var dayOfWeekGroup = dayOfWeek.group();


    //Crea grafici


    daysChart.width(900)
        .margins({top: 20, left: 40, right: 10, bottom: 20})
        .height(250)
        .gap(1)
        .group(daysGroup)
        .dimension(days)
        .mouseZoomable(true)
        .elasticY(true)
        .x(d3.time.scale()
            .domain([ddmin, ddmax])
            .rangeRound([0, 10 * 90]))
        .round(d3.time.month.round)
        .renderHorizontalGridLines(true);

        daysChart.yAxis().ticks(5)



    dayOfWeekChart.width(400)
        .height(250)
        .margins({top: 20, left: 10, right: 10, bottom: 20})
        .group(dayOfWeekGroup)
        .dimension(dayOfWeek)
        .elasticX(true)

        .label(function (d) {
            return d.key;
        })
        .title(function (d) {
            return d.value;
        })



     durationChart.width(450)
        .height(250)
        .margins({top: 50, right: 10, bottom: 30, left: 50})
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


     lengthChart.width(400)
        .height(250)
        .margins({top: 50, right: 50, bottom: 30, left: 50})
        .dimension(length)
        .group(lengthGroup)
        .mouseZoomable(true)
        .elasticY(true)
        .centerBar(true)
        .round(dc.round.floor)
        .x(d3.scale.linear().domain([0, 20]))
        .renderHorizontalGridLines(true)
        lengthChart.xAxis().tickFormat(
        function (v) { return v + 'km'; });
        lengthChart.yAxis().ticks(5);

     hoursChart.width(400)
        .height(250)
        .margins({top: 20, right: 50, bottom: 30, left: 50})
        .dimension(hours)
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


     sexChart.width(250)
        .height(250)
        .radius(80)
        .innerRadius(30)
        .dimension(sex)
        .group(sexGroup);

     nilChart.width(500)
        .height(950)
        .gap(1)
        .margins({top: 20, left: 200, right: 10, bottom: 20})
        .group(nilGroup)
        .dimension(nil)
        .ordering(function(d) { return -d.value })
        .rowsCap(100)
        .elasticX(true)

        .label(function (d) {
            return d.key;
        })
        .title(function (d) {
            return d.key + " : " + d.value;
        })


        dc.dataCount('#data-count')
        .dimension(ndx)
        .group(all)
        .html({
            some:'<br><br><br><br><br><br><br><br><br><br><br><strong>&nbsp;&nbsp;%filter-count</strong> selezionati su <strong>%total-count</strong> records' +
                ' | <a href=\'javascript:dc.filterAll(); dc.renderAll();\'\'> Azzera tutto </a>',
            all:' <br><br><br><br><br><br><br><br><br><br><br> &nbsp; &nbsp; &nbsp;<b>Sono selezionati tutti i record. Cliccare sul grafico per applicare i filtri.</b>'
        });


        dc.renderAll();
        //dc.redrawAll();

        console.log("1");

      });

     // console.log("Version:"+dc.version);
    //  d3.selectAll('#version').text(dc.version);

</script>

</html>

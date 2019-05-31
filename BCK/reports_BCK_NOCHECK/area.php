<!DOCTYPE html>
<html lang="en">
<head>
    <title>dc.js - US Venture Capital Landscape 2011</title>

    <meta charset="UTF-8">

     <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">


    <link rel="stylesheet" type="text/css" href="css/dc.css"/>
</head>
<body>

<div class="container">

    <h2>US Venture Capital Landscape 2011</h2>

    <p>
        This is a <a href="../index.html">dc.js</a> example showing how GeoJson Polygon can be associated with
        crossfilter
        dimension and group using a choropleth chart. Different regions can be colored differently based on different
        calculation (amount raised). Like any other dc.js chart a choropleth chart can then be mixed with other dc.js
        chart
        or your own custom d3 drawing. In this example we have shown how it can work with multiple bubble chart.
    </p>

    <p>
        Public data source
        <a href="http://buzzdata.com/azad2002/the-united-states-of-venture-capital-2011#!/data" target="_blank">BuzzData.com</a>.
    </p>

    <div id="us-chart" style="background-color:#ff0000;">
        <strong>VC Distribution by States (color: total amount raised)</strong>
        <a class="reset" href="javascript:usChart.filterAll();dc.redrawAll();" style="display: none;">reset</a>
        <span class="reset" style="display: none;"> | Current filter: <span class="filter"></span></span>

        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

    <div id="industry-chart">
        <strong>By Industries</strong> (y: number of deals, x: total amount raised in millions, radius: amount raised)
        <a class="reset" href="javascript:industryChart.filterAll();dc.redrawAll();" style="display: none;">reset</a>

        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

    <div id="round-chart">
        <strong>By Rounds</strong> (y: number of deals, x: total amount raised in millions, radius: amount raised)
        <a class="reset" href="javascript:roundChart.filterAll();dc.redrawAll();" style="display: none;">reset</a>

        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>

    <div>
        <a href="javascript:dc.filterAll(); dc.renderAll();">Reset All</a>
    </div>

</div>

<a href="https://github.com/dc-js/dc.js"><img style="position: absolute; top: 0; right: 0; border: 0;"
                                                  src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png"
                                                  alt="Fork me on GitHub"></a>

<script type="text/javascript" src="js/d3.js"></script>
<script type="text/javascript" src="js/crossfilter.js"></script>
<script type="text/javascript" src="js/dc.js"></script>
<script type="text/javascript">
    var numberFormat = d3.format(".2f");

	var width = 800,
    height = 800;


    var usChart = dc.geoChoroplethChart("#us-chart");

    d3.csv('data/trips-data.php', function (trips_record)
	{
        var data =  crossfilter(trips_record);

        var states = data.dimension(function (d) {
            return d.area_id;
        });
        var stateRaisedSum = states.group();


        d3.json("data/urban-areas-data.php", function (statesJson) {
            usChart.width(width)
                    .height(height)
                    .dimension(states)
                    .group(stateRaisedSum)
                    .colors(d3.scale.quantize().range(["#E2F2FF", "#C4E4FF", "#9ED2FF", "#81C5FF", "#6BBAFF", "#51AEFF", "#36A2FF", "#1E96FF", "#0089FF", "#0061B5"]))
                    .colorDomain([0, 200])
                    .colorCalculator(function (d) { return d ? usChart.colors()(d) : '#ccc'; })
                    .overlayGeoJson(statesJson.features, "quartiere", function (d) {
                    	//console.log(d.properties.idarea);
                        return d.properties.name;
                    })
                    .title(function (d) {
                        return "State: ";// + d.key + "\nTotal Amount Raised: " + numberFormat(d.value ? d.value : 0) + "M";
                    })
                    .projection(  
							d3.geo.stereographic()
						    .center([11.1497741, 43.780539])//[3.9,43.0])
						    .scale(300000)
						    .translate([width / 4 , height / 2])

					)
					;


            dc.renderAll();
        });
    });
</script>


</body>
</html>

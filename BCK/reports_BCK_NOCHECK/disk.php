<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Comtible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!--<link rel="icon" href="../../favicon.ico"> -->

    <title>Disk Report</title>

    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" crossorigin="anonymous">

	<!-- Bootstrap additionl components -->
	<link rel="stylesheet" href="css/scaffolding.less">

	<!-- Bootstrap additionl components -->
	<link rel="stylesheet" href="css/navbar.less">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="http://getbootstrap.com/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="css/main.css" media="screen"/>

	<!-- DC CSS -->
	<link rel="stylesheet" type="text/css" href="css/dc.css" media="screen"/>

	<!-- Color Brewer -
    <link rel="stylesheet" href="css/colorbrewer.css">           -->

	<style>

		.node {
		  font: 300 11px "Helvetica Neue", Helvetica, Arial, sans-serif;
		  fill: #bbb;
		}

		.node:hover {
		  fill: #000;
		}

		.link {
		  stroke: steelblue;
		  stroke-opacity: .4;
		  fill: none;
		  pointer-events: none;
		}

		.node:hover,
		.node--source,
		.node--target {
		  font-weight: 700;
		}

		.node--source {
		  fill: #2ca02c;
		}

		.node--target {
		  fill: #d62728;
		}

		.link--source,
		.link--target {
		  stroke-opacity: 1;
		  stroke-width: 2px;
		}

		.link--source {
		  stroke: #d62728;
		}

		.link--target {
		  stroke: #2ca02c;
		}

	</style>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || alert("Non � stata caricata la risorsa jQuery!");</script>

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="http://getbootstrap.com/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

	<!-- Crossfilters [JS] -->
	<script type="text/ecmascript" src="js/crossfilter.js"></script>
	<!-- D3 -->
	<script type="text/ecmascript" src="js/d3.js"></script>
	<!-- DC -->
	<script type="text/ecmascript" src="js/dc.js"></script>

	</head>

	<body data-spy="scroll">

	<!-- Fixed navbar -->
	<nav class="navbar navbar-default" id="top-page">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Reports</a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="trips.php">Trips</a></li>
					<li><a href="map.php">Map</a></li>
					<!--<li><a href="#contact">Contact</a></li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="#">Action</a></li>
							<li><a href="#">Another action</a></li>
							<li><a href="#">Something else here</a></li>
							<li role="separator" class="divider"></li>
							<li class="dropdown-header">Nav header</li>
							<li><a href="#">Separated link</a></li>
							<li><a href="#">One more separated link</a></li>
						</ul>
					</li>-->
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<!-- Begin page content -->
	<div class="container bs-docs-container">
    		<div class="page-header">
				<h1>Trips Report</h1>
			</div>


			<script>
				var diameter = 960,
				    radius = diameter / 2,
				    innerRadius = radius - 120;

				var cluster = d3.layout.cluster()
				    .size([360, innerRadius])
				    .sort(null)
				    .value(function(d) { return d.size; });

				var bundle = d3.layout.bundle();

				var line = d3.svg.line.radial()
				    .interpolate("bundle")
				    .tension(.85)
				    .radius(function(d) { return d.y; })
				    .angle(function(d) { return d.x / 180 * Math.PI; });

				var svg = d3.select("body").append("svg")
				    .attr("width", diameter)
				    .attr("height", diameter)
				  	.append("g")
				    .attr("transform", "translate(" + radius + "," + radius + ")");

				var link = svg.append("g").selectAll(".link"),
				    node = svg.append("g").selectAll(".node");

				d3.json("data/trips-disk-data.php", function(error, classes) {


					if (error) throw error;


					var nodes = cluster.nodes(packageHierarchy(classes)),
					links = packageImports(nodes);

					console.log("NODES:");
			        console.log(nodes);
					console.log("LINKS:");
					console.log(links);



					link = link
						.data(bundle(links))
						.enter().append("path")
						.each(function(d) {d.source = d[0], d.target = d[d.length - 1];})
						.attr("class", "link")
						.attr("d", line);

					node = node
						.data(nodes.filter(function(n) {return !n.children;}))
						.enter().append("text")
						.attr("class", "node")
						.attr("dy", ".31em")
						.attr("transform", function(d) {return "rotate(" + (d.x - 90) + ")translate(" + (d.y + 8) + ",0)" + (d.x < 180? "": "rotate(180)");})
						.style("text-anchor", function(d) {return d.x < 180? "start": "end";})
						.text(function(d) {return d.key;})
						.on("mouseover", mouseovered)
						.on("mouseout", mouseouted);
				});
				function mouseovered(d) {
					node
					.each(function(n) {n.target = n.source = false;});
					link
					.classed("link--target", function(l) {if (l.target === d) return l.source.source = true;})
					.classed("link--source", function(l) {if (l.source === d) return l.target.target = true;})
					.filter(function(l) {return l.target === d || l.source === d;})
					.each(function() {this.parentNode.appendChild(this);});
					node
					.classed("node--target", function(n) {return n.target;})
					.classed("node--source", function(n) {return n.source;});
				}
				function mouseouted(d) {
					link
					.classed("link--target", false)
					.classed("link--source", false);
					node
					.classed("node--target", false)
					.classed("node--source", false);
				}

				d3.select(self.frameElement).style("height", diameter + "px");


				// Lazily construct the package hierarchy from class names.
				function packageHierarchy(classes) {

					// Creo l'oggetto di ritorno
					var map = [];

					// Creo l'oggetto radice
					var root = {id: null, name: "root", children: [], imports: [],parent : null};




					// Ogni quartiere
					classes.forEach(function(d)
					{
						// Creo un nodo corrispondente al quartiere di partenza
					   	if(!root.children[d.areaid]){
                        	root.children[d.areaid] = {id: d.areaid, name: d.areaname, children:[], imports:[], parent: root};
					   	}

						if (d.endareaid){
                        	root.children[d.areaid].imports.push(d.endareaid);
						}

					});


					// Ogni quartiere
					root.children.forEach(function(d)
					{
						if(d.imports.length!=0){
							// "imports" cioè le destinazioni di ogni tragitto
							d.imports.forEach(function(f)
							{
	                        	var subnode = root.children[f];
								d.children[f] 	= subnode;
								d.children[f].parent 	= d;

	                            //root.children.push(subnode);
								//map.push(subnode);
							});
						}
					});

					// Aggiungo l'oggetto radice all'oggetto di ritorno
					map.push(root);


										

					console.log(map[0]);
					return map[0];
				}


				// Return a list of imports for the given array of nodes.
				function packageImports(nodes) {
                    var map = {},
					imports =[];
					// Compute a map from name to node.
					nodes.forEach(function(d)
						{
							map[d.area_id] = d;
						}
					);
					// For each import, construct a link from the source to target node.
					nodes.forEach(function(d)
						{
							if (d.imports)
								d.imports.forEach(function(i)
									{
										imports.push({source: map[d.area_id], target: map[i]});
									}
								);
						}
					);
					return imports;
				}

			</script>
	</div>

	<footer class="footer">
		<div class="container">
			<p class="text-muted">&reg;Omniaevo S.r.l. 2015</p>
		</div>
	</footer>


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>


</body>
</html>

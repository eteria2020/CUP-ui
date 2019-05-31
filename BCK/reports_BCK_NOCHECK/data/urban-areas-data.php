<?php


/* ERROR LOG */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Connect to DB
try {
	$dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5433", 'cs', 'gmjk51pa');
} catch (PDOException $e) {
	echo "-1:Database error : $e";
}



/**
* This page is required by "trips.php"
*
* CASE 1]
* The first function is to return a JSON with the geo data of the specified city
*
*
*/

// First Check, prevent future error end external request
if (!isset($_GET["city"])){
    die(0);
}

//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="urba-areas.json"');

$sql = "
SELECT row_to_json(fc)
FROM ( 	SELECT 'FeatureCollection'	 			As type,
				array_to_json(array_agg(f)) 	As features

		FROM (	SELECT 'Feature' 				As type,
				ST_AsGeoJSON(ua.area)::json 	As geometry,
				row_to_json(lp) 				As properties

				FROM urban_areas 		As ua

				INNER JOIN (
					SELECT 	to_char(id_area,'FM999MI'),
							name ,
							id_area			as id
					FROM urban_areas
				) As lp
				ON ua.id_area = lp.id
				WHERE id_fleet = ".$_GET["city"]."
		) As f
)  As fc
";



$stm = $dbh->prepare($sql);
$stm->execute();
$results = $stm->fetchObject();
echo $results->row_to_json;

?>

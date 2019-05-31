<?php

/* ERROR LOG */
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
	$dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5433", 'cs', 'gmjk51pa');
} catch (PDOException $e) {
	echo "-1:Database error : $e";
}

//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="urba-areas.json"');

$sql_BCK = "
SELECT row_to_json(fc)
FROM ( 	SELECT 'FeatureCollection'	 			As type,
				array_to_json(array_agg(f)) 	As features

		FROM (	SELECT 'Feature' 				As type,
				ST_AsGeoJSON(ua.area)::json 	As geometry,
				row_to_json(lp) 				As properties

				FROM view_bi_urban_areas		As ua

				INNER JOIN (
					SELECT 	to_char(id_area,'FM999MI'),
							name ,
							id_area			as id,
							unique_area_id	as	unique_id
				FROM view_bi_urban_areas
				) As lp

				ON ua.id_area = lp.id

				ORDER BY ua.id_fleet
		) As f
)  As fc";

$sql = "
SELECT row_to_json(fc)
FROM ( 	SELECT 'FeatureCollection'	 			As type,
				array_to_json(array_agg(f)) 	As features

		FROM (	SELECT 'Feature' 				As type,
				ST_AsGeoJSON(ua.area)::json 	As geometry,
				row_to_json(lp) 				As properties,
				ua.id_fleet

				FROM urban_areas 		As ua

				INNER JOIN (
					SELECT 	to_char(id_area,'FM999MI'),
							name ,
							id_area			as id
				FROM urban_areas
				) As lp

				ON ua.id_area = lp.id

				ORDER BY ua.id_fleet
		) As f
		GROUP BY f.id_fleet
)  As fc";



$stm = $dbh->prepare($sql);
$stm->execute();

$data = array();

$string = "{\"city\":[";

$id = 0;
while($data = $stm->fetchObject()){
    if($id > 0){$string.=",";}
	$string .= $data->row_to_json;
	$id++;
}


$string .= "]}";

echo $string;

?>

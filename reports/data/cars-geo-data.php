<?php


require ("lock.php");

/* ERROR LOG *
error_reporting(E_ALL);
ini_set('display_errors', 1); */


try {
	$dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5433", 'cs', 'gmjk51pa');
} catch (PDOException $e) {
	echo "-1:Database error : $e";
}

//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="urba-areas.json"');






$sql = "
	SELECT row_to_json(fc)
	FROM (
		SELECT 	'FeatureCollection' 			As type,
			array_to_json(array_agg(f)) 	As features
		FROM (
			SELECT 		'Feature' 						As type ,
						ST_AsGeoJSON(ua.location)::json 	As geometry,
                       	row_to_json(lp) 				As properties
			FROM 		cars As ua

			INNER JOIN (
					SELECT 	plate
    				FROM cars
        	) As lp

		    ON ua.plate = lp.plate


			WHERE 		ua.active 						= true 	AND
						ua.status 					= 'operative' AND
                        ua.running                  = 'true'

		) As f
	)  As fc";



$stm = $dbh->prepare($sql);

$stm->execute();

$data = array();
$data = $stm->fetchObject();

//file_put_contents("get.log", print_r($_GET,true),FILE_APPEND);

echo $data->row_to_json;

?>

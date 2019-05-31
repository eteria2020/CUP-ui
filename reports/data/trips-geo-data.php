<?php


require ("lock.php");

/* ERROR LOG *
error_reporting(E_ALL);
ini_set('display_errors', 1);
              */

try {
	$dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5433", 'cs', 'gmjk51pa');
} catch (PDOException $e) {
	echo "-1:Database error : $e";
}

//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="urba-areas.json"');


if (isset($_GET['begend'])){
	$begendString = $_GET['begend'] == "0" ? "beginning" : "end";
}else{
    $begendString = "beginning";
}




$dateFrom 	= isset($_GET["dateFrom"]) 	? $_GET["dateFrom"]." 00:00" :  date("Y-m-d",strtotime("-1 month",$_GET["dateTo"]))." 00:00";
$datedateTo = isset($_GET["dateTo"]) 	? date("Y-m-d", strtotime("+1 month", strtotime($_GET["dateTo"])))." 23:59" : date("Y-m-d")." 23:59";


$sql = "
	SELECT row_to_json(fc)
	FROM (
		SELECT 	'FeatureCollection' 			As type,
			array_to_json(array_agg(f)) 	As features   
		FROM (
			SELECT 		'Feature' 						As type ,
						ST_AsGeoJSON(ua.geo_".$begendString.")::json 	As geometry
			FROM 		trips As ua

			LEFT JOIN 	customers c ON c.id = ua.customer_id

			WHERE 		ua.payable 						= true 	AND
						c.gold_list 					= false AND
						c.maintainer 					= false AND
						ua.timestamp_end 	IS NOT NULL 			AND
						ua.timestamp_".$begendString." 	>= ?  	AND
						ua.timestamp_".$begendString."		<= ?
			ORDER BY 	ua.id DESC
		) As f
	)  As fc";



$stm = $dbh->prepare($sql);
$stm->bindValue(1, $dateFrom, PDO::PARAM_STR);
$stm->bindValue(2, $datedateTo, PDO::PARAM_STR);
$stm->execute();

$data = array();
$data = $stm->fetchObject();

//file_put_contents("get.log", print_r($_GET,true),FILE_APPEND);

echo $data->row_to_json;

?>

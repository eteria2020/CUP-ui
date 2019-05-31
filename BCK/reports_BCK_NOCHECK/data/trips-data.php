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
* The first function is to return an array with the name of the city and the relative
* "fleet_id" (the city id) to determinate the specific city statistic
*
* CASE 2]
* The second function is to return to the ajax request the data relative to the
* general statistics.
*
* CASE 3]
* The third function is to return to the ajax request the statistics data of a specific
* city. Is required the "fleet_id" value
*
*/

// First Check, prevent future error end external request
if (!isset($_GET["action"])){
    die(0);
}


switch($_GET["action"]){
	case "getCitys":{
		/************************ CASE 1 ************************/
    	$sql = "
	   	SELECT row_to_json(fc)
		FROM (
			SELECT
				array_to_json(array_agg(f)) 	As city
			FROM (
				SELECT		id					AS fleet_id,
						code				AS fleet_code,
						name				AS fleet_name,
						choropleth_params	AS params

				FROM		fleets
				ORDER BY	name
			) as f
		) AS fc
		";




		$stm = $dbh->prepare($sql);
		/*$stm->execute();
		$result = $stm->fetchAll();  */

		header('Content-type: application/json');
		//echo json_encode($result);
		$stm->execute();

		$data = array();
		$data = $stm->fetchObject();

		//file_put_contents("get.log", print_r($_GET,true),FILE_APPEND);

		echo $data->row_to_json;

	} break;

	case "getGlobalData":{
		/************************ CASE 2 ************************/
		// Output: JSON || CSV
		$output_type_type	=	"CSV";

		if (isset($_GET['output']) && is_string($_GET['output'])) {
			$output_type		=	$_GET['output'];
		}

		//header('Access-Control-Allow-Origin: *');

		$date  = new DateTime();
		$interval = new DateInterval('P30D');

		// porta a venerdi
		$date->sub($interval);
		$start_day    =    $date->format('d-m-Y');

		$date  = new DateTime();
		$interval = new DateInterval('P1D');
		// porta a venerdi
		$date->sub($interval);
		$start_day2    =    $date->format('d-m-Y 23:59:59');


		if ($output_type=='JSON') {
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename="trips.json"');
		}else{
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="trips.csv"');
		}

		$date  = new DateTime();
		$interval = new DateInterval('P30D');

		//Reduce errors
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction

		if (!isset($sidx))	$sidx = "car_plate";
		if (!isset($sord))	$sord = "ASC";

		$sql = "
		SELECT		*,
					to_char(time_beginning, 'YYYY-MM-DD HH24:MI:SS') 			as time_beginning_parsed,
					to_char(time_end, 'YYYY-MM-DD HH24:MI:SS') 					as time_end_formatted,
					EXTRACT(HOUR	FROM time_beginning)						as time_beginning_hour,
					EXTRACT(MINUTE	FROM time_beginning)						as time_beginning_minute,
					EXTRACT(SECOND	FROM time_beginning)						as time_beginning_second,
					EXTRACT(DAY		FROM time_beginning)						as time_beginning_day,
					TRUNC(EXTRACT(EPOCH from  (time_end - time_beginning))/60) 	as time_total_minute,
					EXTRACT(ISODOW from time_beginning) 						as time_dow

		FROM		view_bi_trips
		WHERE		time_beginning >= '".$start_day ."' AND time_beginning	<= '". $start_day2 ."' AND area_id IS NOT NULL
		ORDER BY	time_beginning
		";



		$stm = $dbh->prepare($sql);
		$stm->execute();

		$data=array();


		if ($output_type=="JSON") {
			while ($row = $stm->fetchObject ()) {
				$data[] = $row;
			}
			echo json_encode($data);
		} else {
			$fp = fopen('php://output', 'w');
			$first = true;

			while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
				if ($first) {
					fputcsv($fp, array_keys($row));
					$first =false;
				}
				fputcsv($fp, array_values($row));
			}
			fclose($fp);
		}
	} break;

	case "getCityData":{
		/************************ CASE 3 ************************/
		// Output: JSON || CSV
		$output_type_type	=	"CSV";

		if (isset($_GET['output']) && is_string($_GET['output'])) {
			$output_type		=	$_GET['output'];
		}

		//header('Access-Control-Allow-Origin: *');

		$date  = new DateTime();
		$interval = new DateInterval('P30D');

		// porta a venerdi
		$date->sub($interval);
		$start_day    =    $date->format('d-m-Y');

		$date  = new DateTime();
		$interval = new DateInterval('P1D');
		// porta a venerdi
		$date->sub($interval);
		$start_day2    =    $date->format('d-m-Y 23:59:59');


		if ($output_type=='JSON') {
			header('Content-Type: application/json');
			header('Content-Disposition: attachment; filename="trips.json"');
		}else{
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="trips.csv"');
		}

		$date  = new DateTime();
		$interval = new DateInterval('P30D');

		//Reduce errors
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
        $city = $_GET['city'];

		if (!isset($sidx))	$sidx = "car_plate";
		if (!isset($sord))	$sord = "ASC";
		if (!isset($city))	$city = 0;

		$sql = "
		SELECT		*,
					to_char(time_beginning, 'YYYY-MM-DD HH24:MI:SS') 			as time_beginning_parsed,
					to_char(time_end, 'YYYY-MM-DD HH24:MI:SS') 					as time_end_formatted,
					EXTRACT(HOUR	FROM time_beginning)						as time_beginning_hour,
					EXTRACT(MINUTE	FROM time_beginning)						as time_beginning_minute,
					EXTRACT(SECOND	FROM time_beginning)						as time_beginning_second,
					EXTRACT(DAY		FROM time_beginning)						as time_beginning_day,
					TRUNC(EXTRACT(EPOCH from  (time_end - time_beginning))/60) 	as time_total_minute,
					EXTRACT(ISODOW from time_beginning) 						as time_dow

		FROM		view_bi_trips
		WHERE		time_beginning >= '".$start_day ."' AND time_beginning	<= '". $start_day2 ."'  AND
					fleet_id = ".$city." AND area_id IS NOT NULL
		ORDER BY	time_beginning
		";



		$stm = $dbh->prepare($sql);
		$stm->execute();

		$data=array();


		if ($output_type=="JSON") {
			while ($row = $stm->fetchObject ()) {
				$data[] = $row;
			}
			echo json_encode($data);
		} else {
			$fp = fopen('php://output', 'w');
			$first = true;

			while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {
				if ($first) {
					fputcsv($fp, array_keys($row));
					$first =false;
				}
				fputcsv($fp, array_values($row));
			}
			fclose($fp);
		}
	} break;

	default: die(0);
}

?>

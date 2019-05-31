<?php

/* ERROR LOG */
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

try {
	$dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5433", 'cs', 'gmjk51pa');
} catch (PDOException $e) {
	echo "-1:Database error : $e";
}

// Output: JSON || CSV
$output_type_type	=	"CSV";

if (isset($_GET['output']) && is_string($_GET['output'])) {
	$output_type		=	$_GET['output'];
}

//header('Access-Control-Allow-Origin: *');



$date  = new DateTime();
$interval = new DateInterval('P7D');
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
$interval = new DateInterval('P29D');

//Reduce errors
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction

if (!isset($sidx))	$sidx = "car_plate";
if (!isset($sord))	$sord = "ASC";

$sql = "
SELECT		*,
			to_char(time_beginning, 'YYYY-MM-DD HH24:MI:SS') 			as time_beginning_formatted,
			to_char(time_end, 'YYYY-MM-DD HH24:MI:SS') 					as time_end_formatted,
			EXTRACT(HOUR	FROM time_beginning)						as time_beginning_hour,
			EXTRACT(MINUTE	FROM time_beginning)						as time_beginning_minute,
			EXTRACT(SECOND	FROM time_beginning)						as time_beginning_second,
			EXTRACT(DAY		FROM time_beginning)						as time_beginning_day,
			TRUNC(EXTRACT(EPOCH from  (time_end - time_beginning))/60) 	as time_total_minute,
			EXTRACT(ISODOW from time_beginning) 						as time_dow

FROM		view_bi_trips
WHERE		time_beginning >= '".$start_day ."' AND time_beginning	<= '". $start_day2 ."'
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

?>

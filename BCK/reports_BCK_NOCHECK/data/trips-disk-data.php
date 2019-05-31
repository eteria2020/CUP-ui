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

$sql = "
SELECT		id				AS tripid,
			area_id     	AS begareaid,
			area_name		AS begareaname,
            end_area_id		AS endareaid,
			end_area_name   AS endareaname
FROM		view_bi_trips
WHERE		fleet_id = 1
ORDER BY		area_id
";


$sql = "  	SELECT row_to_json(fc)
FROM (
	SELECT 		u.id_area							AS area_id,
			u.name,
			array_to_json(array_agg(t.end_area_id))			AS imports

	FROM		urban_areas 	u
	INNER JOIN 	view_bi_trips 	t
		ON	t.area_id	=	u.id_area AND
			t.time_beginning > '2015-11-28' AND
			t.end_area_id IS NOT NULL
	WHERE		u.id_fleet 	= 	2

	GROUP BY 	u.id_area,u.name
	ORDER BY	u.id_area
)  As fc"  ;

$sql = "
SELECT row_to_json(fc)
FROM (
	SELECT 		u.id_area			AS areaid,
			u.name					AS areaname,
			t.end_area_id			AS endareaid,
			t.time_beginning		AS timebeginning,
			t.time_end				AS timeend

	FROM		urban_areas 	u

	LEFT JOIN 	(
		SELECT 	t.area_id,t.end_area_id,t.time_beginning,t.time_end
		FROM 	view_bi_trips t
		WHERE 	t.fleet_id = 2		AND
		t.time_beginning 	> '2015-11-30 08:00' AND
		t.time_end		< '2015-11-30 09:00'
	) AS t

	ON 	u.id_area = t.area_id

	WHERE	u.id_fleet = 2

	ORDER BY	u.id_area

)  As fc;
"  ;




$stm = $dbh->prepare($sql);
$stm->execute();


$data = array();

$string = "[";

$id = 0;
while($data = $stm->fetchObject()){
    if($id > 0){$string.=",";}
	$string .= $data->row_to_json;
	$id++;
}


$string .= "]";

echo $string;

?>

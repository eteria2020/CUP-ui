<?php

function getDb() {

  try {
       $dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5433", 'cs', 'gmjk51pa');
  } catch (PDOException $e) {
    echo "-1:Database error : $e";
  }

  return $dbh;

}

  $dbh = getDb();
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//Reduce errors


$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction

if (!isset($sidx))  $sidx = "timestamp_beginning";
if (!isset($sord))  $sord = "ASC";

$dbh = getDb();

$stm = $dbh->prepare("SELECT  trips.* , concat(name,' ',surname) as name FROM trips JOIN customers ON customers.id = customer_id WHERE timestamp_end is null  ORDER BY $sidx $sord");
$stm->execute();



$res = new stdClass();
$res->page=1;
$res->total=1;
$res->records=0;
$res->rows=array();

$i=0;
while ($row = $stm->fetch(PDO::FETCH_ASSOC)) {

    $id= $row['id'];

    $res->rows[] = array('id'=>$id,'cell'=>$row);

    $i++;
}

$res->records=$i-1;
echo json_encode($res);

?>


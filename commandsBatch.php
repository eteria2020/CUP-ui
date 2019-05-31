<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Connection: close");


$fleet_id=1;

function getDb() {

  try {
       $dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5432", 'cs', 'gmjk51pa');
  } catch (PDOException $e) {
    echo "-1:Database error : $e";
  }

  return $dbh;

}

// recupera il set di configurazioni applicabili per una auto considerando le eventuali forzature specifiche
function getConfig($dbh , $fleet_id, $plate='' , $key="%") {
  $stm =  $dbh->prepare("SELECT *  FROM cars_configurations WHERE key like '$key' AND (car_plate='$plate' OR fleet_id= $fleet_id OR (fleet_id is null AND model is null AND car_plate is null) ) ORDER BY key, car_plate DESC ,model DESC,fleet_id DESC ");
  $stm->execute();

  $configs =  array();

  while ($row=$stm->fetch(PDO::FETCH_ASSOC)) {
     //echo $row['key'] . "=" . $row['value'] . "<BR>";
     $configs[$row['key']] = $row['value'];
  }


  return $configs;


}

  $dbh = getDb();
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


  $cmd = 'SET_CONFIG';
  $stm0 = $dbh->prepare("SELECT  *  FROM cars  WHERE fleet_id = :fleet_id");

  $stm0->bindParam(':fleet_id', $fleet_id, PDO::PARAM_INT);
  $stm0->execute();

  while ($row=$stm0->fetch(PDO::FETCH_ASSOC)) {
     $plate =  $row['plate'];
     $configs = getConfig($dbh,1,$plate,'RadioSetup');
     echo $plate . ' : ' . json_encode((array)$configs) . '<BR>';
  }

/*
  $stm = $dbh->prepare("INSERT INTO  commands (car_plate,command,intarg1,txtarg1, queued,to_send,ttl) VALUES (:car_plate,:cmd, :intarg1, :txtarg1 , now(),TRUE,:ttl);");
  $stm->bindParam(':car_plate', $car_plate, PDO::PARAM_STR);
  $stm->bindParam(':cmd', $cmd, PDO::PARAM_STR);
  $stm->bindParam(':intarg1', $intarg1, PDO::PARAM_INT);
  $stm->bindParam(':txtarg1', $txtarg1, PDO::PARAM_STR);
  $stm->bindParam(':ttl', $ttl, PDO::PARAM_INT);
  $stm->execute();
*/


?>



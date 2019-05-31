<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Connection: close");


function getDb() {

  try {
       $dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5432", 'cs', 'gmjk51pa');
  } catch (PDOException $e) {
    echo "-1:Database error : $e";
  }

  return $dbh;

}

  $dbh = getDb();
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  var_dump($_REQUEST);

  if (isset($_REQUEST['targa']) && isset($_REQUEST['comando'])) {
   $targa =   $_REQUEST['targa'];
   $intarg1=0;
   $intarg2=0;
   $txtarg1= $_REQUEST['txtarg1'];
   $txtarg2='';
   $ttl=0;

    switch ($_REQUEST['comando']) {
        case "WLUPDATE":
            $cmd = 'WLUPDATE';
            $txtarg1='';
            $ttl=0;
            break;

        case "WLCLEAN":
            $cmd = 'WLCLEAN';
            $txtarg1='';
            $ttl=0;
            break;

        case 'SET_DOORS&1':
            $cmd = 'SET_DOORS';
            $txtarg1='';
            $intarg1 = 1;
            $ttl=60;
            break;

        case 'SET_DOORS&0':
            $cmd = 'SET_DOORS';
            $intarg1 = 0;
            $txtarg1='';
            $ttl=60;
            break;

        case 'SET_ENGINE&1':
            $cmd = 'SET_ENGINE';
            $intarg1 = 1;
            $txtarg1='';
            $ttl=60;
            break;

        case 'SET_ENGINE&0':
            $cmd = 'SET_ENGINE';
            $intarg1 = 0;
            $txtarg1='';
            $ttl=60;
            break;

        case 'RESEND_TRIP':
            $cmd = 'RESEND_TRIP';
            $txtarg1='';
            $ttl=0;
            break;

        case 'OPEN_TRIP':
            $cmd = 'OPEN_TRIP';
            $txtarg1 = isset($_REQUEST['card'])?$_REQUEST['card']:'';
            $ttl=60;
            break;

        case 'PARK_TRIP':
            $cmd = 'PARK_TRIP';
            $txtarg1 = isset($_REQUEST['card'])?$_REQUEST['card']:'';
            $ttl=60;
            break;

        case 'UNPARK_TRIP':
            $cmd = 'UNPARK_TRIP';
            $txtarg1 = isset($_REQUEST['card'])?$_REQUEST['card']:'';
            $ttl=60;
            break;


        case 'CLOSE_TRIP':
            $cmd = 'CLOSE_TRIP';
            $txtarg1='';
            $ttl=60;
            break;

        case 'SET_NAVIGATOR&0':
            $cmd = 'SET_NAVIGATOR';
            $txtarg1='';
            $intarg1 = 0;
            break;

        case 'SET_NAVIGATOR&1':
            $cmd = 'SET_NAVIGATOR';
            $txtarg1='';
            $intarg1 = 1;
            break;


        case 'SET_DAMAGES':
            $cmd = 'SET_DAMAGES';
            $stm0 = $dbh->prepare("SELECT  vettura_danni FROM tbl_vettura WHERE vettura_targa = :targa");
            $stm0->bindParam(':targa', $targa, PDO::PARAM_STR);
            $stm0->execute();

            $result=$stm0->fetch(PDO::FETCH_ASSOC);

            if ($result)
               $txtarg1=$result['vettura_danni'];
            else
               $txtarg1='';
            $ttl=0;
            break;

        case 'OPEN_SERVICE':
            $cmd = 'OPEN_SERVICE';
            $ttl=60;
            break;

        case 'SEND_LOGS':
            $cmd = 'SEND_LOGS';
            $ttl=600;
            if (!isset($txtarg1))
                $txtarg1 = '';
            break;

        case 'SET_LOCATION':
            $cmd = 'SET_LOCATION';
            $ttl = 600;
            if (!isset($txtarg1))
                $txtarg1 = '';
            break;

        case 'ADMINS_UPDATE':
            $cmd = 'ADMINS_UPDATE';
            $ttl = 0;
            break;

        case 'SHUTDOWN':
            $cmd = 'SHUTDOWN';
            $ttl = 60;
            break;

    }

  $stm = $dbh->prepare("INSERT INTO  commands (car_plate,command,intarg1,txtarg1, queued,to_send,ttl) VALUES (:targa,:cmd, :intarg1, :txtarg1 , now(),TRUE,:ttl);");
  $stm->bindParam(':targa', $targa, PDO::PARAM_STR);
  $stm->bindParam(':cmd', $cmd, PDO::PARAM_STR);
  $stm->bindParam(':intarg1', $intarg1, PDO::PARAM_INT);
  $stm->bindParam(':txtarg1', $txtarg1, PDO::PARAM_STR);
  $stm->bindParam(':ttl', $ttl, PDO::PARAM_INT);
  $stm->execute();

  echo "DONE: " . date("Y-m-d H:i:s");
  flush();
  exit();
  }
  else if (isset($_REQUEST['trip']) && isset($_REQUEST['comando'])) {

    $trip =  $_REQUEST['trip'];
    if (!is_numeric($trip)) {
      echo "Invalid trip id";
      exit();
    }
    $trip_id =  intval($trip);


    switch ($_REQUEST['comando']) {
        case 'CLOSE_TRIP':
            $stm = $dbh->prepare("UPDATE trips SET timestamp_end = timestamp_beginning WHERE id=:id;");
            $stm->bindParam(':id', $trip_id, PDO::PARAM_INT);
            $stm->execute();
            echo "DONE: " . date("Y-m-d H:i:s");
            break;
    }
    flush();
    exit();
  }


//Reduce errors
error_reporting(~E_WARNING)
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<form action="<?=$_SERVER['PHP_SELF']?>">

Veicolo: <select name="targa" id="targa">


<?php
  $stm = $dbh->prepare("SELECT  * FROM cars ORDER BY plate");
  $stm->bindParam(':targa', $targa, PDO::PARAM_STR);
  $stm->execute();


  while ($row = $stm->fetch()) {
    echo "<option value='$row[plate]'>$row[plate]</option>";
  }
?>
</select>

</form>


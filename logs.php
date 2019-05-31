<?php

 if ($_GET['file']) {
 header("Content-type: text/plain");
 header("Content-Encoding: gzip");

 $file =  $_GET['file'];
 passthru("unzip -p $file  log.txt  | gzip -c");

 exit();
 }

 $files = scandir('../api/uploads/',1);

 $logs = array();

 foreach($files as $file) {
    $info = pathinfo($file);
    $name =  $info['filename'];
    $p = split('_',$name);

    if ($p[0]=='Log' && count($p)>3) {
      $log = new stdClass();
      $log->car_plate = $p[1];
      $log->file =  $file;

      echo "<a href='logs.php?file=".urlencode("../api/uploads/$file")."'>$p[1]</a> -";

      $log->date = DateTime::createFromFormat("Ymd His", "$p[2] $p[3]");

      echo ( $log->date->format('d/m/Y h:i:s') ) . "<BR>";

      $logs[]=$log;
    }
/*
    usort($logs, function($a,$b) {
       return
    });
 */


 }

 ?>

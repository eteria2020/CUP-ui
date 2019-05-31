<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

    require('phpGPX.php');

    $mdb = new MongoClient("mongodb://127.0.0.1:27017");
    $db = $mdb->sharengo;

    $collections = $db->listCollections();

    $logs = $db->logs;

    if (isset($_REQUEST['car_plate']) &&  $_REQUEST['car_plate']!='')
        $car_plate=$_REQUEST['car_plate'];
    else
        $car_plate ='ED06263';


    if ($_REQUEST['id_trip']) {

      $id_trip = intval($_REQUEST['id_trip']);

      $query =  array();

      if ($id_trip>0)  {
            $query['id_trip'] = $id_trip;
      } else {
            $query['VIN'] = $car_plate;
            $query['id_trip'] = array('$ne'=>0);
      }



      $cursor =  $logs->find($query);


      $my = new phpGPX();

      $prev=NULL;

      while ($cursor->hasNext()) {
        $doc = $cursor->getNext();
        $lon = $doc['lon'];
        $lat = $doc['lat'];

        $time =  date("Y-m-d\Th:i:s+0000",$doc['log_time']->sec);
        //$name =  date('d-m-Y h:i:s',$doc[log_time]->sec);
        $mapurl = "<a href='http://maps.google.com/maps?q=$lat,$lon&z=16'>$lat , $lon</a>";


        //  function addPoint($name,$cmt,$sym,$type,$description,$latitude,$longitude) {
        if ($lat!=0 && $lon!=0) {
            //$my->addPoint($name,"","","","",$lat,$lon);
            if ($doc['id_trip']!=$prev) {
              if ($prev!=NULL)
                $my->EndTrack();
              $my->StartTrack($doc['id_trip']);
              $prev=$doc['id_trip'];
            }

            $my->addTrackPoint($time,$lat,$lon);
        }


        //echo date('d-M-Y h:i:s',$doc[log_time]->sec)  ."   -  $mapurl <BR>";
      }
      $my->EndTrack();

      //$my->DisplayGPXfile();
      $my->DownloadGPXfile("KML");

    }  else {
       $match = array('VIN' => $car_plate);

       $group = array('_id' => '$id_trip' ,
                      'begin_trip' => array('$first' => '$log_time'),
                      'end_trip' => array('$last' => '$log_time'),
                      'points' => array('$sum' => 1)
                      );

       $sort = array('_id' => 1, 'log_time' => 1);


       $aggregate = array();

       $aggregate[] = array('$match' => $match);
       $aggregate[] = array('$group' => $group);
       $aggregate[] = array('$sort' => $sort);

       //$list = array_values($logs->distinct('id_trip',$query));

       //echo JSON_encode($aggregate);
       $list = $logs->aggregate($aggregate);
       echo JSON_encode($list['result']);

    }
?>
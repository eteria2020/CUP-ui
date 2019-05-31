<?php


/*
error_reporting(E_ALL);
ini_set('display_errors', 1);  
*/

require ("lock.php");

// Set the timeout to null...
// PAY ATTENCTION !!!
MongoCursor::$timeout = -1;

    require('phpGPX.php');

    $mdb = new MongoClient("mongodb://127.0.0.1:27017");
    $db = $mdb->sharengo;

    $collections = $db->listCollections();

    $logs = $db->logs;

    $end    = new MongoDate(strtotime($_REQUEST['date']));
	$start 	= new MongoDate(date(strtotime('-1 days', strtotime($_REQUEST['date']))));

    // Get the record limit, preventing out_of_bunds manual selection
	if(isset($_REQUEST['limit'])){
		$recordlimit = (($_REQUEST['limit'] >= 1) && ($_REQUEST['limit'] <= 300)) ? $_REQUEST['limit']+0 : 100;
	}else{
        $recordlimit = 100;
	}

	//$end 	= new MongoDate(strtotime($_REQUEST['date']." 11:00:00" ));

	//echo "START: ". date('Y-M-d h:i:s', $start->sec) . "<br>END: ".date('Y-M-d h:i:s', $end->sec)."<br><br><br><br><br>";


    if ($_REQUEST['id_trip']) {

      $id_trip = intval($_REQUEST['id_trip']);

      $query =  array();

      if ($id_trip>0)  {
            $query['id_trip'] = $id_trip;
      } else {
            //$query['VIN'] = $car_plate;
            $query['log_time'] = array('$gt' => $start, '$lte' => $end);
			$query['id_trip'] = array('$ne'=>0);
			$query['lat'] = array('$ne' =>0);
			$query['lon'] = array('$ne' =>0);
      }

      $cursor =  $logs->find($query);

	  // Limito il numero degli elementi per il quale eseguirò il sorting, poiché
	  // MongoDB ha un buffer massimo di sorting.

	  // Buffer = 32MiB (33554432 bytes)
	  // Dalle stats, Avarage File Dim = 712 bytes
	  // Quindi 	33554432 / 712 = 47127,01123595506
	  // Cioè al massimo 47126 record.
	  //$cursor->limit(47120);

	  $cursor->sort(array('id_trip' => -1));

      $my = new phpGPX();

      $prev=NULL;

      $i = 0;

      while ($cursor->hasNext() && $i <= $recordlimit) {
        $doc = $cursor->getNext();
        $lon = $doc['lon'];
        $lat = $doc['lat'];

        $time =  date("Y-m-d\Th:i:s+0000",$doc['log_time']->sec);
        //$name =  date('d-m-Y h:i:s',$doc[log_time]->sec);
        $mapurl = "<a href='http://maps.google.com/maps?q=$lat,$lon&z=16'>$lat , $lon</a>";



        //  function addPoint($name,$cmt,$sym,$type,$description,$latitude,$longitude) {
        if ($lat!=0 && $lon!=0) {

			//echo "OK  i= $i         --->       $lat - $lon";

			// Controllo che latidutine e longitudine siano compresi
			// all'interno dell'intervallo di estremi dell'Italia
            $north 	= 47.08333;
			$west	= 6.61666;
			$east	= 18.51666;
            $south 	= 35.48333;

			if(
				$lat <= $north &&
				$lat >= $south &&
				$lon <= $east &&
				$lon >= $west ){

				//echo "    -    SODDISFATTO!";

			    //$my->addPoint($name,"","","","",$lat,$lon);
	            if ($doc['id_trip']!=$prev) {
	              if ($prev!=NULL){
	                $my->EndTrack();
                  	$i++;
				  }
	              $my->StartTrack($doc['id_trip']);
				  echo "DOC --> ".$doc['id_trip']."<br>";


	              $prev=$doc['id_trip'];
	            }

	            $my->addTrackPoint($time,$lat,$lon);
	            echo "\tPoint --> ".$time." | ".$lat." | ".$lon."<br>";
			}

		   //	echo "<br>";
        }


        //echo date('d-M-Y h:i:s',$doc[log_time]->sec)  ."   -  $mapurl <BR>";
      }
      $my->EndTrack();

      //$my->DisplayGPXfile();
      //$my->DownloadGPXfile("KML");

    }  else {
      // $match = array('VIN' => $car_plate);

        // STAGE 1
      	$match = array(
        	//'log_time' => array('$gt' => $start, '$lte' => $end)
        	'log_time' 	=> array('$gt' => $start, '$lte' => $end),
        	'id_trip' 	=> array('$ne' => 0),
            'begin_trip'=> array('$ne' => 'null'),
            'end_trip' 	=> array('$ne' => 'null'),
			'lon'		=> array('$gt' => 0),
			'lat'		=> array('$gt' => 0)
		);

        // STAGE 2
      	$group = array(
			'VIN'			=> array('$last' => '$VIN'),
			'_id' 			=> '$id_trip' ,
			'begin_trip' 	=> array('$first' => '$log_time'),
			'end_trip' 		=> array('$last' => '$log_time'),
			'points' 		=> array('$sum' => 1)
	   	);

		// STAGE 3
		$sort = array('_id' => -1);

		// STAGE 4
	   	$project = array(
			'_id' => 1,
            'VIN' => 1,
			'begin_trip' => 1,
            'end_trip' => 1,
			'points' => 1
        );





       $aggregate = array();

       $aggregate[] = array('$match' => $match);
       $aggregate[] = array('$group' => $group);
       $aggregate[] = array('$sort' => $sort);
       $aggregate[] = array('$project' => $project);
	   $aggregate[] = array('$limit' => $recordlimit);

       //$list = array_values($logs->distinct('id_trip',$query));

       //echo JSON_encode($aggregate);
       $list = $logs->aggregate($aggregate);
       echo JSON_encode($list['result']);

    }
?>
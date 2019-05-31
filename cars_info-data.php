<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

function getDb() {
    try {
       $dbh = new PDO("pgsql:dbname=sharengo;host=localhost;port=5433", 'cs', 'gmjk51pa');
    } catch (PDOException $e) {
        echo "-1:Database error : $e";
    }
    return $dbh;
}
try{
    $dbh = getDb();
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


    $stm = $dbh->prepare("SELECT
        car_plate,
        int_lat,
        int_lon,
        gprs_lat,
        gprs_lon,
        gps,
        fw_ver,
        hw_ver,
        sw_ver,
        sdk,
        sdk_ver,
        gsm_ver,
        android_device,
        android_build,
        tbox_sw,
        tbox_hw,
        mcu_model,
        mcu,
        hw_version,
        hb_ver,
        vehicle_type,
        lastupdate
    FROM cars_info  ORDER BY car_plate");

    $stm->execute();
}catch(PDOException  $e ){
    echo "PDO Error: ".$e;
}

$results            = $stm->fetchAll(PDO::FETCH_ASSOC);
$jsonReults         = json_encode( array("rows" =>$results));

echo $jsonReults;

?>
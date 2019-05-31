<?php


//PDO
function CS_DB () {
//CONNESSIONE AL DB
$conn = new PDO('pgsql:host=185.58.119.117 port=5433 dbname=sharengo user=cs password=gmjk51pa');
//SET ATTRIBUTI
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

return $conn;
}

?>

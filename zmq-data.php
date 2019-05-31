<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require 'Predis/Autoloader.php';
Predis\Autoloader::register();

use Predis\Collection\Iterator;

$redis = new Predis\Client('tcp://127.0.0.1:6379');
$redis->select(1);

$keys = $redis->keys('*');
$data = array();

foreach($keys as $key) {
  if ($key!='subscribers') {
    $data[$key]  = new stdClass();
    foreach (new Iterator\HashKey($redis, $key) as $field => $value) {
      $data[$key]->$field=$value;
    }
  }
}

echo nl2br(json_encode($data, JSON_PRETTY_PRINT));


?>
<?php

include "libs/libs.php";

session_start();

$conn = OpenDB_CS();

//print_r($_POST>";

//Utente di prova
//$data2 = array(
//    'patente' => 'RM7269652G',
//    'cf' => 'TMIDRD92D09H501A',
//    'nome' => 'Edoardo',
//    'cognome' => 'Timo',
//    'data_di_nascita' => '1992-04-09',
//    'origine_nascita' => 'I',
//    'provincia_nascita' => 'RM',
//    'comune_nascita' => 'Roma'
//);

//Query
$query = "SELECT driver_license as patente, tax_code as cf,
driver_license_firstname as nome, driver_license_surname as cognome,
birth_date as data_di_nascita,customers.name as name,surname,
CASE WHEN birth_country='it' THEN 'I' ELSE 'E' END as origine_nascita,
CASE WHEN birth_country='it' THEN birth_province ELSE '' END as provincia_nascita,
CASE WHEN birth_country='it' THEN birth_town ELSE '' END as comune_nascita,
mctc as stato_nascita, countries.name as paese FROM customers
LEFT JOIN countries ON code=birth_country
WHERE id=".$_POST['customer_id'].";";

$result = pg_query($conn, $query);

$data = pg_fetch_assoc($result);

//print_r($data)."<br><br>"; //Print fetch query

$_SESSION['customer_id'] = $_POST['customer_id'];
$_SESSION['patente'] = $data['patente'];
$_SESSION['cf'] = $data['cf'];
$_SESSION['nome'] = $data['nome'];
$_SESSION['cognome'] = $data['cognome'];
$_SESSION['data_di_nascita'] = $data['data_di_nascita'];
$_SESSION['origine_nascita'] = $data['origine_nascita'];
$_SESSION['provincia_nascita'] = $data['provincia_nascita'];
$_SESSION['comune_nascita'] = $data['comune_nascita'];
$_SESSION['stato_nascita'] = $data['stato_nascita']." - ".$data['paese'];
$_SESSION['name'] = $data['name'];
$_SESSION['surname'] = $data['surname'];

$array =   array (
    'patente' => $data['patente'],
    'cf' => $data['cf'],
    'nome' => $data['nome'],
    'cognome' => $data['cognome'],
    'data_di_nascita' => $data['data_di_nascita'],
    'origine_nascita' => $data['origine_nascita'],
    'provincia_nascita' => $data['provincia_nascita'],
    'comune_nascita' => $data['comune_nascita'],
    'stato_nascita' => $data['stato_nascita']
);

$url = 'http://license.sharengo.it:8080/check_dl.php';

//Primo metodo - http_build_query
//
    //$result = pg_query($conn, $query);
    //
    //$rows = array();
    //while ($r = pg_fetch_assoc($result)) {
    //    $rows[] = $r;
    //}
    //
    //echo "test loop json";
    //print json_encode($rows);

    //print_r($data);
    //echo "<br><br>";

    //$options = array(
    //    'http' => array(
    //        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    //        'method'  => 'POST',
    //        'content' => http_build_query($data),
    //    ),
    //);
    //
    //$context  = stream_context_create($options);
    //$result = file_get_contents($url, false, $context);
    //
    //
    //$_SESSION['result'] = $result;
    //
    //print_r("print result".$result."\n");
    //Fine primo metodo

//Prove varie
    //
    //echo "risultato file get contents<br>";
    //echo "<br>";
    //var_dump($result);
    //echo "<br>";
    ////$message = str_split($result);
    //$message = explode(',', $result);
    //var_dump($message);
    ////$message = (array) json_decode($result, TRUE);
    //echo $message[4];


//Secondo metodo - CURL
    $options = json_encode($array); //json_encode dell'array
    //echo "<b>echo array per il curl</b><br>";
    print_r($options); //print dell'encoded array

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
     'Content-Type: application/json')
    );

    $response = curl_exec($ch);
//End CURL

//echo "<br><br><b>Type di response:</b>&nbsp;".gettype($response)."<br>"; //get type della risposta
//print_r($response)."<br><br>"; //echo del response della pagina verifica patenti



//Inizio Operazioni superflue per ripulire la stringa
//    $response = str_replace(str_split('{\"}'),"",$response);
//
//    $array = array();
//    $asarr = explode(',', $response);
//
//    foreach($asarr as $val) {
//        $tmp = explode(':', $val);
//        $array[$tmp[0]] = $tmp[1];
//    }
//
//    print_r($array); //print array ripulito
//echo  $array['descrizioneErrore'];
//Fine Operazioni superflue per ripulire la stringa

    $response = str_replace("\xEF\xBB\xBF",'',$response);

    $decode_response = array_values(json_decode($response, true));

    curl_close($ch);

//echo "<br><br>echo del decode<br>";
//print_r(array_values($decode_response))."print result\n";

$_SESSION['result1'] = $decode_response[2];  //patente valida
$_SESSION['result2'] = $decode_response[4];  //messaggio di errore


//Redirect
header("Location:index.php");

//$str = '{"err":false,"codiceMessaggio":"None","descrizioneMessaggio":"None","codiceErrore":"IG0014","descrizioneErrore":"PATENTE NON TROVATA"}';
//
//print_r(json_decode($str));

?>
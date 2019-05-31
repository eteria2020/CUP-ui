<?php
require('../inc/include.php');

  $dbh = getDb();
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  var_dump($_REQUEST);

  if (isset($_REQUEST['targa']) && isset($_REQUEST['comando'])) {
   $targa =   $_REQUEST['targa'];
   $intarg1=0;
   $intarg2=0;
   $txtarg1='';
   $txtarg2='';

    switch ($_REQUEST['comando']) {
        case "WLUPDATE":
            $cmd = 'WLUPDATE';
            break;

        case "WLCLEAN":
            $cmd = 'WLCLEAN';
            break;

        case 'SET_DOORS&1';
            $cmd = 'SET_DOORS';
            $intarg1 = 1;
            break;

        case 'SET_DOORS&0';
            $cmd = 'SET_DOORS';
            $intarg1 = 0;
            break;

        case 'SET_ENGINE&1';
            $cmd = 'SET_ENGINE';
            $intarg1 = 1;
            break;

        case 'SET_ENGINE&0';
            $cmd = 'SET_ENGINE';
            $intarg1 = 0;
            break;

        case 'RESEND_TRIP';
            $cmd = 'RESEND_TRIP';
            break;

        case 'CLOSE_TRIP';
            $cmd = 'CLOSE_TRIP';
            break;

        case 'SET_NAVIGATOR&0':
            $cmd = 'SET_NAVIGATOR';
            $intarg1 = 0;
            break;

        case 'SET_NAVIGATOR&1':
            $cmd = 'SET_NAVIGATOR';
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


        case 'SET_FUELCARD_PIN':
            $cmd = 'SET_FUELCARD_PIN';
            $stm0 = $dbh->prepare("SELECT  carburante_id,carburante_pin FROM tbl_card_carburante WHERE carburante_targa = :targa AND carburante_attivo = TRUE");
            $stm0->bindParam(':targa', $targa, PDO::PARAM_STR);
            $stm0->execute();

            $result=$stm0->fetch(PDO::FETCH_ASSOC);

            if ($result)
               $txtarg1=$result['carburante_pin'];
            else
               $txtarg1='';

    }

  $stm = $dbh->prepare("INSERT INTO  comandi (targa,comando,intarg1, emesso,da_inviare) VALUES (:targa,:cmd, :intarg1 , now(),TRUE);");
  $stm->bindParam(':targa', $targa, PDO::PARAM_STR);
  $stm->bindParam(':cmd', $cmd, PDO::PARAM_STR);
  $stm->bindParam(':intarg1', $intarg1, PDO::PARAM_INT);
  $stm->execute();

  echo "DONE";
  exit();
  }

//Reduce errors
error_reporting(~E_WARNING)
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<form action="<?=$_SERVER['PHP_SELF']?>">

Veicolo: <select name="targa" id="targa">


<?php
  $stm = $dbh->prepare("SELECT  * FROM tbl_vettura ORDER BY vettura_targa");
  $stm->bindParam(':targa', $targa, PDO::PARAM_STR);
  $stm->execute();


  while ($row = $stm->fetch()) {
    echo "<option value='$row[vettura_targa]'>$row[vettura_targa]</option>";
  }
?>
</select>

</form>


<?php

include "libs/libs.php";

session_start();

$conn = OpenDB_CS();

?>

<!DOCTYPE html>

<html>

<head>
  <title>Verifica patente</title>
    <link rel="stylesheet" type="text/css" href="css/cmd.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="js/npm.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
</head>

<body>
    <div class="container">
        <div class="well" style="font-size:22px; color:#FFF; background:#43A34D;">Validazione Patente</div>
            <div class="well">
                <form class="form-inline" role="form" name="moto" action="check_moto.php" method="post">
                <div class="form-group">
                <?php
                if (isset($_SESSION['customer_id'])) {
                echo "<label><b>Customer ID:</b>&nbsp;<input class=\"form-control\" style=\"width:100px;\" id=\"customer_id\"
                name=\"customer_id\" value=\"".$_SESSION['customer_id']."\" required></label>";
                } else {
                echo "<label><b>Customer ID:</b>&nbsp;<input class=\"form-control\" style=\"width:100px;\" id=\"customer_id\"
                name=\"customer_id\" value=\"\" required></label>";
                }
                ?>
                </div>
                <div class="form-group">
                <button class="btn btn-success" type="submit">Verifica</button>
                <a style="color:#fff;" href="http://license.sharengo.it:8080/check_patente.htm" target="_blank"><button class="btn btn-warning" type="button">Link Form CS</button></a>
                </div>
                <?php
                if (isset($_SESSION['customer_id'])) {
                    echo "<br><br>";
                    echo "<div>";
                    echo "<b>ID Utente:</b>&nbsp;".$_SESSION['customer_id']."<br>";
                    echo "<b>Patente:</b>&nbsp;".$_SESSION['patente']."<br>";
                    echo "<b>Codice Fiscale:</b>&nbsp;".$_SESSION['cf']."<br>";
                    echo "<b>Nome patente:</b>&nbsp;".$_SESSION['nome']."<br>";
                    echo "<b>Cognome patente:</b>&nbsp;".$_SESSION['cognome']."<br>";
                    echo "<b>Nome:</b>&nbsp;".$_SESSION['name']."<br>";
                    echo "<b>Cognome:</b>&nbsp;".$_SESSION['surname']."<br>";
                    echo "<b>Data di nascita:</b>&nbsp;".$_SESSION['data_di_nascita']."<br>";
                    echo "<b>Origine nascita:</b>&nbsp;".$_SESSION['origine_nascita']."<br>";
                    echo "<b>Provincia di nascita:</b>&nbsp;".$_SESSION['provincia_nascita']."<br>";
                    echo "<b>Comune di nascita:</b>&nbsp;".$_SESSION['comune_nascita']."<br>";
                    echo "<b>Stato di nascita:</b>&nbsp;".$_SESSION['stato_nascita']."<br>";
                    echo "<br>";
                    echo "</div>";
                    if ($_SESSION['result1'] == 'PATENTE VALIDA') {
                        echo "<div class=\"alert alert-success\"><b>Messaggio di risposta</b><br>".$_SESSION['result1']."</div>";
                        exit();
                    } else if ($_SESSION['result1'] !== 'PATENTE VALIDA' && $_SESSION['result1'] !== 'None') {
                        echo "<div class=\"alert alert-warning\"><b>Messaggio di risposta</b><br>".$_SESSION['result1']."</div>";
                        exit();
                    } else {
                        echo "<div class=\"alert alert-danger\"><b>Messaggio di risposta</b><br>".$_SESSION['result2']."</div>";
                        exit();
                    }

                    echo stripos($_SESSION['result1']);
                }

                ?>
                </form>
            </div>
    </div>

</body>
</html>
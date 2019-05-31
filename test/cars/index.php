<?php

require("libs/libs.php");

session_start();

$conn = CS_DB();

$last_plate = ''; //variabile ultima targa

//se si proviene da un update targa setta la variabile
if (isset($_SESSION['plate'])) {
    $last_plate = $_SESSION['plate'];
    unset($_SESSION['plate']);
}


//Settaggio hidden = TRUE
if (isset($_POST['hide'])) {
    $plate = $_POST['hide']; //preparo la targa per la query
    $_POST = array();
    $hide = $conn->query("UPDATE cars SET hidden='TRUE' WHERE plate='".$plate."';"); //eseguo la query di update
    $_SESSION['plate'] = $plate;
    header("Location:index.php");
}

//Settaggio hidden = FALSE
if (isset($_POST['unhide'])) {
    $plate = $_POST['unhide']; //preparo la targa per la query
    $_POST = array();
    $unhide = $conn->query("UPDATE cars SET hidden='FALSE' WHERE plate='".$plate."';"); //eseguo la query di update
    $_SESSION['plate'] = $plate;
    header("Location:index.php");
}


//Ricerca lista auto
$query = $conn->query("SELECT plate, fleets.name as fleet, battery, last_contact, status, case when hidden is true then 'SI' ELSE 'NO' END as hidden, cars.latitude, cars.longitude,
(SELECT count(*) FROM reservations WHERE car_plate = plate AND customer_id is null AND active is true) AS reserv_sys,
(SELECT count(*) FROM reservations WHERE car_plate = plate AND customer_id is NOT null AND active is true) AS reserv_user,
(SELECT count(*) FROM trips WHERE car_plate = plate AND timestamp_end is null) as trips
FROM cars left join fleets on fleet_id = fleets.id ORDER BY plate ASC;");


?>
<html>
    <head>
        <title>Cars</title>
            <link rel="stylesheet" type="text/css" href="css/cmd.css">
            <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
            <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
            <link rel="stylesheet" type="text/css" href="css/colReorder.bootstrap.css">
            <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
            <script type="text/javascript" src="js/bootstrap.js"></script>
            <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
            <script type="text/javascript" src="js/dataTables.bootstrap.js"></script>
            <script type="text/javascript" src="js/jquery.dataTables.js"></script>
            <script type="text/javascript" src="js/dataTables.colReorder.js"></script>
    </head>
    <body>
        <div class="container-fluid">
        <!-- INIZIO MAIN -->
        <div class="well" style="font-size:22px; color:#FFF; background:#43A34D;">
            Cars
        </div>
        <table id="cars" class="table table-hover table-bordered table-condensed">
        <thead>
            <th>Targa</th>
            <th>Flotta</th>
            <th>Batteria</th>
            <th>Contatto</th>
            <th>Stato</th>
            <th>Hidden</th>
            <th>Pos.</th>
            <th>Pren. User</th>
            <th>Pren. Sys</th>
            <th>Corse</th>
            <th>Action</th>
        </thead>
        <tbody>

        <?php

            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>".$row['plate']."</td>";
                echo "<td>".$row['fleet']."</td>";
                echo "<td>".$row['battery']."</td>";
                echo "<td>".$row['last_contact']."</td>";
                echo "<td>".$row['status']."</td>";
                echo "<td>".$row['hidden']."</td>";
                echo "<td><a href=\"http://maps.google.com/?q=".$row['latitude'].",".$row['longitude']."\" target=\"_blank\">".$row['latitude']." ".$row['longitude']."</a></td>";
                echo "<td>".$row['reserv_sys']."</td>";
                echo "<td>".$row['reserv_user']."</td>";
                echo "<td>".$row['trips']."</td>";
                echo "<td><form class=\"form-inline\" role=\"form\" name=\"cars\" action=\"#\" method=\"post\">
                <div class=\"form-group\">
                <a class=\"btn btn-success\" href=\"http://admin.sharengo.it/cars/edit/".$row['plate']."\" target=\"_blank\">EDIT</a>&nbsp;";
                if ($row['hidden'] == 'NO') {
                    echo "<input type=\"hidden\" name=\"hide\" value=\"".$row['plate']."\"><button onclick=\"return confirm('Sei sicuro di voler nascondere l&lsquo;auto ".$row['plate']."?')\" type=\"submit\" class=\"btn btn-warning\">HIDE</button></form></div></td>";}
                else {
                    echo "<input type=\"hidden\" name=\"unhide\" value=\"".$row['plate']."\"><button onclick=\"return confirm('Sei sicuro di volere rendere l&lsquo;auto ".$row['plate']." visibile?')\" type=\"submit\" class=\"btn btn-warning\">UNHIDE</button></form></div></td>";
                }
                echo "</tr>";
            }

        ?>

        </tbody>
        </table>
        <!--FINE MAIN-->
        </div>
    </body>


<script>

  $(document).ready(function() {
      $('#cars').DataTable( {
        colReorder: true,
        "search": {
            "search": "<?php echo $last_plate; ?>"
        },
        "lengthMenu": [[50, 75, 100, -1], [50, 75, 100, "Tutte"]],
        "order": [[ 0, 'asc' ]],
        "language": {
            "url": "loc/it_IT.json"
        }
      } );
  } );

</script>

</html>
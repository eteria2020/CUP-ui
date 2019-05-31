<?php

include ('libs/libs.php');

session_start();

$conn = OpenDB_CS();

if (isset($_POST['trip_id'])) {

  $_SESSION['trip_id'] = $_POST['trip_id'];
  $trip_id = $_POST['trip_id'];

  $query = $conn->query("SELECT
  events.id as id, customer_id, name, surname, car_plate, event_time, battery, km, event_id, label, txtval, intval,
  round(lat,4) as lat, round(lon,4) as lon
  FROM events LEFT JOIN customers ON customer_id = customers.id WHERE trip_id=".$trip_id." ORDER BY event_time DESC;");

  $trip = $conn->prepare("SELECT * FROM trips WHERE id=".$trip_id.";");

  $trip->execute();

  $result = $trip->fetch(PDO::FETCH_BOTH);

}

?>

<!DOCTYPE html>
<html>
  <head>
  <title>Trip Events</title>
    <link rel="stylesheet" type="text/css" href="css/cmd.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="js/npm.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/jquery.tablesorter.js"></script>
    <script>
    $(document).ready(function()
    {
        $("#events").tablesorter();
    }
    );

    $(document).ready(function()
    {
        $("#trip").tablesorter();
    }
    );

    </script>



  </head>
  <body>
    <div class="container-fluid">
      <div class="well" style="font-size:22px; color:#FFF; background:#43A34D;">
        Eventi
      </div>
          <form class="form-inline" role="form" name="events" action="#" method="post" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
            <div class="form-group">
            <label>ID Corsa:</label>
              <input class="form-control" style="width:100px;" id="trip_id"
                  name="trip_id" value="" required>
            </div>
            <div class="form-group">
              <button class="btn btn-success" type="submit">Cerca</button>
            </div>
          </form>
          <br>
        <?php

          if(isset($trip_id)) {

            if (is_null($result['id']) ||  $result['id'] === NULL ) {
              echo "<div class=\"alert alert-danger\">";
                echo "<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>";
                echo "Nessuna corsa trovata con ID <b>".$_SESSION['trip_id']."</b>";
              echo "</div>";
            }
            else {

                echo "<table id=\"trip\" class=\"table table-hover sorter table-bordered table-condensed\">";
                  echo "<thead>";
                    echo "<th>ID Corsa</th>";
                    echo "<th>Ora Inizio</th>";
                    echo "<th>Ora Fine</th>";
                  echo "</thead>";
                  echo "<tbody>";
                    echo "<tr>";
                    echo "<td>".$result['id']."</td>";
                    echo "<td>".$result['timestamp_beginning']."</td>";
                    echo "<td>".$result['timestamp_end']."</td>";
                    echo "</tr>";
                  echo "</tbody>";
                echo "</table>";

                echo "<table id=\"events\" class=\"table table-hover sorter table-bordered table-condensed\">";
                  echo "<thead>";
                    echo "<th>ID</th>";
                    echo "<th>ID Cliente</th>";
                    echo "<th>Nome</th>";
                    echo "<th>Cognome</th>";
                    echo "<th>Targa</th>";
                    echo "<th>Event Time</th>";
                    echo "<th>Battery</th>";
                    echo "<th>Km</th>";
                    echo "<th>Event ID</th>";
                    echo "<th>Label</th>";
                    echo "<th>Txtval</th>";
                    echo "<th>Intval</th>";
                    echo "<th>Posizione (Lat/Lon)</th>";
                  echo "</thead>";
                  echo "<tbody>";

              while($row = $query->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr>";
                echo "<td>".$row['id']."</td>";
                echo "<td>".$row['customer_id']."</td>";
                echo "<td>".$row['name']."</td>";
                echo "<td>".$row['surname']."</td>";
                echo "<td>".$row['car_plate']."</td>";
                echo "<td>".$row['event_time']."</td>";
                echo "<td>".$row['battery']."</td>";
                echo "<td>".$row['km']."</td>";
                echo "<td>".$row['event_id']."</td>";
                echo "<td>".$row['label']."</td>";
                echo "<td>".$row['txtval']."</td>";
                echo "<td>".$row['intval']."</td>";
                echo "<td><a href=\"http://maps.google.com/?q=".$row['lat'].",".$row['lon']."\" target=\"_blank\">".$row['lat']." ".$row['lon']."</a></td>";
                echo "</tr>";
              }

              echo "</tbody>";
              echo "</table>";
            }
          }
        ?>
    </div>
  </body>
</html>
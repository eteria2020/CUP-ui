<?php

include("libs/libs.php");

session_start();

$conn = CS_DB();

$invoices_general = $conn->query("with fatture as (
 select invoices.id as invoice_id, invoice_number, invoice_date::TEXT::DATE as invoice_date, fleets.name as fleet, type, generated_ts from invoices
 left join fleets on fleets.id = fleet_id
 order by invoice_number asc)

select * FROM (
 select invoice_id, invoice_number, invoice_date, fleet, type, generated_ts,
 lead(invoice_number) over (order by invoice_number asc) as next_invoice_number,
 lead(invoice_date) over (order by invoice_number asc) as next_invoice_date,
 case when lead(right(invoice_number,6)::int) over (order by invoice_number asc) <> right(invoice_number,6)::int + 1
 and lead(fleet) over (order by invoice_number asc) = fleet
 then true::boolean else false::boolean end as errore_numero,
 case when lead(invoice_date) over (order by invoice_number asc) < invoice_date
 and lead(fleet) over (order by invoice_number asc) = fleet
 then true::boolean else false::boolean end as errore_data
 from fatture
) tmp
where invoice_date='20140101'");

//ARRAY FATTURE ANOMALE GENERALI
$result_general = array();

while($row = $invoices_general->fetch(PDO::FETCH_ASSOC)) {
    $result_general[] = $row;
}

$itemCountGeneral = sizeof($result_general);

$invoices_date = $conn->query("with fatture as (
 select invoices.id as invoice_id, invoice_number, invoice_date::TEXT::DATE as invoice_date, fleets.name as fleet, type from invoices
 left join fleets on fleets.id = fleet_id
 order by invoice_number asc)

select * FROM (
 select invoice_id, invoice_number, invoice_date, fleet, type,
 lead(invoice_number) over (order by invoice_number asc) as next_invoice_number,
 lead(invoice_date) over (order by invoice_number asc) as next_invoice_date,
 case when lead(right(invoice_number,6)::int) over (order by invoice_number asc) <> right(invoice_number,6)::int + 1
 and lead(fleet) over (order by invoice_number asc) = fleet
 then true::boolean else false::boolean end as errore_numero,
 case when lead(invoice_date) over (order by invoice_number asc) < invoice_date
 and lead(fleet) over (order by invoice_number asc) = fleet
 then true::boolean else false::boolean end as errore_data
 from fatture
) tmp
where errore_data is true");

//ARRAY FATTURE ANOMALE DATA
$result_date = array();

while($row = $invoices_date->fetch(PDO::FETCH_ASSOC)) {
    $result_date[] = $row;
}

$itemCount = sizeof($result_date);

?>

<html>
    <head>
    <title>Fatture Anomale</title>
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
            <div class="well" style="font-size:22px; color:#FFF; background:#43A34D;">
                Anomalia Fatture
            </div>

<?php

if ($itemCountGeneral>0) {
            echo "<table id=\"invoices_general\" class=\"table table-hover table-bordered sorter table-condensed\">";
                echo "<thead>";
                    echo "<th>Invoice ID</th>";
                    echo "<th>Invoice Number</th>";
                    echo "<th>Generated TS</th>";
                    echo "<th>Invoice Date</th>";
                    echo "<th>Fleet</th>";
                    echo "<th>Type</th>";
                    echo "<th>Numero Fattura Succ.</th>";
                    echo "<th>Data Fattura Succ.</th>";
                    echo "<th>Errore Numero</th>";
                    echo "<th>Errore Data</th>";
                echo "</thead>";
                echo "<tbody>";

                while($row = $invoices_general->fetch(PDO::FETCH_ASSOC)) {

                        echo "<tr>";
                        echo "<td>".$row['invoice_id']."</td>";
                        echo "<td>".$row['invoice_number']."</td>";
                        echo "<td>".$row['generated_ts']."</td>";
                        echo "<td>".$row['invoice_date']."</td>";
                        echo "<td>".$row['fleet']."</td>";
                        echo "<td>".$row['type']."</td>";
                        echo "<td>".$row['next_invoice_number']."</td>";
                        echo "<td>".$row['next_invoice_date']."</td>";
                        if ($row['errore_numero'] == '1') echo "<td>SI</td>"; else echo "<td></td>";
                        if ($row['errore_data'] == '1') echo "<td>SI</td>"; else echo "<td></td>";
                        echo "</tr>";
                }
} else {
                        echo "<img src=\"https://i.ytimg.com/vi/G9WcjwYnY3g/hqdefault.jpg\">";
}

                echo "</tbody>";
                echo "</table>";


if ($itemCount>0) {

            echo "<div class=\"well-sm toggle-visibility\" data-target=\"#dettagli\" style=\"font-size:22px; color:#FFF; background:#43A34D;\" onmouseover=\"this.style.background='gray';\" onmouseout=\"this.style.background='#43A34D';\">";
            echo "Anomalia Data";
            echo "</div><br>";

            echo "<div id=\"dettagli\">";
            echo "<table id=\"invoices\" class=\"table table-hover table-bordered sorter table-condensed\">";
                echo "<thead>";
                    echo "<th>Invoice ID</th>";
                    echo "<th>Invoice Number</th>";
                    echo "<th>Generated TS</th>";
                    echo "<th>Invoice Date</th>";
                    echo "<th>Fleet</th>";
                    echo "<th>Type</th>";
                echo "</thead>";
                echo "<tbody>";


for ($i = 0; $i < $itemCount; $i++) {

$query_date = $conn->query("SELECT invoices.id, invoice_number, invoice_date, fleets.name as fleet, generated_ts, type FROM invoices
LEFT JOIN fleets ON fleets.id = fleet_id
WHERE invoice_number>'".$result_date[$i]['invoice_number']."' AND fleets.name = '".$result_date[$i]['fleet']."' AND invoice_date::text::date = '".$result_date[$i]['next_invoice_date']."' ORDER BY invoice_number ASC");

                while($row = $query_date->fetch(PDO::FETCH_ASSOC)) {

                    echo "<tr>";
                    echo "<td>".$row['id']."</td>";
                    echo "<td>".$row['invoice_number']."</td>";
                    echo "<td>".$row['generated_ts']."</td>";
                    echo "<td>".$row['invoice_date']."</td>";
                    echo "<td>".$row['fleet']."</td>";
                    echo "<td>".$row['type']."</td>";
                    echo "</tr>";
                }
}
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
}
?>

        </div>
    </body>

<script>

  $(document).ready(function() {
      $('#invoices').DataTable( {
        colReorder: true,
        "lengthMenu": [[50, 75, 100, -1], [50, 75, 100, "All"]],
        "order": [[ 1, 'asc' ]],
        "language": {
            "url": "loc/it_IT.json"
        }
      } );
  } );

$(document).ready(function(){

/* Button which shows and hides div with a id of "post-details" */
$( ".toggle-visibility" ).click(function() {

  var target_selector = $(this).attr('data-target');
  var $target = $( target_selector );

  if ($target.is(':hidden'))
  {
    $target.show( "slow" );
  }
  else
  {
  	$target.hide( "slow" );
  }

  console.log($target.is(':visible'));


});



});

</script>
</html>


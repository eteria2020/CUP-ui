<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php $username = $_SERVER['PHP_AUTH_USER']; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Report auto</title>

    <link rel="stylesheet" type="text/css" media="screen" href="themes/redmond/jquery-ui-custom.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="themes/ui.jqgrid.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="themes/ui.multiselect.css" />


    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery-ui-custom.min.js" type="text/javascript"></script>
    <script src="js/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script type="text/javascript">
        $.jgrid.no_legacy_api = true;
        $.jgrid.useJSON = true;
    </script>

    <script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>

 </head>
<body>


<table  id="data" ></table>
<div id='pager'></div>


<BR />
<form action="#" method="GET" id="form">
Car plate : <input type="text" name="targa" id="targa"/>

Command:

<select name="comando" id="comando">
<option value="WLUPDATE"> Scarica whitelist </option>
<option value="WLCLEAN"> Cancella e riscarica whitelist </option>
<option value="SET_DOORS&1"> Apri portiere</option>
<option value="SET_DOORS&0"> Chiudi portiere</option>
<option value="SET_ENGINE&1"> Abilita motore</option>
<option value="SET_ENGINE&0"> Disabilita motore</option>
<option> Metti fuori servizio </option>
<option value="RESEND_TRIP"> Rispedisci corse </option>
<option value="CLOSE_TRIP"> Chiudi ultima corsa aperta</option>
<option value="OPEN_SERVICE"> Apri finestra di servizio</option>
<option value="SEND_LOGS"> Invia db e logs al server</option>
>

<?php if ($username=="admin") { ?>
    <option value="ADMINS_UPDATE"> Aggiorna lista card admin</option>
    <option value="SHUTDOWN"> Shutdown android</option>
    <option value="SET_LOCATION">Imposta coordinate fisse</option>
<?php } ?>

</select>

<input name="txtarg1" id="txtarg1" size="48"  />

<input type="submit" id="btnSubmit" value="Execute" />

</form>




</body>
<script type="text/javascript">
   jQuery("#txtarg1").hide();

    var dataArray = [];

   jQuery("#comando").change(function() {
     console.log(this.value);
     if (this.value=='SEND_LOGS') {
       jQuery("#txtarg1").show();
       jQuery("#txtarg1").attr("placeholder", "Path of file to download (empty for current log+db)");
     } else if (this.value=="SET_LOCATION") {
       jQuery("#txtarg1").show();
       jQuery("#txtarg1").attr("placeholder", "Coordinates (ex: [45.4739155;9.1687462] )");
     } else {
       jQuery("#txtarg1").hide();
     }
   });

   jQuery("#btnSubmit").click(function() {

    // Get the object containing the plate specified in the input text
    var result = $.grep(dataArray, function(e){ return e.plate.toLowerCase() == $("input#targa").val().toLowerCase(); });

    // Check if ther's a plate corresponding.
    if (result.length === 0){
        $("input#targa").css("background-color","red");
    }else{
        $("input#targa").val(result[0].plate);
        $("input#targa").css("background-color","");
        
        jQuery.ajax({
           type: "GET",
           url: "comandi2.php",
           data : jQuery("#form").serialize(),
           success: function (data) {
             alert(data);
           }
        });
    }

    return false;
   })


    jQuery("#data").jqGrid({
        url:'auto-data.php',
        datatype: "json",
        colNames:['N','Plate','Batt.', 'Km','v. sw','contact',"KEY","RPM","Spd.","Busy","Ready","Park","Charging","Plug","Status" <?php if ( $username=='admin')  echo ',"N.Trips"';?>],
        colModel:[
            {name:'label',index:'label', width:50, align:'center'},
            {name:'plate',index:'plate', width:70, align:'center'},
            {name:'battery',index:'battery', width:60, align:'right'},
            {name:'km',index:'km', width:50, align:'right'},
            {name:'software_version',index:'software_version', width:100, align:'left'},
            {name:'last_contact',index:'last_contact', width:150, align:'right',formatter:'date', formatoptions:{srcformat:"Y-m-d H:i:s",newformat:"d/m/Y   H:i:s"}},
            {name:'key_status',index:'key_status', width:50, align:'center'},
            {name:'rpm',index:'rpm', width:40, align:'right'},
            {name:'speed',index:'speed', width:40, align:'right'},
            {name:'busy',index:'busy', width:30, align:'center',formatter:'checkbox'},
            {name:'running',index:'running', width:30, align:'center',formatter:'checkbox'},
            {name:'parking',index:'parking', width:30, align:'center',formatter:'checkbox'},
            {name:'charging',index:'charging', width:30, align:'center',formatter:'checkbox'},
            {name:'plug',index:'plug', width:30, align:'center',formatter:'checkbox'},
            {name:'status',index:'status', width:40, align:'right'},
            <?php if ( $username=='admin')  echo "{name:'ntrips',index:'ntrips', width:40, align:'right', formatter:'showlink', formatoptions:{baseLinkUrl:'trips.php', addParam: ''} }"; ?>
        ],
        rowNum:0,
        pager: '#pager',
        sortname: 'plate',
        viewrecords: true,
        sortorder: "asc",
        rowList: [],        // disable page size dropdown
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,
        caption:"Elenco vetture",
        width: $(document).width()-30,
        height: $(document).height()-135,
        autowidth: false,
        shrinkToFit: true,
        onSelectRow: function(ids) {
          if (ids != null) {
              jQuery("#targa").val(ids);
          }
        },
        loadComplete: function(data){
            dataArray = jQuery("#data").jqGrid('getRowData');
        }




    });
    jQuery("#data").jqGrid('navGrid','#pager',{edit:false,add:false,del:false});
</script>

</html>


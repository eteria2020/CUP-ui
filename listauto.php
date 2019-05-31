<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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


<table width=100%  id="data" ></table>
<div id="pager"></div>

<BR />
<form action="comandi.php" method="GET" id="form">
Veicolo : <input type="text" name="targa" id="targa"/>  comando :

<select name="comando" id="comando">
<option value="WLUPDATE"> Scarica whitelist </option>
<option value="WLCLEAN"> Cancella e riscarica whitelist </option>
<option value="SET_DOORS&1"> Apri portiere</option>
<option value="SET_DOORS&0"> Chiudi portiere</option>
<option value="SET_ENGINE&1"> Abilita motore</option>
<option value="SET_ENGINE&0"> Disabilita motore</option>
<option value="CLOSE_TRIP"> Chiudi ultima corsa aperta</option>
</select>

<input type="submit" id="btnSubmit" value="Esegui" />

</form>




</body>
<script type="text/javascript">
   jQuery("#btnSubmit").click(function() {

     jQuery.ajax({
       type: "GET",
       url: "comandi.php",
       data : jQuery("#form").serialize(),
       success: function (data) {
         alert(data);
       }
     });

     return false;
   })


    jQuery("#data").jqGrid({
        url:'auto-data.php',
        datatype: "json",
        colNames:['N','Targa','Batt.', 'Carb.', 'Km','v. fw','v. sw','contatto',"Quadro","RPM","Vel.","InUso","Aperte","DaInviare"],
        colModel:[
            {name:'vettura_numero',index:'vettura_numero', width:50, align:'center'},
            {name:'vettura_targa',index:'vettura_targa', width:70, align:'center'},
            {name:'vettura_tensione_batteria',index:'vettura_tensione_batteria', width:60, align:'right'},
            {name:'vettura_livello_carburante',index:'vettura_livello_carburante', width:60, align:'right'},
            {name:'vettura_km',index:'vettura_km', width:50, align:'right'},
            {name:'vettura_versione_fw',index:'vettura_versione_fw', width:60, align:'right'},
            {name:'vettura_versione_sw',index:'vettura_versione_sw', width:100, align:'left'},
            {name:'vettura_ultimo_contatto',index:'vettura_ultimo_contatto', width:150, align:'right',formatter:'date', formatoptions:{srcformat:"Y-m-d H:i:s",newformat:"d/m/Y   H:i:s"}},
            {name:'vettura_quadro_acceso',index:'vettura_quadro_acceso', width:50, align:'center',formatter:'checkbox'},
            {name:'vettura_rpm',index:'vettura_rpm', width:40, align:'right'},
            {name:'vettura_velocita',index:'vettura_velocita', width:40, align:'right'},
            {name:'vettura_in_corsa',index:'vettura_in_corsa', width:40, align:'right'},
            {name:'vettura_corse_aperte',index:'vettura_corse_aperte', width:40, align:'right'},
            {name:'vettura_corse_da_inviare',index:'vettura_corse_da_inviare', width:40, align:'right'},
        ],
        rowNum:300,
        pager: '#pager',
        sortname: 'vettura_targa',
        viewrecords: true,
        sortorder: "asc",
        caption:"Twist - elenco vetture",
        height:'800px',
        onSelectRow: function(ids) {
          if (ids != null) {
              jQuery("#targa").val(ids);
          }
        }


    });
    jQuery("#data").jqGrid('navGrid','#pager',{edit:false,add:false,del:false});
</script>

</html>


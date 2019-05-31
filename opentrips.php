<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php $username = $_SERVER['PHP_AUTH_USER']; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Open trips</title>

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



</body>
<script type="text/javascript">


   function executeClose(id) {

     jQuery.ajax({
       type: "GET",
       url: "comandi2.php",
       data : {comando: 'CLOSE_TRIP', trip:id },
       success: function (data) {
         alert(data);
         jQuery('#data').trigger("reloadGrid");
       }
     });

     return false;
   }


    jQuery("#data").jqGrid({
        url:'opentrips-data.php',
        datatype: "json",
        colNames:['ID','Plate','Customer name', 'Trip begin', 'Operation'],
        colModel:[
            {name:'id',index:'id', width:50, align:'center'},
            {name:'car_plate',index:'car_plate', width:70, align:'center'},
            {name:'name',index:'name', width:200, align:'left'},
            {name:'timestamp_beginning',index:'timestamp_beginning', width:150, align:'left',formatter:'date', formatoptions:{srcformat:"Y-m-d H:i:s",newformat:"d/m/Y   H:i:s"}},
            {name:'operation',index:'operation', width:80,sortable:false}
        ],
        rowNum:0,
        pager: '#pager',
        sortname: 'timestamp_beginning',
        viewrecords: true,
        sortorder: "asc",
        rowList: [],        // disable page size dropdown
        pgbuttons: false,     // disable page control like next, back button
        pgtext: null,
        caption:"Currently open trips",
        width: $(document).width()-30,
        height: $(document).height()-135,
        autowidth: false,
        shrinkToFit: true,

        onSelectRow: function(ids) {
          if (ids != null) {
              jQuery("#targa").val(ids);
          }
        },

        gridComplete: function(){
            var grid = jQuery("#data");
    		var ids = grid.jqGrid('getDataIDs');
    		for(var i=0;i < ids.length;i++){
    			var cl = ids[i];
    			be = "<input style='height:22px;' type='button' value='Close' onclick=\"executeClose("+cl+");\"  />";
    			jQuery("#data").jqGrid('setRowData',ids[i],{operation:be} , {height:35});
    		}
    	}

    });
    jQuery("#data").jqGrid('navGrid','#pager',{edit:false,add:false,del:false});
</script>

</html>


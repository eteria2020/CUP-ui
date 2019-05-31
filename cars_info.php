<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Report auto (Cars Info)</title>

    <!-- The jQuery library is a prerequisite for all jqSuite products -->
    <script type="text/ecmascript" src="js/jquery-1.11.0.min.js"></script>
    <!-- We support more than 40 localizations -->
    <script type="text/ecmascript" src="js/i18n/grid.locale-en.js"></script>
    <!-- This is the Javascript file of jqGrid -->
    <script type="text/ecmascript" src="js/jquery.jqGrid.min.js"></script>

    <!-- This is the localization file of the grid controlling messages, labels, etc.
    <!-- A link to a jQuery UI ThemeRoller theme, more than 22 built-in and many more custom -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <!-- The link to the CSS that the grid needs -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid-bootstrap.css" />

    <script type="text/ecmascript" src="js/bootstrap3-typeahead.min.js"></script>

    <!-- The Date Picker -->
    <script type="text/ecmascript" src="js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="css/datepicker.css" />

    <script type="text/javascript">
        //$.jgrid.no_legacy_api = true;
        $.jgrid.useJSON = true;
		$.jgrid.defaults.responsive = true;
        $.jgrid.defaults.styleUI = 'Bootstrap';
    </script>

    <!-- Color of rows -->
    <style>
        #data tr:nth-child(odd){
            background-color:#EBECF5;
        }
        #data tr:nth-child(even){
            background-color:white;
        }
    </style>
 </head>
<body>


    <table  id="data" ></table>
    <div id='pager'></div>

</body>

<script type="text/javascript">
    $(document).ready(function () {

    var flag = true;

        $("#data").jqGrid({
            url:'cars_info-data.php',
            mtype: "GET",
            datatype: "json",
            page: 1,
            colNames:['car_plate','int_lat','int_lon', 'gprs_lat','gprs_lon','gps','fw_ver','hw_ver','sw_ver','sdk','sdk_ver','gsm_ver','android_device','android_build','tbox_sw','tbox_hw','mcu_model','mcu','hw_version','hb_ver','vehicle_type','lastupdate'],
            colModel: [
                {name:'car_plate',      index:'car_plate',      width:90, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'int_lat',        index:'int_lat',        width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'right'},
                {name:'int_lon',        index:'int_lon',        width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'right'},
                {name:'gprs_lat',       index:'gprs_lat',       width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'right'},
                {name:'gprs_lon',       index:'gprs_lon',       width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'right'},
                {name:'gps',            index:'gps',            width:30, 	searchoptions: {value: ":[All];INT:INT;EXT:EXT"},align:'center', stype: "select"},
                {name:'fw_ver',         index:'fw_ver',         width:30, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'hw_ver',         index:'hw_ver',         width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'left'},
                {name:'sw_ver',         index:'sw_ver',         width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'sdk',            index:'sdk',            width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'sdk_ver',        index:'sdk_ver',        width:30, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'gsm_ver',        index:'gsm_ver',        width:80, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'android_device', index:'android_device', width:40, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'android_build',  index:'android_build',  width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'left'},
                {name:'tbox_sw',        index:'tbox_sw',        width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'left'},
                {name:'tbox_hw',        index:'tbox_hw',        width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'left'},
                {name:'mcu_model',      index:'mcu_model',      width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'mcu',            index:'mcu',            width:40, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'left'},
                {name:'hw_version',     index:'hw_version',     width:30, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'hb_ver',         index:'hb_ver',         width:30, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'vehicle_type',   index:'vehicle_type',   width:50, 	searchoptions: {sopt: ["cn","bw","in","ew"] },  align:'center'},
                {name:'lastupdate',     index:'lastupdate',     width:100, 	searchoptions: {
                            // dataInit is the client-side event that fires upon initializing the toolbar search field for a column
                            dataInit: function (element) {
                                 $(element).datepicker( {
                                    format: "dd/mm/yyyy",
                                    todayBtn: "linked",
                                    clearBtn: true,
                                    todayHighlight: true,
                                    autoclose: true,
                                    orientation : 'bottom'
                                });
                            },sopt: ["gt","lt","eq"]},
                    align:'center',
                    formatter: "date",
                    sorttype:'date',
                    formatoptions: { srcformat: "ISO8601Long", newformat: "d/m/Y H:i:s" }
                }
            ],
            loadonce: true,
            viewrecords: true,
            width: $(document).width()-20,
            height: $(document).height() - 135,
            /************** CASE 1: Virtual Load On Demand Mode - scrollbar paging ******/
            rowNum: 50,
            scroll: 1,
            /************** CASE 2: Full page load **************************************/
            //rowNum: 99999,
            /************** END CASE ****************************************************/
            pgbuttons: false,     // disable page control like next, back button
            pgtext: null,
            pager: "#pager" ,
            loadComplete: function () {
                var $this = $(this);
                var selRowIds = jQuery('#tblCompletedPriceList').jqGrid('getGridParam', 'selarrow');
                postfilt = $this.jqGrid('getGridParam', 'postData').filters;
                postsord = $this.jqGrid('getGridParam', 'postData').sord;
                postsort = $this.jqGrid('getGridParam', 'postData').sidx;
                postpage = $this.jqGrid('getGridParam', 'postData').page;
                var selRowIds = jQuery('#tblCompletedPriceList').jqGrid('getGridParam', 'selarrrow');

                if ($this.jqGrid("getGridParam", "datatype") === "json" && flag) {
                    setTimeout(function () {
                        $this.jqGrid("setGridParam", {
                            datatype: "local",
                            postData: { filters: postfilt, sord: postsord, sidx: postsort },
                            search: true
                        });
                        $this.trigger("reloadGrid", [{ page: postpage }]);
                    }, 15);
                }
                flag=true;

                // Remove Visual unneeded Button
                $("th#gsh_data_gps .ui-search-oper").remove();
                $("th#gsh_data_gps .ui-search-clear").remove();
            },
            beforeRequest: function () {
                if (flag){

                }
            }

        });
        // activate the toolbar searching
        $('#data').jqGrid('filterToolbar',{
            // JSON stringify all data from search, including search toolbar operators
            stringResult: true,
            // instuct the grid toolbar to show the search options
            searchOperators: true
        });
        $('#data').jqGrid('navGrid',"#pager", {
                search: false, // show search button on the toolbar
                add: false,
                edit: false,
                del: false,
                refresh: false,
                reloadGridOptions: { fromServer: true }
            }
        );
        $('#data').jqGrid("navButtonAdd", "#pager", {
            caption: "Clear Filters", // no text near the button
            title: "Clear filters in toolbar without reloading of data",
            buttonicon: "glyphicon-trash", // an example of icon
            onClickButton: function () {
                this.clearToolbar(true); // don't reload grid
            }
        });
        $('#data').jqGrid("navButtonAdd", "#pager", {
            caption: "Reload Page", // no text near the button
            title: "Clear filters in toolbar without reloading of data",
            buttonicon: "glyphicon glyphicon-refresh", // an example of icon
            onClickButton: function () {
                this.clearToolbar(true); // don't reload grid
            }
        });
        $('#data').jqGrid("navButtonAdd", "#pager", {
            caption: "Reload Data from Server", // no text near the button
            title: "Clear filters in toolbar without reloading of data",
            buttonicon: " glyphicon-repeat", // an example of icon
            onClickButton: function () {
                $("#data").setGridParam({datatype: 'json'});
                $("#data").trigger("reloadGrid");
            }
        });
         $('#data').jqGrid("navButtonAdd", "#pager", {
            caption: "Autoreload", // no text near the button
            title: "Clear filters in toolbar without reloading of data",
            buttonicon: "glyphicon-time", // an example of icon
            id: "autoreloadbtn",
            onClickButton: function () {

                // Create the data-status to the div DOM child of the button
                if($("#autoreloadbtn > div").data("status")== null){
                    $("#autoreloadbtn > div").data("status",false) ;
                    $("#autoreloadbtn").toggleClass("btn-danger");
                }

                // TOGGLE AUTORELOAD
                if($("#autoreloadbtn > div").data("status")==false){
                    intervalId = setInterval(
                        function() {
                            $("#data").setGridParam({datatype: 'json'});
                            $("#data").trigger("reloadGrid");
                        },
                    5000); // 5 sec
                    $("#autoreloadbtn div").text("Autoreload ON");

                    $("#autoreloadbtn").toggleClass("btn-success");
                    $("#autoreloadbtn").toggleClass("btn-danger");

                    // Update state
                    $("#autoreloadbtn > div").data("status",true);
                }else{
                    clearInterval(intervalId);
                    $("#autoreloadbtn div").text("Autoreload OFF");

                    $("#autoreloadbtn").toggleClass("btn-success");
                    $("#autoreloadbtn").toggleClass("btn-danger");

                    // Update state
                    $("#autoreloadbtn > div").data("status",false) ;
                }
            }
        });
    });

    var intervalId;


</script>
</html>
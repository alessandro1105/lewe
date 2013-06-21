<?php
	use \Config\Config;
?>

<div id="leweChartContainer">

<style>


#ui-datepicker-div {
    font-family: "Trebuchet MS","Helvetica","Arial","Verdana","sans-serif";
    height: 18.5em;
    width: 11.35em;
}

</style>

<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />


<script>
  $(function() {
    $( "#dateFrom" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#dateTo" ).datepicker( "option", "minDate", selectedDate );
      }
    });
	
	$("#dateFrom").datepicker( "option", "dateFormat", 'dd/mm/yy' );
	
    $( "#dateTo" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#dateFrom" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>


	<?php
        if ($blErrorNoData) {
    
    ?>
    
    <p id="leweChartNoDataMessage" class="leweChartText">Non è stato salvato alcun valore</p>
    
    <?php
        } else {
            
            
    ?>
    
    <form id="leweChartForm" name="searchForm" action="/?<?php echo Config::_PAGES_FORM_NAME . "=" . $strPageName; ?>" method="post">
    
        <input type="hidden" name="<?php echo Config::_HTTP_REQUEST_LEWE_USER_ID; ?>" value="<?php echo $intUserId; ?>" />
    
        <p class="leweChartText">Da </p><input placeholder="gg/mm/aaaa" type="text" class="leweChartFormInput" id="dateFromold" name="<?php echo Config::_HTTP_REQUEST_LEWE_FROM_DATE; ?>" />
        <p class="leweChartText">A </p> <input placeholder="gg/mm/aaaa" type="text" class="leweChartFormInput" id="dateToold" name="<?php echo Config::_HTTP_REQUEST_LEWE_TO_DATE; ?>" />
        <input class="leweChartFormCheck"  type="checkbox" name="<?php echo Config::_HTTP_REQUEST_LEWE_SHOW_TEMP; ?>" <?php echo $blShowTemp ? "checked" : ""; ?>/><p class="leweChartText2"> Temperatura</p>
        <input class="leweChartFormCheck" type="checkbox" name="<?php echo Config::_HTTP_REQUEST_LEWE_SHOW_GSR; ?>" <?php echo $blShowGsr ? "checked" : ""; ?>/><p class="leweChartText2"> GSR</p>
        <div id="leweChartFormButtonContainer">
        	<a class="button" id="leweChartFormSubmit" onclick="document.forms['searchForm'].submit();">Ricerca</a>
        </div>
    </form>
    
    <br />
    <!-- -->
    
    <?php
            if ($blErrorNoResults) {
            
    ?>
    <p>Nessun risultato con i parametri di ricerca impostati</p>
    <?php
    
            } else {
    ?>
    
    <div id="container" ></div>
    
    <div id="leweChartTable">
    	<div id="leweChartTableRowTitles">
            <div class="leweChartTableTitle">Temperatura</div>
            <div class="leweChartTableTitle">GSR</div>
            <div class="leweChartTableTitle">Data</div>
        </div>
        <?php
                for($i = 0; $i < count($arTimestampValue); $i++) {
                
        ?>
        <div class="leweChartTabaleRow">
            <div class="leweChartTableData"><?php echo $blShowTemp ? $arTemperatureValue[$i] . " °C" : "-"; ?></div>
            <div class="leweChartTableData"><?php echo $blShowGsr ? $arGsrValue[$i] . " %" : "-"; ?></div>
            <div class="leweChartTableData"><?php echo $arTemperatureValueNotModified[$i]; ?></div>
        </div>
        <?php
                
                }
        ?>
        
        
    
    </div>
    
    <?php echo $objChart->getScriptTags(); ?>
    
    <script>
    
        $(function () {<?php echo $objChart->getJSChart("#container");; ?>});
    </script>
    
    </div>
    
    <?php
            }
        }
    ?><!-- -->
</div>
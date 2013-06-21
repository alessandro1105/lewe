<?php
	
	require_once("includes.php");
	
	use \Config\Config; // importa la classe config del namespace config
	
	use \Controller\Index;
	use \Controller\Index\Exception as IndexException;
	
	
	try {
	
		$objIndex = new Index(); //potrebbe generare eccezione
		$objIndex->execute();
		
	} catch (IndexException $e) {
	
		echo $e->getMessage();
	
	}
	
	/*use \Controller\Chart;
	
	
	$objChart = new Chart();
	
	$objChart->addChartTitle("titolo");
	
	$objChart->addChartSubTitle("sottotitolo");
	
	//echo "test";
	
	
	$arXLabels = array("1", "2", "3");
	
	$objChart->addXLabels($arXLabels);
	
	
	$objChart->addYAxis("test asse", " °C", "#AA4643", "true");
	$objChart->addYAxis("asse 2", " suff", "#BC4643", "false");
	
	
	$objChart->addTooltip();
	
	//echo "test2";
	
	$arValue = array(1, 2, 3);
	
	$objChart->addSerie($arValue, "test 1 serie", "#AA4643", 0, " C");
	
	
	$arValue = array(5, 6, 8);
	
	$objChart->addSerie($arValue, "test 2 serie", "#AA4643", 1, " C");
	
	
	
	$objChart->addLegend("vertical", "left", 200, 200, "true", "#FFFFFF");
	
	//echo "test3";
	
	echo $objChart->getJSChart("#container");
	?>
</body>*/
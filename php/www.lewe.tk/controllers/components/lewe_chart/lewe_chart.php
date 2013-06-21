<?php
	
	namespace Controller\Components\LeweChart;
	
	use \Config\Config;
	
	use \Controller\Request;
	
	use \Model\Database;
	use \Model\Database\Exception as DatabaseException;
	
	use \Controller\Chart;
	
	class Controller {
		
		private $objRequest;
		
		public function __construct() {
			$this->objRequest = Request::getRequest();
			
		}
		
		
		public function execute($intUserId, $strPageName, $strDateFrom = NULL, $strDateTo = NULL, $blShowTemp = true, $blShowGsr = true) {
			
			
			$objDatabase = new Database();	
			
			$tsDateFrom = 0;
			$tsDateTo = 0;	
			
			$arTemperatureValue = array();
			
			$arTemperatureValueNotModified = array();
			
			$arGsrValue = array();
			$arTimestampValue = array();
			
			
			if (!$blShowTemp and !$blShowGsr) { //non includo nessun sensore indico che non ho trovato niente
			
				$blErrorNoResults = true;
				$blErrorNoData = false;
				
			} else {
				
				
				//VERIFICO SE CI SONO VALORI INSERITI NEL DB PER L'UTENTE
				
				//query per contare i record
				$queryCount = "SELECT COUNT(*) AS numberOfRecord FROM " . Config::_DB_TABLE_SURVEYS;
				$queryCount .= " WHERE " . Config::_DB_TABLE_SURVEYS_SENSOR_NAME . "=:name";
				$queryCount .= " AND " . Config::_DB_TABLE_SURVEYS_USER_ID . "=:user_id";
				
				$objDatabase->prepareQuery($queryCount);
					
				$objDatabase->bindParamString(":name", Config::_JACK_MESSAGE_FILED_GSR); //uso gsr per contare i timestamp
				$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
					
				$objDatabase->executeQuery();
					
				if ($objDatabase->getFieldByName("numberOfRecord") == 0) { //numero record nel db
				
					$blErrorNoData = true;
					$blErrorNoResults = false;
				
					
				
				} else {
					
					
					$blErrorNoResults = false;
					$blErrorNoData = false;
					
					if ($strDateFrom == NULL and $strDateTo == NULL) {
					
						$querySelectTimestamp = "SELECT " . Config::_DB_TABLE_SURVEYS_TIMESTAMP . " FROM " . Config::_DB_TABLE_SURVEYS;
						$querySelectTimestamp .= " WHERE " . Config::_DB_TABLE_SURVEYS_USER_ID . "=:user_id";
						$querySelectTimestamp .= " ORDER BY " . Config::_DB_TABLE_SURVEYS_TIMESTAMP . " DESC";
						
						//echo $querySelectTimestamp;
							
						$objDatabase->prepareQuery($querySelectTimestamp);
						
						$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
						
						$objDatabase->executeQuery();
							
						$timestamp = $objDatabase->getFieldByName(Config::_DB_TABLE_SURVEYS_TIMESTAMP);
							
						//echo $timestamp;
							
						$timestamp = strtotime(date('Y-m-d', strtotime($timestamp)));
							
						$tsDateFrom = $timestamp; //mezzanotte ultimo dato
						$tsDateTo = $timestamp + 86399; //23:59 stesso giorno
							
					} else {
							
							
						//echo substr($strDateFrom, 0, 2) . "-" . substr($strDateFrom, 3, 2) . "-" . substr($strDateFrom, 6, strlen($strDateFrom));
						
						//echo "<br>";
						
						//echo strtotime;
						
						//echo "<br>";
							
						$tsDateFrom = strtotime(date('Y-m-d', strtotime(substr($strDateFrom, 0, 2) . "-" . substr($strDateFrom, 3, 2)  . "-" . substr($strDateFrom, 6, strlen($strDateFrom)))));
						$tsDateTo = strtotime(date('Y-m-d', strtotime(substr($strDateTo, 0, 2) . "-" . substr($strDateTo, 3, 2)  . "-" . substr($strDateTo, 6, strlen($strDateTo)))));
							
							
					}
					
					//prelevo i timestamp e conto contestualmente i dati
					
					
					//prelevo i timestamp
					
					//query per contare i record
					$queryCount = "SELECT COUNT(*) AS numberOfRecord FROM " . Config::_DB_TABLE_SURVEYS;
					$queryCount .= " WHERE " . Config::_DB_TABLE_SURVEYS_SENSOR_NAME . "=:name AND " . Config::_DB_TABLE_SURVEYS_TIMESTAMP;
					$queryCount .= " BETWEEN :date1 AND :date2";
					$queryCount .= " AND " . Config::_DB_TABLE_SURVEYS_USER_ID . "=:user_id";
					
					//query per selezionare i record
					$querySelect = "SELECT " . Config::_DB_TABLE_SURVEYS_TIMESTAMP . " FROM " . Config::_DB_TABLE_SURVEYS;
					$querySelect .= " WHERE " . Config::_DB_TABLE_SURVEYS_SENSOR_NAME . "=:name AND " . Config::_DB_TABLE_SURVEYS_TIMESTAMP;
					$querySelect .= " BETWEEN :date1 AND :date2";
					$querySelect .= " AND " . Config::_DB_TABLE_SURVEYS_USER_ID . "=:user_id";
					$querySelect .= " ORDER BY " . Config::_DB_TABLE_SURVEYS_TIMESTAMP . " ASC";
									
					
					$objDatabase->prepareQuery($queryCount);
						
					$objDatabase->bindParamString(":name", Config::_JACK_MESSAGE_FILED_GSR); //uso gsr per contare i timestamp
					$objDatabase->bindParamString(":date1", date('Y-m-d H:i:s', $tsDateFrom));
					$objDatabase->bindParamString(":date2", date('Y-m-d H:i:s', $tsDateTo));
					$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
						
					$objDatabase->executeQuery();
						
					$intN = $objDatabase->getFieldByName("numberOfRecord"); //numero record nel db
					
					
					if ($intN == 0) {
						$blErrorNoResults = true;
						$blErrorNoData = false;
						
					} else {
					
						
						$objDatabase->prepareQuery($querySelect);
							
						$objDatabase->bindParamString(":name", Config::_JACK_MESSAGE_FILED_GSR);
						$objDatabase->bindParamString(":date1", date('Y-m-d H:i:s', $tsDateFrom));
						$objDatabase->bindParamString(":date2", date('Y-m-d H:i:s', $tsDateTo));
						$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
							
						$objDatabase->executeQuery();
							
								
						for($i = 0; $i < $intN; $i++) { //scarico record in un array
							$arTimestampValue[] = $objDatabase->getFieldByName(Config::_DB_TABLE_SURVEYS_TIMESTAMP, $i);
						}
						
						
						$arTemperatureValueNotModified = $arTimestampValue;
						
						
						for($i = 0; $i < $intN; $i++) { //scarico record in un array
							
							$tsTimestamp = strtotime($arTimestampValue[$i]);
						
							$arTimestampValue[$i] = date("Y-m-d", $tsTimestamp);
							
							$arTimestampValue[$i] .= "<br />" . date("H:i", $tsTimestamp);
						}
						
						
						//query per contare i record
						$queryCount = "SELECT COUNT(*) AS numberOfRecord FROM " . Config::_DB_TABLE_SURVEYS;
						$queryCount .= " WHERE " . Config::_DB_TABLE_SURVEYS_SENSOR_NAME . "=:name AND " . Config::_DB_TABLE_SURVEYS_TIMESTAMP;
						$queryCount .= " BETWEEN :date1 AND :date2";
						$queryCount .= " AND " . Config::_DB_TABLE_SURVEYS_USER_ID . "=:user_id";
						
						//query per contare i record
						$querySelect = "SELECT " . Config::_DB_TABLE_SURVEYS_SENSOR_VALUE . " FROM " . Config::_DB_TABLE_SURVEYS;
						$querySelect .= " WHERE " . Config::_DB_TABLE_SURVEYS_SENSOR_NAME . "=:name AND " . Config::_DB_TABLE_SURVEYS_TIMESTAMP;
						$querySelect .= " BETWEEN :date1 AND :date2";
						$querySelect .= " AND " . Config::_DB_TABLE_SURVEYS_USER_ID . "=:user_id";
						$querySelect .= " ORDER BY " . Config::_DB_TABLE_SURVEYS_TIMESTAMP . " ASC";
						
						//echo $queryCount;
						
						
						if ($blShowTemp) { //valuto le temperature se da mostrare
						
							$objDatabase->prepareQuery($queryCount);
							
							$objDatabase->bindParamString(":name", Config::_JACK_MESSAGE_FILED_TEMPERATURE);
							$objDatabase->bindParamString(":date1", date('Y-m-d H:i:s', $tsDateFrom));
							$objDatabase->bindParamString(":date2", date('Y-m-d H:i:s', $tsDateTo));
							$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
							
							
							$objDatabase->executeQuery();
							
							$intN = $objDatabase->getFieldByName("numberOfRecord"); //numero record nel db
							
							
							//echo $intN;
							//echo "<br>";
							
							$objDatabase->prepareQuery($querySelect);
							
							$objDatabase->bindParamString(":name", Config::_JACK_MESSAGE_FILED_TEMPERATURE);
							$objDatabase->bindParamString(":date1", date('Y-m-d H:i:s', $tsDateFrom));
							$objDatabase->bindParamString(":date2", date('Y-m-d H:i:s', $tsDateTo));
							$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
							
							$objDatabase->executeQuery();
							
							
							
							for($i = 0; $i < $intN; $i++) { //scarico record in un array
								$arTemperatureValue[] = $objDatabase->getFieldByName(Config::_DB_TABLE_SURVEYS_SENSOR_VALUE, $i);
							}
						
						}
						
						if ($blShowGsr) { //valuto gsr se da mostrare
						
							$objDatabase->prepareQuery($queryCount);
							
							$objDatabase->bindParamString(":name", Config::_JACK_MESSAGE_FILED_GSR);
							$objDatabase->bindParamString(":date1", date('Y-m-d H:i:s', $tsDateFrom));
							$objDatabase->bindParamString(":date2", date('Y-m-d H:i:s', $tsDateTo));
							$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
							
							$objDatabase->executeQuery();
							
							$intN = $objDatabase->getFieldByName("numberOfRecord"); //numero record nel db
							
							
							$objDatabase->prepareQuery($querySelect);
							
							$objDatabase->bindParamString(":name", Config::_JACK_MESSAGE_FILED_GSR);
							$objDatabase->bindParamString(":date1", date('Y-m-d H:i:s', $tsDateFrom));
							$objDatabase->bindParamString(":date2", date('Y-m-d H:i:s', $tsDateTo));
							$objDatabase->bindParamInteger(":user_id", $intUserId); //id utente
							
							$objDatabase->executeQuery();
							
							
							
							for($i = 0; $i < $intN; $i++) { //scarico record in un array
								$arGsrValue[] = $objDatabase->getFieldByName(Config::_DB_TABLE_SURVEYS_SENSOR_VALUE, $i);
							}
						
						}
						
						
						
						
						//creo il grafico
						$objChart = new Chart();
						
						$objChart->addChartTitle("");
				
						$objChart->addChartSubTitle("Da " . date('d/m/Y H:i', $tsDateFrom) . " A " . date('d/m/Y H:i', $tsDateTo));
						
						
						//print_r($arTimestampValue);
						
						$objChart->addXLabels($arTimestampValue); //imposto asse x
						
						
						if ($blShowTemp) { //mostro temperatura
							$objChart->addYAxis("Temperatura", " °C", "#FF0000", "false");
							$objChart->addSerie($arTemperatureValue, "Temperatura", "#FF0000", 0, " °C");
						} 
						
						if (!$blShowTemp and $blShowGsr) {
							$objChart->addYAxis("GSR", " %", "#0000FF", "false");
							$objChart->addSerie($arGsrValue, "GSR", "#0000FF", 0, " %");
							
						} else if ($blShowGsr) {
							$objChart->addYAxis("GSR", " %", "#0000FF", "true");
							$objChart->addSerie($arGsrValue, "GSR", "#0000FF", 1, " %");
						}
						
						$objChart->addTooltip();
						
						$objChart->addLegend("vertical", "right", 0, 50, "false", "#FFFFFF");
						
						
						//$strChart = $objChart->getJSChart("#container"); //creo codice grafico
					
						//require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/components/lewe_chart/lewe_chart.php");
					
					}
					
				}
				
			}
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/components/lewe_chart/lewe_chart.php");
			
		}
			
	}
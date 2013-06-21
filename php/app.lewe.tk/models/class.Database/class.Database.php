<?php

	namespace Model;
	
	use \Config\Config;
	
	use \Model\PDOFactory;
	
	use \PDO;

	class Database {
		
		private $objPDO;
		
		private $objStatement;
		
		private $arStatement;
		
		private $strDbName = Config::_DB_NAME;
		private $strDbPort = Config::_DB_PORT;
		private $strDbHost = Config::_DB_HOST;
		private $strDbType = Config::_DB_TYPE;
		
		private $strDbUsername = Config::_DB_USERNAME;
		private $strDbPassword = Config::_DB_PASSWORD;
		
		
		public function __construct() {
			
			$strDSN = $this->strDbType . ":dbname=" . $this->strDbName . ";host=" . $this->strDbHost . ";port=" . $this->strDbPort;
			
			$this->objPDO = PDOFactory::getPDO($strDSN, $this->strDbUsername, $this->strDbPassword, array());
			
			//$this->objPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
							
		}
		
		/*funzione per preparare la query (viene distrutta la query precenedete se non eseguita*/
		public function prepareQuery($strQuery) {
			
			unset($this->objStatement);
			
			$this->objStatement = $this->objPDO->prepare($strQuery);
			
		}
		
		/*funzioni per bind parametri query*/
		public function bindParamString($strParam, $strValue) { //bind string
			
			$this->objStatement->bindParam($strParam, $strValue, PDO::PARAM_STR);
			
		}
		
		public function bindParamInteger($strParam, $intValue) { //bind integer
			
			$this->objStatement->bindParam($strParam, $intValue, PDO::PARAM_INT);
			
		}
		
		public function bindParamBoolean($strParam, $blValue) { //bind boolean
			
			$this->objStatement->bindParam($strParam, $blValue, PDO::PARAM_BOOL);
			
		}
		
		
		/*funzione per eseguire la query*/
		public function executeQuery() {
			
			//echo $this->objStatement->queryString;
			
			$result = $this->objStatement->execute();
			
			$this->arStatement = $this->objStatement->fetchAll(PDO::FETCH_ASSOC);
			
			return $result;
						
		}
		
		
		/*funzione per avere il risultato o funzioni da pensare!!!!!!!!!!!!!!!*/
		
		
		public function getFieldByName($strField, $intPosition = 0) {
			
			//unset($this->arStatement);
			
			
			
			//$this->arStatement = $this->objStatement->fetchAll(PDO::FETCH_ASSOC);
			/*
			echo "<br>";
			echo "DATABASE";
			echo "<br>";
			print_r($this->arStatement);
			echo "<br>";
			echo $strField;
			echo "<br>";/*
			echo $intPosition;
			echo "<br>";
			echo "DATABASE";
			echo "<br>";*/
			
			if (isset($this->arStatement[$intPosition][$strField])) {
				
				//echo "D:".$this->arStatement[$intPosition][$strField].":D:";
				
				return $this->arStatement[$intPosition][$strField];
				
			} else {
				//print_r($this->arStatement);
				
				throw new Database\Exception("errore db");
				
			}
			
		}
		
		
		
	}
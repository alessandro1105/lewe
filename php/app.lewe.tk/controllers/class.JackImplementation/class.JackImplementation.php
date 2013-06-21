<?php
	
	namespace Controller;
	
	use \Model\Jack\Jack;
	
	use \Model\Database;
	
	use \Config\Config;
	
	class JackImplementation extends Jack {
		
		private $intID = 0;
		
		
		//metodi astratti usati per program,mare le azioni da eseguire al ricevimento di un ack o di un dato
		protected function onReceive($objMessageJData){ //medoto da implementare e deve contere le istruzioni eseguire al ricevimento del messaggio
			
			//echo "ok";
			
			//codice che deve essere eseguito per salvare i dati nel db
			$objDatabase = new Database();
			
			//QUERY INSERT
			$strQueryInsert = "INSERT INTO " . Config::_DB_TABLE_SURVEYS . "(" .  
											   Config::_DB_TABLE_SURVEYS_SENSOR_NAME . "," .
											   Config::_DB_TABLE_SURVEYS_SENSOR_VALUE . "," .
											   Config::_DB_TABLE_SURVEYS_TIMESTAMP . "," .
											   Config::_DB_TABLE_SURVEYS_USER_ID . ")" .
							  " VALUES (:sensor_name,:sensor_value,:timestamp,:user_id)";			
			
			//QUERY SELECT COUNT(*)
			$strQuerySelect = "SELECT COUNT(*) AS numberOfRecord FROM " . Config::_DB_TABLE_SURVEYS .
							  " WHERE " . Config::_DB_TABLE_SURVEYS_SENSOR_NAME . "=:sensor_name" . " AND " .
							  			  Config::_DB_TABLE_SURVEYS_SENSOR_VALUE . "=:sensor_value" . " AND " . 
										  Config::_DB_TABLE_SURVEYS_TIMESTAMP . "=:timestamp" . " AND " .
										  Config::_DB_TABLE_SURVEYS_USER_ID . "=:user_id";
			
			//echo $strQueryInsert;
			
			//echo $strQuerySelect;
			
			//TEMPERATURE
			if ($objMessageJData->containsKey(Config::_JACK_MESSAGE_FILED_TEMPERATURE)) {
				
				$objDatabase->prepareQuery($strQuerySelect);
				
				$objDatabase->bindParamString(":sensor_name", Config::_JACK_MESSAGE_FILED_TEMPERATURE); // bindo il nome sensore
				$objDatabase->bindParamString(":sensor_value", $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_TEMPERATURE)); //valore sensore in stringa
				$objDatabase->bindParamInteger(":timestamp",  date('Y-m-d H:i:s', $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_TIMESTAMP))); //timestamp
				$objDatabase->bindParamInteger(":user_id", $this->intID); //id utente proprietario del valore
				
				$objDatabase->executeQuery(); //eseguo la query
				
				
				//echo $objDatabase->getFieldByName("numberOfRecord");
				
				if ($objDatabase->getFieldByName("numberOfRecord") == 0) {
					
					$objDatabase->prepareQuery($strQueryInsert); //preparo la query
				
					$objDatabase->bindParamString(":sensor_name", Config::_JACK_MESSAGE_FILED_TEMPERATURE); // bindo il nome sensore
					$objDatabase->bindParamString(":sensor_value", $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_TEMPERATURE)); //valore sensore in stringa
					$objDatabase->bindParamInteger(":timestamp",  date('Y-m-d H:i:s', $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_TIMESTAMP))); //timestamp
					$objDatabase->bindParamInteger(":user_id", $this->intID); //id utente proprietario del valore
					
					$objDatabase->executeQuery();
				
				}
				
			}
			
			
			
			//GSR
			if ($objMessageJData->containsKey(Config::_JACK_MESSAGE_FILED_GSR)) {
				
				$objDatabase->prepareQuery($strQuerySelect);
				
				$objDatabase->bindParamString(":sensor_name", Config::_JACK_MESSAGE_FILED_GSR); // bindo il nome sensore
				$objDatabase->bindParamString(":sensor_value", $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_GSR)); //valore sensore in stringa
				$objDatabase->bindParamInteger(":timestamp",  date('Y-m-d H:i:s', $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_TIMESTAMP))); //timestamp
				$objDatabase->bindParamInteger(":user_id", $this->intID); //id utente proprietario del valore
				
				$objDatabase->executeQuery(); //eseguo la query
				
				
				if ($objDatabase->getFieldByName("numberOfRecord") == 0) {
				
					$objDatabase->prepareQuery($strQueryInsert); //preparo la query
					
					$objDatabase->bindParamString(":sensor_name", Config::_JACK_MESSAGE_FILED_GSR); // bindo il nome sensore
					$objDatabase->bindParamString(":sensor_value", $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_GSR)); //valore sensore in stringa
					$objDatabase->bindParamInteger(":timestamp",  date('Y-m-d H:i:s', $objMessageJData->getValue(Config::_JACK_MESSAGE_FILED_TIMESTAMP))); //timestamp
					$objDatabase->bindParamInteger(":user_id", $this->intID); //id utente proprietario del valore
					
					$objDatabase->executeQuery();
				}
				
			}
		
		}
		
		protected function onReceiveAck($objMessageJData){ //metodo invocato al ricev1imento di un ack (contiene messaggio inviato da cui prelevare i dati per
			
			//NON RICEVO ACK
			
		}
		
		protected function getTimestamp(){ //funzione da implementare che ritorna il timestamp (usato coe id per il messaggio da inviare)
		
			return time(); //ritorno il timestamp
		
		}
		
		
		public function setUserID($intID) {
			
			$this->intID = $intID;
			
		}
		
		
	}
<?php
	
	namespace Model;
	
	use \Config\Config;

	class Field {
		
		private $value; //valore del campo
		
		private $onLoad;
		private $onModified;
		
		
		private $objDatabase; //oggetto connessione al db
		private $strTable;	//tabella
		private $strField;	//campo
		private $intId;	//id per ricerche di un campo specifico di un record in una tabella
		
		
		public function __construct($objDatabase) {
			
			$this->objDatabase = $objDatabase;
			
			$this->clear();
			
		}
		
		private function clear() {
			
			$this->onLoad = false;
			$this->onModified = false;
			
		}
		
		/*public function selectField($strTable, $strField, $intId) {
			
			$this->strTable = $strTable;
			$this->strField = $strField;
			$this->intId = $intId;
			
			$this->onLoad = false;
			$this->onModified = false;
			
		}*/
		
		
		private function load() {
		
			$querySQL = "SELECT " . $this->strField . " FROM " . $this->strTable . " WHERE id = :id";
			
			$this->objDatabase->prepareQuery($querySQL);
			
			$this->objDatabase->bindParamInteger(":id", $this->intId);
			
			
			$this->objDatabase->executeQuery();
			
			$this->value = $this->objDatabase->getFieldByName($this->strField);
			
			$this->onLoad = true;
			
		}
		
		
		private function save() {
			
			$result;
			
			$querySQL = "UPDATE " . $this->strTable . " SET " . $this->strField . " = :value WHERE id = :id";
			
			$this->objDatabase->prepareQuery($querySQL);
			
			
			$this->objDatabase->bindParamInteger(":id", $this->intId);
			
			$this->objDatabase->bindParamString(":value", $this->value);
			
			$result = $this->objDatabase->executeQuery();
			
			$this->onModified = false;
			
			
			return $result;
			
		}
		
		
		public function update() {
			
			if ($this->onModified) {
				
				$this->save();
				
			}
			
		}
		
		
		
		/*get and set function*/
		
		public function __get($strPropertyName) {

			$strFunctionName =  'get' . $strPropertyName;

			

			if (method_exists($this, $strFunctionName)) {

				return call_user_func(array($this, $strFunctionName));

			} else {

				//throw new UserException(UserException::_PROPERTY_NAME_INESISTENT);

			}
			

		}
		
		
		private function getValue() {
			
			if (! $this->onLoad) {
				$this->load();
				
			}
			
			//echo "F:" . $this->value.":F:";
			return $this->value;
		
		}
		
		
		private function getTable() {
			
			return $this->strTable;
		
		}
		
		private function getField() {
			
			return $this->strField;
			
		}
		
		private function getId() {
			
			return $this->intId;
			
		}
		
		
		
		public function __set($strPropertyName, $value) {

			$strFunctionName =  'set' . $strPropertyName;

			

			if (method_exists($this, $strFunctionName)) {

				return call_user_func(array($this, $strFunctionName), $value);

			} else {

				//throw new AskException(AnswerException::_PROPERTY_NAME_INESISTENT);

			}

		}
		
		
		private function setValue($value) {
			
			$this->value = $value;
			
			$this->onLoad = true;
			$this->onModified = true;
			
		}
		
		
		private function setTable($strTable) {
			
			$this->strTable = $strTable;
			
			$this->clear();
			
		}
		
		private function setField($strField) {
			
			$this->strField = $strField;
			
			$this->clear();
			
		}
		
		private function setId($intId) {
			
			$this->intId = $intId;
			
			$this->clear();
			
		}
		
	}
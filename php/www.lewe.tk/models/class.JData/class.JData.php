<?php
	
	namespace Model\Jack;
	
	use \Model\Jack\JData\Exception as JDataException;
	
	use \Model\HashMap;
	
	class JData {
		
		private $size = 0; //n elenti 
		
		private $objData; //hashmap dati
		
		private $objIndex; //hashmap indici
		
		
		public function __construct() { //costruttore
			$this->objData = new HashMap();
			$this->objIndex = new HashMap();
		}
		
		public function add($strKey, $objValue) { //add elemento
			$this->objData->put($strKey, $objValue);
			
			$this->objIndex->put($this->size, $strKey); 	
			
			$this->size++;
		}
		
		private function getValueString($strKey) { //get elento da chiave
			return $this->objData->getValue($strKey);	
		}
		
		private function getValueInteger($intKey) { //get elemnto tramite indice
			
			return $this->objData->getValue($this->objIndex->getValue($intKey));			
		}
		
		public function getValue($objKey) { //switcher per overload getValue
			
			if (is_int($objKey)) {
				return $this->getValueInteger($objKey);
			} else {
				return $this->getValueString($objKey);
			}
				
		}
		
		public function getKey($intKey) { //ritorna chiave
			return $this->objIndex->getValue($intKey);	
		}
		
		public function length() { //restituisce la lunghezza
			
			return $this->size;
		}
		
		public function containsKey($strKey) { //verifica lòa presenza di una determinata chiave
			return $this->objData->containsKey($strKey);
		}

	}
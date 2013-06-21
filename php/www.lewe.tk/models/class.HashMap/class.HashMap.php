<?php
	
	namespace Model;
	
	use \Model\HashMap\Exception as HashMapException;
	
	class HashMap { //implementazione classe hashmap php (utile per convertire velocemente sorgenti da c++ o java
		
		private $arHashMap;
		
		private function removeElement($objKey) { //rimuovo un elemento dall'array identificato dall'oggetto chiave
			
			unset($this->arHashMap[$objKey]);
			
		}
		
		private function removeElementIterable() { //funzione che rimuove un elemnto (chiamato con l'interfaccia iterable)
			
			$objKey = $this->getKey();
			
			$this->moveToPrev();
			
			$this->removeElement($objKey);
			
		}
		
		public function remove($objKey = NULL) { //funzione switch per implementare overload funzioni
			if ($objKey != NULL) {
				$this->removeElement($objKey);
			} else {
				$this->removeElementIterable();	
			}
		}
		
		private function getValueIterable() { //restituisce il valore dell' elemento puntato dal puntatore array
			
			return current($this->arHashMap);
			
		}
		
		private function getValueElement($objKey) { //preleva il valore
			
			return $this->arHashMap[$objKey];
			
		}
		
		public function getValue($objKey = NULL) { //funzione switch per implementare overload funzioni
			if ($objKey != NULL) {
				return $this->getValueElement($objKey);
			} else {
				return $this->getValueIterable();	
			}
		}
				
		public function __construct() { //costruttore
			
			$this->arHashMap = array();
				
		}
		
		
				
		public function put($objKey, $objValue) { //put
		
			$this->arHashMap[$objKey] = $objValue;
			
		}
		
		
		public function length() { //restituiscer la lunghezza dell'array
			
			return count($this->arHashMap);
		}
		
		public function containsKey($objKey) {
			
			if (isset($this->arHashMap[$objKey])) {
				return true;	
			} else {
				return false;
			}
			
		}
		
		
		//implementazione interfaccia iterable true ok false non ci sono più elemnti
		
		public function moveToFirst() { //reset del puntatore dell'array
			
			if (reset($this->arHashMap) != NULL) {
				return true;	
			} else {
				return false;	
			}
		}
		
		public function moveToLast() { //reset ultimo elemento puntatore array
			
			if (end($this->arHashMap) != NULL) {
				return true;	
			} else {
				return false;	
			}
		}
		
		public function moveToNext() { //sposta in avanti il puntatore
			
			if (next($this->arHashMap) != NULL) {
				return true;	
			} else {
				return false;	
			}			
		}
		
		public function moveToPrev() { //sposta indietro il puntatore
			
			if (prev($this->arHashMap) != NULL) {
				return true;	
			} else {
				return false;	
			}
		}
		
		
		public function getKey() { //chiave elemento array puntato dal puntatore
		
			return key($this->arHashMap);
			
		}
		
	}
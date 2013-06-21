<?php
	
	namespace Controller;
	
	use \Model\Jack\JTrasmissionMethod;
	
	use \Config\Config;
	
	use \Model\Logger;
	
	class JTMHTTP implements JTrasmissionMethod {
		
		private $objRequest;
		
		private $intMessages = 0;
		private $intCurrentMessage = 0;
		
		private $strBufferToSend = "";
		private $intMessagesToSend = 0;
		
		public function __construct($objRequest) {
			
			$this->objRequest = $objRequest;
			
		}
		
		public function start() {
			
			if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_NUMBER_OF_MESSAGES])) {
				$this->intMessages = $this->objRequest->_POST[Config::_HTTP_REQUEST_NUMBER_OF_MESSAGES];
			}
			
			Logger::log("N_MESSAGE: " . $this->intMessages);
				
		}
		
		public function stop() {
			
			if ($this->intMessagesToSend > 0) {
				echo Config::_HTTP_REQUEST_NUMBER_OF_MESSAGES . "=" . $this->intMessagesToSend . ";";
				echo $this->strBufferToSend;
			}
			
			Logger::log("N MESSAGE RETURNED: " . $this->intMessagesToSend);
			
			Logger::log("MESSAGE_RETURNED: " . $this->strBufferToSend);
		}
		
		public function send($message){ //metodo usato per inviare messaggi
		
			$this->intMessagesToSend++; //incremento il numero dei messaggi da inviare
			
			$this->strBufferToSend .= Config::_HTTP_REQUEST_MESSAGE_CONTRAINER_PATTERN . $this->intMessagesToSend . "=" . $message . ";";
			
			Logger::log("MESSAGE_TO_SEND: " . Config::_HTTP_REQUEST_MESSAGE_CONTRAINER_PATTERN . $this->intMessagesToSend . "=" . $message . ";");
		
		}
		
		public function receive(){ //funzione che restituisce il messaggio in stringa
		
			Logger::log("MESSAGE_" . $this->intCurrentMessage . ": " . $this->objRequest->_POST[Config::_HTTP_REQUEST_MESSAGE_CONTRAINER_PATTERN . $this->intCurrentMessage]);
		
			return $this->objRequest->_POST[Config::_HTTP_REQUEST_MESSAGE_CONTRAINER_PATTERN . $this->intCurrentMessage];
		
		}
		
		public function available(){ //funzione che indica se sono disponibili messaggi da scaricare
		
			$this->intCurrentMessage++;
			
			if ($this->intCurrentMessage <= $this->intMessages) {
				return true;
			} else {
				return false;	
			}
		
		}
		
	}
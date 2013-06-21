<?php

	namespace Controller;
	
	use \Config\Config;

	class Request {
		
		
		private $arGet = array();
		
		private $arPost = array();
		
		private $arCookie = array();
		
		private $arServer = array();
		
		private $arSession = array();	
		
		
		
		
		public function getRequest() {
			
			static $objRequest = NULL;
			
			if ($objRequest == NULL) {
				
				$objRequest = new Request();
					
			}
			
			return $objRequest;
			
		}
		
		
		private function __construct() {		
			
			global $_GET;
			global $_POST;
			global $_COOKIE;
			global $_SERVER;
			global $_SESSION;
			
			$this->arGet = $_GET;
			$this->arPost = $_POST;
			$this->arCookie = $_COOKIE;
			$this->arServer = $_SERVER;
			$this->arSession = $_SESSION;
			
			
		}
			
			
		public function unsetParam($var, $property) {
		
			switch ($var) {
			
				case Config::_REQUEST_VAR_GET: 
				
					if (isset($this->arGet[$property])) {
						unset($this->arGet[$property]);
					}
					
					break;
					
				
				case Config::_REQUEST_VAR_POST: 
					
					if (isset($this->arPost[$property])) {
						unset($this->arPost[$property]);
					}
					
					break;
					
					
				case Config::_REQUEST_VAR_COOKIE: 
					
					if (isset($this->arCookie[$property])) {
						unset($this->arCookie[$property]);
					}
					
					break;
					
					
				case Config::_REQUEST_VAR_SERVER: 
					
					if (isset($this->arServer[$property])) {
						unset($this->arServer[$property]);
					}
					
					break;
					
					
				case Config::_REQUEST_VAR_SESSION: 
					
					if (isset($this->arSession[$property])) {
						unset($this->arSession[$property]);
					}
					
					break;
					
					
				default: //possibile eccezzione mancanza variabile
					break;
					
			}
		
			
			
		}
		
		public function setParam($var, $property, $value) {
		
			switch ($var) {
			
				case Config::_REQUEST_VAR_GET: 
				
					$this->arGet[$property] = $value;
					
					break;
					
				
				case Config::_REQUEST_VAR_POST: 
					
					$this->arPost[$property] = $value;
					
					break;
					
					
				case Config::_REQUEST_VAR_COOKIE: 
					
					$this->arCookie[$property] = $value;
					
					break;
					
					
				case Config::_REQUEST_VAR_SERVER: 
					
					$this->arServer[$property] = $value;
					
					break;
					
					
				case Config::_REQUEST_VAR_SESSION: 
					
					$this->arSession[$property] = $value;
					
					break;
					
					
				default: //possibile eccezzione mancanza variabile
					break;
					
			}
		
			
			
		}
		
			
		public function __get($strPropertyName) {

			$strFunctionName =  'get' . $strPropertyName;

			

			if (method_exists($this, $strFunctionName)) {

				return call_user_func(array($this, $strFunctionName));

			} else {

				//throw new UserException(UserException::_PROPERTY_NAME_INESISTENT);

			}

		}
		
		
		private function get_GET() {
			
			return $this->arGet;
		}		
		
		private function get_POST() {
			
			return $this->arPost;
		}
			
		private function get_COOKIE() {
			
			return $this->arCookie;
		}
		
		private function get_SERVER() {
			
			return $this->arServer;
		}
		
		private function get_SESSION() {
			
			return $this->arSession;
		}
			
	}
	
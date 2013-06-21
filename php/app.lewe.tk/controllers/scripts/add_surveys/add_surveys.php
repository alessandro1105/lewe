<?php

	namespace Controller\Scripts\AddSurveys;
	
	use \Config\Config;
	
	use \Controller\User;
	use \Controller\User\Exception as UserException;
	
	use \Controller\JackImplementation as Jack;
	use \Controller\JTMHTTP as JTM;
	
	use \Controller\Request;
	
	class Controller {
		
		private $objRequest;
		
		public function __construct() {
			$this->objRequest = Request::getRequest();
			
		}
		
		public function execute() {
			
			if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL]) and isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD])
				and isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_TYPE])) {
				
				$strEmail = $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL];
				$strPassword = $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD];
				$strType = $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_TYPE];
				
				try {
					
					$objUser = User::loginOnce($strType, $strEmail, $strPassword);
					
				} catch (UserException $e) { //user non loggato
					return;	
				}
				
				$objJTM = new JTM($this->objRequest); //creo JTM
				
				$objJack = new Jack($objJTM, false); //creo jack con modalità SEND_ONE_TIME
				$objJack->setUserID($objUser->Id); //imposto id utente
				
				$objJTM->start(); //faccio partire JTM
				
				$objJack->start(); //faccio partire Jack
				
				$objJack->stop(); //stoppo Jack;
				
				$objJTM->stop(); //stoppo JTM;
						
			} else {
				header("Location: http://www.lewe.tk");	
			}
			
		}
			
	}
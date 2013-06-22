<?php
	
	namespace Controller\Pages\ProtectedSiteMyLewe;
	
	use \Config\Config;
	
	use \Controller\Request;
	
	use \Controller\User;
	use \Controller\User\Exception as UserException;
	
	use \Controller\Components\LeweChart\Controller as LeweChart;
	
	
	class Controller {
		
		private $objRequest;
		
		public function __construct() {
			$this->objRequest = Request::getRequest();
			
		}
		
		public function execute() {
			
			$objUser; //continene l'utente se loggato
			
			try {
				
				$objUser = User::login(); //controllo se l'utente è loggato				
				
			} catch (UserException $e) {
				
				header("Location: " . Config::_PAGE_SITE_INDEX); //se non loggato reindirizzo a home
					
			}
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_header/header.php"); //header
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_menu/menu.php"); //menu
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_content_my_lewe/my_lewe_header.php"); //main
			
			//scarico i dati dalla richiesta se ci sono
			
			$strDateFrom = NULL;
			$strDateTo = NULL;
			$blShowTemp = false;
			$blShowGsr = false;
			
			
			if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_FROM_DATE])) { //date from
				$strDateFrom = $this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_FROM_DATE];
			}
			
			if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_TO_DATE])) { //date to
				$strDateTo = $this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_TO_DATE];
			}
			
			if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_SHOW_TEMP])) { //show temp
				$blShowTemp = true;
			}
			
			if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_SHOW_GSR])) { //show gsr
				$blShowGsr = true;
			}
			
			//richiesta senza ricerca imposto i valori di default
			if (!isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_FROM_DATE]) and 
				!isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_TO_DATE]) and
				!isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_SHOW_TEMP]) and
				!isset($this->objRequest->_POST[Config::_HTTP_REQUEST_LEWE_SHOW_GSR])) { //show gsr
				
				$strDateFrom = NULL;
				$strDateTo = NULL;
				$blShowTemp = true;
				$blShowGsr = true;
			}
			
			
			$objLeweChart = new LeweChart();
			
			$objLeweChart->execute($objUser->Id, Config::_PAGE_SITE_MY_LEWE, $strDateFrom, $strDateTo, $blShowTemp, $blShowGsr);
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_content_my_lewe/my_lewe_footer.php"); //main
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_footer/footer.php"); //footer
				
		}
			
	}
<?php
	
	namespace Controller\Pages\ProtectedSiteHome;
	
	use \Config\Config;
	
	use \Controller\Request;
	
	use \Controller\User;
	use \Controller\User\Exception as UserException;
	
	
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
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_content_home/home.php"); //main
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_footer/footer.php"); //footer
				
		}
			
	}
<?php
	
	namespace Controller\Pages\PresentationSiteCode;
	
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
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_header/header.php"); //header
			
			
			try {
				
				$objUser = User::login(); //controllo se l'utente è loggato		
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_menu/menu.php"); //menu		
				
			} catch (UserException $e) {
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_menu/menu.php"); //menu
					
			}
			
				
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_content_code/code.php"); //main
				
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_footer/footer.php"); //footer
				
			
		}
			
	}
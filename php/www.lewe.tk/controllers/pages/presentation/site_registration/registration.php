<?php
	
	namespace Controller\Pages\PresentationSiteRegistration;
	
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
			
			$blErrorMessage = false;
			$strErrorMessage = "";
		
			try { //verifico che l'utente sia già loggato
				
				User::login(); //vedo se l'utente è loggato se no eccezione
				
				header("Location: /?" . Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOME); //reindirizzo a home (pagina protetta)
				
			} catch (UserException $e) { //non loggato (carico homepage sito presentazione)
			
			
				if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL]) and
					isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL_CONFIRM]) and
					isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_USERNAME]) and
					isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD]) and
					isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD_CONFIRM])) {
						
						
					
						
					try {
						
						User::registration($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL],
										   $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL_CONFIRM],
										   $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_USERNAME],
										   $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD],
										   $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD_CONFIRM]);
						
						//registrazione andata a buon fine reindirizzamento a home
						header("Location: /?" . Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOME); //reindirizzo a home (pagina protetta)
						
					} catch (UserException $e) {
						
						$blErrorMessage = true;
						$strErrorMessage = $e->getMessage();						
						
					}
				
				}
				
		
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_header/header.php"); //header
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_menu/menu.php"); //menu
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_content_registration/registration.php"); //main
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_footer/footer.php"); //footer
				
			}
		}
			
	}
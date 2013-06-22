<?php
	
	namespace Controller\Pages\PresentationSiteLogin;
	
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
			
			$blCookie = false;
			$strEmail;
			$strPassword;
			
			$blErrorLogin = false;
			
			try { //verifico che l'utente sia già loggato
				
				User::login(); //vedo se l'utente è loggato se no eccezione
				
				header("Location: /?" . Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOME); //reindirizzo a home (pagina protetta)
				
			} catch (UserException $e) { //non loggato
				
				//echo $e->getMessage();
				
				if (($e->getMessage() == Config::_USER_EXCEPTION_USER_NOT_LOGGED) and isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL])
					and isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD])) { //verifco se l'errore è non loggato e siano stati inviati 
																								 //i campi per il login	
																						   
						$strEmail = $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_EMAIL];
						$strPassword = $this->objRequest->_POST[Config::_HTTP_REQUEST_USER_PASSWORD];
						
					if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_USER_COOKIE])) {	//controllo se salvare la sessione a lungo termine nei cookie
						$blCookie = true;
					}
					
					try {
												
						User::login(Config::_USER_TYPE_NORMAL, $strEmail, $strPassword, $blCookie); //tento login utente
						
						header("Location: /?" . Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOME); //reindirizzo a home (pagina protetta)
						
					} catch (UserException $e) { //dati login errati carico login con errori	
						
						$blErrorLogin = true;
						
					}
				
				}
				
			}
			
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_header/header.php"); //header
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_menu/menu.php"); //menu
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_content_login/login.php"); //main
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_footer/footer.php"); //footer
			
		}
			
	}
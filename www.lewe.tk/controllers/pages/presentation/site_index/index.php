<?php
	
	namespace Controller\Pages\PresentationSiteIndex;
	
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
		
			try { //verifico che l'utente sia già loggato
				
				User::login(); //vedo se l'utente è loggato se no eccezione
				
				header("Location: /?" . Config::_PAGES_FORM_NAME . "=" . Config::_PAGE_SITE_HOME); //reindirizzo a home (pagina protetta)
				
			} catch (UserException $e) { //non loggato (carico homepage sito presentazione)
		
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_header/header.php"); //header
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_menu/menu.php"); //menu
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_content_index/index.php"); //main
				
				require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/presentation/site_footer/footer.php"); //footer
				
			}
		}
			
	}
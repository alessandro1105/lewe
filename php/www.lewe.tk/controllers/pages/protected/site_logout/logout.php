<?php
	
	namespace Controller\Pages\ProtectedSiteLogout;
	
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
		
			User::logout();
		
			header("Location: /"); //dopo logout reindirizzo a homepage sito presentazione
				
		}
			
	}
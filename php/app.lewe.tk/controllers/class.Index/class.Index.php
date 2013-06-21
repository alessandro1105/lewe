<?php

	namespace Controller;
	
	use \Controller\Index\Exception as IndexException;
	
	
	use \Config\Config;
	
	use \Controller\Request;
	
	use \Model\Database;
	use \Model\Database\Exception as DatabaseException;

	
	
	class Index {
		
		private $objDatabase;
				
		private $objRequest;
		
		
		private $strController;
		
		
		public function __construct($strPage = NULL) {
			
			$strQuery;
			$strOwner = NULL;
			
			
			$this->objRequest = Request::getRequest();
			
			if ($strPage == NULL) {
				
				switch (Config::_PAGES_FORM_METHOD) {
					
					case Config::_FORM_METHOD_POST:
						if (isset($this->objRequest->_POST[Config::_PAGES_FORM_NAME])) {
							$strPage = $this->objRequest->_POST[Config::_PAGES_FORM_NAME];
						} else {
				
							$strPage = Config::_PAGE_DEFAULT_PAGE_NAME;
						}
						break;
						
					case Config::_FORM_METHOD_GET:
						if (isset($this->objRequest->_GET[Config::_PAGES_FORM_NAME])) {
							$strPage = $this->objRequest->_GET[Config::_PAGES_FORM_NAME];
						} else {
				
							$strPage = Config::_PAGE_DEFAULT_PAGE_NAME;
						}
						break;
				}
			} 
			
			
			$this->objDatabase = new Database();
			
			$strQuery = "SELECT " . Config::_DB_TABLE_PAGES_OWNER . " FROM " . Config::_DB_TABLE_PAGES;
			$strQuery .= " WHERE " . Config::_DB_TABLE_PAGES_NAME . "=:name";
			
			$this->objDatabase->prepareQuery($strQuery);
			
			$this->objDatabase->bindParamString(":name", $strPage);
			
			$this->objDatabase->executeQuery();
			
			
			try {
				
				$strOwner = $this->objDatabase->getFieldByName(Config::_DB_TABLE_PAGES_OWNER);
								
			} catch (DatabaseException $e) {
								
				$strPage = Config::_PAGE_DEFAULT_PAGE_NAME;
				
			}
			
			switch ($strOwner) {
				
				case Config::_CONFIG_TYPE:					
				case Config::_REMOTE_SCRIPT_OWNER;
				
					break;
					
				default: //solo se l'owner è il contrario di quello di _COPNFIG_TYPE
				
					$strPage = Config::_PAGE_DEFAULT_PAGE_NAME;  //carico pagina default
					$strOwner = Config::_CONFIG_TYPE;	// cambio oner della pagina di default
					
					break;
					
			}
			
			
			$strQuery = "SELECT " . Config::_DB_TABLE_PAGES_CONTROLLER . " FROM " . Config::_DB_TABLE_PAGES;
			$strQuery .= " WHERE " . Config::_DB_TABLE_PAGES_OWNER . "=:owner AND " . Config::_DB_TABLE_PAGES_NAME . "=:name";
			
			$this->objDatabase->prepareQuery($strQuery);
			
			$this->objDatabase->bindParamString(":owner", $strOwner);
			$this->objDatabase->bindParamString(":name", $strPage);
			
			$this->objDatabase->executeQuery();
			
			
			try {
				
				$this->strController = $this->objDatabase->getFieldByName(Config::_DB_TABLE_PAGES_CONTROLLER);
				
				//echo $this->objDatabase->getFieldByName(Config::_DB_TABLE_PAGES_CONTROLLER);
				
				
			} catch (DatabaseException $e) {
								
				throw new IndexException(Config::_INDEX_EXCEPTION_DEFAULT_PAGE_NOT_FOUND);
				
			}
			
			
		}
		
		
		public function execute() {
			
			//echo $this->strController;
			
			$ControllerClass = $this->strController . "\Controller";
			
			$objController = new $ControllerClass($this->objRequest);
			
			$objController->execute();
			
		}
		
	}
<?php
	
	namespace Controller\Pages\ProtectedSiteFriendLewe;
	
	use \Config\Config;
	
	use \Controller\Request;
	
	use \Controller\User;
	use \Controller\User\Exception as UserException;
	
	use \Model\Database;
	
	
	class Controller {
		
		private $objRequest;
		
		public function __construct() {
			$this->objRequest = Request::getRequest();
			
		}
		
		public function execute() {
			
			$objDatabase = new Database();
			
			$objUser; //continene l'utente se loggato
			
			$arUserAllowedId = array();//array per gli utenti autorizzati (ID)
			$arUserAllowedUsername = array();//array per gli utenti autorizzati
			
			try {
				
				$objUser = User::login(); //controllo se l'utente è loggato				
				
			} catch (UserException $e) {
				
				header("Location: " . Config::_PAGE_SITE_INDEX); //se non loggato reindirizzo a home
					
			}
			
			
			
			$queryCount = "SELECT COUNT(*) AS numberOfRecord FROM ";
			$queryCount .= Config::_DB_TABLE_AUTHORIZATION . " WHERE " . Config::_DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID . "=:user_allowed_id";
			
			//echo $queryCount;
			
			$objDatabase->prepareQuery($queryCount);
			
			$objDatabase->bindParamInteger(":user_allowed_id", $objUser->Id);
			
			$objDatabase->executeQuery();
			
			$intN = $objDatabase->getFieldByName("numberOfRecord");
			
			//echo $intN;
			
			if ($intN > 0) { //se ci sono utenti abilitati li prelevo dal db
			
				
				
				$querySelect = "SELECT " . Config::_DB_TABLE_AUTHORIZATION_USER_ID . " FROM ";
				$querySelect .= Config::_DB_TABLE_AUTHORIZATION . " WHERE " . Config::_DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID . "=:user_allowed_id";
				
				$objDatabase->prepareQuery($querySelect);
				
				$objDatabase->bindParamInteger(":user_allowed_id", $objUser->Id);
				
				$objDatabase->executeQuery();
				
				for ($i = 0; $i < $intN; $i++) {
					
					try {
							
						$objUserAllowed = User::loginById($objDatabase->getFieldByName(Config::_DB_TABLE_AUTHORIZATION_USER_ID, $i));
							
					} catch (UserException $e) {
						
						continue; //username errato
								
					}
					
					$arUserAllowedUsername[] = $objUserAllowed->Username;
					$arUserAllowedId[] = $objUserAllowed->Id;
						
				}
				
			}
			
			$a = 1;
			
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_header/header.php"); //header
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_menu/menu.php"); //menu
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_content_friend_lewe/friend_lewe.php"); //main
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_footer/footer.php"); //footer
				
		}
			
	}
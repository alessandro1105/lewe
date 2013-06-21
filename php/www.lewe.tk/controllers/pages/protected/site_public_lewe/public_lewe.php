<?php
	
	namespace Controller\Pages\ProtectedSitePublicLewe;
	
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
			
			$arUserAllowed = array();
			
			try {
				
				$objUser = User::login(); //controllo se l'utente è loggato				
				
			} catch (UserException $e) {
				
				header("Location: " . Config::_PAGE_SITE_INDEX); //se non loggato reindirizzo a home
					
			}
			
			
			//PRELEVO I DATI DAL DB DEGLI UTENTI GIA' REGISTRATI E IMPOSTO QUELLI NUOVI
			
			//controllo se ci sono utenti da rimuovere
			if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_PUBLIC_LEWE_DIS])) { //date from
			
				//echo "ok";
			
				$queryDelete = "DELETE FROM " . Config::_DB_TABLE_AUTHORIZATION . " WHERE " . Config::_DB_TABLE_AUTHORIZATION_USER_ID . "=:user_id";
				$queryDelete .= " AND " . Config::_DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID . "=:user_allowed_id";
				
				//echo $queryDelete;
				
				try {
							
					$objUserToDisallow = User::loginByUsername($this->objRequest->_POST[Config::_HTTP_REQUEST_PUBLIC_LEWE_DIS]);
					
					$objDatabase->prepareQuery($queryDelete);
				
					$objDatabase->bindParamInteger(":user_id", $objUser->Id);
					$objDatabase->bindParamInteger(":user_allowed_id", $objUserToDisallow->Id);
				
					$objDatabase->executeQuery();
							
				} catch (UserException $e) {
			
				}
				
				
				
			
			} else if (isset($this->objRequest->_POST[Config::_HTTP_REQUEST_PUBLIC_LEWE_N])) {
				
				$intN = $this->objRequest->_POST[Config::_HTTP_REQUEST_PUBLIC_LEWE_N]; //numero utenti da inserire
				
				//query per inserire un utente nella tabella di quelli autorizzati
				$queryInsert = "INSERT INTO " . Config::_DB_TABLE_AUTHORIZATION . "(" . Config::_DB_TABLE_AUTHORIZATION_USER_ID;
				$queryInsert .= "," . Config::_DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID . ") VALUES (:user_id, :user_allowed_id)";
				
				$querySelect = "SELECT COUNT(*) AS numberOfRecord FROM " . Config::_DB_TABLE_AUTHORIZATION;
				$querySelect .= " WHERE " . Config::_DB_TABLE_AUTHORIZATION_USER_ID . "=:user_id AND ";
				$querySelect .= Config::_DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID . "=:user_allowed_id";
				
				//echo $querySelect;
			
				for ($i = 0; $i < $intN; $i++) {
					
					$strUserAllowed = $this->objRequest->_POST[Config::_HTTP_REQUEST_PUBLIC_LEWE_KEY . $i]; //scarico il nome utente
					
					if ($strUserAllowed != "") { 
						
						try {
							
							$objUserAllowed = User::loginByUsername($strUserAllowed);
							
						} catch (UserException $e) {
						
							continue; //username errato
								
						}
						
						
						if ($objUser->Id == $objUserAllowed->Id) { //non posso autoautorizzarmi
							continue;	
						} else {
							
							$objDatabase->prepareQuery($querySelect);
						
							$objDatabase->bindParamInteger(":user_id", $objUser->Id);
							$objDatabase->bindParamInteger(":user_allowed_id", $objUserAllowed->Id);
							
							
							$objDatabase->executeQuery();
							
							if ($objDatabase->getFieldByName("numberOfRecord") > 0) {
								continue;	
							}
								
						}
						
						
						$objDatabase->prepareQuery($queryInsert);
						
						$objDatabase->bindParamInteger(":user_id", $objUser->Id);
						$objDatabase->bindParamInteger(":user_allowed_id", $objUserAllowed->Id);
						
						
						$objDatabase->executeQuery();
						
					}
					
				}
					
			}
			
			$queryCount = "SELECT COUNT(*) AS numberOfRecord FROM ";
			$queryCount .= Config::_DB_TABLE_AUTHORIZATION . " WHERE " . Config::_DB_TABLE_AUTHORIZATION_USER_ID . "=:user_id";
			
			//echo $queryCount;
			
			$objDatabase->prepareQuery($queryCount);
			
			$objDatabase->bindParamInteger(":user_id", $objUser->Id);
			
			$objDatabase->executeQuery();
			
			$intN = $objDatabase->getFieldByName("numberOfRecord");
			
			//echo $intN;
			
			if ($intN > 0) { //se ci sono utenti abilitati li prelevo dal db
			
				
				
				$querySelect = "SELECT " . Config::_DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID . " FROM ";
				$querySelect .= Config::_DB_TABLE_AUTHORIZATION . " WHERE " . Config::_DB_TABLE_AUTHORIZATION_USER_ID . "=:user_id";
				
				$objDatabase->prepareQuery($querySelect);
				
				$objDatabase->bindParamInteger(":user_id", $objUser->Id);
				
				$objDatabase->executeQuery();
				
				for ($i = 0; $i < $intN; $i++) {
					
					try {
							
						$objUserAllowed = User::loginById($objDatabase->getFieldByName(Config::_DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID, $i));
							
					} catch (UserException $e) {
						
						continue; //username errato
								
					}
					
					$arUserAllowed[] = $objUserAllowed->Username;
						
				}
				
			}
			

			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_header/header.php"); //header
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_menu/menu.php"); //menu
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_content_public_lewe/public_lewe.php"); //main
			
			require_once($this->objRequest->_SERVER['DOCUMENT_ROOT'] . "/views/pages/protected/site_footer/footer.php"); //footer
				
		}
			
	}
<?php

	namespace Controller;
	
	use \Config\Config;
	
	use \Controller\User\Exception as UserException;
	
	use \Model\User as UserModelClass;
	use \Model\User\Exception as UserModelClassException;
	
	use \Controller\Request;
	
	use \Model\Database;
	
	class User {
		
		private $objUser;
		
		
		
		
		private function __construct($objUser) {
			
			$this->objUser = $objUser;
		}
		
		
		public static function login($TypeUser = Config::_USER_TYPE_NORMAL, $strEmail = NULL, $strPassword = NULL, $boolCookie = false) {
			
			$objRequest = Request::getRequest();
			
			$objUserModel;
			
			$blLoginCookie = false;
			//$blLoginCookieOk = false;
			
			
			
			//echo "ok0";
			
			if ($strEmail == NULL and $strPassword == NULL) {
				
				//echo "ok1";
			
				if (isset($objRequest->_COOKIE[Config::_USER_COOKIE_EMAIL]) and isset($objRequest->_COOKIE[Config::_USER_COOKIE_PASSWORD])) {
					
					//echo "ok2";
					
					$strEmail = $objRequest->_COOKIE[Config::_USER_COOKIE_EMAIL];
					$strPassword = $objRequest->_COOKIE[Config::_USER_COOKIE_PASSWORD];

					$blLoginCookie = true;
				} else {
					//echo "ok3";
					throw new UserException(Config::_USER_EXCEPTION_USER_NOT_LOGGED);
				}
				
			} 
			
			try {
				//echo "ok4";
				
				$objUserModel = UserModelClass::login($TypeUser, $strEmail, $strPassword);
				
				//echo "ok5";
				
				if (!$blLoginCookie) {
					if ($boolCookie) {

						setcookie(Config::_USER_COOKIE_EMAIL, $strEmail, time() + Config::_USER_COOKIE_TIME, "/");

						setcookie(Config::_USER_COOKIE_PASSWORD, $strPassword, time() + Config::_USER_COOKIE_TIME, "/");

					} else {

						setcookie(Config::_USER_COOKIE_EMAIL, $strEmail, 0, Config::_USER_COOKIE_LOCATION);

						setcookie(Config::_USER_COOKIE_PASSWORD, $strPassword, 0, Config::_USER_COOKIE_LOCATION);
						
						
					}
					
					//echo "ok6";
					
					$objRequest->setParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_EMAIL, $strEmail);
					$objRequest->setParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_PASSWORD, $strPassword);
					
				}
				
				return new User($objUserModel);
						
			} catch (UserModelClassException $e) {
				
				throw new UserException($e->getMessage());
										
			}
			
		}
		
		public static function loginById($intId) { //login utente provvisorio con id
			
			try {
			
				return new User(UserModelClass::loginById($intId));
			
			} catch (UserModelClassException $e) {
				throw new UserException($e->getMessage());
			}
			
		}
		
		
		public static function loginOnce($TypeUser = Config::_USER_TYPE_NORMAL, $strEmail = NULL, $strPassword = NULL) { //login che non setta cookie o sessioni
			
			$objRequest = Request::getRequest();
			
			$objUserModel;
			
			$blLoginCookie = false;
			//$blLoginCookieOk = false;
			
			
			
			//echo "ok0";
			
			if ($strEmail == NULL and $strPassword == NULL) {
				
				//echo "ok1";
			
				if (isset($objRequest->_COOKIE[Config::_USER_COOKIE_EMAIL]) and isset($objRequest->_COOKIE[Config::_USER_COOKIE_PASSWORD])) {
					
					//echo "ok2";
					
					$strEmail = $objRequest->_COOKIE[Config::_USER_COOKIE_EMAIL];
					$strPassword = $objRequest->_COOKIE[Config::_USER_COOKIE_PASSWORD];

					$blLoginCookie = true;
				} else {
					//echo "ok3";
					throw new UserException(Config::_USER_EXCEPTION_USER_NOT_LOGGED);
				}
				
			} 
			
			try {
				//echo "ok4";
				
				$objUserModel = UserModelClass::login($TypeUser, $strEmail, $strPassword);
				
				//echo "ok5";
				
				if (!$blLoginCookie) {
					
					$objRequest->setParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_EMAIL, $strEmail);
					$objRequest->setParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_PASSWORD, $strPassword);
					
				}
				
				return new User($objUserModel);
						
			} catch (UserModelClassException $e) {
				
				throw new UserException($e->getMessage());
										
			}
			
		}
		
		public static function loginByUsername($strUsername) {
		
			$objDatabase = new Database(); //creo oggetto dtabase
			
			$strQuery = $strQuery = "SELECT COUNT(*) AS numberRecord FROM " . Config::_DB_TABLE_USERS . //query per contare se ci sono utenti con l'username passato
						" WHERE " . Config::_DB_TABLE_USERS_USERNAME . "=:username";
						
			$objDatabase->prepareQuery($strQuery);
			$objDatabase->bindParamString(":username", $strUsername);
			$objDatabase->executeQuery();
			
			if ($objDatabase->getFieldByName("numberRecord") == 0) { //se 0 utenti getto eccezione
				
				throw new UserException(Config::_USER_EXCEPTION_BAD_LOGIN_CREDENTIAL);
				
			}
			
			
			$strQuery = "SELECT " . Config::_DB_TABLE_USERS_ID . " FROM " . Config::_DB_TABLE_USERS .
						" WHERE " . Config::_DB_TABLE_USERS_USERNAME . "=:username";
						
			$objDatabase->prepareQuery($strQuery);
			$objDatabase->bindParamString(":username", $strUsername);
			$objDatabase->executeQuery();
			
			$intId = $objDatabase->getFieldByName(Config::_DB_TABLE_USERS_ID);
			
			return User::loginById($intId); //eseguo il login by id
			
			
		
		}
		
		
		public static function registration($strEmail = NULL, $strEmailConfirm = NULL, $strUsername = NULL, $strPassword = NULL, $strPasswordConfirm = NULL, $strTypeUser = Config::_USER_TYPE_NORMAL) {
			
			$objRequest;
			$objUserModel;
			
			
			
			$objRequest = Request::getRequest();
			
			try {
				
				$objUserModel = UserModelClass::registration($strEmail, $strEmailConfirm, $strUsername, $strPassword, $strPasswordConfirm, $strTypeUser);
				
				$objRequest->setParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_EMAIL, $strEmail);
				$objRequest->setParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_PASSWORD, $strPassword);
				
				return User::login($strTypeUser, $strEmail, $strPassword, false);
				
			} catch (UserModelClassException $e) {
				
				throw new UserException($e->getMessage());
				
			}
				
				
		}
		
		
		//funzione di logout (cancella i cookie)

		public function logout() {
			$objRequest = Request::getRequest();

			setcookie(Config::_USER_COOKIE_EMAIL, "", time() -1, Config::_USER_COOKIE_LOCATION);

			setcookie(Config::_USER_COOKIE_PASSWORD, "", time() -1, Config::_USER_COOKIE_LOCATION);
			
			$objRequest->unsetParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_EMAIL);
			$objRequest->unsetParam(Config::_REQUEST_VAR_COOKIE, Config::_USER_COOKIE_PASSWORD);

		}
		
		
		
		public function __get($strPropertyName) {

			return call_user_func(array($this->objUser, "__get"), $strPropertyName);

		}
		
		
	}
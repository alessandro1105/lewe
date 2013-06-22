<?php

	namespace Model;
	
	use \Config\Config;
	
	use \Model\User\Exception as UserException;
	
	use \Model\Database;
	use \Model\Database\Exception as DatabaseException;
	
	use \Model\Field;
	use \Model\Field\Exception as FieldException;	

	class User {

				
		/*campi utente*/
		private $objEmail;
		private $objUsername;
		private $objPassword;
		private $objTypeUser;
		private $objActive;
		

		
		/*parametri classe user*/
		private $objDatabase;
		
		private $intId;
	
	
		//costruttore classe User

		private function __construct($intId) {
						
			$this->intId = $intId;
			
			$this->objDatabase = new Database();
			
			$this->objEmail = new Field($this->objDatabase);			
			$this->objEmail->Table = Config::_DB_TABLE_USERS;
			$this->objEmail->Field = Config::_DB_TABLE_USERS_EMAIL;
			$this->objEmail->Id = $this->intId;
			
			
			$this->objUsername = new Field($this->objDatabase);
			$this->objUsername->Table = Config::_DB_TABLE_USERS;
			$this->objUsername->Field = Config::_DB_TABLE_USERS_USERNAME;
			$this->objUsername->Id = $this->intId;
			
			
			$this->objPassword = new Field($this->objDatabase);
			$this->objPassword->Table = Config::_DB_TABLE_USERS;
			$this->objPassword->Field = Config::_DB_TABLE_USERS_PASSWORD;
			$this->objPassword->Id = $this->intId;
			
			
			$this->objTypeUser = new Field($this->objDatabase);
			$this->objTypeUser->Table = Config::_DB_TABLE_USERS;
			$this->objTypeUser->Field = Config::_DB_TABLE_USERS_TYPEUSER;
			$this->objTypeUser->Id = $this->intId;
			
			
			$this->objActive = new Field($this->objDatabase);
			$this->objActive->Table = Config::_DB_TABLE_USERS;
			$this->objActive->Field = Config::_DB_TABLE_USERS_ACTIVE;
			$this->objActive->Id = $this->intId;
			
			
		}

		

		//funzione di login che restituisce un oggetto user instanziato

		public static function login($TypeUser, $strEmail, $strPassword) {
			
			$intId;
			
			$strTypeUserDb;
			
			$querySQL = "SELECT " . Config::_DB_TABLE_USERS_ID . ", " . Config::_DB_TABLE_USERS_TYPEUSER . " FROM " . Config::_DB_TABLE_USERS; 
			$querySQL .= " WHERE " . Config::_DB_TABLE_USERS_EMAIL . " = :email" . " AND " . Config::_DB_TABLE_USERS_PASSWORD;
			$querySQL .= " = :password";
			
			
			
			$objDatabase = new Database();
			
			$objDatabase->prepareQuery($querySQL);

				
			$objDatabase->bindParamString(":email", $strEmail);
			$objDatabase->bindParamString(":password", $strPassword);
			//$objDatabase->bindParamString(":typeUser", $TypeUser);
				
					
			$objDatabase->executeQuery();
					
			try {
						
				$intId = $objDatabase->getFieldByName(Config::_DB_TABLE_USERS_ID);
				
				$strTypeUserDb = $objDatabase->getFieldByName(Config::_DB_TABLE_USERS_TYPEUSER);					
						
			} catch (DatabaseException $e) {
						
				throw new UserException(Config::_USER_EXCEPTION_BAD_LOGIN_CREDENTIAL);
						
			}			

			
			switch ($strTypeUserDb) {
				
				case $TypeUser:
				case Config::_USER_TYPE_ADMIN:
				
					return new User($intId);
					
					break;
					
				
				default:
					
					throw new UserException(Config::_USER_EXCEPTION_BAD_LOGIN_CREDENTIAL);
					
					
			}
					
				


		}

		
		
		public static function loginById($intId) {
			
			$intIdN;
			
			$objDatabase = new Database();
			
			$querySQL = "SELECT COUNT(*) AS number FROM " . Config::_DB_TABLE_USERS . "	WHERE ";
			$querySQL .= Config::_DB_TABLE_USERS_ID . "=:id";
			
			$objDatabase->prepareQuery($querySQL);
			
			$objDatabase->bindParamInteger(":id", $intId);
			
			$objDatabase->executeQuery();
			
			
			try {
					
				$intIdN = $objDatabase->getFieldByName("number");
								
			} catch (DatabaseException $e) {
				throw new UserException(Config::_USER_EXCEPTION_ID_NOT_EXISTS);
				
				return;
			}
			
			
			if ($intIdN == 1) {
				
				return new User($intId);
			
			} else {
				
				throw new UserException(Config::_USER_EXCEPTION_ID_NOT_EXISTS); 	
			}
				
		}
		

		//funzione di registrazione che restituisce un oggetto user instanziato

		public static function registration($strEmail, $strEmailConfirm, $strUsername, $strPassword, $strPasswordConfirm, $strTypeUser) {
			
			$querySQL;
			
			$intId;
			
			$objDatabase = new Database();
			
			$strError = "";
			
			//test email
			if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
				$strError .= Config::_USER_EXCEPTION_EMAIL_NOT_VALID;
			}
			
			if (!($strEmail == $strEmailConfirm)) {
				$strError .= Config::_USER_EXCEPTION_EMAIL_NOT_MATCH;
			}
			
			
			//controllo nel db se esiste la mail
			$querySQL = "SELECT " . Config::_DB_TABLE_USERS_ID . " FROM " . Config::_DB_TABLE_USERS . " WHERE " . Config::_DB_TABLE_USERS_EMAIL . " = :email";
			
			$objDatabase->prepareQuery($querySQL);
			$objDatabase->bindParamString(":email", $strEmail);
			
			$objDatabase->executeQuery();
			
			try {
				
				$objDatabase->getFieldByName("id");
				
				$strError .= Config::_USER_EXCEPTION_EMAIL_ALREADY_USED;
				
			} catch (DatabaseException $e) {
				
			}
			
			
			
							
			
			//test username
			if (!(strlen($strUsername) <= 30 and preg_match("([a-z][a-z0-9]{5})", $strUsername))) {
				$strError .= Config::_USER_EXCEPTION_USERNAME_NOT_VALID;
			}
			
			
			
			//controllo nel db se esiste l'username
			$querySQL = "SELECT id FROM " . Config::_DB_TABLE_USERS . " WHERE " . Config::_DB_TABLE_USERS_USERNAME . " = :username";
			
			$objDatabase->prepareQuery($querySQL);
			$objDatabase->bindParamString(":username", $strUsername);
			
			$objDatabase->executeQuery();
			
			try {
				
				$objDatabase->getFieldByName("id");
				
				$strError .= Config::_USER_EXCEPTION_USERNAME_ALREADY_USED;
				
			} catch (DatabaseException $e) {
				
			}
			
			
			
			
			//test password
			if (!(strlen($strPassword) >= 8 and strlen($strPassword) <=20)) {
				$strError .= Config::_USER_EXCEPTION_PASSWORD_NOT_VALID;
			}
					
			if (!($strPassword == $strPasswordConfirm)) {
				$strError .= Config::_USER_EXCEPTION_PASSWORD_NOT_MATCH;
			}
			
			
			
			//inserisco l'utente nel db se non ci sono stati errori ed effettuo il login
			if ($strError == "") {
				$querySQL = "INSERT INTO " . Config::_DB_TABLE_USERS . " (" . Config::_DB_TABLE_USERS_EMAIL; 
				$querySQL .= "," . Config::_DB_TABLE_USERS_USERNAME . "," . Config::_DB_TABLE_USERS_PASSWORD . "," . Config::_DB_TABLE_USERS_TYPEUSER;
				$querySQL .= "," . Config::_DB_TABLE_USERS_ACTIVE . ") VALUES (";
				$querySQL .= ":email,:username,:password,:typeUser,:active)";
				
				$objDatabase->prepareQuery($querySQL);
				
				$objDatabase->bindParamString(":email", $strEmail);
				$objDatabase->bindParamString(":username", $strUsername);
				$objDatabase->bindParamString(":password", $strPassword);
				$objDatabase->bindParamString(":typeUser", $strTypeUser);
				
				//if ($strTypeUser == Config::_USER_TYPE_OPERATOR) {
				//	$objDatabase->bindParamBoolean(":active", false);
				//} else {
					$objDatabase->bindParamBoolean(":active", true);
				//}
				
				
				$objDatabase->executeQuery();
				
				//return User::login($strTypeUser, $strEmail, $strPassword, false);
											
			} else {
				throw new UserException($strError);
			}
			
			
		}

		

				

		//get function sfrutta sistema $userInstance->email;

		

		public function __get($strPropertyName) {

			$strFunctionName =  'get' . $strPropertyName;

			

			if (method_exists($this, $strFunctionName)) {

				return call_user_func(array($this, $strFunctionName));

			} else {

				throw new UserException(Config::_USER_EXCEPTION_PROPERTY_NAME_INESISTENT);

			}

		}

				

		//get singole proprietà caricano l'oggetto solo se serve		

				

		private function getEmail() {

			return $this->objEmail->Value;

		}
		
		private function getPassword() {

			return $this->objPassword->Value;

		}
		

		private function getUsername() {

			return $this->objUsername->Value;

		}

		

		private function getTypeUser() {

			return $this->objTypeUser->Value;

		}

		private function getId() {
		
			return $this->intId;	
		}

	}
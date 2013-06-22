<?php

	namespace Config; //dichiarazione namespace classe config
	
	use Model\Logger;
	
	
	class Config {
		
		//const _VIEWS_PATH = "/mobile/
		
		const _CONFIG_TYPE = Config::_SITE;
		
		/*costanti per configurazione db*/
		const _DB_TYPE = "mysql";
		const _DB_NAME = "lewe_tk";
		const _DB_HOST = "localhost";
		const _DB_PORT = 3306;
		const _DB_USERNAME = "root";
		const _DB_PASSWORD = "alepas1105";
		
		const _PATH_JS_FOLDER = "/js";
		
		
		/* config diverso per app o operatore */
		const _APP = "application"; //app
		const _SITE = "site";		//sito
		
		/*onwer per gli script*/
		const _REMOTE_SCRIPT_OWNER = "script";
		
		
		/*tabella pages*/
		const _DB_TABLE_PAGES = "pages";
		const _DB_TABLE_PAGES_ID = "id";
		const _DB_TABLE_PAGES_OWNER = "owner";
		const _DB_TABLE_PAGES_NAME = "name";
		const _DB_TABLE_PAGES_CONTROLLER = "controller";
		
		
		/*tabella utenti*/
		const _DB_TABLE_USERS = "users";	//nome
		
		const _DB_TABLE_USERS_ID = "id";
		const _DB_TABLE_USERS_EMAIL = "email";	//campo email
		const _DB_TABLE_USERS_USERNAME = "username"; //campo username
		const _DB_TABLE_USERS_PASSWORD = "password"; //campo password
		const _DB_TABLE_USERS_TYPEUSER = "typeUser"; //campo typeUser
		const _DB_TABLE_USERS_ACTIVE = "active";	//campo active
		
		
		/*TABELLA RILEVAZIONI*/
		const _DB_TABLE_SURVEYS = "surveys";
		
		const _DB_TABLE_SURVEYS_ID = "id";
		const _DB_TABLE_SURVEYS_SENSOR_NAME = "sensor_name";
		const _DB_TABLE_SURVEYS_SENSOR_VALUE = "sensor_value";
		const _DB_TABLE_SURVEYS_TIMESTAMP = "sensor_timestamp";
		const _DB_TABLE_SURVEYS_USER_ID = "user_id";
		
		
		/*TABELLA AUTORIZZAZIONI*/
		const _DB_TABLE_AUTHORIZATION = "authorization";
		
		const _DB_TABLE_AUTHORIZATION_ID = "id";
		const _DB_TABLE_AUTHORIZATION_USER_ID = "user_id";
		const _DB_TABLE_AUTHORIZATION_USER_ALLOWED_ID = "user_allowed_id";
		
		
		//CAMPI DEL MESSAGGIO JACK
		const _JACK_MESSAGE_FILED_TIMESTAMP = "TIMESTAMP";
		const _JACK_MESSAGE_FILED_TEMPERATURE = "TEMPERATURE";
		const _JACK_MESSAGE_FILED_GSR = "GSR";
		
		
		/*nomi pagine per forzatura caricamento pagina (utile solo per medico)*/
		const _PAGE_DEFAULT_PAGE_NAME = "default"; //deve obbligatoriamente esistere una pagina dal nome dedfault per tutte le owqner
				
		/*nomi pagine per forzatura caricamento del sottodominio app*/
		
		/*nomi script remoti*/
		const _REMOTE_SCRIPT_NAME_ADD_SURVEYS = "remote_script_add_surveys";	
		
		
		/*nomi pagine site PUBLIC*/		
		
		const _PAGE_SITE_INDEX = "/";
		const _PAGE_SITE_LOGIN = "login";
		const _PAGE_SITE_REGISTRATION = "registration";

		const _PAGE_SITE_CODE = "code";
		const _PAGE_SITE_CONTACT = "contact";
		
		const _PAGE_SITE_HOWITWORKS = "how-it-works";
		
		/*nomi pagine site PRIVATE*/
		const _PAGE_SITE_HOME = "my_lewe";
		
		//const _PAGE_SITE_HOME = "home";
		
		const _PAGE_SITE_LOGOUT = "logout";
		const _PAGE_SITE_MY_LEWE = "my_lewe";
		const _PAGE_SITE_PUBLIC_LEWE = "public_lewe";
		const _PAGE_SITE_FRIEND_LEWE = "friend_lewe";
		const _PAGE_SITE_FRIEND_LEWE_DETAILS = "friend_lewe_details";
		
				
		/*PARAMETRI GENERALI*/
		
		/*definizione utenti*/
		const _USER_TYPE_NORMAL = "normal";
		const _USER_TYPE_ADMIN = "admin";
		
		
		/*parametri POST, GET*/
		const _FORM_METHOD_POST = "POST";
		const _FORM_METHOD_GET = "GET";
		
		
		/*parametri indicazione pagina richiesta dinamica*/
		const _PAGES_FORM_METHOD = Config::_FORM_METHOD_GET;
		const _PAGES_FORM_NAME = "page";
		

		/*parametri cookie login*/
		const _USER_COOKIE_EMAIL = "email";
		const _USER_COOKIE_PASSWORD = "password";
		const _USER_COOKIE_TIME = 63072000;
		const _USER_COOKIE_LOCATION = "/";


		/*REMOTE SCRIPT result*/
		const _REMOTE_SCRIPT_RESULT_OK = "1";
		const _REMOTE_SCRIPT_RESULT_NOT_OK = "-1";
		
		/* PARAMETRI PER RICHIESTE*/
		const _HTTP_REQUEST_USER_EMAIL = "user_email";
		const _HTTP_REQUEST_USER_PASSWORD = "user_password";
		const _HTTP_REQUEST_USER_TYPE = "user_type"; //da usare per differenziare operartore, normale e admin
		const _HTTP_REQUEST_USER_COOKIE = "user_cookie";
		const _HTTP_REQUEST_USER_EMAIL_CONFIRM = "user_email_confirm";
		const _HTTP_REQUEST_USER_PASSWORD_CONFIRM = "user_password_confirm";
		const _HTTP_REQUEST_USER_USERNAME = "user_username";
		
		const _HTTP_REQUEST_LEWE_FROM_DATE = "from_date";
		const _HTTP_REQUEST_LEWE_TO_DATE = "to_date";
		const _HTTP_REQUEST_LEWE_SHOW_TEMP = "show_temp";
		const _HTTP_REQUEST_LEWE_SHOW_GSR = "show_gsr";
		
		const _HTTP_REQUEST_PUBLIC_LEWE_N = "n_user_allowed";
		const _HTTP_REQUEST_PUBLIC_LEWE_KEY = "user_allowed_";
		
		const _HTTP_REQUEST_PUBLIC_LEWE_DIS = "user_disallowed";
		
		
		const _HTTP_REQUEST_LEWE_USER_ID = "lewe_user_id"; //usato per vedere il lewe di un amico
		//parametri per message
		const _HTTP_REQUEST_NUMBER_OF_MESSAGES = "n_messages";
		const _HTTP_REQUEST_MESSAGE_CONTRAINER_PATTERN = "message_";
		
		
		/*parametri classe request per unset\isset*/
		const _REQUEST_VAR_GET = "get";
		const _REQUEST_VAR_POST = "post";
		const _REQUEST_VAR_COOKIE = "cookie";
		const _REQUEST_VAR_SERVER = "server";
		const _REQUEST_VAR_SESSION = "session";
		

		/*exception classe index*/
		const _INDEX_EXCEPTION_DEFAULT_PAGE_NOT_FOUND = "default page not found";


		//CONFIG LOGGING CLASS
		const _LOG_FILE_NAME = "log.txt";
		const _LOG_DEFAULT_LEVEL = Logger::DEBUG;



		/*exception classe model\user*/
		
		const _USER_EXCEPTION_ID_NOT_EXISTS = "Id utente non esistente!";
		
		const _USER_EXCEPTION_PROPERTY_NAME_INESISTENT = "Proprietà della classe User inesistente!";// classe UserException proprietà inesistente

		//const _USER_EXCEPTION_BAD_LOGIN_COOKIE = "Cookie per il login errati!";
		const _USER_EXCEPTION_USER_NOT_LOGGED = "Utente non loggato!";
		const _USER_EXCEPTION_BAD_LOGIN_CREDENTIAL = "Credenziali di login errate!";
		
		//errori di registrazione
		//EMAIL
		const _USER_EXCEPTION_EMAIL_ALREADY_USED = "Email gia' in uso!"; //email fià in uso
		const _USER_EXCEPTION_EMAIL_NOT_VALID = "Email non valida!"; //email non valida	
		const _USER_EXCEPTION_EMAIL_NOT_MATCH = "Email non corrisponde!"; //email non corrisponde
		
		//USERNAME
		const _USER_EXCEPTION_USERNAME_ALREADY_USED = "Username gia' in uso!"; //username già in uso
		const _USER_EXCEPTION_USERNAME_NOT_VALID = "Username non valido!"; //username non valido
		
		//PASSWORD
		const _USER_EXCEPTION_PASSWORD_NOT_VALID = "Password non valida!"; //password non valida
		const _USER_EXCEPTION_PASSWORD_NOT_MATCH = "Password non corrisponde!"; //password non corrisponde
		
		
		
		
		/*exception classe controller\user*/
		//USO ERRORI _USER_..

	}

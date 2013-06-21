<?php
	
	//includo le pagine dell'applicazione
	
	//SITO DI PRESENTAZIONE
	require_once("presentation/site_index/index.php"); //controller pagina homepage (default)
	
	require_once("presentation/site_login/login.php"); //controller pagina login
	
	require_once("presentation/site_registration/registration.php"); //controller pagina registration	
	
	require_once("presentation/site_code/code.php"); //controller pagina pubblicazione codice
	
	require_once("presentation/site_contact/contact.php"); //controller pagina registration
	
	require_once("presentation/site_how-it-works/how-it-works.php"); //controller pagina registration
	
	//SITO PROTETTO
	require_once("protected/site_home/home.php"); //controller pagina home
	
	require_once("protected/site_logout/logout.php"); //controller pagina logout
	
	require_once("protected/site_my_lewe/my_lewe.php"); //controller pagina mio lewe
	
	require_once("protected/site_public_lewe/public_lewe.php"); //controller pagina chi può vedere il mio lewe
	
	require_once("protected/site_friend_lewe/friend_lewe.php"); //controller pagina lewe degli amici
	
	require_once("protected/site_friend_lewe_details/friend_lewe_details.php"); //controller pagina dettagli lewe di un amico
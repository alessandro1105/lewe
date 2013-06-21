<?php
	
	require_once("includes.php");
	
	use \Config\Config; // importa la classe config del namespace config
	
	use \Controller\Index;
	use \Controller\Index\Exception as IndexException;
	
	
	try {
	
		$objIndex = new Index(Config::_REMOTE_SCRIPT_NAME_ADD_SURVEYS); //potrebbe generare eccezione (FORZO IL CARICAMENTO DELLO SCRIPT PER SALVARE I DATI)
		$objIndex->execute();
		
	} catch (IndexException $e) {
	
		echo $e->getMessage();
	
	}
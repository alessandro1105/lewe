<?php

	/****************************************************************
	*																*
	* file da includere nell'index.php della pagina principale per  *
	* redere disponibile nello spazio dei nomi i namespace definiti *
	*																*
	****************************************************************/	

	require_once("config/class.Config.php"); //inclusione classe di condifurazione dell'app

	require_once("models/model_includes.php"); //includo file di inclusione dei modelli
	require_once("controllers/controller_includes.php"); //includo file di inclusione controlli
	//require_once("views/views.php"); //inclusione viste
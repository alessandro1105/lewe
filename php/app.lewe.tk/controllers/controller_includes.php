<?php

	/****************************************************************
	*																*
	* file contente tutte le inclusioni dei controlli			    *
	*																*
	****************************************************************/	
	
	require_once("class.Index/class.Index.php"); 							// Controller\Index
	require_once("class.Index/class.Index.Exception.php"); 					// Controller\Index\Exception
	
	require_once("class.Request/class.Request.php");						// Controller\Request					
	require_once("class.Request/class.Request.Exception.php");				// Controller\Request\Exception
	
	require_once("class.User/class.User.php");				// Controller\User
	require_once("class.User/class.User.Exception.php");	// Controller\User\Exception
	
	require_once("class.JackImplementation/class.JackImplementation.php");				// Controller\JackImplementatio
	//require_once("class.User/class.User.Exception.php");	// Controller\JackImplementatio\Exception
	
	require_once("class.JTMHTTP/class.JTMHTTP.php");				// Controller\JTMHTTP
	//require_once("class.User/class.User.Exception.php");	// Controller\JTMHTTP\Exception
	
	require_once("pages/pages.php"); //inclusione controller di pagina
	
	require_once("scripts/scripts.php"); //inclusione remote script;
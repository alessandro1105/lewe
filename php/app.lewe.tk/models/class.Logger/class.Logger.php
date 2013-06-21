<?php

	namespace Model;
	
	use \Config\Config;
	
	class Logger { //log del tipo: ora	level	message (separati da tab)
	
		const DEBUG = 0;
		const FINEST = 1;
		const FINER = 2;
		const FINE = 3;
		const CONFIG = 4;
		const INFO = 5;
		const WARNING = 6;
		const SEVERE = 7;
		
		const _LOG_DEFAULT_LEVEL = Config::_LOG_DEFAULT_LEVEL;
		
		private static $handleFileLog;
		
		
		public static function openHandle() {
			
			if (Logger::$handleFileLog == NULL) {
				
				Logger::$handleFileLog = fopen(Config::_LOG_FILE_NAME, "a+");	
			}
			
		}
		
		
		public static function log($strMessage, $debugLevel = Logger::DEBUG) {
			
			if ($debugLevel >= Logger::_LOG_DEFAULT_LEVEL) {
				
				Logger::openHandle(); //apro connessione al file
				
				fwrite(Logger::$handleFileLog, date('Y-m-d H:i:s', time())); //data
				fwrite(Logger::$handleFileLog, "\t"); //tab
				
				
				switch ($debugLevel) { //level log
					
					case Logger::DEBUG:
						fwrite(Logger::$handleFileLog, "DEBUG"); //level
						break;
						
					case Logger::FINEST:
						fwrite(Logger::$handleFileLog, "FINEST"); //level
						break;
						
					case Logger::FINER:
						fwrite(Logger::$handleFileLog, "FINER"); //level
						break;
						
					case Logger::FINE:
						fwrite(Logger::$handleFileLog, "FINE"); //level
						break;
						
					case Logger::CONFIG:
						fwrite(Logger::$handleFileLog, "CONFIG"); //level
						break;
						
					case Logger::INFO:
						fwrite(Logger::$handleFileLog, "INFO"); //level
						break;
						
					case Logger::WARNING:
						fwrite(Logger::$handleFileLog, "WARNING"); //level
						break;
						
					case Logger::SEVERE:
						fwrite(Logger::$handleFileLog, "SEVERE"); //level
						break;
					
				}
				
				
				fwrite(Logger::$handleFileLog, "\t"); //tab
				
				fwrite(Logger::$handleFileLog, $strMessage); //messaggio
				fwrite(Logger::$handleFileLog, "\n"); //a capo
					
			}
			
			
		}
	
		
	}
<?php

	namespace Model\Jack;

	interface JTrasmissionMethod {
		
		public function send($message); //metodo usato per inviare messaggi
		public function receive(); //funzione che restituisce il messaggio in stringa
		
		public function available(); //funzione che indica se sono disponibili messaggi da scaricare
		
	}
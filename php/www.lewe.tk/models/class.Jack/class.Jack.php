<?php
	
	namespace Model\Jack;
	
	use \Model\Jack\Jack\Exception as JackException;
	
	use \Model\HashMap;
	
	abstract class Jack {
	
		private $TIME_BEFORE_RESEND = 1000; //tempo timer per reinvio messaggi non confermati
		private $SEND_ONE_TIME = false; //indica se inviare il messaggio una volta anche se non confermato
		
		const TIMER_POLLING = 100;
	
	
		const MESSAGE_TYPE = "message_type"; //campo dentro messageJackData che contine il tipo di mex
		const MESSAGE_DATA = "message_data"; //campo contente dati nel messaggio tipo dati 
		
		const MESSAGE_ID = "id";
			
		const MESSAGE_TYPE_ACK = "ack"; //messaggio ack per qualcosa inviato
		const MESSAGE_TYPE_DATA = "values"; //messaggio normale contenente dati
			
		const MESSAGE_BOOLEAN_TRUE = "t"; //simobolo true in booleano nel mex
		const MESSAGE_BOOLEAN_FALSE = "f"; //simbolo false nel mex
		
		 
		
		private $objJTM; //contiene il metodo di trasmissione da usare e deve rispettare l'interfaccia
		
		//private $stopPolling = true;
		
		private $objSendMessageBuffer; //buffer per i mex da inviare
		private $objSendMessageTimer; //buffer dei timer per i mex da inviare
		private $objSendMessageBufferJData; //buffer per i messaggi da inviare nel formato JData
		
		private $objSendAckBuffer; //buffer che contiene i mex ack da inviare (non necessitano di timer)
		
		private $objIdMessageReceived; //buffer che contiene gli id dei messaggi ricevuti per evitare duplicazioni nei dati
		
		
		//metodi astratti usati per program,mare le azioni da eseguire al ricevimento di un ack o di un dato
		abstract protected function onReceive($objMessageJData); //medoto da implementare e deve contere le istruzioni
													   //eseguire al ricevimento del messaggio
		abstract protected function onReceiveAck($objMessageJData); //metodo invocato al ricev1imento di un ack (contiene messaggio inviato da cui prelevare i dati per 
		
		abstract protected function getTimestamp(); //funzione da implementare che ritorna il timestamp (usato coe id per il messaggio da inviare)
		
		
		public function __construct($objJTM, $objParam) { //costruttore
		
			$this->objJTM = $objJTM;
			
			
			//buffer
			$this->objSendMessageBuffer = new HashMap(); //buffer messaggi
			
			$this->objSendMessageTimer = new HashMap(); //timer per invio messaggi
			
			$this->objSendMessageBufferJData = new HashMap(); //buffer messaggi da inviare formato JData
			
			$this->objSendAckBuffer = new HashMap(); //buffer ack
			
			$this->objIdMessageReceived = new HashMap(); //buffer id messaggi già ricevuti
			
			if (is_long($objParam) or is_int($objParam)) { //parametro long o int usato per specificare il tempo di prima di rispedire
				
				$this->TIME_BEFORE_RESEND = $objParam;
			} else if (is_bool($objParam)) { //parametro boolean usato per specificare se usare la modalità send one time
				
				$this->SEND_ONE_TIME = $objParam;
			}
			
		}
		
		
		public function start() { //start jack (richiamo la lòettura della soprgente dei dati)
			
			//$this->stopPolling = false; //varibile che indica se far partire i mertodi per il polling
			
			while ($this->objJTM->available()) {
				$this->getMessagePollingFunction();	
			}
			
		}
		
		public function stop() { //stop jack (richiamo l'invio dei dati)
			
			//$this->stopPolling = true;
			
			$this->sendMessagePollingFunction();
					
		}
		
		public function flushBufferSend() { //funziona che cancella il buffer dei mex da inviare
			
			$this->sendMessageBuffer = new HashMap(); //reset buffer messaggi da inviare
			$this->sendMessageBufferJData = new HashMap();
			
		}
		
		private function execute($strMessage) { //corpo centrale della classe e decide cosa deve fare
					
			if ($this->validate($strMessage)) {
		
				$objMessageJData = $this->getJDataMessage($strMessage);
				//echo 1;
				if ($objMessageJData->getValue(Jack::MESSAGE_TYPE) == Jack::MESSAGE_TYPE_DATA) {
				//echo 2;
					if (!$this->checkMessageAlreadyReceived($objMessageJData)) {
				
						$this->onReceive($objMessageJData);
					}
			
				} else {
				
					$this->checkAck($objMessageJData);
				}
		
			}
			
		}
		
		
		
		//verifica che il messaggio non sia già stato ricevuto (reinviato per non ricezione ack)
		private function checkMessageAlreadyReceived($objMessageJData) {
			
			//grezza validazione dei dati da sostituire con il metodo validate
			if (!$objMessageJData->containsKey(Jack::MESSAGE_ID)) { //se non presente id incorrerei in errori e quindi blocco elaborazione
				
				return true; //con true blocco l'elaborazione del messaggio come se fosse già stato ricevuto
			}
			// fine grezza validazione del messaggio
			
			
			$this->sendAck($objMessageJData); //invio ack per problema scadenza timer (invio anche se già ricevuto per perdita o ritardo ack precedente)
			
			if (!$this->objIdMessageReceived->containsKey($objMessageJData->getValue(Jack::MESSAGE_ID))) {
				
				$this->objIdMessageReceived->put($objMessageJData->getValue(Jack::MESSAGE_ID), 0); //è importante solo la chiave non il valore (cast Long dell'id)
				
				return false; //messaggio non già ricevuto
				
			} else {
				
				return true; //messaggio già ricevuto
				
			}
			
		}
		
		
		//valida il messaggio conforme al protocollo JACK
		private function validate($strMessage) {
			//medoto da implementare
			return true;
		}		
		
		private function getJDataMessage($strMessage) {
			
			$objMessageJData = new JData();		
			
			
			$temp = "";
			$temp2 = "";
			
			$nChar = 0;
			
			$value;
			
			
			$strMessage = substr($strMessage, 2); //elimino 2 caratteri iniziali
			
			for($i = 0; $i < 2; $i++) {
				
				$temp = "";
			
			
				if ($this->startsWith($strMessage, Jack::MESSAGE_ID)) { //indicazione id 
					
					//echo "id</br>";
					//echo $strMessage;
					//echo "</br>";
					$strMessage = substr($strMessage, strlen(Jack::MESSAGE_ID) + 2); //elimino dal mex id + 2 caratteri (":)
					//echo $strMessage;
					//echo "</br>";
				
					for ($x = 0; $strMessage[$x] != ','; $x++) { //prelevo l'id e lo memorizzo in temp
						//echo $strMessage[$x];
						
						$temp = $temp . $strMessage[$x];
					}
					
					//echo "</br>";
					
					$strMessage = substr($strMessage, strlen($temp) + 2); //elimino dal mex la lunghezza dell'id + 2
					//echo $temp;
					//echo "</br>";
					//echo $strMessage;
					
					$objMessageJData->add(Jack::MESSAGE_ID, (float) $temp); //converto in long l'id
					
				} else if ($this->startsWith($strMessage, Jack::MESSAGE_TYPE_ACK)) { //indicazione ack  messaggio ack
				
					//echo "ack";
					
					$objMessageJData->add(Jack::MESSAGE_TYPE, Jack::MESSAGE_TYPE_ACK);
					
					if ($i < 1) //sono al primo giro e manca ancora l'id
						$strMessage =substr($strMessage, strlen(Jack::MESSAGE_TYPE_ACK) + 5); //elimino la lunghezza di ack + 5 caratteri
						
				} else if ($this->startsWith($strMessage, Jack::MESSAGE_TYPE_DATA)) { //indicazione values messaggio contenente dati
					
					//echo "data";
					
					$objMessageJData->add(Jack::MESSAGE_TYPE, Jack::MESSAGE_TYPE_DATA);
					
					$strMessage =substr($strMessage, strlen(Jack::MESSAGE_TYPE_DATA) + 5);
					
					//azzero le variabili prima di entrare nel ciclo
					$value = false;
					$temp = ""; 
					$temp2 = "";
					$nChar = 0;
					
					for ($x = 0; $strMessage[$x] != ']'; $x++) { //scorro i caratteri di message
						
						$nChar++; //serve per contare i carattri che elimenerò da message
						
						if ($strMessage[$x] == ',' || $strMessage[$x] == '}') { //store value nel JData						
												
							if ($temp2[0] == '"') { //stringa
								
								$objMessageJData->add($temp, substr($temp2, 1, strlen($temp2) -1));
								
							} else if ($this->contains($temp2, ".")) { //double
								
								$objMessageJData->add($temp, (double) $temp2);
								
							} else if ($temp2 == Jack::MESSAGE_BOOLEAN_TRUE or $temp2 == Jack::MESSAGE_BOOLEAN_FALSE) { // boolean
								
								if ($temp2 == Jack::MESSAGE_BOOLEAN_TRUE) { //true
									
									$objMessageJData->add($temp, true);
									
								} else { //false
									
									$objMessageJData->add($temp, false);
									
								}
								
							} else { //long
								
								$objMessageJData->add($temp, (float) $temp2);
							
							} //fine switch tipi
							
							//azzero i valori
							$value = false;
							$temp = "";
							$temp2 = "";
							 
						} else if ($strMessage[$x] == ':') { //passo da caratteri della chiave a caratteri del valore
							
							$value = true;
							
						} else if (!$value and $strMessage[$x] != '"') { //value = true caratteri CHIAVE
							
							$temp = $temp . $strMessage[$x];
							
						} else if ($value) { //caratteri del VALORE value = false
							
							$temp2 = $temp2 . $strMessage[$x];
						}
						
					} //fine for values 
					

					if ($i < 1) //manca ancora id
						$strMessage = substr($strMessage, $nChar +3);
						 
									
				} //fine values
				
				
			}
			
			//echo $objMessageJData->getValue("message_type");
			
			return $objMessageJData;
		}
		
		
		public function send($objMessageJData) {
			
			$intId = $this->getTimestamp(); //id = timestamp
					
			$strMessage = "{\"id\":" . $intId . ",\"values\":[{"; //intenstazione id + values
					
					
			for($i = 0; $i < $objMessageJData->length(); $i++) {
						
				$strMessage .= $objMessageJData->getKey(i) . ":"; //inerisco la chiave nel messaggio
							
				if (is_int($objMessageJData->getValue(i))) { //type integer
						
					$strMessage .= $objMessageJData->getValue(i);
							
				} else if (is_double($objMessageJData->getValue(i))) { //type double
						
					$strMessage .= $objMessageJData->getValue(i);
						
				} else if (is_bool($objMessageJData->getValue(i))) { //boolean traducon i valori impostati
							
					if ($objMessageJData->getValue(i)) {
								
						$strMessage .= Jack::MESSAGE_BOOLEAN_TRUE;
								
					} else {
								
						$strMessage .= Jack::MESSAGE_BOOLEAN_FALSE;
								
					}
							
				} else if (is_string($objMessageJData->getValue(i))) { //stringa aggiungo "" inzio e fine
							
					$strMessage .= "\"" . $objMessageJData->getValue(i) . "\"";
							
							
				} /*else { //nessun tipo predefinito
							
							messageString += message.getValue(i).toString();
				}*/
						
				$strMessage .= ","; //metto la virgola per separaere i valori
						
			}
					
					
			$strMessage = substr($strMessage, 0, strlen($strMessage) -1); //elimino l'ultima virgola
					
			$strMessage .= "}]}"; //messaggio in stringa creato
					
				
					
					
			$this->objSendMessageBuffer->put(id, messageString); //carico il mex nel buffer (sarà spedito automaticamente)
			$this->objSendMessageBufferJData->put(id, message);
					
		}
				
		
		
		//verifico l'ack
		private function checkAck($objMessageJData) {
					
			$intId = $objMessageJData->getValue(Jack::MESSAGE_ID);
					
			if ($this->objSendMessageBufferJData->length() > 0) { //verifico che esistano messaggi in attesa di conferma
						
				if ($this->objSendMessageBufferJData->containsKey($intId)) { //verifico che l'id conetnuto ack esista
							
					if ($this->objSendMessageBuffer->containsKey($intId)) {
						$this->objSendMessageBuffer->remove($intId); //elimino il messaggio (CONFERMO) non verrà più reinviato
					} 
							
					$this->onReceiveAck($this->objSendMessageBufferJData->getValue($intId)); //richiamo metodo astratto invocato al ricevimento di un ack
					
					$this->objSendMessageBufferJData->remove($intId);		
							
				}
			}
		}
				
		
		//creo ack e lo invio
		private function sendAck($objMessageJData) { //invio ack
			//echo "sendack jack";
			//echo "<br>";
			//echo $objMessageJData->getValue(Jack::MESSAGE_ID);
			//echo "<br>";
			
			$strMessage = "{\"" . Jack::MESSAGE_ID . "\":";
			
			$strMessage .= $objMessageJData->getValue(Jack::MESSAGE_ID);
			
			$strMessage .= ",\"" . Jack::MESSAGE_TYPE_ACK . "\":1}";
					
			$this->objSendAckBuffer->put($objMessageJData->getValue(Jack::MESSAGE_ID), $strMessage); //carico il mex nel buffer (sarà spedito automaticamente)	
					
			//echo $strMessage;
					
		}
				
		/*
		public function loop() { //luppa per simulare il thread

			$this->getMessagePollingFunction();
			
			$this->sendMessagePollingFunction();
	
		} */
	
		private function getMessagePollingFunction() { //funzione che sostituisce il thread per il get dei messaggi
		
			//if (!$this->stopPolling) {
				
				$strMessage = $this->objJTM->receive();
				
				if (strlen($strMessage) > 0) {
					
					$this->execute($strMessage);
					
				}
				
			//}
			
		}
		
		
		private function sendMessagePollingFunction() { //" " " per inviare i messaggi
		
			//if (!$this->stopPolling) {
			
				if ($this->objSendAckBuffer->moveToFirst()) { //invio ack
					
					//echo "length: " . $this->objSendAckBuffer->length();
					//echo "<br>";
				
					do {
					
						$this->objJTM->send($this->objSendAckBuffer->getValue());
						
						//$this->objSendAckBuffer->remove();
					
					} while ($this->objSendAckBuffer->moveToNext());
					
					
					$this->objSendAckBuffer = new HashMap();
					
					//echo "length: " . $this->objSendAckBuffer->length();
					//echo "<br>";
					
					
				}
				
				if ($this->objSendMessageBuffer->moveToFirst()) { //invio messaggi
					
					do {
						
						$intKey = $this->objSendMessageBuffer->getKey(); //prelevo la chiave (id)
						
						if ($this->objSendMessageTimer->containsKey($intKey)) { //controllo se il messaggio è già stato inviato (presenza del buffer)
						
							if ((time() - $this->objSendMessageTimer->getValue($intKey)) > $this->TIME_BEFORE_RESEND) { //controllo se è scaduto il tempo di attesa prima di reinviare il messaggio
								
								$this->objJTM->send($this->objSendMessageBuffer->getValue()); //invio il messaggio
								
								$this->objSendMessageTimer->remove(key);
								
								$this->objSendMessageTimer->put(key, millis());
								
							}
						
						
						} else { //messaggio da inviare per la prima volta
						
							$this->objJTM->send($this->objSendMessageBuffer->getValue()); //invio il messaggio
							
							if (!$this->SEND_ONE_TIME) {//controllo se non è da inviare una volta sola
								
								$this->objSendMessageTimer->put($intKey, time());
								
							} else { //messaggio da inviare una sola volta
							
								$this->objSendMessageBuffer->remove(key);
								
							}
						
						}
					
					} while ($this->objSendMessageBuffer->moveToNext());
					
				
				}
			//}
		
		}
		
		
		
		//FUNZIONI PER LE STRINGHE (DA PASSARE IN UNA CLASSE)
		private function startsWith($haystack, $needle) {
    		return !strncmp($haystack, $needle, strlen($needle));
		}

		private function endsWith($haystack, $needle) {
   			$length = strlen($needle);
    		if ($length == 0) {
        		return true;
    		}

    		return (substr($haystack, -$length) === $needle);
		}
		
		private function contains($haystack, $needle) {
			if (strstr($haystack, $needle, false) != NULL) {
				return true;	
			} else {
				return false;	
			}
		}
		//FINE FUNZIONI STRINGHE
		
		
	}//fine classe
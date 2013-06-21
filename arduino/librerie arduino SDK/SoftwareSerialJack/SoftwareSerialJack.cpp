#include "SoftwareSerialJack.h"

//SOFTWARE SERIAL JACK

char SoftwareSerialJack::MESSAGE_START_CHARACTER = '<'; //carattere inzio messaggio
char SoftwareSerialJack::MESSAGE_FINISH_CHARACTER = '>'; //carattere fine messaggio

SoftwareSerialJack::SoftwareSerialJack(int RX, int TX, long baudRate) {
	
	messageBuffer = "";
	
	softwareSerial = new SoftwareSerial(RX, TX);
	
	softwareSerial->begin(baudRate);
}

void SoftwareSerialJack::send(String message) { //invia il messaggio

	String messageToSend = String(MESSAGE_START_CHARACTER);
	
	messageToSend += message;
	
	messageToSend += String(MESSAGE_FINISH_CHARACTER);
	
	Serial.println(messageToSend);
	
	softwareSerial->print(messageToSend);
	
	//softwareSerial->print("<{\"id\":123456789,\"values\":{[\"GSR\":2200,\"TIMESTAMP\":123456789,\"TEMPERATURE\":38.5]}}>");
	
}

int SoftwareSerialJack::available() { //restituisce true se ci sono dati da elaborare

	return 1;
	
}

String SoftwareSerialJack::receive() { //deve restituire il messaggio da passare a Jack
		
	String message = ""; //variabile che conterra il messaggio da restituire
		
	int nCharIncorrect = 0; //caratteri incorretti all'inizio
	int nCharMessage = 0; //caratteri del mesaggio
	
	////Serial.print("new: ");
    while (softwareSerial->available() > 0) { //scarico i caratteri ricevuti nel buffer
	
		//char c = char(softwareSerial->read());
	
		messageBuffer += char(softwareSerial->read());
	  
		////Serial.print(c);
    }
	
	//if (messageBuffer.length() > 0) //Serial.println(messageBuffer);
			
	//controllo che non ci siani caratteri non validi prima del messaggio (mi fermo quando trovo il char di inizio)
	for(int i = 0; i < messageBuffer.length() && messageBuffer.charAt(i) != MESSAGE_START_CHARACTER; i++) {
				
		nCharIncorrect++;
			
	}					
	
	////Serial.println(nCharIncorrect);
	
	if (nCharIncorrect < messageBuffer.length()) { //trovato il carattere di inizio messaggio
				
		for (int i = nCharIncorrect + 1; i < messageBuffer.length() && messageBuffer.charAt(i) != MESSAGE_FINISH_CHARACTER; i++) {
			nCharMessage++;
				
			message += messageBuffer.charAt(i);
				
		}
				
				
		if ((nCharIncorrect + nCharMessage + 2) < messageBuffer.length() && messageBuffer.charAt(nCharIncorrect + nCharMessage + 2 - 1) == MESSAGE_FINISH_CHARACTER) {
					
			//E' presente un messaggio
			nCharMessage += 2;
					
		} else {
					
			//non è presente un messaggio azzero message e il numero dei suoi caratteri
			message = "";
			nCharMessage = 0;
					
		}
				
	} //fine prelievo messaggio
			
			
	messageBuffer = messageBuffer.substring(nCharIncorrect + nCharMessage); //elimino il messaggio e i caratteri errati dal buffer;
		
		//if (message.length() > 0) Serial.println("message ricevuto: " + message);
		
	return message; //restituisco il messaggio o stringa vuota}
	
	//return "";

}
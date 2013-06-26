#ifndef __SOFTWARE_SERIAL_JACK_H__
#define __SOFTWARE_SERIAL_JACK_H__

#include <Arduino.h>
#include <SoftwareSerial.h>
#include <Jack.h>

//class SoftwareSerial;

//class JTrasmissionMethod;

class SoftwareSerialJack : public JTrasmissionMethod {

	private:
		
		SoftwareSerial * softwareSerial;
		String messageBuffer;
		
		static char MESSAGE_START_CHARACTER; //carattere inzio messaggio
		static char MESSAGE_FINISH_CHARACTER; //carattere fine messaggio

	public:
	
		SoftwareSerialJack(int RX, int TX, long baudRate);
		~SoftwareSerialJack();
		
		String receive(); //deve restituire il messaggio da passare a Jack
		void send(String message); //invia il messaggio
		
		int available(); //restituisce true se ci sono dati da elaborare

};


#endif
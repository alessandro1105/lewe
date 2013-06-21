#ifndef __JACK_H__
#define __JACK_H__


#include <Arduino.h>
#include <HashMap.h>
#include <Wrapper.h>


class JData;

//wrapper per jData
class JDataWrapper : public Wrapper {

	public:

		JDataWrapper(JData * value); //costruttore per riempire l'oggetto

		int type(); //funzione che restituisce il tipo di wrapper

		JData * getJData(); //funzione per estrarre il valore

	private:

		JData * value; //contenitore del valore

};

//"interfaccia" per il mezzo di trasmissione
class JTrasmissionMethod {

	public:
		
		virtual String receive(); //deve restituire il messaggio da passare a Jack
		virtual void send(String message); //invia il messaggio
		virtual int available(); //restituisce true se ci sono dati da ricevere nel buffer

};

//classe usata come contenitore per i messaggi
class JData {

	private:
		int size;
		
		HashMap<String, Wrapper *> * data;
		HashMap<int, String> * indexes;
		
		void addWrapper(String key, Wrapper * wrapper);
		
		Wrapper * getWrapper(String key);
		Wrapper * getWrapper(int index);
		
		int getWrapperType(String key);
		int getWrapperType(int index);

	public:
		
		static const int LONG = 0;
		static const int DOUBLE = 1;
		static const int BOOLEAN = 2;
		static const int STRING = 3;
		static const int JDATA = 4;
		
		JData();
		
		//adder
		void addLong(String key, long value);
		void addDouble(String key, double value);
		void addBoolean(String key, int value);
		void addString(String key, String value);
		void addJData(String key, JData * value);
		
		//get 
		long getLong(String key);
		long getLong(int index);
		
		double getDouble(String key);
		double getDouble(int index);
		
		String getDoubleString(String key);
		String getDoubleString(int index);
		
		int getBoolean(String key);
		int getBoolean(int index);
		
		String getString(String key);
		String getString(int index);
		
		JData * getJData(String key);
		JData * getJData(int index);
		
		//get key
		String getKey(int index);
		
		//get type stored
		int getType(String key);
		int getType(int index);
		
		//get size
		int length();
		
		//contains
		int containsKey(String key);

};


//classe Jack per il protocollo
class Jack {

	private:
	
		long TIME_BEFORE_RESEND; //tempo reinvio ms
		int SEND_ONE_TIME; //inviare i pacchetti una volta sola
		
		static const int TIMER_POLLING = 100;
		
		static String MESSAGE_TYPE; //key tipo messaggio
		static String MESSAGE_DATA; //messaggio dati
		
		static String MESSAGE_ID; //id messaggio
		
		static String MESSAGE_TYPE_ACK; //tipo ack
		static String MESSAGE_TYPE_DATA; //tipo dati
		
		static String MESSAGE_BOOLEAN_TRUE; //simbolo invio boolean true
		static String MESSAGE_BOOLEAN_FALSE;//simbolo invio boolean falso
		
		
		JTrasmissionMethod * mmJTM; //contiene il metodo di trasmissione da usare
		
		HashMap<long, String> * sendMessageBuffer; //buffer per i messaggi da inviare
		HashMap<long, long> * sendMessageTimer; //buffer per i timer per i mex da inviare
		
		HashMap<long, JData *> * sendMessageBufferJData; //buffer contenente il messaggi das inviare nel formato JData
		
		HashMap<long, String> * sendAckBuffer; //buffer degli ack da inviare
		
		HashMap<long, long> * idMessageReceived; //buffer contiene gli id dei messaggi già ricevuti
		
		int stopPolling; //booloean che indica se stoppare il polling

		void (* onReceive) (JData *); //puntatore a funzione OnReceive
		void (* onReceiveAck) (JData *); //puntatore a funzione OnReceiveAck
		
		long (* getTimestamp) (); //puntatore a funzione per ottenere il timestamp in long
		
		void getMessagePollingFunction(); //funzione che sostituisce il thread per il get dei messaggi
		void sendMessagePollingFunction(); //" " " per inviare i messaggi
		
		void execute(String message); //funzione che gestisce il protocollo
		
		int checkMessageAlreadyReceived(JData * message); //verifica se il messaggio è già stato ricevuto
		
		int validate(String message); //verifica se il messaggio è conforme al protocollo
		
		JData * getJDataMessage(String message); //preleva i dati dal messaggio e crea il messaggio nel formato JData
		
		void sendAck(JData * message); //invia l'ack di conferma
		
		void checkAck(JData * message); //controlla l'ack
		
	public:
	
		Jack(JTrasmissionMethod * mmJTM, void (* onReceive) (JData *), void (* onReceiveAck) (JData *), long (* getTimestamp) ()); //costruttore con mmJTM e funzione onRceive e OnReceiveAck
		
		Jack(JTrasmissionMethod * mmJTM, void (* onReceive) (JData *), void (* onReceiveAck) (JData *), long (* getTimestamp) (), long timeBeforeResend); //tempo per il reinvio
		
		Jack(JTrasmissionMethod * mmJTM, void (* onReceive) (JData *), void (* onReceiveAck) (JData *), long (* getTimestamp) (), int sendOneTime); //indica se effettuare il reinvio dei mex se non confermati
		
		void start(); //avvia il polling
		
		void stop(); //stoppa il polling
		
		void flushBufferSend(); //cancella i buffer contenente i messaggi da inviare
		
		void send(JData * message); //invia il messaggio
		
		void loop(); //luppa per simulare il thread
		
		//void remove(JData * message); //elimina un messaggio cancellato
		
		

};


#endif
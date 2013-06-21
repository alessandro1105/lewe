#include "Jack.h"


//JDATA WRAPPER

JDataWrapper::JDataWrapper(JData * value) { //construttore/incapsulatore

	this->value = value;

}

JData * JDataWrapper::getJData() { //restituisce il valore memorizzato

	return value;

}

int JDataWrapper::type() { //zrestituisco il tipo di wrapper

	return Wrapper::JDATA;

}


//JDATA

JData::JData() { //costruttore
	
	data = new HashMap<String, Wrapper *>();
	
	indexes = new HashMap<int, String>();
	
	size = 0;
	
}

//add wrapper
void JData::addWrapper(String key, Wrapper * wrapper) {
		
	data->put(key, wrapper);
	
	indexes->put(size, key);
	
	size++;
	
};

//get wrapper
Wrapper * JData::getWrapper(String key) {
	
	return data->getValue(key);

}
Wrapper * JData::getWrapper(int index) {
	
	return getWrapper(getKey(index));
	
}
		
int JData::getWrapperType(String key) {
	
	return getWrapper(key)->type();
	
}

int JData::getWrapperType(int index) {
	
	return getWrapperType(getKey(index));
	
}

//adder
void JData::addLong(String key, long value) {

	LongWrapper * wrapper = new LongWrapper(value);
	
	addWrapper(key, wrapper);
}

void JData::addDouble(String key, double value) {
	
	DoubleWrapper * wrapper = new DoubleWrapper(value);
	
	addWrapper(key, wrapper);

}

void JData::addBoolean(String key, int value) {

	BooleanWrapper * wrapper = new BooleanWrapper(value);
	
	addWrapper(key, wrapper);

}

void JData::addString(String key, String value) {
	
	StringWrapper * wrapper = new StringWrapper(value);
	
	addWrapper(key, wrapper);

}

void JData::addJData(String key, JData * value) {
	
	JDataWrapper * wrapper = new JDataWrapper(value);
	
	addWrapper(key, wrapper);

}

//get 
long JData::getLong(String key) {
	return static_cast<LongWrapper *>(getWrapper(key))->getLong();
}

long JData::getLong(int index) {
	return getLong(getKey(index));
}
		
double JData::getDouble(String key) {
	return static_cast<DoubleWrapper*>(getWrapper(key))->getDouble();
}

double JData::getDouble(int index) {
	return getDouble(getKey(index));
}

String JData::getDoubleString(String key) {
	return static_cast<DoubleWrapper*>(getWrapper(key))->getString();
}

String JData::getDoubleString(int index) {
	return getDoubleString(getKey(index));
}
	
int JData::getBoolean(String key) {
	return static_cast<BooleanWrapper*>(getWrapper(key))->getBoolean();
}

int JData::getBoolean(int index) {
	return getBoolean(getKey(index));
}
		
String JData::getString(String key) {
	return static_cast<StringWrapper*>(getWrapper(key))->getString();
}

String JData::getString(int index) {
	return getString(getKey(index));
}

JData * JData::getJData(String key) {
	return static_cast<JDataWrapper*>(getWrapper(key))->getJData();
}

JData * JData::getJData(int index) {
	return getJData(getKey(index));
}
		
//get key
String JData::getKey(int index) {

	return this->indexes->getValue(index);

}
		
//get type stored
int JData::getType(String key) {
	
	switch (getWrapperType(key)) {
		
		case Wrapper::LONG:
			return LONG;
			break;
			
		case Wrapper::DOUBLE:
			return DOUBLE;
			break;
		
		case Wrapper::BOOLEAN:
			return BOOLEAN;
			break;
			
		case Wrapper::STRING:
			return STRING;
			break;
			
		case Wrapper::JDATA:
			return JDATA;
			break;
			
	
	}

}

int JData::getType(int index) {

	return getType(getKey(index));


}
		
//get size
int JData::length() {
	
	return size;
}


int JData::containsKey(String key) {
	
	return data->containsKey(key);
	
}

		
//JACK

String Jack::MESSAGE_TYPE = "message_type"; //key tipo messaggio
String Jack::MESSAGE_DATA = "message_data"; //messaggio dati
		
String Jack::MESSAGE_ID = "id"; //id messaggio
		
String Jack::MESSAGE_TYPE_ACK = "ack"; //tipo ack
String Jack::MESSAGE_TYPE_DATA = "values"; //tipo dati
		
String Jack::MESSAGE_BOOLEAN_TRUE = "t"; //simbolo invio boolean true
String Jack::MESSAGE_BOOLEAN_FALSE = "f";//simbolo invio boolean falso
		
Jack::Jack(JTrasmissionMethod * mmJTM, void (* onReceive) (JData *), void (* onReceiveAck) (JData *), long (* getTimestamp) ()) { //costruttore con mmJTM e funzione onRceive e OnReceiveAck
	
	//valori di default
	TIME_BEFORE_RESEND = 1000; //tempo reinvio ms
	
	SEND_ONE_TIME = 0; //inviare i pacchetti una volta sola
	
	stopPolling = 1;
	
	this->mmJTM = mmJTM;
	
	//creo buffer
	
	sendMessageBuffer = new HashMap<long, String>();
	sendMessageTimer = new HashMap<long, long>();
	sendMessageBufferJData = new HashMap<long, JData *>();
	sendAckBuffer = new HashMap<long, String>();
	
	idMessageReceived = new HashMap<long, long>();
	
	
	//imposto le funzioni
	
	this->onReceive = onReceive;
	
	this->onReceiveAck = onReceiveAck;
	
	this->getTimestamp = getTimestamp;
	
}

Jack::Jack(JTrasmissionMethod * mmJTM, void (* onReceive) (JData *), void (* onReceiveAck) (JData *), long (* getTimestamp) (), long timeBeforeResend) { //tempo per il reinvio
	
	
	//valori di default
	TIME_BEFORE_RESEND = timeBeforeResend; //tempo reinvio ms
	
	SEND_ONE_TIME = 0; //inviare i pacchetti una volta sola
	
	stopPolling = 1;
	
	this->mmJTM = mmJTM;
	
	//creo buffer
	
	sendMessageBuffer = new HashMap<long, String>();
	sendMessageTimer = new HashMap<long, long>();
	sendMessageBufferJData = new HashMap<long, JData *>();
	sendAckBuffer = new HashMap<long, String>();
	
	idMessageReceived = new HashMap<long, long>();
	
	
	//imposto le funzioni
	
	this->onReceive = onReceive;
	
	this->onReceiveAck = onReceiveAck;
	
	this->getTimestamp = getTimestamp;
	
}

Jack::Jack(JTrasmissionMethod * mmJTM, void (* onReceive) (JData *), void (* onReceiveAck) (JData *), long (* getTimestamp) (), int sendOneTime) { //indica se effettuare il reinvio dei mex se non confermati
	
	//valori di default
	TIME_BEFORE_RESEND = 1000; //tempo reinvio ms
	
	SEND_ONE_TIME = sendOneTime;
	
	stopPolling = 1;
	
	this->mmJTM = mmJTM;
	
	//creo buffer
	
	sendMessageBuffer = new HashMap<long, String>();
	sendMessageTimer = new HashMap<long, long>();
	sendMessageBufferJData = new HashMap<long, JData *>();
	sendAckBuffer = new HashMap<long, String>();
	
	idMessageReceived = new HashMap<long, long>();
	
	
	//imposto le funzioni
	
	this->onReceive = onReceive;
	
	this->onReceiveAck = onReceiveAck;
	
	this->getTimestamp = getTimestamp;
}

void Jack::start() { //avvia il polling

	stopPolling = 0;
}

void Jack::stop() { //stoppa il polling

	stopPolling = 1;
}
		

void Jack::flushBufferSend() { //cancella i buffer contenente i messaggi da inviare		
		
	sendMessageBuffer = new HashMap<long, String>();
	sendMessageBufferJData = new HashMap<long, JData *>();
}

void Jack::execute(String message) { //funzione che gestisce il protocollo
			
	if (validate(message)) {
		
		JData * messageJData = getJDataMessage(message);
		
		if (messageJData->getString(MESSAGE_TYPE).equals(MESSAGE_TYPE_DATA)) {
		
			if (!checkMessageAlreadyReceived(messageJData)) {
				
				(* onReceive) (messageJData->getJData(MESSAGE_DATA));
			}
			
		} else {
				
			checkAck(messageJData);
		}
		
	}
		
}

int Jack::checkMessageAlreadyReceived(JData * message) { //verifica se il messaggio è già stato ricevuto
		
	if (!message->containsKey(MESSAGE_ID)) { //validazione veloce
		return 1;
	}
	
	sendAck(message);
	
	if (!idMessageReceived->containsKey(message->getLong(MESSAGE_ID))) {
		
		idMessageReceived->put(message->getLong(MESSAGE_ID), 0); //da ottimizzare con una lista o un vettore
		
		return 0;
		
	} else {
		return 1;
	}
		
}

int Jack::validate(String message) { //verifica se il messaggio è conforme al protocollo
		
	//da implementare
		
	return 1;
		
}

void Jack::sendAck(JData * message) { //invia l'ack di conferma

	String messageString = "{\"";
	messageString += MESSAGE_ID;
	messageString += "\":";
	messageString += message->getLong(MESSAGE_ID);
	messageString += ",\"";
	messageString += MESSAGE_TYPE_ACK;
	messageString += "\":1}";
	
	sendAckBuffer->put(message->getLong(MESSAGE_ID), messageString);
}

void Jack::checkAck(JData * message) { //controlla l'ack
	
	long id = message->getLong(MESSAGE_ID);
	
	if (sendMessageBufferJData->length() > 0) {
		
		if (sendMessageBufferJData->containsKey(id)) {
			
			if (sendMessageBuffer->containsKey(id)) {
				sendMessageBuffer->remove(id);
			}
			
			Serial.println("message Confirmed");
			
			(* onReceiveAck) (sendMessageBufferJData->getValue(id));
			
			sendMessageBufferJData->remove(id);
		}
		
	}
		
}

void Jack::loop() { //luppa per simulare il thread

	if (mmJTM->available()) {
		getMessagePollingFunction();
	}
	
	sendMessagePollingFunction();

}

void Jack::getMessagePollingFunction() { //funzione che sostituisce il thread per il get dei messaggi

	if (!stopPolling) {
		
		String message = mmJTM->receive();
		
		if (message.length() > 0) {
			
			Serial.println("gmpf: " + message);
			
			execute(message);
			
		}
		
	}
	
}


void Jack::sendMessagePollingFunction() { //" " " per inviare i messaggi

	if (!stopPolling) {
	
		if (sendAckBuffer->moveToFirst()) { //invio ack
		
			do {
			
				mmJTM->send(sendAckBuffer->getValue());
				
				sendAckBuffer->remove();
			
			} while (sendAckBuffer->moveToNext());
			
		}
		
		if (sendMessageBuffer->moveToFirst()) { //invio messaggi
			
			do {
				
				long key = sendMessageBuffer->getKey(); //prelevo la chiave (id)
				
				if (sendMessageTimer->containsKey(key)) { //controllo se il messaggio è già stato inviato (presenza del buffer)
				
					if ((millis() - sendMessageTimer->getValue(key)) > TIME_BEFORE_RESEND) { //controllo se è scaduto il tempo di attesa prima di reinviare il messaggio
						
						mmJTM->send(sendMessageBuffer->getValue()); //invio il messaggio
						
						sendMessageTimer->remove(key);
						
						sendMessageTimer->put(key, millis());
						
					}
				
				
				} else { //messaggio da inviare per la prima volta
				
					mmJTM->send(sendMessageBuffer->getValue()); //invio il messaggio
					
					if (!SEND_ONE_TIME) {//controllo se non è da inviare una volta sola
						
						sendMessageTimer->put(key, millis());
						
					} else { //messaggio da inviare una sola volta
					
						sendMessageBuffer->remove(key);
						
					}
				
				}
			
			} while (sendMessageBuffer->moveToNext());
			
		
		}
	}

}


void Jack::send(JData * message) { //invia il messaggio
	
	long id = (* getTimestamp) ();
	
	String messageString = "{\"" + MESSAGE_ID + "\":";
	messageString += id;
	messageString += ",\"" + MESSAGE_TYPE_DATA + "\":[{";
	
	for(int i = 0; i < message->length(); i++) {
		
		messageString += "\"";
		messageString += message->getKey(i);
		messageString += "\":";
		
		int type = message->getType(i);
		
		if (type == JData::LONG) {
		
			messageString += message->getLong(i);
		
		} else if (type == JData::DOUBLE) {
			
			messageString += message->getDoubleString(i);
		
		} else if (type == JData::BOOLEAN) {
			
			if (message->getBoolean(i)) {
				messageString += MESSAGE_BOOLEAN_TRUE;
			} else {
				messageString += MESSAGE_BOOLEAN_FALSE;
			}
		
		} else if (type == JData::STRING) {
		
			messageString += "\"";
			messageString += message->getString(i);
			messageString += "\"";
			
		}
		
		messageString += ",";
		
	}
	
	
	messageString = messageString.substring(0, messageString.length() -1);
	
	messageString += "}]}";
	
	
	sendMessageBuffer->put(id, messageString);
	
	sendMessageBufferJData->put (id, message);
	
}

JData * Jack::getJDataMessage(String message) { //preleva i dati dal messaggio e crea il messaggio nel formato JData
	
	JData * messageJData = new JData();
	
	String temp = "";
	String temp2 = "";
	
	int nChar = 0;
	
	int value;
	
	message = message.substring(2, message.length());
	
	for(int i = 0; i < 2; i++) {
	
		temp = "";
		
		//Serial.println(message);
		
		if (message.startsWith(MESSAGE_ID)) { //id
			
			message = message.substring(MESSAGE_ID.length() + 2, message.length());
			
			for(int x = 0; message.charAt(x) != ','; x++) { //prelevo l'id dal messaggio
				
				temp += message.charAt(x);
				
			}
			
			if (i < 1) {
				message = message.substring(temp.length() + 2, message.length());
			}
			
			LongWrapper lw(temp);
			
			messageJData->addLong(MESSAGE_ID, lw.getLong());
			
			//Serial.println("id: " + temp);
			
			
		} else if (message.startsWith(MESSAGE_TYPE_ACK)) { //ack
		
			messageJData->addString(MESSAGE_TYPE, MESSAGE_TYPE_ACK);
			
			if (i < 1) {
				message = message.substring(MESSAGE_TYPE_DATA.length() + 5, message.length());
			}
			
			//Serial.println("ack");
		}
	
	}
	
	return messageJData;

}

































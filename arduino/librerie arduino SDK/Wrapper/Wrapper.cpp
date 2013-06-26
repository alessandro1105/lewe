#include "Wrapper.h"


//BOOLEAN WRAPPER
BooleanWrapper::BooleanWrapper(int value) { //construttore/incapsulatore

	if (value > 0) {

		this->value = 1;

	} else {

		this->value = 0;
	}

}

int BooleanWrapper::getBoolean() { //restituisce il valore memorizzato

	return value;

}

int BooleanWrapper::type() { //zrestituisco il tipo di wrapper

	return Wrapper::BOOLEAN;
	
}

//DOUBLE WRAPPER
DoubleWrapper::DoubleWrapper(double value) { //construttore/incapsulatore

	this->value = value;

}

DoubleWrapper::DoubleWrapper(String value) { //construttore/incapsulatore

	char buffer[value.length() +1];
	
	value.toCharArray(buffer, sizeof(buffer));
	

	this->value = atof(buffer);

}

double DoubleWrapper::getDouble() { //restituisce il valore memorizzato

	return value;

}

String DoubleWrapper::getString(int decimal) { // restituisce il double convertito in stringa
	
	long integerPart;
	double decimalPart;
	
	int moltiplier = 1;
	
	if (decimal > 5) {
		decimal = 5;
	}
	
	integerPart = floor(value);
	
	decimalPart = (value - integerPart);
	

	
	while (((int) decimalPart) - decimalPart < 0) {		
		
		if (moltiplier > (decimal)) { //verifico l'arrotondamento con una cifra dopo di quelle richieste
		
			if (((decimalPart - floor(decimalPart)) * 10) >= 5) { //arrotondamento necessario per eccesso
				
				String decimalPartString = String((long) decimalPart); //trasformo in stringa la parte decimale
				
				int size = decimalPartString.length(); //ricavo la lunghezza
				
				decimalPart += 1; //sommo 1 perchè devo arrotondare
				
				decimalPartString = String((long) decimalPart); //ritrasformo in stringa il numero arrotondato
				
				if (size != decimalPartString.length()) { //overlow (tutti erano a 9)
				
					integerPart += 1; //aggiungo 1 alla parte intera
					
					decimalPart = 0; //azzero la parte decimale
				
				}
				
			}
			
			break;
		}
		
		decimalPart *= 10;
		moltiplier++;
		
	}

	return String(integerPart) + "." + String((long) decimalPart);
	
	/*
	
	String string;
	
	int decimalPlaces = precision;
	
	double input = value;
	
	if(decimalPlaces!=0){
string = String((int)(input*pow(10,decimalPlaces)));
if(abs(input)<1){
if(input>0)
string = "0"+string;
else if(input<0)
string = string.substring(0,1)+"0"+string.substring(1);
}
return string.substring(0,string.length()-decimalPlaces)+"."+string.substring(string.length()-decimalPlaces);
}
else {
return String((int)input);
}
	
	
	
	/*
	
	long integerPart;
	double decimalPart;
	
	int moltiplier = 1;
	
	integerPart = floor(value);
	
	decimalPart = (value - integerPart);// - integerPart * pow(10, precision);
	
	//Serial.println(integerPart);
	//Serial.println(decimalPart);
	//Serial.println((int) decimalPart);
	
	while (((int) decimalPart) - decimalPart < 0) {
		
		decimalPart *= 10;
		moltiplier++;
		
		//Serial.println(decimalPart);4
		//Serial.println(moltiplier);
		
		if (moltiplier > precision) 
			break;
		
	}
	
	//Serial.println(decimalPart);
	
	//Serial.println((long) decimalPart);

	return String(integerPart) + "." + String((long) decimalPart);
	
	*/
}

String DoubleWrapper::getString() { // restituisce il double convertito in stringa

	return getString(5);
}

int DoubleWrapper::type() { //zrestituisco il tipo di wrapper

	return Wrapper::DOUBLE;

}


//LONG WRAPPER
LongWrapper::LongWrapper(long value) { //construttore/incapsulatore

	this->value = value;

}

LongWrapper::LongWrapper(String value) { //construttore/incapsulatore

	char buffer[value.length() +1];
	
	value.toCharArray(buffer, sizeof(buffer));
	

	this->value = atol(buffer);

}

long LongWrapper::getLong() { //restituisce il valore memorizzato

	return value;

}

int LongWrapper::type() { //zrestituisco il tipo di wrapper

	return Wrapper::LONG;

}


//STRING WRAPPER
StringWrapper::StringWrapper(String value) { //construttore/incapsulatore

	this->value = value;

}

String StringWrapper::getString() { //restituisce il valore memorizzato

	return value;

}

int StringWrapper::type() { //zrestituisco il tipo di wrapper

	return Wrapper::STRING;

}
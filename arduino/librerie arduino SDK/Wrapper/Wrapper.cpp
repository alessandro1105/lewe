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

String DoubleWrapper::getString(int precision) { // restituisce il double convertito in stringa
	
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
		
		//Serial.println(decimalPart);
		//Serial.println(moltiplier);
		
		if (moltiplier > precision) 
			break;
		
	}
	
	//Serial.println(decimalPart);
	
	//Serial.println((long) decimalPart);

	return String(integerPart) + "." + String((long) decimalPart);
}

String DoubleWrapper::getString() { // restituisce il double convertito in stringa
	
	return getString(6);
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
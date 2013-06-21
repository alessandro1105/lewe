#ifndef __WRAPPER_H__
#define __WRAPPER_H__

#include <Arduino.h>

class Wrapper {

	public:

		virtual int type(); //funzione che restituisce il tipo di wrapper
		
		static const int STRING = 0; //String
		static const int DOUBLE = 1; //Long
		static const int BOOLEAN = 2; //bool
		static const int LONG = 3; //long
		static const int JDATA = 4; //jdata
		
};

class BooleanWrapper : public Wrapper {

	public:

		BooleanWrapper(int value); //costruttore per riempire l'oggetto

		int type(); //funzione che restituisce il tipo di wrapper

		int getBoolean(); //funzione per estrarre il valore

	private:

		int value; //contenitore del valore

};

class DoubleWrapper : public Wrapper {

	public:

		DoubleWrapper(double value); //costruttore per riempire l'oggetto
		
		DoubleWrapper(String value);

		int type(); //funzione che restituisce il tipo di wrapper

		double getDouble(); //funzione per estrarre il valore
		
		String getString(); //use default precision
		String getString(int precision); //use default precision

	private:

		double value; //contenitore del valore

};

class LongWrapper : public Wrapper {

	public:

		LongWrapper(long value); //costruttore per riempire l'oggetto
		
		LongWrapper(String value);

		int type(); //funzione che restituisce il tipo di wrapper

		long getLong(); //funzione per estrarre il valore

	private:

		long value; //contenitore del valore

};

class StringWrapper : public Wrapper {

	public:

		StringWrapper(String value); //costruttore per riempire l'oggetto

		int type(); //funzione che restituisce il tipo di wrapper

		String getString(); //funzione per estrarre il valore

	private:

		String value; //contenitore del valore

};

#endif

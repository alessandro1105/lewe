#include <SoftwareSerial.h>
SoftwareSerial mySerial(10, 6); // RX, TX
unsigned long T;
String Buffer;                                       // buffer Rx/Tx

void setup() {
  Serial.begin(9600);
  mySerial.begin(9600);

  
}



void loop() {
  
 Buffer = "";
 T = millis();
  
  while (millis() - T < 10)  {
    while (Serial.available() > 0) {
      Buffer += char(Serial.read());
    }
  }

  if (Buffer.length() > 0) {                  // transmit to Serial Monitor        
    mySerial.print(Buffer);
  }
  
  
  
 
 Buffer = "";
 T = millis();
  
  while (millis() - T < 10)  {
    while (mySerial.available() > 0) {
      Buffer += char(mySerial.read());
    }
  }

  if (Buffer.length() > 0) {                  // transmit to Serial Monitor        
    Serial.print(Buffer);
  }
  
  
 
  
 
}

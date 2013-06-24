/*
Copyright [2013] [Lewe]

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
   
   
   Creator:
     Alessandro Pasqualini
     alessandro.pasqualini.1105@gmail.com
*/

#include <ColorLCDShield.h>

#include <Wire.h>
#include <RTClib.h>

#include <HashMap.h>
#include <SoftwareSerial.h>
#include <Wrapper.h>
#include <Jack.h>
#include <SoftwareSerialJack.h>

//COSTANTI

//GENERALI
const long INTERVAL_BETWEEN_SURVEY = 30000; //DEBUG
//const long INTERVAL_BETWEEN_SURVEY = 300000; //5 min (tempo in ms)

const int DEBUG = 1; //1 stampa info di debug, 0 non stampa niente

//BT
const int BT_RX_PIN = 10; //bt rx
const int BT_TX_PIN = 6; //bt tx
const long BT_BAUDRATE = 9600; //bt baudrate

//LM35
const int TEMP_SWITCH_PIN = 12; //pin per comandare l'accensione del sensore
const int TEMP_PIN = A0; //pin sensore 

//GSR
const int GSR_SWITCH_PIN = 7; //pin per comandare l'accensione
const int GSR_PIN = A1; //pin del sensore
//commentato senza analogReference
const int GSR_MIN = 620;//220; //valore minimo
const int GSR_MAX = 1023;//640; //valore max


//LCD
const long TIMER_BEFORE_AUTO_SLEEP_LCD = 15000; //15 sec

//COSTANTI PER JACK
const long TIME_BEFORE_RESEND_MESSAGE = 5000; //5 sec

//CHIAVI PER IL PROTOCOLLO
const String TIMESTAMP_KEY = "TIMESTAMP";
const String TEMPERATURE_KEY = "TEMPERATURE";
const String GSR_KEY = "GSR";




//VARIABILI

//GENERALI
double lastSurveyTemperature = 0;
long lastSurveyGsr = -1;


//JACK
SoftwareSerialJack * mmJTM; //variabile contenente il metodo di trasmissione
Jack * jack; //varibile per il protocollo jack


//VARIABILI PER RTC
RTC_DS1307 RTC;


//VARIABILI PER LCD
LCDShield lcd;
int lcdButtons[3] = {3, 4, 5};  // S1 = 3, S2 = 4, S3 = 5

//timer per l'autosleep
long lcdTimerSleep;

//stato lcd
int lcdState;

//contenitori per il tempo
int lcdDay;
int lcdMonth;
int lcdYear;
int lcdHour;
int lcdMinute;
int lcdSecond;

//contenitori sensori
double lcdTemperature = 0;
long lcdGsr = -1;


//variabili per l'invio dei dati
long timeLastSurvey; //varibile contenente il tempo per scandire gli invii
int sensorState;



//FUNZIONI

//JACK
void onReceive(JData * message) {} //funzione richiamata alla ricezione di un messaggio dati
void onReceiveAck(JData * message) {} //funzione richiamata alla conferma di un messaggio



//RILEVAZIONI
long getTimestamp() { //preleva il timestamp da RTC in unix timestamp
  
  long timestamp = RTC.now().unixtime();
  
  if (DEBUG) {
    Serial.print("Timestamp: ");   
    Serial.println(timestamp);  
  }
  
  timestamp -= 7200; //correzzione timestamp (avanti di due ore)
  
  return timestamp; //get unix timestamp (secondi a partire da mezzanotte di );
  
}

long getGSR() { //preleva il valore del sensore GSR
  
  int gsr = analogRead(GSR_PIN); //leggo il sensore
  
  if (DEBUG) {
    Serial.print("GSR tick: ");   
    Serial.println(gsr);  
  
  }
  
  
  gsr -= GSR_MIN;
  
  if (gsr < 0) { 
    gsr = 0;  
  }

  gsr = (int) (gsr * 100.0 / (GSR_MAX - GSR_MIN));
 
  
  return gsr; //ritorno il valore in percentuale
}


double getTemperature() { //preleva il valore della temperatura da LM35 con 1 cifra dopo la virgola
  
  double temp = analogRead(TEMP_PIN); //prelevo la lettura dal sensore
  
  temp = (temp * 1.1 / 1023.0) * 100.0; //converto la temperatura letta in gradi
  
  
  DoubleWrapper dw(temp); //inserisco la temperatura nel wrapper
  
  DoubleWrapper dw2(dw.getString(1)); //creo un nuovo wrapper con la temperatura convertita in stringa con un decimale
  
  temp = dw2.getDouble();
  
  if (DEBUG) {
    Serial.print("Temp: ");   
    Serial.println(temp);  
  }
  
  
  return temp; //restituisco la temperatura con un decimale

}

void getSurvey() { //funzione che esegue una rilevazione
  
  JData * survey = new JData(); // creo il contenitore per i dati
  
  //memorizzo le ultime rilevazioni
  lastSurveyTemperature = getTemperature();
  lastSurveyGsr = getGSR(); 
  
  survey->addLong(TIMESTAMP_KEY, getTimestamp()); //inserisco il timestamp 
  survey->addLong(GSR_KEY, lastSurveyGsr); //inserisco il gsr
  survey->addDouble(TEMPERATURE_KEY, lastSurveyTemperature); //inserisco la temperatura
  
  if (DEBUG) {   
    Serial.println("survey getted");  
  }
  
  
  jack->send(survey); //invio il messaggio
  
  
  if (DEBUG) {   
    Serial.println("survey send");  
  }
  
}

//FUNZIONI PER LCD
void setupLCD() {
  
  DateTime now = RTC.now();
  
  for (int i = 0; i < 3; i++) {
    
    pinMode(lcdButtons[i], INPUT); //pulsanti lcd input
    digitalWrite(lcdButtons[i], HIGH); //asttivo resistenza pull-ìup
    
  }
  
  lcd.init(PHILIPS); //inizializzo il display 
  lcd.contrast(60); //imposto il contrasto
  
  lcd.clear(WHITE); //pulisco lo shermo (lo coloro di nero)
  
  
  if (DEBUG) {   
    Serial.println("lcd preparato");  
  }
  
  lcdState = 1; //accendo lo schermo (1 accesso 0 spento)
  
  lcdTimerSleep = millis(); //faccio partire il timer per l'autospegnimento
  
  lcdDay = now.day();
  lcdMonth = now.month();
  lcdYear = now.year();
  lcdHour = now.hour();
  lcdMinute = now.minute();
  lcdSecond = now.second();
  
  
  lcd.setStr("LEWE", 5, 50, RED, WHITE);
  
  lcd.setStr(String(lcdDay).length() == 1 ? String("0" + String(lcdDay)) : String(lcdDay), 25, 16, BLACK, WHITE); //giorno
  lcd.setStr("-", 25, 36, BLACK, WHITE); //sep
  lcd.setStr(String(lcdMonth).length() == 1 ? String("0" + String(lcdMonth)) : String(lcdMonth), 25, 48, BLACK, WHITE); //mese
  lcd.setStr("-", 25, 68, BLACK, WHITE); //sep
  lcd.setStr(String(lcdYear).length() == 1 ? String("0" + String(lcdYear)) : String(lcdYear), 25, 80, BLACK, WHITE); //anno
  
  lcd.setStr(String(lcdHour).length() == 1 ? String("0" + String(lcdHour)) : String(lcdHour), 42, 28, BLACK, WHITE); //ora
  lcd.setStr(":", 42, 46, BLACK, WHITE); //sep
  lcd.setStr(String(lcdMinute).length() == 1 ? String("0" + String(lcdMinute)) : String(lcdMinute), 42, 55, BLACK, WHITE); //minuti
  lcd.setStr(":", 42, 73, BLACK, WHITE); //sep
  lcd.setStr(String(lcdSecond).length() == 1 ? String("0" + String(lcdSecond)) : String(lcdSecond), 42, 82, BLACK, WHITE); //secondi
  
  lcd.setStr("TEMP: ", 70, 20, BLACK, WHITE); //TEMP
  lcd.setStr("NA", 70, 65, BLUE, WHITE);

  lcd.setStr("SUDO: ", 95, 20, BLACK, WHITE); //SUDO
  //lcd.setStr("8", 95, 74, BLUE, WHITE);
  lcd.setStr("NA", 95, 65, BLUE, WHITE);

}

void tickLCD() {
  
  DateTime now = RTC.now();
  
  if (!digitalRead(lcdButtons[0])) { //tasto che spegne il display
    lcdState = 0;
    lcd.off();
  } if (!digitalRead(lcdButtons[1])) { //tasto che accende il display
    lcdState = 1;
    lcd.on(); 
      
    lcdTimerSleep = millis();
  } 
 
  if (lcdState && (millis() - lcdTimerSleep > TIMER_BEFORE_AUTO_SLEEP_LCD)) { //sleep automatico se display acceso e timer scaduto
    
    lcdState = 0;
    lcd.off();
  }
  
  
  //verifico se è cambiato l'orario e la data
  
  if (lcdDay != now.day()) { //giorno
    lcdDay = now.day();
    
    lcd.setStr(String(lcdDay).length() == 1 ? String("0" + String(lcdDay)) : String(lcdDay), 25, 16, BLACK, WHITE); //giorno
  }
  
  if (lcdMonth != now.month()) { //mese
    lcdMonth = now.month();
    
    lcd.setStr(String(lcdMonth).length() == 1 ? String("0" + String(lcdMonth)) : String(lcdMonth), 25, 48, BLACK, WHITE); //mese
  }
  
  if (lcdYear != now.year()) { //anno
    lcdYear = now.year();
    
    lcd.setStr(String(lcdYear).length() == 1 ? String("0" + String(lcdYear)) : String(lcdYear), 25, 80, BLACK, WHITE); //anno
  }
  
  if (lcdHour != now.hour()) { //ora
    lcdHour = now.hour();
    
    lcd.setStr(String(lcdHour).length() == 1 ? String("0" + String(lcdHour)) : String(lcdHour), 42, 28, BLACK, WHITE); //ora
  }
  
  if (lcdMinute != now.minute()) { //minuti
    lcdMinute = now.minute();
    
    lcd.setStr(String(lcdMinute).length() == 1 ? String("0" + String(lcdMinute)) : String(lcdMinute), 42, 55, BLACK, WHITE); //minuti
  }
  
  if (lcdSecond != now.second()) { //secondi
    lcdSecond = now.second();
    
    lcd.setStr(String(lcdSecond).length() == 1 ? String("0" + String(lcdSecond)) : String(lcdSecond), 42, 82, BLACK, WHITE); //secondi
  }
  
  //verifico se ci sono stati cambiamenti nei dati dei sensori salvati
  if (lcdTemperature != lastSurveyTemperature) { //temperatura (uso il wrapper per la conversione double -> string
    lcdTemperature = lastSurveyTemperature;
    
    DoubleWrapper tempToString(lcdTemperature);
    
    lcd.setStr(tempToString.getString(1) + " C", 70, 65, BLUE, WHITE);
  }
  
  if (lcdGsr != lastSurveyGsr) { //gsr
    lcdGsr = lastSurveyGsr;
    
    lcd.setStr(String(lcdGsr).length() == 1 ? String("0" + String(lcdGsr) + " %") : String(lcdGsr) + " %", 95, 65, BLUE, WHITE); //secondi
  }
  
}


//SLEEP E WAKEUP PER I SENSORI

void setupSensor() {
  
  analogReference(INTERNAL1V1); //abbasso la sensibilità dei sensori da 5v a 1.1v
  
  //setup RTC
  Wire.begin();
  RTC.begin();

  if (! RTC.isrunning()) {
    Serial.println("RTC is NOT running!");
    
    RTC.adjust(DateTime(__DATE__, __TIME__)); //setto RTC con il data e ora di compilazione dello sketch
  }
    
}

void wakeupSensor() { //funzione che sveglia i sensor

  digitalWrite(TEMP_SWITCH_PIN, HIGH); //abilito i sensori accendendoli alimentandoli a 5v
  digitalWrite(GSR_SWITCH_PIN, HIGH);

  sensorState = 1;
  
  if (DEBUG) {   
    Serial.println("sensori risveglio");  
  }
  
}

void sleepSensor() { //funzione che addormenta i sensori
  
  digitalWrite(TEMP_SWITCH_PIN, LOW); //spengo i sensori togliendo la tensione
  digitalWrite(GSR_SWITCH_PIN, LOW);
  
  sensorState = 0;
  
  if (DEBUG) {   
    Serial.println("sensori addormentati");  
  }
 
}

int getSensorState() { //ritorna lo stato dei sensori
  
  return sensorState; 
}



void setup() {
  
  Serial.begin(9600); //avvio Serial
  
  setupSensor(); //funzione che esegue il setup dei sensori.
  
  setupLCD(); //funzione di inizializzazione dello schermo lcd
  
  mmJTM = new SoftwareSerialJack(BT_RX_PIN, BT_TX_PIN, BT_BAUDRATE); //creo il metodo di trasmissione
  
  if (DEBUG) {   
    Serial.println("mmJTM creato");  
  }
  
  jack = new Jack(mmJTM, &onReceive, &onReceiveAck, &getTimestamp, TIME_BEFORE_RESEND_MESSAGE); //creo jack passando mmJTM e le funzioni
  
  if (DEBUG) {   
    Serial.println("Jack creato");  
  }
  
  jack->start(); //faccio partire il protocollo
  
  if (DEBUG) {   
    Serial.println("Jack partito");  
  }
  
  sleepSensor(); //addormento i sensori
  
  //wakeupSensor();
 
  timeLastSurvey = millis(); //imposto la variabile con il tempo così il prossimo invio è tra 10 
  
  
  //Serial.println(getTimestamp());
  
}
  

void loop() {
  
  
  //tickLCD();
  
  wakeupSensor();
  
  DoubleWrapper dw(getTemperature()); //inserisco la temperatura nel wrapper
  
  Serial.println(dw.getString()); //creo un nuovo wrapper con la temperatura convertita in stringa con un decimale
  
  //getTemperature();
  
  //delay(1000);
  
 /* */
  
  /*
  tickLCD();
  
  
  jack->loop(); //funzione che invia i messaggi e riceve i messaggi (simulazione thread)
  
  long now = millis();
  
  if (now - timeLastSurvey > INTERVAL_BETWEEN_SURVEY) { //scaduto il tempo di attesa tra invii (invio la rilevazione)
    
    if (DEBUG) {   
      Serial.println("invio rilevazione");  
    }
    
    getSurvey(); //prelevo le rilevazioni e le invio

    sleepSensor(); //addormento i sensori

    timeLastSurvey = now;
    
  } else if (!getSensorState() && (now - timeLastSurvey > (int) (INTERVAL_BETWEEN_SURVEY / 2))) { //sono a metà del tempo di intervallo e sveglio i sensori
    
    if (DEBUG) {   
      Serial.println("preparazione sensori");  
    }
    
    
    wakeupSensor();
   
  }
*/
}

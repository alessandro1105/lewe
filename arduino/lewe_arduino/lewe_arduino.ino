#include <Wire.h>
#include <RTClib.h>

#include <HashMap.h>
#include <SoftwareSerial.h>
#include <Wrapper.h>
#include <Jack.h>
#include <SoftwareSerialJack.h>

//COSTANTI

//GENERALI
//const long INTERVAL_BETWEEN_SURVEY = 30000; //30 secondi per debug
const long INTERVAL_BETWEEN_SURVEY = 300000; //5 min (tempo in ms)

const int DEBUG = 1; //1 stampa info di debug, 0 non stampa niente

//BT
const int BT_RX_PIN = 10; //bt rx
const int BT_TX_PIN = 6; //bt tx
const long BT_BAUDRATE = 9600; //bt baudrate

//LM35
const int TEMP_SWITCH_PIN = 8; //pin per comandare l'accensione del sensore
const int TEMP_PIN = A0; //pin sensore 

//GSR
const int GSR_SWITCH_PIN = 7; //pin per comandare l'accensione
const int GSR_PIN = A1; //pin del sensore
const long GSR_R = 1000000; //1MOhm
const double GSR_VCC = 5.0; //ddp applicata al sensore



//COSTANTI PER JACK
const long TIME_BEFORE_RESEND_MESSAGE = 5000;

//CHIAVI PER IL PROTOCOLLO
const String TIMESTAMP_KEY = "TIMESTAMP";
const String TEMPERATURE_KEY = "TEMPERATURE";
const String GSR_KEY = "GSR";




//VARIABILI

//JACK
SoftwareSerialJack * mmJTM; //variabile contenente il metodo di trasmissione
Jack * jack; //varibile per il protocollo jack


//VARIABILI PER RTC
RTC_DS1307 RTC;

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
  
  return timestamp; //get unix timestamp (secondi a partire da mezzanotte di );
  
}

long getGSR() { //preleva il valore del sensore GSR
  

  int gsr = analogRead(GSR_PIN); //leggo il sensore
  
  if (DEBUG) {
    Serial.print("GSR tick: ");   
    Serial.println(gsr);  
  
  }
  
  gsr = (int) ((gsr * 100.0) / 1023.0); //traformo la lettura in percentuale
  
  /*
  double vGSR = analogRead(GSR_PIN); //leggo il sensore
  vGSR = vGSR * GSR_VCC / 1023.0; //trasformo la lettura in volt
  long RGSR = (GSR_R / vGSR * GSR_VCC) - GSR_R;
  
 
  if (RGSR == -2147483648) {
    RGSR = 2147483647;   
  }

  
  if (DEBUG) {
    Serial.print("GSR Volt: ");   
    Serial.println(vGSR);  
  }
  
  if (DEBUG) {
    Serial.print("GSR R: ");   
    Serial.println(RGSR);  
  }
  
  
  
  return RGSR;//ricavo il valore della resistenza e la restituisco
  */
  
  
  return gsr; //ritorno il valore in percentuale
}

double getTemperature() { //preleva il valore della temperatura da LM35 con 1 cifra dopo la virgola
  
  double temp = analogRead(TEMP_PIN); //prelevo la lettura dal sensore
  
  temp = (temp * 5.0 / 1023.0) * 100.0; //converto la temperatura letta in gradi
  
  double decimalPart = temp - floor(temp); //ricavo la parte decimale
  
  temp = floor(temp) + (floor(decimalPart * 10) / 10); //sommo la parte intera e una cifra dopo la virgola
  
  if (DEBUG) {
    Serial.print("Temp: ");   
    Serial.println(temp);  
  }
  
  
  return temp; //restituisco la temperatura con un decimale

}

void getSurvey() { //funzione che esegue una rilevazione
  
  JData * survey = new JData(); // creo il contenitore per i dati
  
  survey -> addLong(TIMESTAMP_KEY, getTimestamp()); //inserisco il timestamp 
  survey -> addLong(GSR_KEY, getGSR()); //inserisco il gsr
  survey -> addDouble(TEMPERATURE_KEY, getTemperature()); //inserisco la temperatura
  
  if (DEBUG) {   
    Serial.println("survey getted");  
  }
  
  
  jack -> send(survey); //invio il messaggio
  
  
  if (DEBUG) {   
    Serial.println("survey send");  
  }
  
}


//SLEEP E WAKEUP PER I SENSORI

void setupSensor() {
  
  //analogReference(INTERNAL1V1); //abbasso la sensibilità dei sensori da 5v a 1.1v
  
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
  
  mmJTM = new SoftwareSerialJack(BT_RX_PIN, BT_TX_PIN, BT_BAUDRATE); //creo il metodo di trasmissione
  
  if (DEBUG) {   
    Serial.println("mmJTM creato");  
  }
  
  jack = new Jack(mmJTM, &onReceive, &onReceiveAck, &getTimestamp, TIME_BEFORE_RESEND_MESSAGE); //creo jack passando mmJTM e le funzioni
  
  if (DEBUG) {   
    Serial.println("Jack creato");  
  }
  
  jack -> start(); //faccio partire il protocollo
  
  if (DEBUG) {   
    Serial.println("Jack partito");  
  }
  
  sleepSensor(); //addormento i sensori
    
  timeLastSurvey = millis(); //imposto la variabile con il tempo così il prossimo invio è tra 10 
  
}
  

void loop() {
  
  jack -> loop(); //funzione che invia i messaggi e riceve i messaggi (simulazione thread)
  
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
  
}

package com.lewe.app.lewe.bluetooth.service;

import com.lewe.app.jack.JData;
import com.lewe.app.jack.Jack;
import com.lewe.app.logger.Logger;

import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.Bundle;
import android.os.IBinder;
import android.util.Log;


public class LeweBluetoothService extends Service {
	
	public static final String INTENT_FILTER_COMMAND = "com.lewe.app.lewe.bluetooth.service.LeweBluetoothService.COMMAND"; //intent filter in entrata per i comandi
	
	public static final String INTENT_FILTER_NEW_DATA = "com.lewe.app.lewe.bluetooth.service.LeweBluetoothService.NEW_DATA"; //intent filter in uscita per i dati ricevuti
	
	public static final String INTENT_FILTER_CONNECTION_STATUS = "com.lewe.app.lewe.bluetooth.service.LeweBluetoothService.CONNECTION_STATUS"; //intent filter per lo stato sulla connessione
	
	public static final String COMMAND_SET_MAC = "command_set_mac"; //set mac
	public static final String COMMAND_START_CONNECTION = "command_start_connection"; //start connection
	public static final String COMMAND_STOP_CONNECTION = "command_stop_connection"; //stop connection
	public static final String COMMAND_SET_AUTOCONNECTION = "command_set_autoconnection"; //set autoconnection se connessione persa o fallita fino a stop
	
	public static final String CONNECTION_STARTED = "connection_started"; //avviso connessione avviata
	public static final String CONNECTION_STOPPED = "connection_stopped"; //avviso connessione fermata
	public static final String CONNECTION_LOST = "connection_lost"; //avvissio connessione persa
	public static final String CONNECTION_FAILED = "connection_failed"; //avviso connessione fallita
	
	
	Jack jack;
	BluetoothSerialService mmJTM;
	
	String mmJTMMac = "";
	
	BroadcastReceiver receiver; //broadcast receiver
	
	public static boolean started = false;
	
	@Override
	public IBinder onBind(Intent arg0) {
		// TODO Auto-generated method stub
		return null;
	}
	
	
	@Override
	public void onCreate() {
		
		Logger.d("LBS", "creazione...");
		
		receiver = new BroadcastReceiver() {

			@Override
			public void onReceive(Context context, Intent intent) {
				
				final Intent i = intent;
				
				Thread execute = new Thread() {
		    		
		    		public void run() {
		    			
		    			Logger.d("LBS", "intent ricevuto");
						
						Bundle extras = i.getExtras();
								
						Logger.d("LBS", "intent command");
								
						if (extras.containsKey(COMMAND_SET_MAC)) { //set mac
									
							mmJTMMac = extras.getString(COMMAND_SET_MAC);
									
						}
								
						if (extras.containsKey(COMMAND_SET_AUTOCONNECTION)) { //set autoconnection
									
							mmJTM.setAutoConnection(extras.getBoolean(COMMAND_SET_AUTOCONNECTION));
									
						}
								
						if (extras.containsKey(COMMAND_START_CONNECTION)) { //start connection (o start o stop non tutte e due)
									
							if (mmJTM.getState() != BluetoothSerialService.STATE_NONE) { //se sono connesso mi sconnetto
										
								jack.stop(); //stoppo message
										
								mmJTM.stop(); //stoppo mmJTM
										
							}
									
							if (mmJTMMac != "") { //se mac è settato faccio partire mmjtm (a connessione avvevuta viene avviato anche message
										
								mmJTM.connect(mmJTMMac);
							}
									
						} else if (extras.containsKey(COMMAND_STOP_CONNECTION)) { //stop connection
									
							jack.stop(); //stoppo message
									
							mmJTM.stop(); //stoppo mmJTM
								
						}
								
					}
							
		    		
		    	};
		    	
		    	execute.start();
				
				
			}
        	
        };
		
        
        registerReceiver(receiver, new IntentFilter(INTENT_FILTER_COMMAND)); //registro il receiver collegato all'intent filter
        
        
        
        //creo message e mmJTM
        mmJTM = new BluetoothSerialService() {

			@Override
			public void onStateChange(int s) { //cosa fare se lo stato cambia
				// TODO Auto-generated method stub
				
				final int state = s;
				
				Thread execute = new Thread() {
		    		
		    		public void run() {
		    			
		    			if (state == BluetoothSerialService.STATE_CONNECTED) { //se sono connesso faccio partire Jack
		    				jack.start();
							
							Intent intent = new Intent(INTENT_FILTER_CONNECTION_STATUS); //creo intent diretto a LS
							
							intent.putExtra(CONNECTION_STARTED, 0); //connessione iniziata
							
							sendBroadcast(intent);
						}
		    			
		    			
		    		}
		    		
		    	};
		    	
		    	execute.start();
				
			}

			@Override
			public void onConnectionLost() { //cosa fare se si perde la connessione
				// TODO Auto-generated method stub
				
				Thread execute = new Thread() {
		    		
		    		public void run() {
		    			
		    			//invio intent con l'indicazione di connessione fallita
						
						Intent intent = new Intent(INTENT_FILTER_CONNECTION_STATUS); //creo intent diretto a LS
						
						
						intent.putExtra(CONNECTION_LOST, 0); //connessione persa
						
						
						sendBroadcast(intent);
		    			
		    			
		    		}
		    		
		    	};
		    	
		    	execute.start();
			
			}

			@Override
			public void onConnectionFailed() { //cosa fare se fallisce la connessione
				// TODO Auto-generated method stub
				
				Thread execute = new Thread() {
		    		
		    		public void run() {
		    			
		    			//invio intent con indicazione connessione fallita
						
						Intent intent = new Intent(INTENT_FILTER_CONNECTION_STATUS); //creo intent diretto a LS
						
						intent.putExtra(CONNECTION_FAILED, 0); //connessione fallita
						
						sendBroadcast(intent);
		    			
		    			
		    		}
		    		
		    	};
		    	
		    	execute.start();
			}
        	
        };
		
		mmJTM.start();
		 
		
		jack = new Jack(mmJTM) { //creo message

			@Override
			public void onReceive(JData message) {
				
				final JData data = message;
				
				Thread execute = new Thread() {
		    		
		    		public void run() {
		    			
		    			Logger.d("LBS", "JData received");
						//converto i dati in intent e lo invio
						
						Intent intent = new Intent(INTENT_FILTER_NEW_DATA);
						
						for(int i = 0; i <data.size(); i++) {
							
							if ((String) data.getKey(i) != Jack.MESSAGE_TYPE) { //elimino message_type (errore di jack)
							
								if (data.getValue(i) instanceof String) { //string
								
									intent.putExtra((String) data.getKey(i), (String) data.getValue(i));
								
								} else if (data.getValue(i) instanceof Long) { //integer
								
									intent.putExtra((String) data.getKey(i), (Long) data.getValue(i));
								
								} else if (data.getValue(i) instanceof Double) { //double
								
									intent.putExtra((String) data.getKey(i), (Double) data.getValue(i));
								
								} else if (data.getValue(i) instanceof Boolean) { //boolean
									
									intent.putExtra((String) data.getKey(i), (Boolean) data.getValue(i));
									
								}
							}
						}
						
						sendBroadcast(intent); //invio intent a LS
						
						Logger.d("LBS", "JData sent");
		    			
		    			
		    		}
		    		
		    	};
		    	
		    	execute.start();

			}

			@Override
			public void onReceiveAck(JData messageConfirmed) { //invocato al ricevimento di un ack
				// TODO Auto-generated method stub
				
			}

			@Override
			protected long getTimestamp() {
				// TODO Auto-generated method stub
				return System.currentTimeMillis();
			}
			
		};
		
		
		
		Logger.d("LBS", "creato");
		
		started = true; //indico che il servizio è partito
	}

	@Override
	public void onDestroy() {
		
		Logger.d("LBS", "distruzione...");
		
		
		unregisterReceiver(receiver); //scollego il broadcast receiver
		
		
		Logger.d("LBS", "stop message");
		jack.stop(); //stoppo la classe message
		
		
		Logger.d("LBS", "stop mmJTM");
		mmJTM.stop(); //stoppo mmJTM
		
		
		
		Logger.d("LBS", "distrutto");
		
		started = false; //indico che il servizio non è attivo
	}
	
	
	public int onStartCommand(Intent intent, int flags, int startId) {
		
		//Logger.d("LBS", "executing...");
		
		
		
		//Logger.d("LBS", "executed");
		
		
		
		return Service.START_STICKY; //servizio attivo obligatoriamente
	}
	

}
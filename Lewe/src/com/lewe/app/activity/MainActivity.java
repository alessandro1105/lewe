package com.lewe.app.activity;


import com.lewe.app.R;
import com.lewe.app.config.Config;
import com.lewe.app.database.Database;
import com.lewe.app.lewe.database.service.LeweDatabaseService;
import com.lewe.app.lewe.service.LeweService;
import com.lewe.app.logger.Logger;

import android.os.Bundle;
import android.preference.PreferenceManager;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.bluetooth.BluetoothAdapter;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageButton;

@SuppressLint("NewApi")
public class MainActivity extends Activity {
	
	public static final String INTENT_FILTER_DATA_FROM_DATABASE = "com.lewe.app.MainActivity.DATA_FROM_DATABASE";
	
	
	//variabili usate per verificare se il bt è abilitato o è necessario abilitarlo
	private boolean mEnablingBT;
	private BluetoothAdapter mBluetoothAdapter;
	
	private static final int REQUEST_ENABLE_BT = 10;

	
	//broadcast receiver
	BroadcastReceiver exitReceiver; //bcr per comando uscita
	BroadcastReceiver newData; //bcr per nuovi dati da LEWE
	
	
	
	//icone sensori
	
	SensorIconLayout sensorTemperature;
	SensorIconLayout sensorGsr;
	
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);		

		SharedPreferences sharedPreferences = PreferenceManager.getDefaultSharedPreferences(MainActivity.this); //usato per prelevare dati dei sensori on load		
		
		//DICHIARAZIONI PER LA GRAFICA
		
		requestWindowFeature(Window.FEATURE_CUSTOM_TITLE); //custom title
		
		setContentView(R.layout.activity_main);
		
		
		getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.custom_title_home); //setto il mio custom title
		
		
		ImageButton titleSettingsButton = (ImageButton) findViewById(R.id.custom_title_settings);
		
		
		titleSettingsButton.setOnClickListener( new View.OnClickListener() { //on click listener settings button sul titolo

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
		    			
		    	Intent intent = new Intent(MainActivity.this, PreferencesMainActivity.class); //creo intent per aprire activity dei settaggi
						
				startActivity(intent);		//avvio l'activity
				
			}
			
			
		});
		
		
		//impostazione icone sensori
		sensorTemperature = (SensorIconLayout) findViewById(R.id.temperature); //prelevo sensore temperatura
		sensorGsr = (SensorIconLayout) findViewById(R.id.gsr); //sensore gsr
				
		sensorTemperature.draw(); //imposto il custom layout
		sensorGsr.draw();
				
		sensorTemperature.setTitle(getString(R.string.sensor_temperature_icon_title));
		sensorTemperature.setLogo(R.drawable.temperature_logo);
		
				
		sensorGsr.setTitle(getString(R.string.sensor_gsr_icon_title));
		sensorGsr.setLogo(R.drawable.gsr_logo);
		
		
		
		//prelevato dal db (se non disponibile Nd)
		sensorTemperature.setValue(sharedPreferences.getString(Config.SENSOR_KEY_TEMPERATURE, getString(R.string.sensor_value_not_available))); //prelevo i valori dalle preferenze
		sensorGsr.setValue(sharedPreferences.getString(Config.SENSOR_KEY_GSR, getString(R.string.sensor_value_not_available))); //prelevo i valori dalle preferenze
		
		
		//imposto gli eventi onClick per aprire i grafici
		
		sensorTemperature.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				
				Thread execute = new Thread() {
		    		
					public void run() {
						
						Intent intent = new Intent(MainActivity.this, ChartActivity.class);
						
						intent.putExtra(Config.SENSOR_KEY_TYPE, Config.SENSOR_KEY_TEMPERATURE);
						
						startActivity(intent);
						
					}
					
				
				};
				
				execute.start();
				
			}
		});
		
		sensorGsr.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				
				
				Intent intent = new Intent(MainActivity.this, ChartActivity.class);
				
				intent.putExtra(Config.SENSOR_KEY_TYPE, Config.SENSOR_KEY_GSR);
				
				startActivity(intent);
				
			}
			
		});
		
		//FINE DICHIARAZIONI GRAFICHE		
				
		

		
		//INIZIO DICHIARAZIONI BCR
		
		//receiver per la richiesta di chiusura app
		exitReceiver = new BroadcastReceiver() {

			@Override
			public void onReceive(Context context, Intent intent) {

				finish();
				
			}
			
		};
		
		registerReceiver(exitReceiver, new IntentFilter(ExitActivity.INTENT_FILTER));
		
		
		
		//bcr per la ricezione di nuovi dati dal dipositrivo LEWE
		newData = new BroadcastReceiver() {

			@Override
			public void onReceive(Context context, Intent intent) {
				// TODO Auto-generated method stub
						
				Bundle extras = intent.getExtras();
				
				SharedPreferences.Editor sharedPreferencesEditor = PreferenceManager.getDefaultSharedPreferences(MainActivity.this).edit(); //editor preferenze
						
				//verifico se sono arrivati nuovi dati per sensore temperatura e li imposto
				if (extras.containsKey(Config.SENSOR_KEY_TEMPERATURE)) {
					sensorTemperature.setValue("" + extras.getDouble(Config.SENSOR_KEY_TEMPERATURE) + "°C");
							
					sharedPreferencesEditor.putString(Config.SENSOR_KEY_TEMPERATURE, "" + extras.getDouble(Config.SENSOR_KEY_TEMPERATURE) + " °C");		
							
				}
						
				//verifico se sono arrivati nuovi dati per sensore gsr e li imposto
				if (extras.containsKey(Config.SENSOR_KEY_GSR)) {
					sensorGsr.setValue("" + extras.getLong(Config.SENSOR_KEY_GSR) + " %");
					
					sharedPreferencesEditor.putString(Config.SENSOR_KEY_GSR, "" + extras.getLong(Config.SENSOR_KEY_GSR ) + " %");
				}
				
				
				
				sharedPreferencesEditor.commit(); //salvo le preferenze
				
			}
			
		};
		
		registerReceiver(newData, new IntentFilter(LeweService.INTENT_FILTER_NEW_DATA));	
		
		//FINE DICHIARAZIONE BCR
		
		
		
		
		//TEST BT
		
		//verifo che esista un bluetooth e chiedo di abilitarlo
		mBluetoothAdapter = BluetoothAdapter.getDefaultAdapter();
		
		if (mBluetoothAdapter == null) {
            finishDialogNoBluetooth();
            
            return;
		}
		
	} 
	
	
	@Override
	public void onStart() {
		super.onStart();
		
		Logger.d("MA", "starting...");
				
		mEnablingBT = false; //variabile che indica che non stiamo abilitando il bt
		
	}
	
	
	@Override
	public synchronized void onResume() {
		super.onResume();
		
		Logger.d("MA", "resuming...");
		
		if (!mEnablingBT) { // If we are turning on the BT we cannot check if it's enable
			
		    if ( (mBluetoothAdapter != null)  && (!mBluetoothAdapter.isEnabled()) ) {
		    	
		    	mEnablingBT = true; //variabile usata per non etrare nell'if se si sta abilitando il bt
		    	
		    	
		    	//invio intent per avviare il bt
        		Intent enableBtIntent = new Intent(BluetoothAdapter.ACTION_REQUEST_ENABLE);
        		
        		startActivityForResult(enableBtIntent, REQUEST_ENABLE_BT);
		    } else {
		    	
		    	startLeweService(); //bt abilitato quindi avvio LS
		    	
		    }
		    
		}
		
		Logger.d("MA", "resumed");
		
	}
	
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        
        switch (requestCode) {	    		
        		
        	//verifico che l'utente abbia accettato di attivare il bt 
        	case REQUEST_ENABLE_BT:
        		
        		Logger.d("MA", "risultato richiesta abilitazione bt");
        		
        		if (!(resultCode == Activity.RESULT_OK)) { //non ho abilitato il bt e chiudo app
        			
        			Logger.d("MA", "Bt non abilitato!");
        		
        			finishDialogNoBluetooth(); //richiamo dialog di chiusura
        			
        		} else { //ho accettato l'asbilitazione bt e avvio LS
        			
        			startLeweService(); //avvio LS
        		}
        		
        		break;
        		
        }
    }
	
	
	@Override
	public void onDestroy() {
		
		Logger.d("MA", "distruzione...");
		
		
		unregisterReceiver(exitReceiver); //scollego bcr per comando chiusura
		
		unregisterReceiver(newData); //scollego receiver per i nuovi dati
		
		
		Logger.d("MA", "distrutto");
		
		
		super.onDestroy();
		
	}

	
	
	//form che visualizza un mex che avverte la necessità del bt e chiude l'app
    public void finishDialogNoBluetooth() {
    	
    	Logger.e("MA", "finish dialog");
    	
        AlertDialog.Builder builder = new AlertDialog.Builder(this); //creo costruttore alert
        
        builder.setMessage(R.string.alert_dialog_no_bt) //inserisco il messaggio
        
        .setIcon(android.R.drawable.ic_dialog_info) //imposto icona informazioni
        
        .setTitle(R.string.app_name) //coem titolo metto ilo nome dell'app
        
        .setCancelable(false) //non è possibile cancellare il mex (obbligatorio accettarlo)
        
        .setPositiveButton(R.string.alert_dialog_ok, new DialogInterface.OnClickListener() { //imposto il listener sulla pressione di ok
        
        	public void onClick(DialogInterface dialog, int id) {
            
        		finish(); //chiudo l'app 	
            }
        });
        
        
        AlertDialog alert = builder.create(); //creo il warning

        alert.show(); //mostro il warning

    }
    
    
    //START SERVICE PRINCIPALE
	public void startLeweService() { //funzione per avviare LS
		
		//start Lewe Service
				
		if (!LeweService.started) {
			
			Thread execute = new Thread() {
	    		
	    		public void run() {
	    			
	    			Intent intent;
	    			
	    			Logger.d("MA", "LS avvio...");
					
	    			intent = new Intent(MainActivity.this, LeweService.class); //creo intent per avvio LS
	    					
	    			startService(intent); //avvio LS
	    					
	    			Logger.d("MA", "LS avviato");		
	    		}
	    		
	    	};
	    	
	    	execute.start();
		
		} else {
					
			Logger.d("MA", "LS già avviato");
					
					
		}
		
		
	}
    
    
}
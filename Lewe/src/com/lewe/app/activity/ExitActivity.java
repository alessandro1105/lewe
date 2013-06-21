package com.lewe.app.activity;

import com.lewe.app.logger.Logger;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;

public class ExitActivity extends Activity { 
	
	public static final String INTENT_FILTER = "com.lewe.app.ExitActivity";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		
		//activity che invia i comandi di chiusura dell'app
		
		Thread execute = new Thread() {
    		
    		public void run() {
    			
    			Intent intent = new Intent(INTENT_FILTER);
    			
    			sendBroadcast(intent);
    			
    			Logger.d("EA", "intent sent");
    			
    			finish();			
    		}
    		
    	};
    	
    	execute.start();
		
	}

}

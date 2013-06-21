package com.lewe.app.logger;

import com.lewe.app.config.Config;

import android.util.Log;

public class Logger {
	
	private static final boolean DEBUG = Config.DEBUG;
	
	public static void i(String tag, String msg) {
        if (DEBUG) {
            Log.i(tag, msg);
        }
    }
	
	public static void d(String tag, String msg) {
        if (DEBUG) {
            Log.d(tag, msg);
        }
    }
	
	public static void e(String tag, String msg) {
        if (DEBUG) {
            Log.e(tag, msg);
        }
    }
	
	public static void e(String tag, String msg, Exception e) {
        if (DEBUG) {
            Log.e(tag, msg, e);
        }
    }

}
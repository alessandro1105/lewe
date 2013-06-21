package com.lewe.app.config;

public class Config {
	
	public static final boolean DEBUG = true; //varibile che indica se mostrare le info di debug
	
	
	//chiavi per shared preferences
	public static final String SHARED_PREFERENCES_LEWE_DEVICE_MAC = "shared_preferences_lewe_device_mac"; //s.p. mac device lewe
	public static final String SHARED_PREFERENCES_LEWE_DEVICE_NAME = "shared_preferences_lewe_device_name"; //nome device lewe

	public static final String SHARED_PREFERENCES_WEB_CLOUD_ENABLED = "shared_preferences_web_cloud_enabled"; //s.p upload solo su rete wifi
	public static final String SHARED_PREFERENCES_WEB_CLOUD_ONLY_ON_WIFI = "shared_preferences_web_cloud_only_on_wifi"; //s.p upload solo su rete wifi
	public static final String SHARED_PREFERENCES_WEB_CLOUD_EMAIL = "shared_preferences_web_cloud_email"; //nome device lewe
	public static final String SHARED_PREFERENCES_WEB_CLOUD_PASSWORD = "shared_preferences_web_cloud_password"; //nome device lewe
	public static final String SHARED_PREFERENCES_WEB_CLOUD_URL = "shared_preferences_web_cloud_url"; //nome device lewe
	
	
	//chiavi per nomi sensori
	public static final String SENSOR_KEY_TEMPERATURE = "TEMPERATURE";
	public static final String SENSOR_KEY_GSR = "GSR";
	public static final String SENSOR_KEY_TIMESTAMP = "TIMESTAMP";
	public static final String SENSOR_KEY_TYPE = "sensor_type";
											      


}

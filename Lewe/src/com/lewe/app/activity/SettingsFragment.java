package com.lewe.app.activity;

import android.annotation.SuppressLint;
import android.os.Bundle;
import android.preference.Preference.OnPreferenceClickListener;
import android.preference.PreferenceFragment;

@SuppressLint({ "NewApi", "ValidFragment" })
public class SettingsFragment extends PreferenceFragment {
	
	int resource;

	public SettingsFragment(int resource) {
		this.resource = resource;
		
		//super();
	}
	
	
    @SuppressLint("NewApi")
	@Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
 
        // Load the preferences from an XML resource
        addPreferencesFromResource(resource);
        
    }
    /*
    
    public void setOnClickListener(String preferenceKey, OnPreferenceClickListener listener) {
    	
    	findPreference(preferenceKey).setOnPreferenceClickListener(listener);
    	
    }*/
}
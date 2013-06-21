package com.lewe.app.activity;

import com.lewe.app.R;

import android.content.Context;
import android.util.AttributeSet;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;


public class SensorIconLayout extends RelativeLayout {
	
	RelativeLayout sensorIcon;
	
	TextView sensorIconTitle;
	
	ImageView sensorIconLogo;
	
	TextView sensorIconValue;
	
	
	
	public SensorIconLayout(Context context) {
	    super(context);
	}

	public SensorIconLayout(Context context, AttributeSet attrs) {
	    super(context, attrs);
	}

	public SensorIconLayout(Context context, AttributeSet attrs, int defStyle) {
	    super(context, attrs, defStyle);
	}
	
	public void draw() {
		
		inflateLayout(R.layout.sensor_icon); //imposto il layout
		
		
		//creo le variabile per accedere ai componenti del layout
		sensorIcon = (RelativeLayout) findViewById(R.id.sensor_icon);
		
		sensorIconTitle = (TextView) findViewById(R.id.sensor_icon_title);
		
		sensorIconLogo = (ImageView) findViewById(R.id.sensor_icon_logo);
		
		sensorIconValue = (TextView) findViewById(R.id.sensor_icon_value);

	}
	
	
	public void setTitle(String title) {
		
		sensorIconTitle.setText(title);
		
	}
	
	public void setLogo(int resourceImage) {
		
		sensorIconLogo.setImageResource(resourceImage);
		
	}
	
	
	public void setValue(String value) {
		
		sensorIconValue.setText(value);
		
	}
	
	public void setOnClickListener(View.OnClickListener onClickListener) {
		
		sensorIcon.setOnClickListener(onClickListener);
		
	}
	
	
	private void inflateLayout(int layout) {
		
	    LayoutInflater inflater = (LayoutInflater) getContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
	    
	    inflater.inflate(layout, this);
	}

}

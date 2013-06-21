package com.lewe.app.database;

import java.io.Serializable;
import java.util.HashMap;


public class DatabaseResult implements Serializable{
	
	private HashMap<Integer, HashMap<String, Object>> result;
	
	private int size;
	
	public DatabaseResult() {
		
		result = new HashMap<Integer, HashMap<String, Object>>();
		
		size = 0;
		
	}
	
	public int addRecord() {
		
		HashMap<String, Object> record = new HashMap<String, Object>();
		
		result.put(size, record);
		
		size++;
		
		return size -1;
		
	}
	
	public void addRecordField(int index, String key, Object value) {
		
		result.get(index).put(key, value);
		
	}
	
	public Object getRecordField(int index, String key) {
		
		return result.get(index).get(key);
		
	}
	
	public int size() {
		return size;
	}

}
package com.lewe.app.jack;

import java.util.HashMap;

public class JData {
	
	private int size = 0;
	
	private HashMap<String, Object> data = new HashMap<String, Object>(); //buffer key value
	
	private HashMap<Integer, String> index = new HashMap<Integer, String>(); //buffer index key
	
	
	
	public JData() { //costruttore vuoto
		
		
	}
	
	
	public void add(String key, Object value) { //add
		
		data.put(key, value); //metto key value nel buffer 1
		
		index.put(this.size, key); //metto index key nel buffer 2
		
		this.size++; //incremento la size
		
	}
	
	public Object getValue(String key) {
		
		return data.get(key); //get from key
		
	}
	
	public Object getValue(int index) {
		
		return data.get(this.index.get(index)); //prelevo la chiave tramite indice e la uso per prelevare l'oggetto value
		
	}
	
	public Object getKey(int index) { //prelevo la chiave con index
		
		return this.index.get(index);
		
	}

	public int size() { //restituisco la dimensione
		
		return this.size;
	}
	
	public boolean containsKey(String key) { //metodo che controlla l'esistenza di una chiave
		
		return data.containsKey(key);
		
	}
	
}

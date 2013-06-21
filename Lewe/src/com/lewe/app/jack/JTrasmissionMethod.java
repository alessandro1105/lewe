package com.lewe.app.jack;

public interface JTrasmissionMethod { //interfaccia per mmJTM
	
	public void send(String message); //metodo per inviare messaggi
	
	public String receive(); //metodo per rivezione in polling
	
	public boolean available(); //metodo che indica se sono disponibili messaggi nel mezzo di trasmissione

}

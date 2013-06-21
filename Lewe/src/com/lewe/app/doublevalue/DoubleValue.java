package com.lewe.app.doublevalue;

public class DoubleValue<T, T2> {
	
	private T value1;
	private T2 value2;
	
	public DoubleValue(T value1, T2 value2) {
		
		this.value1 = value1;
		this.value2 = value2;
		
	}
	
	public T getValue1() {
		
		return this.value1;
	}
	
	public T2 getValue2() {
		
		return this.value2;
	}

}

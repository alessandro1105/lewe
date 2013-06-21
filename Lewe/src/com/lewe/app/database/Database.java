package com.lewe.app.database;

import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;

public class Database {
	
	private Context context; //context dell'applicazione (per aprire connessione con db)
	private SQLiteDatabase database; //database
	private DatabaseHelper databaseHelper; //helper per la connessione al db
	
	private static final String DATABASE_NAME = "mydb.db";
	private static final int DATABASE_VERSION = 1;
	
	
	private static final String DATABASE_CREATE_STATEMENT = "CREATE TABLE sensor (" +
															"id INTEGER PRIMARY KEY AUTOINCREMENT, " +
															"sensor_name TEXT NOT NULL, " +
															"sensor_value TEXT NOT NULL, " +
															"timestamp INTEGER NOT NULL, " +
															"updated INTEGER NOT NULL);";
															
	
															
	//campi del db (usati per la richiesta di esecuzione query)
	public static final String FIELD_ID = "id";
	public static final String FIELD_SENSOR_NAME = "sensor_name";
	public static final String FIELD_SENSOR_VALUE = "sensor_value";
	public static final String FIELD_TIMESTAMP = "timestamp";
	public static final String FIELD_UPDATED = "updated";
	
	public static final String TABLE_SENSOR = "sensor";
	
	
	public Database(Context context) {
		
		this.context = context;
		
		database = null;
		
		databaseHelper = null;
		
	}
	
	
	public void Open() { //apre la connessione con il db
		
		databaseHelper = new DatabaseHelper(context, DATABASE_NAME, DATABASE_VERSION, DATABASE_CREATE_STATEMENT);
		
		database = databaseHelper.getWritableDatabase();
		
	}
	
	public void close() { //chiude la connessione con il db
		
		
		if (databaseHelper != null)
			databaseHelper.close();
		
		database = null;
		
	}
	
	
	public Cursor executeQuery(String querySQL) {
		
		
		if (querySQL.startsWith("SELECT")) {
			
			return database.rawQuery(querySQL, null);
			
		} else {
			
			database.execSQL(querySQL);
			
			return null;
		}
		
	}
	
	
	

}

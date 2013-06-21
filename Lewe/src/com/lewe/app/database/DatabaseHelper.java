package com.lewe.app.database;
import com.lewe.app.logger.Logger;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteDatabase.CursorFactory;
import android.database.sqlite.SQLiteOpenHelper;


public class DatabaseHelper extends SQLiteOpenHelper {
	
	//private String databaseName = ""; //nome del db (passato nel costruttore)
	//private int databaseVersion = 1; //versione del db
	
	private String databaseCreateStatement = ""; //query di creazione del db se non esiste (passata nel costruttore)

	public DatabaseHelper(Context context, String databaseName, int databaseVersion, String databaseCreateStatement) {
		super(context, databaseName, null, databaseVersion);
		// TODO Auto-generated constructor stub
	
		this.databaseCreateStatement = databaseCreateStatement;
	}
	

	@Override
	public void onCreate(SQLiteDatabase database) {
		// TODO Auto-generated method stub
		
		database.execSQL(databaseCreateStatement);

	}

	@Override
	public void onUpgrade(SQLiteDatabase database, int oldVersion, int newVersion) {
		// TODO Auto-generated method stub
		
		Logger.d("DBH", "database upgrading...");
		
		//Logger.d("DBH", "database drop");
		
		//onCreate(database);
		
		Logger.d("DBH", "database upgraded");
		
	}

}
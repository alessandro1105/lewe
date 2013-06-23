package com.lewe.app.lewe.bluetooth.service;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.UUID;

import com.lewe.app.jack.JTrasmissionMethod;
import com.lewe.app.logger.Logger;



import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.bluetooth.BluetoothSocket;

/**
 * This class does all the work for setting up and managing Bluetooth
 * connections with other devices. It has a thread that listens for
 * incoming connections, a thread for connecting with a device, and a
 * thread for performing data transmissions when connected.
 */
public abstract class BluetoothSerialService implements JTrasmissionMethod {
	
	private static final long TIMER_RECONNECT = 200;

	private boolean autoConnection = false;
	
	private String deviceAddress;
	
	
	//buffer messaggi string
	private String messageBuffer = "";
	
	private boolean bufferAvailable = true;//variabile per controllare accesso al buffer
	
	
	private static final char MESSAGE_START_CHARACTER = '<'; //carattere inzio messaggio
	private static final char MESSAGE_FINISH_CHARACTER = '>';//carattere fine messaggio
	
	
	//public BlueTerm bt;
	
    // Debugging
    private static final String TAG = "BluetoothReadService";


	private static final UUID SerialPortServiceClass_UUID = UUID.fromString("00001101-0000-1000-8000-00805F9B34FB");

    // Member fields
    private final BluetoothAdapter mAdapter;

    private ConnectThread mConnectThread;
    private ConnectedThread mConnectedThread;
    private int mState;
    

    // Constants that indicate the current connection state
    public static final int STATE_NONE = 0;       // we're doing nothing
    public static final int STATE_LISTEN = 1;     // now listening for incoming connections
    public static final int STATE_CONNECTING = 2; // now initiating an outgoing connection
    public static final int STATE_CONNECTED = 3;  // now connected to a remote device

    /**
     * Constructor. Prepares a new BluetoothChat session.
     * @param context  The UI Activity Context
     * @param handler  A Handler to send messages back to the UI Activity
     */
    public BluetoothSerialService() {
       
    	mAdapter = BluetoothAdapter.getDefaultAdapter(); //imposto la radio bt;
        
        mState = STATE_NONE; //stato radio bt (disconnesso)
        
    }
    
    
    public void setAutoConnection(boolean autoConnection) {
    	
    	this.autoConnection = autoConnection;
    	
    }
    
    
    abstract public void onStateChange(int state);
    abstract public void onConnectionLost();
    abstract public void onConnectionFailed();
    
    
    
    /**
     * Set the current state of the chat connection
     * @param state  An integer defining the current connection state
     */
    private synchronized void setState(int state) {
    	
    	Logger.d(TAG, "setState() " + mState + " -> " + state);
        
    	//cambiamento di stato del bt service
        mState = state;
        
        
        onStateChange(state);
        

        // Give the new state to the Handler so the UI Activity can update
        //mHandler.obtainMessage(BlueTerm.MESSAGE_STATE_CHANGE, state, -1).sendToTarget();
    }

    /**
     * Return the current connection state. */
    public synchronized int getState() {
        return mState;
    }

    /**
     * Start the chat service. Specifically start AcceptThread to begin a
     * session in listening (server) mode. Called by the Activity onResume() */
    public synchronized void start() {
        Logger.d(TAG, "start");

        // Cancel any thread attempting to make a connection
        if (mConnectThread != null) {
        	
        	mConnectThread.cancel(); 
        	mConnectThread = null;
        	Logger.d(TAG, "mConnectThread = null");
        }

        // Cancel any thread currently running a connection
        if (mConnectedThread != null) {
        	mConnectedThread.cancel(); 
        	mConnectedThread = null;
        	
        	Logger.d(TAG, "mConnectedThread = null");
        }

        setState(STATE_NONE);
    }

    /**
     * Start the ConnectThread to initiate a connection to a remote device.
     * @param device  The BluetoothDevice to connect
     */
    public synchronized void connect(String deviceAddress) {
    	
    	
    	this.deviceAddress = deviceAddress;
    	
    	
    	Logger.d(TAG, "connecting...");
    			
    	
    	BluetoothDevice device = mAdapter.getRemoteDevice(deviceAddress.toUpperCase());
    	
    	
        Logger.d(TAG, "connect to: " + device);

        // Cancel any thread attempting to make a connection
        if (mState == STATE_CONNECTING) {
            if (mConnectThread != null) {
            	
            	mConnectThread.cancel();
            	
            	mConnectThread = null;
            }
        }

        // Cancel any thread currently running a connection
        if (mConnectedThread != null) {
        	
        	mConnectedThread.cancel(); 
        	
        	mConnectedThread = null;
        }

        // Start the thread to connect with the given device
        mConnectThread = new ConnectThread(device);
        
        mConnectThread.start();
        
        setState(STATE_CONNECTING);
    }

    /**
     * Start the ConnectedThread to begin managing a Bluetooth connection
     * @param socket  The BluetoothSocket on which the connection was made
     * @param device  The BluetoothDevice that has been connected
     */
    private synchronized void connected(BluetoothSocket socket, BluetoothDevice device) {
        Logger.d(TAG, "connected");

        // Cancel the thread that completed the connection
        if (mConnectThread != null) {
        	mConnectThread.cancel(); 
        	mConnectThread = null;
        }

        // Cancel any thread currently running a connection
        if (mConnectedThread != null) {
        	mConnectedThread.cancel(); 
        	mConnectedThread = null;
        }

        // Start the thread to manage the connection and perform transmissions
        mConnectedThread = new ConnectedThread(socket);
        mConnectedThread.start();
        
        
        /*
        // Send the name of the connected device back to the UI Activity
        Message msg = mHandler.obtainMessage(BlueTerm.MESSAGE_DEVICE_NAME);
        Bundle bundle = new Bundle();
        bundle.putString(BlueTerm.DEVICE_NAME, device.getName());
        msg.setData(bundle);
        mHandler.sendMessage(msg);
	    */
        
        
        setState(STATE_CONNECTED);
    }

    /**
     * Stop all threads
     */
    public synchronized void stop() {
    	
    	stop(true);
        
    }
    
    private synchronized void stop(boolean stopAutoConnection) {
    	Logger.d(TAG, "stop");
        
    	
    	if (stopAutoConnection)
    		setAutoConnection(false); //setto l'autoconnesione a false

        if (mConnectThread != null) {
        	mConnectThread.cancel(); 
        	mConnectThread = null;
        }

        if (mConnectedThread != null) {
        	mConnectedThread.cancel(); 
        	mConnectedThread = null;
        }

        setState(STATE_NONE);
    }


    /**
     * Write to the ConnectedThread in an unsynchronized manner
     * @param out The bytes to write
     * @see ConnectedThread#write(byte[])
     */
    
    public void write(String message) {
        // Create temporary object
        ConnectedThread r;
        
        if (getState() == STATE_CONNECTED) { //controllo se sono connesso
        
        	// Synchronize a copy of the ConnectedThread
        	synchronized (this) {
        		if (mState != STATE_CONNECTED) return;
        		r = mConnectedThread;
        	}
        
        	byte[] messageByte = new byte[message.length()];
        
        	for(int i = 0; i < message.length(); i++) {
        		messageByte[i] = (byte) message.charAt(i);
        	}
        
        	// Perform the write unsynchronized
        	r.write(messageByte);
        }
    }
    
    /**
     * Indicate that the connection attempt failed and notify the UI Activity.
     */
    private void connectionFailed() {
        
    	setState(STATE_NONE);
        
    	
    	Logger.e(TAG, "connessione fallita!");
    	
    	
    	onConnectionFailed();
    	
    	
    	autoconnect();
    	
    	
    }
    
    

    /**
     * Indicate that the connection was lost and notify the UI Activity.
     */
    private void connectionLost() {
        
    	setState(STATE_NONE);
        
    	Logger.e(TAG, "connessione persa!");
    	
    	
    	onConnectionLost();
    	
    	
    	autoconnect();
   
    }
    
    
    
    private void autoconnect() { //metodo per l'autoconnessione
    	
    	if (autoConnection && getState() == STATE_NONE) { //controllo se devo tentare l'autoconnessione
    		
    		Thread execute = new Thread() {
    			
    			public void run() {
    				
    				try {
						Thread.sleep(TIMER_RECONNECT);
					} catch (InterruptedException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
    				
    				
    				BluetoothSerialService.this.stop(false);
    				
    				BluetoothSerialService.this.start();
    				
    				BluetoothSerialService.this.connect(deviceAddress);
    			}
    			
    		};
    		
    		execute.start();
    		
    		
    		
    	}
    	
    	
    }
    
    
    
    //implementazione metodi dell'interfaccia JTrasmissionMethod
    @Override
	public void send(String message) {
    	
    	message = MESSAGE_START_CHARACTER + message + MESSAGE_FINISH_CHARACTER;
    	
    	write(message);
		
	}

    
    @Override
	public boolean available() {
		// TODO Auto-generated method stub
		return true;
	}
    

	@Override
	public String receive() {
		
		String message = "";
		
		int nCharIncorrect = 0;
		int nCharMessage = 0;
		
		
		
		if (getState() == STATE_CONNECTED) {
			
			
			if (mConnectedThread != null) {
				mConnectedThread.setPause(true); //metto in pausa la ricezione dei messaggi
			}
			
			
			
			while (bufferAvailable) //ciclo di attesa per il buffer disponibile
				
				;
			
			//controllo che non ci siani caratteri non validi prima del messaggio (mi fermo quando trovo il char di inizio)
			for(int i = 0; i < messageBuffer.length() && messageBuffer.charAt(i) != MESSAGE_START_CHARACTER; i++) {
				
				nCharIncorrect++;
			
			}
			
						
			if (nCharIncorrect < messageBuffer.length()) { //trovato il carattere di inizio messaggio
				
				
				for (int i = nCharIncorrect + 1; i < messageBuffer.length() && messageBuffer.charAt(i) != MESSAGE_FINISH_CHARACTER; i++) {
					nCharMessage++;
				
					message += messageBuffer.charAt(i);
				
				}
				
				
				if ((nCharIncorrect + nCharMessage + 2) < messageBuffer.length() && messageBuffer.charAt(nCharIncorrect + nCharMessage + 2 - 1) == MESSAGE_FINISH_CHARACTER) {
					
					//E' presente un messaggio
					nCharMessage += 2;
					
				} else {
					
					//non è presente un messaggio azzero message e il numero dei suoi caratteri
					message = "";
					nCharMessage = 0;
					
				}
				
			} //fine prelievo messaggio
			
			
			messageBuffer = messageBuffer.substring(nCharIncorrect + nCharMessage);
			
			if (mConnectedThread != null) {
				mConnectedThread.setPause(false); //riabilito la ricezione
			}
		}
		
		
		if (message != "") Logger.d("receive", message);
		
		return message;
	}
    

    /**
     * This thread runs while attempting to make an outgoing connection
     * with a device. It runs straight through; the connection either
     * succeeds or fails.
     */
    private class ConnectThread extends Thread {
        private final BluetoothSocket mmSocket;
        private final BluetoothDevice mmDevice;

        public ConnectThread(BluetoothDevice device) {
            mmDevice = device;
            BluetoothSocket tmp = null;

            // Get a BluetoothSocket for a connection with the
            // given BluetoothDevice
            try {
                tmp = device.createRfcommSocketToServiceRecord(SerialPortServiceClass_UUID);
            } catch (IOException e) {
                Logger.e(TAG, "create() failed", e);
            }
            mmSocket = tmp; 
        }

        public void run() {
            Logger.i(TAG, "BEGIN mConnectThread");
            
            setName("ConnectThread");

            // Always cancel discovery because it will slow down a connection
            mAdapter.cancelDiscovery();

            // Make a connection to the BluetoothSocket
            try {
                // This is a blocking call and will only return on a
                // successful connection or an exception
                mmSocket.connect();
            } catch (IOException e) {
                connectionFailed();
                // Close the socket
                try {
                    mmSocket.close();
                } catch (IOException e2) {
                    Logger.e(TAG, "unable to close() socket during connection failure", e2);
                }
                // Start the service over to restart listening mode
                //BluetoothSerialService.this.start();
                return;
            }

            // Reset the ConnectThread because we're done
            synchronized (BluetoothSerialService.this) {
                mConnectThread = null;
            }

            // Start the connected thread
            connected(mmSocket, mmDevice);
        }

        public void cancel() {
            try {
                mmSocket.close();
            } catch (IOException e) {
                Logger.e(TAG, "close() of connect socket failed", e);
            }
        }
    }

    /**
     * This thread runs during a connection with a remote device.
     * It handles all incoming and outgoing transmissions.
     */
    private class ConnectedThread extends Thread {
        private final BluetoothSocket mmSocket;
        private final InputStream mmInStream;
        private final OutputStream mmOutStream;
        
        private boolean pause = false; //variabile che mette in pausa la ricezione (usata nell passaggio dei dati con getMessage())
        private boolean stop = false; //usata per richiedere la chiusura della connessione
        

        public ConnectedThread(BluetoothSocket socket) {
            Logger.d(TAG, "create ConnectedThread");
            mmSocket = socket;
            InputStream tmpIn = null;
            OutputStream tmpOut = null;
            
            

            // Get the BluetoothSocket input and output streams
            try {
                tmpIn = socket.getInputStream();
                tmpOut = socket.getOutputStream();
            } catch (IOException e) {
                Logger.e(TAG, "temp sockets not created", e);
            }

            mmInStream = tmpIn;
            mmOutStream = tmpOut;
        }

        public void run() {
            Logger.i(TAG, "BEGIN mConnectedThread");
            byte[] buffer = new byte[1024];
            int bytes;

            
            
            // Keep listening to the InputStream while connected
            while (!stop) {
            	
            	while (!pause /*&& !stop*/) {
            		
            		setBufferState(false); //buffer occupato in operazioni di scrittura
            		
            		try {
            			// Read from the InputStream
            			bytes = mmInStream.read(buffer);
            			
            		} catch (IOException e) {
            			
            			Logger.e(TAG, "bluetooth device DISCONNECTED");
            			
            			cancel(); //invoco la chiusura del thread (chiusura socket)
            			
            			connectionLost(); //funzione per gestire la connessione 
            			
            			break;
            			
            		}
                    
            		synchronized (BluetoothSerialService.this) {
                    	
            			//scarico il buffer dello stream in imput nel buffer messagggi
            			for(int n=0; n<bytes; n++) {
                    		
            				messageBuffer += (char) buffer[n];
            					
            				Logger.d("BluetoothSS", messageBuffer);
                       
            			}
                    	
                    	                
            		} //fine sincronizzazione
                     
            	} //fine ciclo riempimento buffer
            	
            	if (!getBufferState())
            	
            		setBufferState(true); //indico che il buffer è disponibile
            	
            	//Logger.e("BSS", "paused");
            	
            } //fine cliclo vuoto
            
            try {
            	
            	Logger.e("BSS", "chiusura socket...");
            	
                mmSocket.close(); //chiudo il socket
                
                Logger.e("BSS", "socket chiuso");
                
            } catch (IOException e) {
                Logger.e(TAG, "chiusura socket fallita!", e);
            }
            
        }

        
        public void write(byte[] buffer) { //scrive il buffer del parametro nel socket (invia i dati tramite bt)
            try {
                mmOutStream.write(buffer);
                
            } catch (IOException e) {
                Logger.e(TAG, "Exception during write", e);
            }
        }
        

        public void cancel() { //termina il socket appena possibile
        	
           stop = true; //richiedo la chiusura del socket appena possibile
           
           /*
           try {
           	
           	Logger.e("BSS", "closing socket...");
           	
               mmSocket.close(); //chiudo il socket
               
               Logger.e("BSS", "socket closed");
               
           } catch (IOException e) {
               Logger.e(TAG, "close() of connect socket failed", e);
           }
           
           
           
           Logger.e("BSS", "stopped");
        */
        }
        
        
        public void setPause(boolean pause) { //mette in pausa il riempimento del buffer (pausa la lettura dal socket)
        	
        	this.pause = pause;  	
        	
        }
        
        
        private boolean getBufferState() {
        	
        	return BluetoothSerialService.this.bufferAvailable;
        	
        }
        
        private void setBufferState(boolean available) {
        
        	BluetoothSerialService.this.bufferAvailable = available;
        	
        }
        
    }
}

// Reciever
// @lolenseu
// https:github.com/lolenseu

// -------------------- include and define --------------------
#include <WiFi.h>
#include <esp_now.h>
#include <MAVLink.h>
#include <ESP32Servo.h>

// wifi switch pinout
#define WIFISWITCH 2

// uart switch pinout
#define UARTSWITCH 15

// serial pinout
#define RXD 16
#define TXD 17

// buzzer pinout
#define BUZZER 22

// servo pinout
#define GPIOTrottle 4
#define GPIOYaw 5
#define GPIOPitch 19
#define GPIORoll 21
#define GPIOMode 23

// buffer
#define BUFFER 128

// -------------------- variables --------------------
// manualvar ----------
// wifi softap credentials
const char* ssid="apm2.8-hexa";
const char* password="12345678";

// esp-now mymac and targetmac
uint8_t myMac[]={0x40,0x22,0xD8,0x03,0x2E,0x50};
uint8_t targetMac[]={0x40,0x22,0xD8,0x08,0xBB,0x48};

// fixvar ----------
bool espnowEnabled=false;
bool wifiEnabled=false;

// wifi server
WiFiServer server(5760);
WiFiClient client;

// peerinfo
esp_now_peer_info_t peerInfo;

// task
TaskHandle_t core0;
TaskHandle_t core1;

// mavlink
uint8_t c;
mavlink_message_t msg;
mavlink_status_t status;
uint8_t buf[MAVLINK_MAX_PACKET_LEN];
uint16_t len;

// container send buf
uint8_t sbuf[MAVLINK_MAX_PACKET_LEN];
uint16_t slen;

// send packet and status
size_t sbuflen=0;
size_t soffset=0;
uint8_t spacketStatus=0;

// container receive buf
uint8_t rbuf[MAVLINK_MAX_PACKET_LEN];
uint16_t rlen;

// receive packet and status
size_t rbuflen=0;
size_t roffset=0;
uint8_t rpacketStatus=0;

// mavlink heartbeattime
unsigned long lastHeartbeatTime=0;

// wifi switch state
int wifiSwitchState;

// uart switch state
int uartSwitchState;

// counter
int loop1=0;
int loop2=0;

// time
unsigned long globaltime;
unsigned long startTime1;
unsigned long startTime2;
unsigned long elapsedTime1;
unsigned long elapsedTime2;

// clock
unsigned long clock1=0;
unsigned long clock2=0;

// dji tone
const int notes[]={1046,1318,1568}; // C6, E6, G6

// process data
int Trottle;
int Yaw;
int Pitch;
int Roll;
int Mode;
String Mods;

// servo
Servo servo1;
Servo servo2;
Servo servo3;
Servo servo4;
Servo servo5;

// counter incase of lost signal
bool buzzerState=LOW;
unsigned long losscount1=0;
unsigned long losscount2=0;
unsigned long losscount3=0;

// percent data
int percentTrottle;
int percentYaw;
int percentPitch;
int percentRoll;

// connection and send data espnow
String comStatus;
int ping;

// send message
typedef struct send_message{
  uint64_t time1;
  uint64_t time2;
  uint16_t len;
  uint8_t buf[BUFFER];
  uint8_t status;
};
send_message sndxMsg;

// recive message
typedef struct receive_message{
  uint32_t trottle;
  uint32_t yaw;
  uint32_t pitch;
  uint32_t roll;
  uint32_t mode;
  uint64_t time1;
  uint64_t time2;
  uint16_t len;
  uint8_t buf[BUFFER];
  uint8_t status;
};
receive_message rcvxMsg;

// -------------------- fuctions --------------------
// processing ----------
void playNote(int frequency,int duration){
  int period=1000000/frequency;
  int cycles=(duration*1000)/period;

  for (int i=0;i<cycles;i++){
    digitalWrite(BUZZER,HIGH);
    delayMicroseconds(period/2);
    digitalWrite(BUZZER,LOW);
    delayMicroseconds(period/2);
  }
}

// map to percent
int mapPercent(int toMapPercent){
  int mapValuePercent=map(toMapPercent,1000,2000,0,100);
  return mapValuePercent;
}

// mapmode
void mapMode(int toMode){
  int mapMode=map(toMode,1000,2000,1000,2000);
  if(mapMode>1000&&mapMode<1230)Mods="Stab";
  else if(mapMode>1231&&mapMode<1360)Mods="PosH";
  else if(mapMode>1361&&mapMode<1490)Mods="AltH";
  else if(mapMode>1491&&mapMode<1621)Mods="Loit";
  else if(mapMode>1621&&mapMode<1749)Mods="RTL ";
  else if(mapMode>1750&&mapMode<2000)Mods="Land";
}

// esp-now ----------
void OnDataSent(const uint8_t *mac_addr,esp_now_send_status_t status){
  if(status==ESP_NOW_SEND_SUCCESS)comStatus="ok!";
  else comStatus="bd!";
}

void OnDataRecv(const uint8_t *mac_addr,const uint8_t *incomingData,int data_len){
  memcpy(&rcvxMsg,incomingData,sizeof(rcvxMsg));
}

// startup ----------
// initBoot
void initBoot(){
  Serial.println("");
  Serial.println("Botting ...");
  Serial.println("");

  // Startup tone
  for (int i=0;i<3;i++){
    playNote(notes[i],200);
  }
}

// connection ----------
// init esp-now
void initespnow(){
  if(!espnowEnabled){
    WiFi.mode(WIFI_STA);
    
    // init ESP-NOW
    Serial.println("Initiating ESP-NOW ..");

    if(esp_now_init()!=ESP_OK){
      Serial.println("Error Initializing ESP-NOW");
      espnowEnabled=false;
      return;
    }

    // register peer
    memcpy(peerInfo.peer_addr,targetMac,6);
    peerInfo.channel=0;  
    peerInfo.encrypt=false;

    if(esp_now_add_peer(&peerInfo)!=ESP_OK){
      Serial.println("Failed to add peer");
      return;
    }
    
    // register callbacks
    esp_now_register_send_cb(OnDataSent);
    esp_now_register_recv_cb(reinterpret_cast<esp_now_recv_cb_t>(OnDataRecv));

    espnowEnabled=true;
  }
  delay(500);
}

// disable espnow
void disableespnow(){
  if(espnowEnabled){
    esp_now_deinit();
    espnowEnabled=false;
    Serial.println("ESP-NOW disabled.");
  }
}

// init wifi
void initwifi(){
  if(!wifiEnabled){
    // init WiFi
    Serial.println("Initiating WiFi AP ..");

    bool success=WiFi.softAP(ssid,password);
    if(success){
      server.begin();
      wifiEnabled=true;
    }
    else{
      Serial.println("Error initializing WiFi AP.");
      wifiEnabled=false;
    }
  }
  delay(500);
}

// disable wifi
void disablewifi(){
  if(wifiEnabled){
    WiFi.softAPdisconnect(true);
    WiFi.disconnect();
    wifiEnabled=false;
    Serial.println("WiFi AP disabled.");
  }
}

// printing ----------
// serial debug
void serialDebug(){
  Serial.println("\n");
  Serial.println("-------------------- debug --------------------");
  Serial.println("ESP-NOW");
  Serial.printf("Com Status: ");
  Serial.println(comStatus);
  Serial.printf("ping: %dms\n",ping);
  Serial.println("");
  /*
  Serial.println("Processed Data");
  Serial.printf("Trottle: %d\n",Trottle);
  Serial.printf("Yaw: %d\n",Yaw);
  Serial.printf("Pitch: %d\n",Pitch);
  Serial.printf("Roll: %d\n",Roll);
  Serial.printf("Mode: %d\n",Mode);
  Serial.println("");
  */
  Serial.println("Official Data");
  Serial.printf("Trottle: %d%%\n",percentTrottle);
  Serial.printf("Yaw: %d%%\n",percentYaw);
  Serial.printf("Pitch: %d%%\n",percentPitch);
  Serial.printf("Roll: %d%%\n",percentRoll);
  Serial.printf("Mode: %s\n",Mods);
  Serial.println("");
  Serial.println("Cpu Usage");
  Serial.printf("Core0: %dms\n",elapsedTime1);
  Serial.printf("Core1: %dms\n",elapsedTime2);
  Serial.println("");
  Serial.printf("Uptime: %dsec\n",globaltime);
  Serial.println("-------------------- debug --------------------");
}

// -------------------- task1 --------------------
void Task1code(void*pvParameters){
  for(;;){
    // core0 counter
    loop1+=1;
    if(loop1==100)loop1=0;

    // uptime
    globaltime=millis()/1000;

    // core0 load start
    startTime1=millis();

    // receiving msg ----------
    // rcv controls
    Trottle=rcvxMsg.trottle;
    Yaw=rcvxMsg.yaw;
    Pitch=rcvxMsg.pitch;
    Roll=rcvxMsg.roll;
    Mode=rcvxMsg.mode;

    // rcv ping
    if(rcvxMsg.time2<=0)ping=0;
    else ping=millis()-rcvxMsg.time2;

    // ping from control
    sndxMsg.time1=rcvxMsg.time1;

    // process ----------
    // safety in case of out of signal
    if(ping>=3000){
      // stay on position
      Trottle=1500;
      Yaw=1500;
      Pitch=1500;
      Roll=1500;
      Mode=1540; // Loiter mode
      
      if(buzzerState==LOW&&millis()-losscount1>=1000){
        buzzerState=HIGH;
        losscount1=millis();
        digitalWrite(BUZZER,HIGH);
      }
      if(buzzerState==HIGH&&millis()-losscount1>=50){
        buzzerState=LOW;
        losscount1=millis();
        digitalWrite(BUZZER,LOW);
      }

      if(millis()-losscount2>=10000){
        // Return to Land
        Mode=1690; // RTL mode

        if(buzzerState==LOW&&millis()-losscount1>=500){
          buzzerState=HIGH;
          losscount1=millis();
          digitalWrite(BUZZER,HIGH);
        }
        if(buzzerState==HIGH&&millis()-losscount1>=50){
          buzzerState=LOW;
          losscount1=millis();
          digitalWrite(BUZZER,LOW);
        }
      }

      if(millis()-losscount3>=60000){
        if(buzzerState==LOW&&millis()-losscount1>=250){
          buzzerState=HIGH;
          losscount1=millis();
          digitalWrite(BUZZER,HIGH);
        }
        if(buzzerState==HIGH&&millis()-losscount1>=50){
          buzzerState=LOW;
          losscount1=millis();
          digitalWrite(BUZZER,LOW);
        }
      }
    }
    else{
      losscount1=millis();
      losscount2=millis();
      losscount3=millis();
    }

    // write servo
    servo1.write(Trottle);
    servo2.write(Yaw);
    servo3.write(Pitch);
    servo4.write(Roll);
    servo5.write(Mode);

    // preparing msg ----------
    // snd ping
    sndxMsg.time2=millis();

    // percent data
    percentTrottle=mapPercent(Trottle);
    percentYaw=mapPercent(Yaw);
    percentPitch=mapPercent(Pitch);
    percentRoll=mapPercent(Roll);
    mapMode(Mode);

    delay(5); // run delay

    // core0 load end
    elapsedTime1=millis()-startTime1;

    // debug ----------
    // serial debug
    //serialDebug(); // enable this for fast debug
    if(millis()-clock1>=200){
      clock1=millis();
      serialDebug();
    }
  }
}

// -------------------- task2 --------------------
void Task2code(void*pvParameters){
  for(;;){
    // core1 counter
    loop2+=1;
    if(loop2==100)loop2=0;

    // core1 load start
    startTime2=millis();

    // wifi switch read state
    wifiSwitchState=digitalRead(WIFISWITCH);

    // uart switch read state
    uartSwitchState=digitalRead(UARTSWITCH);

    // serial uart ----------
    // uart wifi
    if(wifiSwitchState==LOW){
      if(espnowEnabled){
        // disable espnow
        disableespnow();
      }
      
      if(!wifiEnabled){
        // init wifi
        initwifi();
      }

      client=server.available();
      while(client.connected()){
        // heartbeat
        if(millis()-lastHeartbeatTime>=1000){
          lastHeartbeatTime=millis();
          mavlink_msg_heartbeat_pack(1,MAV_COMP_ID_AUTOPILOT1,&msg,MAV_TYPE_QUADROTOR,MAV_AUTOPILOT_GENERIC,MAV_MODE_FLAG_MANUAL_INPUT_ENABLED,0,MAV_STATE_STANDBY);
          len=mavlink_msg_to_send_buffer(buf,&msg);
          Serial2.write(buf,len);
        }

        // read and writing to serial2
        else{
          while(client.available()){
            c=client.read();
            if(mavlink_parse_char(MAVLINK_COMM_0,c,&msg,&status)){
              len=mavlink_msg_to_send_buffer(buf,&msg);
              Serial2.write(buf,len);
            }
          }
        }

        // sending to client
        while(Serial2.available()){
          c=Serial2.read();
          if(mavlink_parse_char(MAVLINK_COMM_0,c,&msg,&status)){
            len=mavlink_msg_to_send_buffer(buf,&msg);
            client.write(buf,len);
          }
        }
      }
    }

    // uart usb
    else if(uartSwitchState==LOW){
      if(wifiEnabled){
        //disable wifi
        disablewifi();
      }

      if(!espnowEnabled){
        // init espnow
        initespnow();
      }

      // heartbeat
      if(millis()-lastHeartbeatTime>=1000){
        lastHeartbeatTime=millis();
        mavlink_msg_heartbeat_pack(1,MAV_COMP_ID_AUTOPILOT1,&msg,MAV_TYPE_QUADROTOR,MAV_AUTOPILOT_GENERIC,MAV_MODE_FLAG_MANUAL_INPUT_ENABLED,0,MAV_STATE_STANDBY);
        len=mavlink_msg_to_send_buffer(buf,&msg);
        Serial2.write(buf,len);
      }

      // read serial and write serial2
      else{
        while(Serial.available()){
          c=Serial.read();
          if(mavlink_parse_char(MAVLINK_COMM_0,c,&msg,&status)){
            len=mavlink_msg_to_send_buffer(buf,&msg);
            Serial2.write(buf,len);
          }
        }
      }
      
      // read serial2 and write serial
      while(Serial2.available()){
        c=Serial2.read();
        if(mavlink_parse_char(MAVLINK_COMM_0,c,&msg,&status)){
          len=mavlink_msg_to_send_buffer(buf,&msg);
          Serial.write(buf,len);
        }
      }
    }

    // espnow
    else{
      if(wifiEnabled){
        //disable wifi
        disablewifi();
      }

      if(!espnowEnabled){
        // init espnow
        initespnow();
      }

      // receive msg ----------
      if(rcvxMsg.status>rpacketStatus){
        rpacketStatus=rcvxMsg.status;
        rbuflen=sizeof(rcvxMsg.buf);
        size_t chunkSize=min(sizeof(rcvxMsg.buf),rbuflen-roffset);
        memcpy(rbuf+roffset,rcvxMsg.buf,chunkSize);
        roffset+=chunkSize;
      }
      else if(rcvxMsg.status==0){
        rlen=rcvxMsg.len;

        // reset to zero
        rbuflen=0;
        roffset=0;
        rpacketStatus=0;

        Serial2.write(rbuf,rlen);
        memset(rbuf,0,sizeof(rbuf));
      }

      // sending msg ----------
      if(sbuflen==0){
        // heartbeat
        if(millis()-lastHeartbeatTime>=1000){
          lastHeartbeatTime=millis();
          mavlink_msg_heartbeat_pack(1,MAV_COMP_ID_AUTOPILOT1,&msg,MAV_TYPE_QUADROTOR,MAV_AUTOPILOT_GENERIC,MAV_MODE_FLAG_MANUAL_INPUT_ENABLED,0,MAV_STATE_STANDBY);
          slen=mavlink_msg_to_send_buffer(sbuf,&msg);
        }

        // read
        else{
          while(Serial2.available()){
            c=Serial2.read();
            if(mavlink_parse_char(MAVLINK_COMM_0,c,&msg,&status)){
              slen=mavlink_msg_to_send_buffer(sbuf,&msg);
            }
          }
        }

        sbuflen=slen;
      }
      if(soffset<sbuflen){
        size_t chunkSize=min(sizeof(sndxMsg.buf),sbuflen-soffset);
        memcpy(sndxMsg.buf,sbuf+soffset,chunkSize);
        soffset+=chunkSize;
        sndxMsg.status+=1;
      }
      else{
        sndxMsg.len=slen;

        // reset to zero
        sbuflen=0;
        soffset=0;
        sndxMsg.status=0;
      }
    }

    // sending msg ----------
    // snd msg via ESP-NOW
    if(wifiSwitchState==HIGH){
      esp_now_send(targetMac,(uint8_t*)&sndxMsg,sizeof(sndxMsg));
    }

    delay(5); // run delay
    
    // core1 load end
    elapsedTime2=millis()-startTime2;
  } 
} 

// -------------------- setup --------------------
void setup(){
  // put your setup code here, to run once:
  // Initialize Serial Monitor
  Serial.begin(115200);
  Serial2.begin(115200,SERIAL_8N1,RXD,TXD);

  // int ESP-NOW
  initespnow();

  // wifi switch
  pinMode(WIFISWITCH,INPUT_PULLUP);

  // uart switch
  pinMode(UARTSWITCH,INPUT_PULLUP);

  // buzzer
  pinMode(BUZZER,OUTPUT);

  // servo
  servo1.attach(GPIOTrottle);
  servo2.attach(GPIOYaw);
  servo3.attach(GPIOPitch);
  servo4.attach(GPIORoll);
  servo5.attach(GPIOMode);

  // boot
  initBoot();

  // startup delay
  delay(200);

  // task handler
  xTaskCreatePinnedToCore(Task1code,"core0",10000,NULL,2,&core0,0);
  xTaskCreatePinnedToCore(Task2code,"core1",10000,NULL,1,&core1,1);
}

// -------------------- loop --------------------
void loop(){
  // put your main code here, to run repeatedly:
  
}

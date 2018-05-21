//#include "Sodaq_wdt.h"

#include <ESP8266WiFi.h>
#include "DHT.h"
#include <Adafruit_NeoPixel.h>

#ifdef __AVR__
  #include <avr/power.h>
#endif

const char* ssid     = "GRAVITECH";
const char* password = "27/14GtechRICH";
const char* host = "www.novel-thai.xyz";  //Doamin ที่ต้องการดึงค่ามาใช้

void AlertSend(String Msg);
void AlertShow();
// Pin config
#define GasPin          0
#define BuzzerPin       2
#define TRIGGER_PIN     16   // Ultrasonic Config
#define ECHO_PIN        5    // Ultrasonic Config
#define DHTPIN          4
#define LedPin1         12
#define LedPin2         13
#define LdrPin          A0

// Config Temp Value
#define TempAlert         30
#define DHTTYPE           DHT11  
#define distanceAlert     14
DHT dht(DHTPIN, DHTTYPE);

// Config RGB Led
Adafruit_NeoPixel Led1 = Adafruit_NeoPixel(16, LedPin1, NEO_GRB + NEO_KHZ800);
Adafruit_NeoPixel Led2 = Adafruit_NeoPixel(16, LedPin2, NEO_GRB + NEO_KHZ800);


int LedEn1,LedEn2,GasEn,MotionEn,TempEn,Enable = 0;
float LedVal_1,LedVal_2,Temp_Val = 0.0;
long duration, distance; 
int LedColor1 = 0;
int LedColor2 = 0;
// Gas Variable 
int sensorValue;
int AlertQ1 = 0;
int AlertQ2 = 0;
int AlertQ3 = 0;
void setup() {
  Serial.begin(9600);
  Serial.print("Connecting to ");
  Serial.println(ssid);

  pinMode(TRIGGER_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  pinMode(LedPin1,OUTPUT);
  pinMode(LedPin2,OUTPUT);
  pinMode(BuzzerPin,OUTPUT);
  pinMode(LdrPin,INPUT);
  pinMode(GasPin,INPUT);
  
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);  

  while (WiFi.status() != WL_CONNECTED) 
  {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected");  
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
  dht.begin();
  #if defined (__AVR_ATtiny85__)
    if (F_CPU == 16000000) clock_prescale_set(clock_div_1);
  #endif
  Led1.begin(); // This initializes the NeoPixel library.
  Led2.begin();
}



void loop() {
  delay(2000);
  Serial.print("connecting to ");
  Serial.println(host);

  WiFiClient client;
  const int httpPort = 80;
  if (!client.connect(host, httpPort)) {
    Serial.println("connection failed");
    return;
  }
   
  float tempValue = dht.readTemperature();
  int LdrValue = map(analogRead(LdrPin), 0, 1023, 0, 255);
  int vout1 = analogRead(A0); // Read the analogue pin
  float vout = vout1/204.6;
  float lux2 = 65.9 * (pow( vout1, 0.352));
 
  Serial.print("Requesting URL: ");
  String url = "/IOT/index.php?GenCommand=DeviceRead&TEMPValue=" + String(tempValue) + "&LEDValue1=" + String(lux2);
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
              "Host: " + host + "\r\n" + 
              "Connection: close\r\n\r\n");
  unsigned long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 5000) {
      Serial.println(">>> Client Timeout !");
      client.stop();
      return;
    }
  }

  if(Enable == 1)
  {
    // Ultrasonic
    digitalWrite(TRIGGER_PIN, LOW);  // Added this line
    delayMicroseconds(2); // Added this line
    digitalWrite(TRIGGER_PIN, HIGH);
    delayMicroseconds(10); // Added this line
    digitalWrite(TRIGGER_PIN, LOW);
    duration = pulseIn(ECHO_PIN, HIGH);
    distance = (duration/2) / 29.1;
    //==================================================
    Serial.print("Distance = ");
    Serial.println(distance);
    //==================================================
    sensorValue = digitalRead(GasPin);
    if(distance <= distanceAlert && MotionEn == 1)
    {
       if(AlertQ1 == 0)
       {
          AlertQ1 = 1;
          AlertShow();
       }
       AlertSend("%E0%B8%95%E0%B8%A3%E0%B8%A7%E0%B8%88%E0%B8%9E%E0%B8%9A%E0%B8%A1%E0%B8%B5%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B9%80%E0%B8%84%E0%B8%A5%E0%B8%B7%E0%B9%88%E0%B8%AD%E0%B8%99%E0%B9%84%E0%B8%AB%E0%B8%A7%E0%B8%A0%E0%B8%B2%E0%B8%A2%E0%B9%83%E0%B8%99%E0%B8%9A%E0%B9%89%E0%B8%B2%E0%B8%99%E0%B8%84%E0%B8%A3%E0%B8%B1%E0%B8%9A%20.!!!!");
    }
    if(sensorValue == 0 && GasEn ==1)
    {
       if(AlertQ2 == 0)
       {
          AlertQ2 = 1;
          AlertShow();
       }
        AlertSend("%E0%B8%95%E0%B8%A3%E0%B8%A7%E0%B8%88%E0%B8%9E%E0%B8%9A%E0%B9%81%E0%B8%81%E0%B9%8A%E0%B8%AA%E0%B8%A0%E0%B8%B2%E0%B8%A2%E0%B9%83%E0%B8%99%E0%B8%9A%E0%B9%89%E0%B8%B2%E0%B8%99.!!!!");
        
        
    }
    if(tempValue >= TempAlert && TempEn == 1)
    {
       if(AlertQ3 == 0)
       {
          AlertQ3 = 1;
          AlertShow();
       }
        AlertSend("%E0%B8%AD%E0%B8%B8%E0%B8%93%E0%B8%A0%E0%B8%B9%E0%B8%A1%E0%B8%B4%E0%B8%A0%E0%B8%B2%E0%B8%A2%E0%B9%83%E0%B8%99%E0%B8%9A%E0%B9%89%E0%B8%B2%E0%B8%99%E0%B8%AA%E0%B8%B9%E0%B8%87%E0%B9%80%E0%B8%81%E0%B8%B4%E0%B8%99%E0%B8%81%E0%B8%A7%E0%B9%88%E0%B8%B2%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%81%E0%B8%B3%E0%B8%AB%E0%B8%99%E0%B8%94");
       
    }
    if(distance >= distanceAlert)
    {
       if(AlertQ1 == 1)
       {
          AlertQ1 = 0;
          AlertShow();
       }
    }
    if(sensorValue == 1){
       if(AlertQ2 == 1)
       {
          AlertQ2 = 0;
          AlertShow();
       }
    }
    if(tempValue <= TempAlert){
       if(AlertQ3 == 1)
       {
          AlertQ3 = 0;
          AlertShow();
       }
    }
  }
    if(LedEn1 == 1 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      if(LedColor1 == 0){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,255,255));
          Led1.show();
        } 
      }else if(LedColor1 == 1){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(255,0,0));
          Led1.show();
        } 
      }else if(LedColor1 == 2){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,255,0));
          Led1.show();
        } 
      }else if(LedColor1 == 3){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(25,51,0));
          Led1.show();
        } 
      }else if(LedColor1 == 4){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(255,128,0));
          Led1.show();
        } 
      }else if(LedColor1 == 5){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(255,0,255));
          Led1.show();
        } 
      }else if(LedColor1 == 6){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,0,255));
          Led1.show();
        } 
      }
    }
    if(LedEn2 == 1 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      if(LedColor2 == 0){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,255,255));
          Led2.show();
        } 
      }else if(LedColor2 == 1){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(255,0,0));
          Led2.show();
        } 
      }else if(LedColor2 == 2){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,255,0));
          Led2.show();
        } 
      }else if(LedColor2 == 3){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(25,51,0));
          Led2.show();
        } 
      }else if(LedColor2 == 4){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(255,128,0));
          Led2.show();
        } 
      }else if(LedColor2 == 5){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(255,0,255));
          Led2.show();
        } 
      }else if(LedColor2 == 6){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,0,255));
          Led2.show();
        } 
      }
    }
    if(LedEn1 == 0 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,0,0)); // Moderately bright green color.
          Led1.show(); // This sends the updated pixel color to the hardware.
      }
    }
    if(LedEn2 == 0 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,0,0)); // Moderately bright green color.
          Led2.show(); // This sends the updated pixel color to the hardware.
      }
    }

// ในส่วนของการดึง Json โดยการดึง ตัวแปรที่ชื่อว่าตัวแปรมาใช้งาน
// ยกตัวอย่าง ตัวแปร ch1 ค่าที่ได้จะเป็น 1 แสดงออกมา เราสามารถนำ ตัว แปร ch1 ไปใช้งานต่างๆได้เช่นการแสดงข้อความออกจอ LCD เปิดปิดไฟตามกำหนด

  if(client.find("")){
      
      client.find("Led1");  // 
      LedEn1 = client.parseInt();

      client.find("Led2");
      LedEn2 = client.parseInt();

      client.find("Gas");
      GasEn = client.parseInt();

      client.find("PIR");
      MotionEn = client.parseInt();

      client.find("Temp_status");
      TempEn = client.parseInt();

      client.find("Led1_Color");
      LedColor1 = client.parseInt();
      
      client.find("Led2_Color");
      LedColor2 = client.parseInt();
      
      client.find("enable");
      Enable = client.parseInt();

      
      Serial.println("");
      
      Serial.print("Enable = ");
      Serial.println(Enable); 
      
      Serial.print("Led1En = ");
      Serial.println(LedEn1); 

      Serial.print("LedEn2 = ");
      Serial.println(LedEn2); 

      Serial.print("GasEn = ");
      Serial.println(GasEn); 

      Serial.print("TempSensor = ");
      Serial.print(tempValue); 
      Serial.println(" *C");
      
      Serial.print("TempEn = ");
      Serial.println(TempEn); 
      
      Serial.print("sensorValue = ");
      Serial.println(sensorValue); 
      
      Serial.print("LedColor1 = ");
      Serial.println(sensorValue);  

      Serial.print("AlertQ3 = ");
      Serial.println(AlertQ3);
        
      Serial.println("Closing Connection");
    }
}

void AlertShow()
{
  if(AlertQ1 == 1 || AlertQ2 == 1 || AlertQ3 == 1)
  {
      Serial.println("AlertShow");
      tone(BuzzerPin,800);
      for(int i=0;i<16;i++)
      {
         Led1.setPixelColor(i, Led1.Color(255,0,0));
         Led1.show();
         Led2.setPixelColor(i, Led2.Color(255,0,0)); 
         Led2.show(); 
      }
      return;
  }
  else
  {
    if(LedEn1 == 1 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      if(LedColor1 == 0){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,255,255));
          Led1.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor1 == 1){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(255,0,0));
          Led1.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor1 == 2){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,255,0));
          Led1.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor1 == 3){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(25,51,0));
          Led1.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor1 == 4){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(255,128,0));
          Led1.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor1 == 5){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(255,0,255));
          Led1.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor1 == 6){
        for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,0,255));
          Led1.show();
        }
        tone(BuzzerPin,0);
        return; 
      }
    }
    if(LedEn2 == 1 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      if(LedColor2 == 0){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,255,255));
          Led2.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor2 == 1){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(255,0,0));
          Led2.show();
        }
        tone(BuzzerPin,0); 
        return;
      }else if(LedColor2 == 2){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,255,0));
          Led2.show();
        }
        tone(BuzzerPin,0);
        return;
      }else if(LedColor2 == 3){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(25,51,0));
          Led2.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor2 == 4){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(255,128,0));
          Led2.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor2 == 5){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(255,0,255));
          Led2.show();
        }
        tone(BuzzerPin,0);
        return; 
      }else if(LedColor2 == 6){
        for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,0,255));
          Led2.show();
        } 
      }
      tone(BuzzerPin,0);
      return;
    }
    if(LedEn1 == 0 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      for(int i=0;i<16;i++){
          Led1.setPixelColor(i, Led1.Color(0,0,0)); // Moderately bright green color.
          Led1.show(); // This sends the updated pixel color to the hardware.
      }
      tone(BuzzerPin,0);
      return;
    }
    if(LedEn2 == 0 && AlertQ1 == 0 && AlertQ2 == 0 && AlertQ3 == 0){
      for(int i=0;i<16;i++){
          Led2.setPixelColor(i, Led2.Color(0,0,0)); // Moderately bright green color.
          Led2.show(); // This sends the updated pixel color to the hardware.
      }
      tone(BuzzerPin,0);
      return;
    }   
  }
}

void AlertSend(String Msg){
  WiFiClient client;
  const int httpPort = 80;
  if (!client.connect(host, httpPort)) {
    Serial.println("connection failed");
    return;
  }

  Serial.println("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
  String url = "/IOT/push.php?Message=" + Msg;
  Serial.println(url);
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
              "Host: " + host + "\r\n" + 
              "Connection: close\r\n\r\n");
  unsigned long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 5000) {
      Serial.println(">>> Client Timeout !");
      client.stop();
      return;
    }
  }
  //delay(4000);
}



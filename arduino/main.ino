#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_ADXL345_U.h>
#include <TinyGPSPlus.h>       // <-- Menggunakan TinyGPSPlus
#include <HardwareSerial.h>
#include <WiFi.h>
#include <HTTPClient.h>

#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_RESET -1
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

// Alamat I2C default adalah 0x53
Adafruit_ADXL345_Unified accel = Adafruit_ADXL345_Unified(12345);
TinyGPSPlus gps;               // <-- Menggunakan objek TinyGPSPlus
HardwareSerial gpsSerial(2); 

// --- Pin Aktuator dan Sensor ---
#define BUZZER_PIN 2
#define GPS_RX 16
#define GPS_TX 17

// --- Konfigurasi WiFi dan API lokal ---
const char* WIFI_SSID = "Maison";
const char* WIFI_PASSWORD = "SANGATLUXU";
const char* DEVICE_ID = "esp32-sigma-01";
const char* API_BASE_URL = "http://192.168.1.8:8000/api";

float baseline_g = 0.0; 
float smoothed_pga = 0.0; // Variabel untuk menyimpan nilai halus (filter)

// --- Variabel Timer Buzzer ---
unsigned long buzzer_timer = 0; 
bool status_buzzer_aktif = false; 

// --- Variabel Anti-Benturan (Debounce / Time Windowing) ---
unsigned long waktu_mulai_getar = 0;
unsigned long waktu_terakhir_getar = 0;
const int SYARAT_DURASI_GEMPA = 1200; // Getaran harus bertahan 1.2 detik (1200 ms) agar dianggap gempa
const int JEDA_RESET_GETARAN = 800;   // Jika 0.8 detik tidak ada getaran, reset timer (berarti cuma benturan)
const float BATAS_AWAL_GETARAN = 1.5; // Batas minimal getaran mulai dihitung
const float BATAS_ALARM_BUNYI = 3.9 * 2; // Batas getaran untuk membunyikan sirine (Sesuaikan kebutuhan)
unsigned long lastUploadMillis = 0;
const unsigned long UPLOAD_INTERVAL_MS = 5000;

String apiEndpointGps = String(API_BASE_URL) + "/sensors/gps";
String apiEndpointAccelerometer = String(API_BASE_URL) + "/sensors/accelerometer";

void connectToWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  Serial.print("Menghubungkan WiFi");

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("OK: WiFi Terhubung.");
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("WARNING: WiFi belum tersambung, upload JSON akan dilewati dulu.");
  }
}

String buildRecordedAtJsonValue() {
  if (gps.date.isValid() && gps.time.isValid()) {
    char buffer[25];
    snprintf(
      buffer,
      sizeof(buffer),
      "%04d-%02d-%02dT%02d:%02d:%02dZ",
      gps.date.year(),
      gps.date.month(),
      gps.date.day(),
      gps.time.hour(),
      gps.time.minute(),
      gps.time.second()
    );
    return String("\"") + buffer + String("\"");
  }

  return String("null");
}

bool postJson(const String& url, const String& payload) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("ERROR: WiFi belum terhubung.");
    return false;
  }

  WiFiClient client;
  HTTPClient http;

  if (!http.begin(client, url)) {
    Serial.println("ERROR: Gagal memulai HTTP client.");
    return false;
  }

  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  int httpCode = http.POST(payload);

  Serial.print("POST ");
  Serial.print(url);
  Serial.print(" -> HTTP ");
  Serial.println(httpCode);

  if (httpCode > 0) {
    String response = http.getString();
    Serial.println(response);
  }

  http.end();
  return httpCode > 0 && httpCode < 400;
}

bool sendGpsJson() {
  if (!gps.location.isValid()) {
    return false;
  }

  String payload = String("{")
    + "\"device_id\":\"" + DEVICE_ID + "\""
    + ",\"latitude\":" + String(gps.location.lat(), 7)
    + ",\"longitude\":" + String(gps.location.lng(), 7)
    + ",\"altitude\":" + (gps.altitude.isValid() ? String(gps.altitude.meters(), 2) : String("null"))
    + ",\"satellites\":" + String(gps.satellites.isValid() ? gps.satellites.value() : 0)
    + ",\"status\":\"" + String(gps.location.isValid() ? "3D FIX" : "NO FIX") + "\""
    + ",\"recorded_at\":" + buildRecordedAtJsonValue()
    + "}";

  return postJson(apiEndpointGps, payload);
}

bool sendAccelerometerJson(float pga, const String& mmiStatus) {
  float x = 0.0;
  float y = 0.0;
  float z = 0.0;

  sensors_event_t event;
  if (accel.getEvent(&event)) {
    x = event.acceleration.x;
    y = event.acceleration.y;
    z = event.acceleration.z;
  }

  String payload = String("{")
    + "\"device_id\":\"" + DEVICE_ID + "\""
    + ",\"x\":" + String(x, 4)
    + ",\"y\":" + String(y, 4)
    + ",\"z\":" + String(z, 4)
    + ",\"magnitude\":" + String(pga, 4)
    + ",\"recorded_at\":" + buildRecordedAtJsonValue()
    + "}";

  Serial.print("Status kirim accelerometer: ");
  Serial.println(mmiStatus);

  return postJson(apiEndpointAccelerometer, payload);
}

String getStatusMMI(float pga) {
  if (pga < 0.17 * 2) return "I(Aman)";
  else if (pga < 1.4 * 2) return "II-III(Lemah)";
  else if (pga < 3.9 * 2) return "IV(Waspada)";
  else if (pga < 9.2 * 2) return "V(Bahaya!)";
  else return "VI+(AWAS!)"; 
}

void setup() {
  Serial.begin(115200);
  Serial.println("\n--- Memulai Sistem Pendeteksi Gempa SIGMA ---");
  
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW); 
  
  gpsSerial.begin(9600, SERIAL_8N1, GPS_RX, GPS_TX);

  connectToWiFi();
  
  if(!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println("ERROR: Gagal menemukan layar OLED!");
    for(;;); 
  }
  Serial.println("OK: OLED Terhubung.");
  
  if(!accel.begin()) {
    Serial.println("ERROR: Gagal menemukan ADXL345!");
    display.clearDisplay();
    display.setCursor(0, 0);
    display.setTextColor(WHITE);
    display.println("ADXL345 Error!");
    display.display();
    for(;;); 
  }
  Serial.println("OK: ADXL345 Terhubung.");
  
  accel.setRange(ADXL345_RANGE_2_G);

  Serial.println("Memulai kalibrasi penempatan...");
  for (int i = 5; i > 0; i--) {
    display.clearDisplay();
    display.setTextSize(1);
    display.setTextColor(WHITE);
    display.setCursor(0, 10);
    display.println("Mode Tembok SIGMA");
    display.print("Kalibrasi dalam: ");
    display.print(i);
    display.println(" s");
    display.println("JAUHI TEMBOK!");
    display.display();
    
    Serial.print("Kalibrasi dalam: ");
    Serial.print(i);
    Serial.println(" detik");
    delay(1000);
  }

  display.clearDisplay();
  display.setCursor(0, 10);
  display.println("Merekam Gravitasi...");
  display.display();
  Serial.println("Merekam Gravitasi Dasar (Baseline)...");
  
  float sum_g = 0;
  for(int i = 0; i < 100; i++) {
    sensors_event_t event;
    accel.getEvent(&event);
    sum_g += sqrt(pow(event.acceleration.x, 2) + pow(event.acceleration.y, 2) + pow(event.acceleration.z, 2));
    delay(20);
  }
  baseline_g = sum_g / 100.0; 
  
  Serial.print("Baseline gravitasi didapat: ");
  Serial.println(baseline_g);
  Serial.println("--- ALAT AKTIF & STANDBY ---");
  
  display.clearDisplay();
  display.setCursor(0, 10);
  display.println("Alat Aktif!");
  display.display();
  delay(1000);
}

void loop() {
  // Update Data GPS
  while (gpsSerial.available() > 0) {
    gps.encode(gpsSerial.read());
  }

  // Baca Data ADXL345
  sensors_event_t event;
  if(accel.getEvent(&event)) { 
    float total_g = sqrt(pow(event.acceleration.x, 2) + pow(event.acceleration.y, 2) + pow(event.acceleration.z, 2));
    float raw_pga = abs(total_g - baseline_g); 

    // 1. FILTER LOW-PASS (Data Smoothing)
    smoothed_pga = (0.15 * raw_pga) + (0.85 * smoothed_pga);

    // 2. FILTER DEADZONE (Abaikan getaran sangat kecil/noise sensor)
    if (smoothed_pga < 0.8) { 
        smoothed_pga = 0.0;
    }

    // 3. LOGIKA DETEKSI GEMPA VS BENTURAN (Time Windowing)
    if (smoothed_pga >= BATAS_AWAL_GETARAN) {
      waktu_terakhir_getar = millis(); 
      
      if (waktu_mulai_getar == 0) {
        waktu_mulai_getar = millis(); 
      }

      unsigned long durasi_bergetar = millis() - waktu_mulai_getar;
      
      if (durasi_bergetar >= SYARAT_DURASI_GEMPA && smoothed_pga >= BATAS_ALARM_BUNYI) {
        status_buzzer_aktif = true;      
        buzzer_timer = millis();         
        digitalWrite(BUZZER_PIN, HIGH);  
      }
    }

    if (waktu_mulai_getar > 0 && (millis() - waktu_terakhir_getar > JEDA_RESET_GETARAN)) {
      waktu_mulai_getar = 0; 
    }

    // --- PROSEDUR PENGUNCIAN SUARA 1 DETIK ---
    if (status_buzzer_aktif) {
      if (millis() - buzzer_timer >= 1000) {
        status_buzzer_aktif = false;   
        digitalWrite(BUZZER_PIN, LOW); 
      }
    } else {
      digitalWrite(BUZZER_PIN, LOW); 
    }

    // --- Pembaruan Antarmuka (Setiap 1000ms) ---
    static unsigned long lastUpdate = 0;
    if (millis() - lastUpdate > 1000) {
      String mmiStatus = getStatusMMI(smoothed_pga);
      
      display.clearDisplay();
      display.setCursor(0, 0);
      display.println("-- SIGMA Penopang --");
      display.print("PGA: "); display.print(smoothed_pga, 2); display.println(" m/s2");
      display.print("MMI: "); display.println(mmiStatus);
      
      // Menggunakan sintaks TinyGPSPlus
      if (gps.location.isValid()) {
        display.print("Lat: "); display.println(gps.location.lat(), 4);
      } else {
        display.println("GPS: Mencari Sinyal");
      }
      display.display();

      Serial.println("============================");
      Serial.print("Getaran (Filter): "); Serial.print(smoothed_pga, 2); Serial.println(" m/s2");
      Serial.print("Status MMI      : "); Serial.println(mmiStatus);
      
      if (waktu_mulai_getar > 0) {
        Serial.print("Durasi Getaran  : "); Serial.print(millis() - waktu_mulai_getar); Serial.println(" ms");
      }

      // Menggunakan sintaks TinyGPSPlus
      if (gps.location.isValid()) {
        Serial.print("Lokasi GPS      : Lat "); 
        Serial.print(gps.location.lat(), 6);
        Serial.print(", Lng ");
        Serial.println(gps.location.lng(), 6);
      } else {
        Serial.println("Lokasi GPS      : Mencari Sinyal Satelit...");
      }
      
      if (status_buzzer_aktif) {
         Serial.println("ALARM BUZZER    : AKTIF !!! (GEMPA DETECTED)");
      } else {
         Serial.println("ALARM BUZZER    : Standby (Aman)");
      }
      Serial.println("============================\n");

      if (millis() - lastUploadMillis >= UPLOAD_INTERVAL_MS) {
        bool gpsSent = sendGpsJson();
        bool accelSent = sendAccelerometerJson(smoothed_pga, mmiStatus);
        Serial.print("Upload GPS       : ");
        Serial.println(gpsSent ? "Berhasil" : "Gagal");
        Serial.print("Upload Accel     : ");
        Serial.println(accelSent ? "Berhasil" : "Gagal");
        lastUploadMillis = millis();
      }

      lastUpdate = millis(); 
    }
  }
}
/*
 * SmartPark IoT — ESP32 Firmware v2.0
 * Kirim data sensor langsung ke Laravel API + Supabase
 * 
 * Pin Mapping:
 *   HC-SR04 KIRI   → TRIG: 5,  ECHO: 18
 *   HC-SR04 KANAN  → TRIG: 33, ECHO: 25
 *   HC-SR04 BKNG   → TRIG: 19, ECHO: 32
 *   Buzzer         → 26
 *   LED Hijau      → 27
 *   LED Kuning     → 14
 *   LED Merah      → 12
 *   LCD I2C        → SDA: 21, SCL: 22 (Addr: 0x27)
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// ============== KONFIGURASI ==============
const char* WIFI_SSID     = "ZERO26";
const char* WIFI_PASS     = "nomorRumahku";

// Ganti dengan IP komputer kamu (cek pakai ipconfig)
// Kalau pakai laragon, pake IP lokal: 192.168.x.x
// Kalau pakai artisan serve, pake IP komputer + port 8000
const char* API_BASE_URL  = "http://192.168.1.11/api/v1";
const char* DEVICE_ID     = "ESP32-PARKIR-001";
const char* DEVICE_NAME   = "Sensor Parkir Utama";
const char* FW_VERSION    = "2.0.0";

// Threshold jarak (cm) — harus sama dengan config/sensor.php
const int THRESHOLD_SAFE    = 50;
const int THRESHOLD_WARNING = 20;

// Interval (ms)
const int LOOP_DELAY      = 50;
const int SEND_INTERVAL   = 5000;  // kirim data tiap 5 detik (server lambat)
// =========================================

LiquidCrystal_I2C lcd(0x27, 16, 2);

// Pin Sensor Ultrasonik
const int TRIG_KIRI = 5,    ECHO_KIRI = 18;
const int TRIG_KANAN = 33,  ECHO_KANAN = 25;
const int TRIG_BELAKANG = 19, ECHO_BELAKANG = 32;

// Pin Aktuator
const int BUZZER = 26, LED_HIJAU = 27, LED_KUNING = 14, LED_MERAH = 12;

String apiToken = "";
unsigned long lastSend = 0;

int jarakKiri = 0, jarakKanan = 0, jarakBelakang = 0;
String statusKiri, statusKanan, statusBelakang, overallStatus;

// ======================= SETUP =======================
void setup() {
  Serial.begin(115200);
  Serial.println("\n\n=== SmartPark IoT v2.0 ===");

  initPins();
  initLCD();
  connectWiFi();
  registerDevice();

  Serial.println("=== SETUP SELESAI ===\n");
  updateLCD("Siap!", "");
  delay(2000);

  // Set lastSend agar tidak langsung kirim data setelah register
  lastSend = millis();
}

void initPins() {
  pinMode(TRIG_KIRI, OUTPUT); pinMode(ECHO_KIRI, INPUT);
  pinMode(TRIG_KANAN, OUTPUT); pinMode(ECHO_KANAN, INPUT);
  pinMode(TRIG_BELAKANG, OUTPUT); pinMode(ECHO_BELAKANG, INPUT);
  pinMode(BUZZER, OUTPUT);
  pinMode(LED_HIJAU, OUTPUT);
  pinMode(LED_KUNING, OUTPUT);
  pinMode(LED_MERAH, OUTPUT);

  digitalWrite(LED_HIJAU, LOW);
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_MERAH, LOW);
  digitalWrite(BUZZER, LOW);
}

void initLCD() {
  lcd.init();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("SmartPark IoT");
  lcd.setCursor(0, 1);
  lcd.print("v2.0");
}

void updateLCD(String baris1, String baris2) {
  lcd.setCursor(0, 0);
  lcd.print("                ");
  lcd.setCursor(0, 0);
  lcd.print(baris1);
  lcd.setCursor(0, 1);
  lcd.print("                ");
  lcd.setCursor(0, 1);
  lcd.print(baris2);
}

// ======================= WiFi =======================
void connectWiFi() {
  Serial.print("[WiFi] Menghubungkan ke ");
  Serial.println(WIFI_SSID);

  updateLCD("WiFi Conn...", "");

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 30) {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[WiFi] Terhubung!");
    Serial.print("[WiFi] IP: ");
    Serial.println(WiFi.localIP());
    updateLCD("WiFi OK", WiFi.localIP().toString().c_str());
    delay(1500);
  } else {
    Serial.println("\n[WiFi] GAGAL!");
    updateLCD("WiFi GAGAL!", "Restart ESP...");
    delay(3000);
    ESP.restart();
  }
}

// ======================= REGISTER DEVICE =======================
void registerDevice() {
  if (WiFi.status() != WL_CONNECTED) return;

  updateLCD("Register...", "");

  StaticJsonDocument<256> doc;
  doc["device_id"] = DEVICE_ID;
  doc["name"] = DEVICE_NAME;
  doc["firmware_version"] = FW_VERSION;
  doc["ip_address"] = WiFi.localIP().toString();

  String body;
  serializeJson(doc, body);

  for (int i = 0; i < 3; i++) {
    if (apiToken.length() > 0) break;

    Serial.printf("[API] Register (percobaan %d)...\n", i + 1);

    HTTPClient http;
    http.begin(String(API_BASE_URL) + "/devices/register");
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(10000);

    int httpCode = http.POST(body);
    String responseBody = http.getString();
    http.end();

    if (httpCode == 200) {
      StaticJsonDocument<512> res;
      DeserializationError err = deserializeJson(res, responseBody);
      if (!err) {
        apiToken = res["api_token"].as<String>();
        Serial.print("[API] Register sukses! Token: ");
        Serial.println(apiToken);
        updateLCD("Register OK", "Token saved");
      }
    } else {
      Serial.printf("[API] Gagal HTTP %d, tunggu 5 detik...\n", httpCode);
      delay(5000);
    }
  }

  if (apiToken.length() == 0) {
    Serial.println("[API] Register GAGAL total");
    updateLCD("Reg GAGAL!", "Restart...");
    delay(3000);
    ESP.restart();
  }
}

// ======================= BACA SENSOR =======================
int bacaJarak(int trig, int echo) {
  digitalWrite(trig, LOW);
  delayMicroseconds(2);
  digitalWrite(trig, HIGH);
  delayMicroseconds(10);
  digitalWrite(trig, LOW);

  long duration = pulseIn(echo, HIGH, 30000);
  if (duration == 0) return 400;

  return duration * 0.034 / 2;
}

String calcStatus(int jarak) {
  if (jarak > THRESHOLD_SAFE) return "SAFE";
  if (jarak >= THRESHOLD_WARNING) return "WARNING";
  return "DANGER";
}

String calcOverall(String s1, String s2, String s3) {
  if (s1 == "DANGER" || s2 == "DANGER" || s3 == "DANGER") return "DANGER";
  if (s1 == "WARNING" || s2 == "WARNING" || s3 == "WARNING") return "WARNING";
  return "SAFE";
}

// ======================= LED & BUZZER =======================
void updateAktuator() {
  // Matikan semua LED dulu
  digitalWrite(LED_MERAH, LOW);
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_HIJAU, LOW);

  if (overallStatus == "DANGER") {
    digitalWrite(LED_MERAH, HIGH);
    digitalWrite(BUZZER, (millis() % 200 < 100) ? HIGH : LOW);
  } else if (overallStatus == "WARNING") {
    digitalWrite(LED_KUNING, HIGH);
    digitalWrite(BUZZER, (millis() % 600 < 100) ? HIGH : LOW);
  } else {
    digitalWrite(LED_HIJAU, HIGH);
    digitalWrite(BUZZER, LOW);
  }
}

// ======================= LCD DISPLAY =======================
void updateLCDDisplay() {
  char buf1[17], buf2[17];

  snprintf(buf1, 17, "L:%dcm R:%dcm", jarakKiri, jarakKanan);
  snprintf(buf2, 17, "B:%dcm %s", jarakBelakang, overallStatus.c_str());

  lcd.setCursor(0, 0);
  lcd.print("                ");
  lcd.setCursor(0, 0);
  lcd.print(buf1);

  lcd.setCursor(0, 1);
  lcd.print("                ");
  lcd.setCursor(0, 1);
  lcd.print(buf2);
}

// ======================= KIRIM DATA =======================
void sendSensorData() {
  if (WiFi.status() != WL_CONNECTED) return;
  if (apiToken.length() == 0) return;

  StaticJsonDocument<512> doc;
  doc["device_id"] = DEVICE_ID;
  doc["sensor_left"] = jarakKiri;
  doc["sensor_right"] = jarakKanan;
  doc["sensor_back"] = jarakBelakang;
  doc["wifi_rssi"] = WiFi.RSSI();
  doc["firmware_version"] = FW_VERSION;

  String body;
  serializeJson(doc, body);

  WiFiClient client;
  HTTPClient http;
  http.begin(client, String(API_BASE_URL) + "/sensor-data");
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + apiToken);
  http.setTimeout(2000);

  int httpCode = http.POST(body);
  if (httpCode == 201) {
    Serial.println("[API] OK");
  } else if (httpCode > 0) {
    Serial.printf("[API] HTTP %d\n", httpCode);
  }

  http.end();
}

// ======================= MAIN LOOP =======================
void loop() {
  // Baca sensor
  jarakKiri = bacaJarak(TRIG_KIRI, ECHO_KIRI);
  jarakBelakang = bacaJarak(TRIG_BELAKANG, ECHO_BELAKANG);
  jarakKanan = bacaJarak(TRIG_KANAN, ECHO_KANAN);

  // Hitung status
  statusKiri = calcStatus(jarakKiri);
  statusKanan = calcStatus(jarakKanan);
  statusBelakang = calcStatus(jarakBelakang);
  overallStatus = calcOverall(statusKiri, statusKanan, statusBelakang);

  // Update aktuator & LCD (tidak pernah delay)
  updateAktuator();
  updateLCDDisplay();

  // Kirim data tiap SEND_INTERVAL ms (blocking maks 1,5 detik)
  if (millis() - lastSend >= SEND_INTERVAL) {
    lastSend = millis();
    sendSensorData();
  }

  delay(LOOP_DELAY);
}

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
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <WiFiClientSecure.h>

// ============== KONFIGURASI ==============
const char* WIFI_SSID     = "ZERO26";
const char* WIFI_PASS     = "nomorRumahku";

// Ganti dengan IP komputer kamu (cek pakai ipconfig)
// Kalau pakai laragon, pake IP lokal: 192.168.x.x
// Kalau pakai artisan serve, pake IP komputer + port 8000
const char* API_BASE_URL  = "https://easypark.my.id/api/v1";
const char* DEVICE_ID     = "ESP32-PARKIR-001";
const char* DEVICE_NAME   = "Sensor Parkir Utama";
const char* FW_VERSION    = "2.0.0";

// Threshold jarak (cm) — harus sama dengan config/sensor.php
const int THRESHOLD_SAFE    = 50;
const int THRESHOLD_WARNING = 20;

// Interval (ms)
const int LOOP_DELAY      = 50;
const int SEND_INTERVAL   = 10000;  // kirim data tiap 10 detik (server lambat)
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
WiFiClientSecure clientSecure;
HTTPClient http;

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
  pinMode(LED_HIJAU, OUTPUT);
  pinMode(LED_KUNING, OUTPUT);
  pinMode(LED_MERAH, OUTPUT);

  digitalWrite(LED_HIJAU, LOW);
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_MERAH, LOW);

  // Setup buzzer PWM (2kHz, 8-bit)
  ledcAttach(BUZZER, 2000, 8);
  ledcWrite(BUZZER, 0);
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

    WiFiClientSecure regClient;
    regClient.setInsecure();
    HTTPClient httpReg;
    httpReg.begin(regClient, String(API_BASE_URL) + "/devices/register");
    httpReg.addHeader("Content-Type", "application/json");
    httpReg.setTimeout(5000);

    int httpCode = httpReg.POST(body);
    String responseBody = httpReg.getString();
    httpReg.end();

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
      Serial.printf("[API] Register HTTP %d\n", httpCode);
      delay(2000);
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

  unsigned long timeout = micros() + 30000UL;
  while (digitalRead(echo) == LOW && micros() < timeout);
  if (micros() >= timeout) return 400;

  unsigned long start = micros();
  timeout = micros() + 30000UL;
  while (digitalRead(echo) == HIGH && micros() < timeout);
  if (micros() >= timeout) return 400;

  return (micros() - start) * 0.034 / 2;
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
  digitalWrite(LED_MERAH, LOW);
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_HIJAU, LOW);

  if (overallStatus == "DANGER") {
    digitalWrite(LED_MERAH, HIGH);
    if (millis() % 200 < 100) {
      ledcWriteTone(BUZZER, 2500);
    } else {
      ledcWrite(BUZZER, 0);
    }
  } else if (overallStatus == "WARNING") {
    digitalWrite(LED_KUNING, HIGH);
    if (millis() % 600 < 200) {
      ledcWriteTone(BUZZER, 1800);
    } else {
      ledcWrite(BUZZER, 0);
    }
  } else {
    digitalWrite(LED_HIJAU, HIGH);
    ledcWrite(BUZZER, 0);
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

  unsigned long t = millis();
  if (!clientSecure.connected()) {
    clientSecure.setInsecure();
    clientSecure.setTimeout(1500);
    if (!clientSecure.connect("easypark.my.id", 443)) {
      Serial.printf("[API] SSL fail (%lums)\n", millis() - t);
      return;
    }
  }

  clientSecure.printf("POST /api/v1/sensor-data HTTP/1.1\r\n"
    "Host: easypark.my.id\r\n"
    "Content-Type: application/json\r\n"
    "Authorization: Bearer %s\r\n"
    "Content-Length: %d\r\n"
    "Connection: keep-alive\r\n"
    "\r\n"
    "%s",
    apiToken.c_str(), body.length(), body.c_str()
  );

  while (clientSecure.available() == 0) {
    if (millis() - t > 3000) break;
    delay(1);
  }
  while (clientSecure.available()) {
    clientSecure.read();
  }
  Serial.printf("[API] OK (%lums)\n", millis() - t);
}

// ======================= MAIN LOOP =======================
void loop() {
  // Baca sensor (belakang duluan, jeda anti-crosstalk)
  jarakBelakang = bacaJarak(TRIG_BELAKANG, ECHO_BELAKANG);
  delay(20);
  jarakKiri = bacaJarak(TRIG_KIRI, ECHO_KIRI);
  delay(20);
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

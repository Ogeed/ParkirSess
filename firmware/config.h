#ifndef CONFIG_H
#define CONFIG_H

// WiFi
#define WIFI_SSID     "NamaWiFi"
#define WIFI_PASSWORD "PasswordWiFi"

// API
#define API_BASE_URL  "https://smartpark-api.railway.app/api/v1"
#define DEVICE_ID     "ESP32-PARKIR-001"
#define DEVICE_NAME   "Sensor Parkir Utama"

// Pin Mapping (sesuai firmware.ino & PRD)
#define TRIG_LEFT     5
#define ECHO_LEFT     18
#define TRIG_RIGHT    33
#define ECHO_RIGHT    25
#define TRIG_BACK     19
#define ECHO_BACK     32
#define BUZZER_PIN    26
#define LED_RED       12
#define LED_YELLOW    14
#define LED_GREEN     27
#define LCD_SDA       21
#define LCD_SCL       22

// Threshold jarak (cm) — harus sama dengan config/sensor.php
#define THRESHOLD_SAFE    50
#define THRESHOLD_WARNING 20

// Interval (ms) — sesuai firmware.ino
#define LOOP_DELAY_MS        50
#define SEND_INTERVAL_MS     5000
#define API_TIMEOUT_MS       2000
#define API_RETRY_COUNT      3

// Buzzer interval (ms)
#define BUZZ_SAFE_INTERVAL    0
#define BUZZ_WARNING_INTERVAL 600
#define BUZZ_DANGER_INTERVAL  200

// Ultrasonic
#define MAX_DISTANCE_CM 400
#define SOUND_SPEED     0.034

#endif

#ifndef CONFIG_H
#define CONFIG_H

// WiFi
#define WIFI_SSID     "NamaWiFi"
#define WIFI_PASSWORD "PasswordWiFi"

// API
#define API_BASE_URL  "https://smartpark-api.railway.app/api/v1"
#define DEVICE_ID     "ESP32-PARKIR-001"
#define DEVICE_NAME   "Sensor Parkir Utama"

// Pin Mapping
#define TRIG_LEFT     5
#define ECHO_LEFT     18
#define TRIG_RIGHT    19
#define ECHO_RIGHT    21
#define TRIG_BACK     22
#define ECHO_BACK     23
#define BUZZER_PIN    25
#define LED_RED       26
#define LED_YELLOW    27
#define LED_GREEN     14
#define LCD_SDA       21
#define LCD_SCL       22

// Threshold jarak (cm)
#define THRESHOLD_SAFE    50
#define THRESHOLD_WARNING 20

// Interval (ms)
#define LOOP_INTERVAL_MS      500
#define API_SEND_INTERVAL_MS  1000
#define API_TIMEOUT_MS        2000
#define API_RETRY_COUNT       3

// Buzzer interval (ms)
#define BUZZ_SAFE_MS    0
#define BUZZ_WARNING_MS 500
#define BUZZ_DANGER_MS  100

// Ultrasonic
#define MAX_DISTANCE_CM 400
#define SOUND_SPEED     0.034

#endif

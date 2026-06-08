# 📋 PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Sistem Dashboard IoT Sensor Parkir Kendaraan

---

> **Dokumen ini adalah PROJECT GROUNDING utama.**
> Setiap AI yang mengerjakan proyek ini WAJIB membaca dokumen ini terlebih dahulu sebelum menulis satu baris kode pun.
> Gunakan tanda ✅ di samping setiap fitur/section yang sudah selesai diimplementasikan.

---

## 📌 CHANGELOG & STATUS IMPLEMENTASI

| Versi | Tanggal | Perubahan | Status |
|-------|---------|-----------|--------|
| 1.0.0 | 2026-06-03 | Dokumen PRD dibuat pertama kali | 🟡 Draft |
| 1.1.0 | 2026-06-05 | Implementasi penuh: Backend, Frontend, Firmware, Database lokal | ✅ Implemented |

---

## 1. RINGKASAN PROYEK

### 1.1 Nama Proyek
**SmartPark IoT — Sistem Sensor Parkir Kanan, Kiri & Belakang Kendaraan**

### 1.2 Latar Belakang ✅
Benturan kendaraan saat parkir merupakan salah satu insiden paling umum di area parkir, baik di rumah, mall, maupun perkantoran. Sistem ini hadir untuk memberikan umpan balik visual dan audio secara real-time kepada pengemudi berdasarkan jarak kendaraan terhadap objek di sekitarnya menggunakan sensor ultrasonik yang terhubung ke ESP32, serta menampilkan data tersebut di sebuah dashboard web berbasis Laravel.

### 1.3 Tujuan Proyek ✅
1. Mendeteksi jarak objek di sisi **kanan**, **kiri**, dan **belakang** kendaraan secara real-time.
2. Memberikan umpan balik melalui **buzzer** (frekuensi bunyi makin cepat = makin dekat) dan **LED** (Hijau / Kuning / Merah).
3. Menampilkan jarak pada **LCD 1602** yang terpasang di kendaraan.
4. Mengirimkan data sensor ke **PostgreSQL lokal** via **RESTful API** secara berkala.
5. Menyediakan **dashboard web** berbasis Laravel untuk monitoring real-time, historis, dan visualisasi status.

### 1.4 Stakeholder ✅

| Peran | Nama / Entitas | Kepentingan |
|-------|----------------|-------------|
| Owner / Developer | Tim Proyek | Membangun dan memelihara sistem |
| End User | Pengemudi Kendaraan | Menggunakan umpan balik parkir |
| Admin Dashboard | Operator | Monitoring data dan status perangkat |

---

## 2. ARSITEKTUR SISTEM ✅

### 2.1 Gambaran Umum Arsitektur

```
┌──────────────────────────────────────────────┐
│              HARDWARE LAYER                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │ Sensor   │  │ Sensor   │  │ Sensor   │   │
│  │Ultrasonik│  │Ultrasonik│  │Ultrasonik│   │
│  │  KIRI    │  │  KANAN   │  │BELAKANG  │   │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘   │
│       └──────────────┴─────────────┘         │
│                      │                       │
│               ┌──────▼──────┐                │
│               │   ESP32     │                │
│               │ Microcontrl │                │
│               └──┬──┬──┬───┘                │
│          ┌───────┘  │  └────────┐            │
│     ┌────▼───┐ ┌────▼────┐ ┌───▼────┐       │
│     │ Buzzer │ │LED (R/Y/G│ │LCD 1602│       │
│     └────────┘ └─────────┘ └────────┘       │
└──────────────────────┬───────────────────────┘
                       │ WiFi / HTTP POST
                       ▼
┌──────────────────────────────────────────────┐
│              BACKEND / API LAYER             │
│                                              │
│  ┌────────────────────────────────────────┐  │
│  │       Laravel 11 RESTful API           │  │
│  │  POST /api/v1/sensor-data              │  │
│  │  GET  /api/v1/sensor-data              │  │
│  │  GET  /api/v1/sensor-data/latest       │  │
│  │  GET  /api/v1/devices/{id}/status      │  │
│  └──────────────────┬─────────────────────┘  │
│                     │                        │
│  ┌──────────────────▼─────────────────────┐  │
│  │      PostgreSQL 14 (Lokal - Laragon)   │  │
│  │  Tables: sensor_readings, devices,     │  │
│  │          sensor_alerts, users          │  │
│  └────────────────────────────────────────┘  │
└──────────────────────┬───────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────┐
│           DASHBOARD / FRONTEND LAYER         │
│                                              │
│        Laravel 11 + Blade + Livewire         │
│          (Real-time via Livewire Polling)    │
│                                              │
│  - Live Distance Display            ✅       │
│  - Riwayat Jarak (Table)            ✅       │
│  - Top View Car Visualization       ✅       │
│  - Status Keamanan (Kiri/Kanan/Belakang) ✅  │
│  - Live Chart Jarak vs Waktu        ✅       │
│  - Status Perangkat ESP32           ✅       │
└──────────────────────────────────────────────┘
```

### 2.2 Stack Teknologi ✅

| Layer | Teknologi | Versi | Keterangan |
|-------|-----------|-------|------------|
| **Firmware** | Arduino IDE / C++ | - | Kode ESP32 |
| **Mikrokontroler** | ESP32 DevKit | - | WiFi built-in |
| **Sensor** | HC-SR04 Ultrasonic | - | 3 buah (kiri, kanan, belakang) |
| **Aktuator** | Buzzer Aktif | - | Feedback audio |
| **Aktuator** | LED RGB / Terpisah | - | Merah, Kuning, Hijau |
| **Display** | LCD 1602 + I2C | - | Tampilan jarak lokal |
| **Backend** | Laravel | 11 | RESTful API + Dashboard |
| **Database** | PostgreSQL (Laragon) | 14.5 | Database lokal |
| **Frontend** | Blade + Livewire | 3 | Real-time UI |
| **Charting** | Chart.js | 4.4 | Grafik live |
| **CSS Framework** | Tailwind CSS | 3.x | Styling dashboard |
| **Web Server** | Apache (Laragon) | - | Hosting lokal |
| **Auth** | Laravel Sanctum + Session | - | API Token + Session Auth |

---

## 3. HARDWARE SPECIFICATION ✅

### 3.1 Komponen Hardware

| Komponen | Jumlah | Fungsi |
|----------|--------|--------|
| ESP32 DevKit V1 | 1 | Mikrokontroler utama, WiFi |
| Sensor Ultrasonik HC-SR04 | 3 | Pengukur jarak (kiri, kanan, belakang) |
| Buzzer Aktif 5V | 1 | Feedback audio berdasarkan jarak |
| LED Merah | 1 | Indikator zona bahaya (< 20 cm) |
| LED Kuning | 1 | Indikator zona waspada (20–50 cm) |
| LED Hijau | 1 | Indikator zona aman (> 50 cm) |
| LCD 1602 + Modul I2C | 1 | Tampilan jarak lokal |
| Resistor 220Ω | 3 | Pembatas arus LED |
| Power Supply 5V | 1 | Sumber daya sistem |
| Breadboard / PCB | 1 | Rangkaian komponen |

### 3.2 Pin Mapping ESP32 (Aktual)

| Komponen | Pin ESP32 | Mode |
|----------|-----------|------|
| HC-SR04 KIRI — TRIG | GPIO 5 | OUTPUT |
| HC-SR04 KIRI — ECHO | GPIO 18 | INPUT |
| HC-SR04 KANAN — TRIG | GPIO 33 | OUTPUT |
| HC-SR04 KANAN — ECHO | GPIO 25 | INPUT |
| HC-SR04 BELAKANG — TRIG | GPIO 19 | OUTPUT |
| HC-SR04 BELAKANG — ECHO | GPIO 32 | INPUT |
| Buzzer | GPIO 26 | OUTPUT |
| LED Hijau | GPIO 27 | OUTPUT |
| LED Kuning | GPIO 14 | OUTPUT |
| LED Merah | GPIO 12 | OUTPUT |
| LCD SDA (I2C) | GPIO 21 | I2C SDA |
| LCD SCL (I2C) | GPIO 22 | I2C SCL |

### 3.3 Logika Threshold Jarak

| Zona | Jarak | Warna LED | Buzzer | Status |
|------|-------|-----------|--------|--------|
| **AMAN** | > 50 cm | 🟢 Hijau | OFF | SAFE |
| **WASPADA** | 20–50 cm | 🟡 Kuning | Beep lambat (600ms) | WARNING |
| **BAHAYA** | < 20 cm | 🔴 Merah | Beep cepat (200ms) | DANGER |

### 3.4 Logika LCD Display
```
Baris 1: L:XXcm R:XXcm
Baris 2: B:XXcm [STATUS]
```

---

## 4. API SPECIFICATION (RESTful) ✅

### 4.1 Base URL
```
Development: http://easypark.test/api/v1  (Laragon vhost)
             http://192.168.1.11/api/v1    (via IP lokal)
```

### 4.2 Authentication ✅
- ESP32 menggunakan **API Token** (Bearer Token) yang di-hardcode di firmware.
- Dashboard internal menggunakan **Laravel Session Auth**.
- API eksternal menggunakan **Laravel Sanctum Token**.

### 4.3 Endpoint: Kirim Data Sensor (ESP32 → Server)

**POST** `/api/v1/sensor-data`

| Field | Tipe | Wajib | Keterangan |
|-------|------|-------|------------|
| device_id | string | ✅ | ID perangkat terdaftar |
| sensor_left | numeric | ✅ | Jarak kiri (0-400 cm) |
| sensor_right | numeric | ✅ | Jarak kanan (0-400 cm) |
| sensor_back | numeric | ✅ | Jarak belakang (0-400 cm) |
| wifi_rssi | integer | - | Kekuatan sinyal |
| firmware_version | string | - | Versi firmware |
| battery_level | integer | - | Level baterai (0-100) |
| timestamp | date | - | Waktu baca |

### 4.4 Endpoint Lainnya ✅

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/api/v1/sensor-data` | Riwayat data sensor |
| GET | `/api/v1/sensor-data/latest` | Data terbaru |
| GET | `/api/v1/sensor-data/chart` | Data untuk chart |
| GET | `/api/v1/devices` | Daftar perangkat |
| GET | `/api/v1/devices/{id}/status` | Status perangkat |
| POST | `/api/v1/devices/register` | Register ESP32 |
| PUT | `/api/v1/devices/{id}` | Update perangkat |
| DELETE | `/api/v1/devices/{id}` | Hapus perangkat |
| POST | `/api/v1/auth/login` | Login API (Sanctum) |
| GET | `/api/v1/auth/me` | Profile user |

---

## 5. DATABASE SCHEMA (PostgreSQL 14 Lokal) ✅

### 5.1 Relasi Tabel

```
┌──────────┐       ┌──────────────────┐       ┌────────────────┐
│  devices │──<────│ sensor_readings  │──<────│ sensor_alerts  │
│  (PK:id) │       │  (FK:device_id)  │       │  (FK:device_id)│
└──────────┘       └──────────────────┘       │  (FK:reading_id│
                                              │   nullable)    │
┌──────────┐                                  └────────────────┘
│  users   │  (independent)
│  (PK:id) │
└──────────┘
```

### 5.2 Tabel: `devices`

| Column | Type | Keterangan |
|--------|------|------------|
| id | VARCHAR(50) PK | `ESP32-PARKIR-001` |
| name | VARCHAR(100) | Nama perangkat |
| firmware_version | VARCHAR(20) | Versi firmware ESP32 |
| ip_address | VARCHAR(45) | IP ESP32 |
| wifi_rssi | INTEGER | Kekuatan sinyal WiFi |
| api_token | VARCHAR(255) UNIQUE | Hash SHA-256 |
| is_online | BOOLEAN | Default false |
| last_seen | TIMESTAMPTZ | Terakhir kirim data |
| battery_level | INTEGER | Level baterai (0-100) |
| uptime_seconds | INTEGER | Lama menyala (detik) |
| deleted_at | TIMESTAMPTZ | Soft delete |
| created_at | TIMESTAMPTZ | Waktu buat |
| updated_at | TIMESTAMPTZ | Waktu update |

### 5.3 Tabel: `sensor_readings`

| Column | Type | Keterangan |
|--------|------|------------|
| id | UUID PK | Auto generate |
| device_id | VARCHAR(50) FK | → devices.id |
| sensor_left | NUMERIC(6,2) | Jarak kiri (cm) |
| sensor_right | NUMERIC(6,2) | Jarak kanan (cm) |
| sensor_back | NUMERIC(6,2) | Jarak belakang (cm) |
| status_left | VARCHAR(10) | SAFE / WARNING / DANGER |
| status_right | VARCHAR(10) | SAFE / WARNING / DANGER |
| status_back | VARCHAR(10) | SAFE / WARNING / DANGER |
| overall_status | VARCHAR(10) | Status terburuk dari 3 |
| wifi_rssi | INTEGER | RSSI saat baca |
| created_at | TIMESTAMPTZ | Waktu baca |

Index: `(device_id, created_at DESC)`

### 5.4 Tabel: `sensor_alerts`

| Column | Type | Keterangan |
|--------|------|------------|
| id | UUID PK | Auto generate |
| device_id | VARCHAR(50) FK | → devices.id |
| reading_id | UUID FK nullable | → sensor_readings.id |
| alert_type | VARCHAR(20) | DANGER / DEVICE_OFFLINE / DEVICE_ONLINE |
| sensor_position | VARCHAR(10) | LEFT / RIGHT / BACK / null |
| distance_value | NUMERIC(6,2) | Jarak saat alert (cm) |
| is_acknowledged | BOOLEAN | Default false |
| acknowledged_at | TIMESTAMPTZ | Waktu ditandai |
| created_at | TIMESTAMPTZ | Waktu alert |

### 5.5 Tabel: `users`

| Column | Type | Keterangan |
|--------|------|------------|
| id | BIGINT PK | Auto increment |
| name | VARCHAR(255) | Nama admin |
| email | VARCHAR(255) UNIQUE | Login |
| password | VARCHAR(255) | Bcrypt hash |
| role | VARCHAR(20) | Default 'admin' |
| created_at | TIMESTAMP | Waktu buat |
| updated_at | TIMESTAMP | Waktu update |

### 5.6 Logika Kalkulasi Status

```php
// SensorStatusHelper.php ✅
public static function calculateStatus(float $distance): string
{
    if ($distance > config('sensor.threshold_safe')) return 'SAFE';
    if ($distance >= config('sensor.threshold_warning')) return 'WARNING';
    return 'DANGER';
}

public static function calculateOverallStatus(string $left, string $right, string $back): string
{
    $priority = ['DANGER' => 3, 'WARNING' => 2, 'SAFE' => 1];
    $worst = max($priority[$left], $priority[$right], $priority[$back]);
    return array_search($worst, $priority);
}
```

---

## 6. FIRMWARE ESP32 ✅

### 6.1 Flow Utama Firmware ✅

```
BOOT
  ├── 1. Inisialisasi Serial Monitor
  ├── 2. Inisialisasi LCD 1602 (I2C)
  ├── 3. Inisialisasi Pin Sensor (TRIG/ECHO × 3)
  ├── 4. Inisialisasi Pin LED (R/Y/G) dan Buzzer
  ├── 5. Connect ke WiFi (SSID + Password)
  ├── 6. Register device ke API (POST /api/v1/devices/register)
  └── 7. Simpan API Token ke RAM
       │
LOOP (setiap 50ms, kirim data tiap 5 detik)
  ├── A. Baca jarak dari 3 sensor (kiri, kanan, belakang)
  ├── B. Hitung status masing-masing sensor
  ├── C. Update LED berdasarkan overall status
  ├── D. Update Buzzer (interval berbeda per status)
  ├── E. Update LCD dengan nilai jarak
  └── F. Kirim data ke API tiap 5 detik via HTTP POST
```

### 6.2 Lokasi File ✅
- Firmware: `C:\laragon\www\easypark\firmware\firmware.ino`
- Konfigurasi: `C:\laragon\www\easypark\firmware\config.h`
- API: `http://{IP_LAPTOP}/api/v1`
- Device ID: `ESP32-PARKIR-001`

---

## 7. DASHBOARD — SPESIFIKASI HALAMAN & FITUR ✅

### 7.1 Struktur Halaman Dashboard

```
/login                  → Halaman Login Admin          ✅
/register               → Halaman Register Admin       ✅
/dashboard              → Halaman Utama (real-time)    ✅
/dashboard/history      → Riwayat data sensor          ✅
/dashboard/devices      → Manajemen perangkat ESP32    ✅
/dashboard/alerts       → Riwayat alert/peringatan     ✅
/dashboard/settings     → Pengaturan threshold, akun   ✅
```

### 7.2 Halaman Dashboard Utama (`/dashboard`) ✅

**Widget A — Status Bar Ringkasan** ✅
- 4 kartu: KIRI, KANAN, BELAKANG, PERANGKAT
- Jarak dalam cm dengan badge warna (Hijau/Kuning/Merah)
- Animasi pulse pada badge DANGER
- Status device ONLINE/OFFLINE dengan indikator titik

**Widget B — Visualisasi Top View Kendaraan** ✅
- SVG mobil tampak atas
- Bar dinamis di kiri, kanan, belakang (warna mengikuti threshold)
- Animasi transisi smooth

**Widget C — Grafik Live Chart.js** ✅
- 3 garis: Kiri (biru), Kanan (hijau), Belakang (merah)
- Rolling window 60 detik
- Threshold line dashed (50cm WARNING, 20cm DANGER)
- Legend toggle per sensor

**Widget D — Tabel Riwayat Real-time** ✅
- 10 data terbaru
- Highlight warna per status
- Link "Lihat Semua →" ke halaman history

### 7.3 Halaman Riwayat Data (`/dashboard/history`) ✅
- Filter: device, status, posisi sensor, range tanggal
- Pagination (25/50/100)
- Sortable columns
- Export CSV
- Highlight warna per baris
- Modal detail per baris

### 7.4 Halaman Manajemen Perangkat (`/dashboard/devices`) ✅
- Tabel perangkat dengan status online/offline
- Edit nama perangkat (inline)
- Reset API token
- Hapus perangkat (soft delete)
- Logika online: `last_seen < 30 detik`

### 7.5 Halaman Alert (`/dashboard/alerts`) ✅
- Tabel alert dengan filter tipe & tanggal
- DANGER (merah), DEVICE_OFFLINE (kuning), DEVICE_ONLINE (hijau)
- Mark as acknowledged (satu / semua)

### 7.6 Halaman Pengaturan (`/dashboard/settings`) ✅
- Threshold jarak (safe/warning)
- Update akun (nama, email, password)
- Tampilkan API base URL

---

## 8. LIVEWIRE COMPONENTS ✅

| Component | Halaman | Fungsi | Polling |
|-----------|---------|--------|---------|
| `SensorStatusCard` | Dashboard | 4 kartu status sensor + device | 1 detik |
| `CarTopView` | Dashboard | SVG visualisasi top view | 1 detik |
| `LiveChart` | Dashboard | Grafik Chart.js real-time | 1 detik |
| `RecentReadings` | Dashboard | Tabel 10 data terbaru | 2 detik |
| `DeviceStatus` | Dashboard | Status online/offline | 5 detik |
| `HistoryTable` | History | Tabel + filter + pagination + sort | Manual |
| `DeviceList` | Devices | Tabel perangkat | 10 detik |
| `AlertList` | Alerts | Tabel alert + acknowledge | Manual |

---

## 9. STRUKTUR PROJECT LARAVEL ✅

```
C:\laragon\www\easypark\
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DeviceController.php
│   │   │   │   └── SensorDataController.php
│   │   │   └── Dashboard/
│   │   │       ├── AlertController.php
│   │   │       ├── AuthController.php
│   │   │       ├── DashboardController.php
│   │   │       ├── DeviceController.php
│   │   │       ├── HistoryController.php
│   │   │       └── SettingsController.php
│   │   └── Middleware/
│   │       └── EspTokenAuth.php
│   ├── Livewire/
│   │   ├── AlertList.php
│   │   ├── CarTopView.php
│   │   ├── DeviceList.php
│   │   ├── DeviceStatus.php
│   │   ├── HistoryTable.php
│   │   ├── LiveChart.php
│   │   ├── RecentReadings.php
│   │   └── SensorStatusCard.php
│   ├── Models/
│   │   ├── Device.php
│   │   ├── SensorAlert.php
│   │   ├── SensorReading.php
│   │   └── User.php
│   ├── Services/
│   │   ├── AlertService.php
│   │   ├── DeviceOnlineService.php
│   │   └── SensorStatusService.php
│   └── Helpers/
│       └── SensorStatusHelper.php
├── bootstrap/
│   └── app.php
├── config/
│   └── sensor.php
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── 0001_01_01_000001_create_cache_table.php
│       ├── 0001_01_01_000002_create_jobs_table.php
│       ├── 2024_01_01_000001_create_devices_table.php
│       ├── 2024_01_01_000002_create_sensor_readings_table.php
│       ├── 2024_01_01_000003_create_sensor_alerts_table.php
│       ├── 2024_01_01_000004_add_role_to_users_table.php
│       ├── 2026_06_03_183629_create_personal_access_tokens_table.php
│       └── 2026_06_04_000001_add_columns_to_devices_table.php
├── firmware/
│   ├── config.h
│   └── firmware.ino
├── resources/
│   └── views/
│       ├── auth/
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── dashboard/
│       │   ├── alerts.blade.php
│       │   ├── devices.blade.php
│       │   ├── history.blade.php
│       │   ├── index.blade.php
│       │   └── settings.blade.php
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── auth.blade.php
│       └── livewire/
│           ├── alert-list.blade.php
│           ├── car-top-view.blade.php
│           ├── device-list.blade.php
│           ├── device-status.blade.php
│           ├── history-table.blade.php
│           ├── live-chart.blade.php
│           ├── recent-readings.blade.php
│           └── sensor-status-card.blade.php
├── routes/
│   ├── api.php
│   └── web.php
├── .env
├── composer.json
└── package.json
```

---

## 10. KONFIGURASI DATABASE ✅

### 10.1 Koneksi Laravel → PostgreSQL Lokal

**`.env`:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=easypark
DB_USERNAME=Rico
DB_PASSWORD=
DB_SSLMODE=disable
```

**Database**: PostgreSQL 14.5 via Laragon
**Auth**: Trust (tanpa password untuk localhost)
**User default**: `Rico` (Superuser — sesuai username Windows)

---

## 11. SECURITY & VALIDASI ✅

### 11.1 Keamanan API ESP32

```php
// Middleware: EspTokenAuth.php ✅
public function handle(Request $request, Closure $next)
{
    $token = $request->bearerToken();
    $device = Device::where('api_token', hash('sha256', $token))->first();

    if (!$device) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $request->merge(['authenticated_device' => $device]);
    return $next($request);
}
```

### 11.2 Validasi Input Sensor ✅
- sensor_left/right/back: required, numeric, min:0, max:400
- device_id: required, exists:devices,id
- wifi_rssi: nullable, integer, min:-100, max:0
- firmware_version: nullable, string, max:20

### 11.3 Rate Limiting ✅
- 60 request per menit per device_id

---

## 12. REAL-TIME STRATEGY ✅

### 12.1 Livewire Polling (Aktif) ✅
Polling interval per komponen:
- SensorStatusCard: 1 detik
- CarTopView: 1 detik
- LiveChart: 1 detik
- RecentReadings: 2 detik
- DeviceStatus: 5 detik

### 12.2 Laravel Echo + Pusher/Soketi
Tidak digunakan. Menggunakan Livewire polling karena lebih sederhana.

---

## 13. DESAIN UI/UX ✅

### 13.1 Color Palette Dashboard

| Elemen | Warna Hex | Keterangan |
|--------|-----------|------------|
| Background | `#0F172A` | Dark navy (slate-900) |
| Card Background | `#1E293B` | Slate-800 |
| Border | `#334155` | Slate-700 |
| Primary Accent | `#3B82F6` | Blue-500 |
| Safe / Green | `#22C55E` | Green-500 |
| Warning / Yellow | `#EAB308` | Yellow-500 |
| Danger / Red | `#EF4444` | Red-500 |
| Text Primary | `#F1F5F9` | Slate-100 |
| Text Secondary | `#94A3B8` | Slate-400 |
| Font UI | Inter | Body text |
| Font Display | Hanken Grotesk | Headings |
| Font Mono | JetBrains Mono | Data & kode |

### 13.2 Responsivitas ✅
- Mobile (<768px): Single column
- Tablet (768–1024px): 2 column grid
- Desktop (>1024px): Full layout

---

## 14. TESTING PLAN

### 14.1 Unit Tests
| Test | Status |
|------|--------|
| Status kalkulasi | 🔲 |
| API validation | 🔲 |
| Device online logic | 🔲 |
| Alert creation | 🔲 |

### 14.2 Feature Tests (API)
| Test | Status |
|------|--------|
| ESP32 submit sensor data | 🔲 |
| Invalid token returns 401 | 🔲 |
| Out of range distance returns 422 | 🔲 |
| Overall status calculated correctly | 🔲 |

### 14.3 Hardware Testing Checklist ✅
- [x] Sensor KIRI membaca jarak dengan akurat
- [x] Sensor KANAN membaca jarak dengan akurat
- [x] Sensor BELAKANG membaca jarak dengan akurat
- [x] LED berganti warna sesuai threshold
- [x] Buzzer berbunyi dengan interval yang benar
- [x] LCD menampilkan 3 nilai jarak dengan format benar
- [x] ESP32 berhasil terkoneksi ke WiFi
- [x] Data berhasil dikirim ke API
- [x] Dashboard menerima dan menampilkan data real-time

---

## 15. DEPLOYMENT PLAN

### 15.1 Environment Saat Ini

| Environment | URL | Web Server | Database |
|-------------|-----|-----------|----------|
| Development | `http://easypark.test` | Laragon Apache | PostgreSQL 14 lokal |
| ESP32 | `http://192.168.1.11/api/v1` | via IP lokal | - |

### 15.2 Rencana Deploy ke VM Kampus
- Ganti `API_BASE_URL` di firmware ke IP VM
- Ganti `DB_HOST` di `.env` jika PostgreSQL terpisah
- Buka port 80 (Apache) dan 5432 (PostgreSQL) jika perlu

---

## 16. MILESTONES & TIMELINE ✅

### Fase 1 — Hardware & Firmware ✅
- [x] Rangkai semua komponen hardware
- [x] Tulis dan upload firmware ESP32
- [x] Test sensor ultrasonik × 3 secara lokal
- [x] Test buzzer + LED + LCD
- [x] Test koneksi WiFi ESP32

### Fase 2 — Backend & Database ✅
- [x] Setup project Laravel 11
- [x] Setup PostgreSQL lokal (Laragon)
- [x] Buat migration & model (Device, SensorReading, Alert)
- [x] Implementasi API: POST /sensor-data
- [x] Implementasi Middleware ESP Token Auth
- [x] Implementasi API: GET endpoints
- [x] Test API dengan Postman
- [x] Test ESP32 kirim data ke API lokal

### Fase 3 — Frontend Dashboard ✅
- [x] Setup Livewire + Tailwind CSS
- [x] Implementasi halaman login & register
- [x] Implementasi layout dashboard utama
- [x] Widget: SensorStatusCard (4 kartu)
- [x] Widget: CarTopView (SVG visualisasi)
- [x] Widget: LiveChart (Chart.js)
- [x] Widget: RecentReadings (tabel mini)
- [x] Widget: DeviceStatus (online/offline)
- [x] Halaman Riwayat Data (tabel + filter)
- [x] Halaman Manajemen Perangkat
- [x] Halaman Alert
- [x] Halaman Settings

### Fase 4 — Testing & Finishing
- [ ] Unit test & Feature test
- [x] Test end-to-end (ESP32 → API → Dashboard)
- [x] Optimasi query database (indexing)
- [ ] Dokumentasi API (Postman Collection)

---

## 17. DEFINISI OF DONE (DoD)

Sebuah fitur dinyatakan **selesai** jika:
1. ✅ Kode sudah di-commit dan di-push ke repository
2. ✅ Tidak ada error di console/log
3. ✅ Sudah di-test secara manual (fitur bekerja sesuai spesifikasi)
4. ✅ Sudah di-test dengan data dari ESP32 nyata (jika fitur hardware-related)
5. ✅ Responsif di mobile dan desktop
6. ✅ Loading state / error state sudah di-handle

---

## 18. CATATAN KHUSUS UNTUK AI DEVELOPER

> **🤖 Baca bagian ini sebelum mengkoding apapun!**

### Prinsip Koding Proyek Ini:

1. **Gunakan Laravel 11** — Pastikan syntax sesuai Laravel 11 (tidak ada `app/Http/Kernel.php`, gunakan bootstrap/app.php untuk middleware).

2. **Database adalah PostgreSQL** — Jangan gunakan syntax MySQL-only. Gunakan UUID untuk primary key (kecuali tabel `users`).

3. **Livewire untuk real-time** — Pendekatan default adalah Livewire polling.

4. **Tailwind CSS** — Semua styling menggunakan Tailwind utility classes.

5. **API menggunakan versioning** — Semua route API berawalan `/api/v1/`.

6. **ESP32 menggunakan Bearer Token** — Token di-hash dengan SHA-256 sebelum disimpan di database.

7. **Semua response API dalam format JSON** dengan struktur:
   ```json
   { "success": true/false, "data": {...}, "message": "..." }
   ```

8. **Threshold jarak** tersimpan di `config/sensor.php` bukan hardcoded.

9. **Jangan pernah** melakukan query database langsung di Blade/Livewire view.

---

## 19. GLOSSARY

| Istilah | Definisi |
|---------|----------|
| ESP32 | Mikrokontroler dengan WiFi bawaan, otak dari sistem hardware |
| HC-SR04 | Sensor ultrasonik untuk mengukur jarak |
| Livewire | Framework PHP untuk komponen reaktif di Laravel |
| PostgreSQL | Database relasional open-source |
| RESTful API | Arsitektur API berbasis HTTP |
| Bearer Token | Metode autentikasi API menggunakan token di header HTTP |
| Polling | Teknik refresh data berkala via HTTP |
| Top View | Tampilan kendaraan dari sudut pandang atas |
| Overall Status | Status terburuk dari 3 sensor |
| RSSI | Received Signal Strength Indicator |
| Laragon | Local development environment untuk Windows |

---

## 20. REFERENSI & RESOURCE

| Resource | URL / Keterangan |
|----------|-----------------|
| Laravel 11 Docs | https://laravel.com/docs/11.x |
| Livewire 3 Docs | https://livewire.laravel.com/docs |
| PostgreSQL 14 Docs | https://www.postgresql.org/docs/14/ |
| Chart.js Docs | https://www.chartjs.org/docs/latest/ |
| Tailwind CSS Docs | https://tailwindcss.com/docs |
| ESP32 Arduino Docs | https://docs.espressif.com/projects/arduino-esp32 |
| Laragon | https://laragon.org |

---

*PRD ini diperbarui pada 2026-06-05. Versi 1.1.0 — Status: ✅ Implemented*

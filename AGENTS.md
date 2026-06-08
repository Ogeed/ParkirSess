# SmartPark IoT — EasyPark

## Status
- Laravel 11 + Livewire 3 + Supabase (PostgreSQL via IPv6)
- Apache Laragon port 80 (vhost easypark.local), artisan serve port 8000
- ESP32 terdaftar, data sensor masuk ke Supabase (delay 3-8s)
- Dashboard: http://192.168.1.222/

## Firmware
- `C:\laragon\www\easypark\firmware\firmware.ino`
- API: `http://192.168.1.222/api/v1`
- Token: uPeJVlyGp9vMbHzEd5HkA2faoQa9XZsjxEhEXRsIMXSdSXQqR4x9OtEcIYrh3jGo
- Device ID: ESP32-PARKIR-001

## Issues
- Deploy ke VM kampus nanti: ganti API_BASE_URL, DB_HOST, buka port

## To Do
- [ ] Opsional: setup queue untuk response lebih cepat
- [ ] Deploy ke VM kampus: ganti IP di firmware + .env

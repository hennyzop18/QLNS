# 📋 Hệ Thống Quản Lý Nhân Sự & Chấm Công

> Tài liệu hướng dẫn cài đặt, khởi chạy và sử dụng ứng dụng

---

## 📌 Tổng Quan Hệ Thống

Đây là **hệ thống quản lý nhân sự và chấm công** gồm **2 ứng dụng** hoạt động liên kết với nhau:

| Ứng dụng | Thư mục | Mô tả |
|---|---|---|
| **Web App (Backend + Frontend)** | `qlnhansutest/` | Ứng dụng Laravel quản lý toàn bộ nghiệp vụ HR |
| **Kiosk Desktop App** | `kiosk-helper-app/` | Ứng dụng Electron hỗ trợ chấm công tại kiosk vật lý |

---

## 🛠 Công Nghệ Sử Dụng

### 1. Web App (`qlnhansutest`)

| Lớp | Công nghệ | Phiên bản |
|---|---|---|
| **Backend Framework** | Laravel | ^12.0 |
| **Ngôn ngữ** | PHP | ^8.2 |
| **Database** | MySQL (Docker) | 8.0 |
| **Frontend Bundler** | Vite | ^6.2.4 |
| **CSS Framework** | Tailwind CSS | ^3.1 & v4 |
| **JS Alpine** | Alpine.js | ^3.4.2 |
| **Nhận diện khuôn mặt** | face-api.js | ^0.22.2 |
| **Authentication** | Laravel Breeze | ^2.3 |
| **Testing** | Pest PHP | ^3.8 |
| **Containerization** | Docker + Docker Compose | — |
| **Web Server** | Nginx | 1.21-alpine |
| **DB Admin** | phpMyAdmin | — |
| **Queue** | Laravel Queue (Database driver) | — |
| **Cache** | Database Cache | — |

### 2. Kiosk Helper App (`kiosk-helper-app`)

| Lớp | Công nghệ | Phiên bản |
|---|---|---|
| **Framework Desktop** | Electron | ^38.0.0 |
| **HTTP Client** | Axios | ^1.11.0 |
| **Mạng WiFi** | node-wifi / system_profiler | ^2.0.16 |
| **Mở URL** | open | ^10.2.0 |

---

## 🎯 Chức Năng Của Ứng Dụng

### 👑 Phân Hệ Admin

| Module | Chức năng |
|---|---|
| **Quản lý Nhân viên** | Thêm, sửa, xóa mềm (soft delete) nhân viên; quản lý ảnh đại diện |
| **Quản lý Phòng Ban** | CRUD phòng ban |
| **Quản lý Chức vụ** | CRUD chức danh/vị trí công việc |
| **Quản lý Ca làm việc** | Tạo và cấu hình ca làm việc (giờ vào, giờ ra, tolerance) |
| **Phân công Lịch làm việc** | Gán ca làm việc dài hạn cho từng nhân viên |
| **Phê duyệt Lịch đăng ký** | Xem và phê duyệt/từ chối lịch đăng ký hàng tháng của nhân viên |
| **Quản lý Chấm công** | Xem và chỉnh sửa dữ liệu chấm công; theo dõi trạng thái (đúng giờ, trễ, vắng) |
| **Quản lý Lương** | Tính toán và quản lý bảng lương nhân viên |
| **Khen thưởng / Kỷ luật** | Ghi nhận và quản lý các quyết định khen thưởng / kỷ luật |
| **Quản lý Tài khoản** | Tạo và quản lý tài khoản người dùng hệ thống |
| **Báo cáo & Thống kê** | Thống kê nhân sự, báo cáo chấm công, báo cáo lương |

### 👤 Phân Hệ Nhân Viên

| Module | Chức năng |
|---|---|
| **Chấm công** | Check-in / Check-out với nhận diện khuôn mặt qua Kiosk |
| **Lịch sử chấm công** | Xem lịch sử ra vào theo ngày/tháng |
| **Đăng ký khuôn mặt** | Đăng ký và cập nhật dữ liệu khuôn mặt (face descriptor) + ảnh đại diện |
| **Đăng ký lịch làm việc** | Đăng ký lịch làm việc hàng tháng (chờ admin phê duyệt) |
| **Hồ sơ cá nhân** | Xem và cập nhật thông tin cá nhân |

### 🖥 Kiosk Desktop App

| Chức năng | Mô tả |
|---|---|
| **Xác thực mạng WiFi** | Kiểm tra SSID WiFi hiện tại — chỉ cho phép từ mạng nội bộ văn phòng |
| **Lấy OTP Token** | Gọi API backend để lấy token tạm thời (hiệu lực 120 giây) |
| **Mở trang Kiosk** | Tự động mở trình duyệt đến trang chấm công kèm token — Token tự xóa khỏi URL sau khi trang load |
| **Nhận diện khuôn mặt** | Trang kiosk web dùng `face-api.js` để nhận diện và ghi nhận chấm công |

---

## 🗄 Cấu Trúc Database

```
positions          - Chức vụ (id, name, ...)
departments        - Phòng ban (id, name, ...)
employees          - Nhân viên (id, code, name, dob, phone, position_id, department_id, ...)
users              - Tài khoản đăng nhập (id, employee_id, email, password, role, ...)
work_schedules     - Ca làm việc (id, name, start_time, end_time, ...)
employee_schedules - Lịch gán dài hạn (id, employee_id, work_schedule_id, start_date, end_date)
monthly_registrations - Lịch đăng ký hàng tháng (id, employee_id, date, work_schedule_id, status)
attendances        - Chấm công (id, employee_id, date, check_in_time, check_out_time, status)
salaries           - Bảng lương (id, employee_id, month, year, ...)
rewards_disciplines - Khen thưởng/Kỷ luật
face_descriptors   - Dữ liệu khuôn mặt (id, user_id, descriptor)
```

---

## 🚀 Hướng Dẫn Khởi Chạy

### Yêu Cầu Hệ Thống

- **Docker** & **Docker Compose** (cho môi trường production/staging)
- **PHP** >= 8.2 + **Composer** (cho môi trường local thuần)
- **Node.js** >= 18 + **npm**
- **Git**

---

### ▶ Cách 1: Chạy Bằng Docker (Khuyến nghị)

#### Bước 1: Clone & cài đặt

```bash
cd qlnhansutest
cp .env.example .env
```

#### Bước 2: Cấu hình `.env` cho Docker

Mở file `.env` và đảm bảo các thông số sau:

```env
DB_CONNECTION=mysql
DB_HOST=localhost          # Tên service trong docker-compose
DB_PORT=3306
DB_DATABASE=qlnhansutest
DB_USERNAME=root
DB_PASSWORD=
```

```qlnhansutest\config\hr.php
thêm allowed_office_ips = ip máy
thêm allowed_office_ssids = tên mạng đang sử dụng .
``` 

#### Bước 3: Khởi động Docker

```bash
docker-compose up -d --build
```

Các container sẽ được tạo:
- `laravel_app`     → PHP-FPM application server
- `laravel_nginx`   → Nginx web server (port **8000**)
- `laravel_db`      → MySQL 8.0 (port **33066**)
- `laravel_phpmyadmin` → phpMyAdmin (port **8081**)
- `laravel_node`    → Node.js / Vite dev server (port **5173**)

#### Bước 4: Cài đặt dependencies & migrate

```bash
# Vào container app để chạy lệnh artisan
docker exec -it laravel_app bash

# Bên trong container:
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed      # (nếu có seeders)
php artisan storage:link
exit
```

#### Bước 5: Truy cập ứng dụng

| Dịch vụ | URL |
|---|---|
| **Web App** | http://localhost:8000 |
| **phpMyAdmin** | http://localhost:8081 |
| **Vite Dev** | http://localhost:5173 |

---

### ▶ Cách 2: Chạy Thuần Local (Development)

#### Bước 1: Cài đặt dependencies

```bash
cd qlnhansutest
composer install
npm install
cp .env.example .env
```

#### Bước 2: Cấu hình `.env` cho local

```env
DB_CONNECTION=sqlite
# Hoặc cấu hình MySQL local của bạn
```

#### Bước 3: Generate key & migrate

```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
```

#### Bước 4: Chạy tất cả services cùng lúc

```bash
composer run dev
```

Lệnh này sẽ chạy đồng thời:
- `php artisan serve` → Backend Laravel tại http://localhost:8000
- `npm run dev`       → Vite frontend
- `php artisan queue:listen` → Queue worker
- `php artisan pail`         → Log viewer

---

### ▶ Khởi Chạy Kiosk Helper App

#### Yêu Cầu

- **macOS / Windows / Linux** (macOS dùng `system_profiler`, Windows/Linux dùng `node-wifi`)
- Web App (`qlnhansutest`) phải đang **chạy** trên `http://localhost:8000`

#### Cài đặt

```bash
cd kiosk-helper-app
npm install
```

#### Cấu hình

Mở file `main.js` và điều chỉnh 2 hằng số:

```javascript
// URL API backend để lấy OTP token
const LARAVEL_API_URL = 'http://localhost:8000/api/kiosk/request-token';

// URL trang Kiosk chấm công
const KIOSK_WEB_URL = 'http://localhost:8000/kiosk/attendance';
```

#### Khởi chạy

```bash
npm start
```

Cửa sổ Electron sẽ mở ra với nút **"Bắt Đầu"** để khởi động phiên chấm công.

---

## 🔄 Flow Hoạt Động Của Ứng Dụng

### Flow 1: Đăng nhập & Phân quyền

```
Người dùng truy cập http://localhost:8000
        │
        ▼
   Chưa đăng nhập? → Redirect → /login
        │
        ▼ (sau khi đăng nhập)
   Kiểm tra role
     ├── Admin   → /admin/employees   (Trang quản lý nhân sự)
     └── Employee → /employee/attendance/history  (Lịch sử chấm công)
```

### Flow 2: Chấm Công Qua Kiosk (Face ID)

```
[macOS Kiosk Machine]                    [Backend Laravel]              [Kiosk Web Page]
        │                                       │                              │
        │  1. User mở Kiosk Helper App          │                              │
        │     (Electron App)                    │                              │
        │                                       │                              │
        │  2. User nhấn "Bắt Đầu"              │                              │
        │                                       │                              │
        │  3. App đọc SSID WiFi hiện tại        │                              │
        │     (system_profiler)                 │                              │
        │                                       │                              │
        │  4. POST /api/kiosk/request-token ──► │                              │
        │       { ssid: "OfficeWiFi" }          │                              │
        │                                       │ Kiểm tra SSID hợp lệ?       │
        │                                       │ → Tạo OTP Token (120s)       │
        │  5. ◄── { otp_token: "abc123" }       │                              │
        │                                       │                              │
        │  6. Mở trình duyệt:                   │                              │
        │     /kiosk/attendance?token=abc123    │                              │
        │                                       │             ◄────────────────│
        │                                       │                              │
        │                                       │  7. Trang Kiosk load          │
        │                                       │     face-api.js models        │
        │                                       │                              │
        │                                       │  8. GET /api/kiosk/           │
        │                                       │     face-descriptors ────────►│
        │                                       │     ◄── [face data array]     │
        │                                       │                              │
        │                                  9. Camera bật, nhận diện khuôn mặt │
        │                                                                      │
        │                                  10. Khớp khuôn mặt → user_id       │
        │                                                                      │
        │                                  11. POST /api/kiosk/record-attendance
        │                                       { user_id, otp_token }         │
        │                                       │                              │
        │                                       │ Kiểm tra token, xác định     │
        │                                       │ check-in hoặc check-out      │
        │                                       │ So sánh với ca làm việc      │
        │                                       │ → Ghi status (present/late/  │
        │                                       │              absent)          │
        │                                       │                              │
        │                               12. ◄── "Chào mừng Nguyễn Văn A!      │
        │                                        Check-in lúc 08:02 thành công"│
```

### Flow 3: Nhân Viên Đăng Ký Khuôn Mặt

```
Nhân viên đăng nhập
        │
        ▼
/employee/face-registration
        │
        ▼
Bật camera → Chụp ảnh
        │
        ▼
face-api.js xử lý → tạo face descriptor (128 float array)
        │
        ▼
POST /employee/face/register
  { descriptor: "...", avatar: "base64..." }
        │
        ▼
Backend lưu:
  - face_descriptors table (descriptor JSON)
  - storage/app/public/avatars/ (ảnh đại diện)
```

### Flow 4: Đăng Ký & Phê Duyệt Lịch Làm Việc

```
[Nhân viên]                          [Admin]
     │                                  │
     │  Vào /employee/schedule-registration
     │  Chọn ngày + ca làm việc mong muốn
     │  POST → Lưu vào monthly_registrations (status: pending)
     │                                  │
     │                    Admin vào /admin/schedule-approvals
     │                    Xem danh sách đăng ký pending
     │                    POST approve → status: approved/rejected
     │                                  │
     │  Khi chấm công, hệ thống ưu tiên │
     │  lịch đã duyệt (approved) trước  │
     │  lịch gán dài hạn               │
```

---

## 📁 Cấu Trúc Thư Mục Chính

```
qlnhansutest/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          ← Controllers cho Admin (Employee, Salary, Report...)
│   │   │   ├── Api/            ← API Controllers (FaceApi, KioskApi)
│   │   │   ├── Employee/       ← Controllers cho nhân viên
│   │   │   └── KioskController.php
│   │   └── Middleware/
│   └── Models/                 ← Eloquent Models (Employee, Attendance, Salary...)
├── database/
│   ├── migrations/             ← Cấu trúc database
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── admin/              ← Blade views cho Admin
│   │   ├── employee/           ← Blade views cho Employee
│   │   └── kiosk/              ← Blade view trang Kiosk chấm công
│   └── js/
├── routes/
│   ├── web.php                 ← Toàn bộ routes web
│   └── api.php                 ← Routes API (Kiosk, Face)
├── docker/                     ← Docker config files (PHP, Nginx)
├── docker-compose.yml
└── .env

kiosk-helper-app/
├── main.js        ← Electron main process (WiFi check, API call)
├── preload.js     ← Context bridge (bảo mật IPC)
├── renderer.js    ← UI logic
├── index.html     ← Giao diện Kiosk Helper
└── package.json
```

---

## 🔐 Phân Quyền Hệ Thống

| Role | Middleware | Quyền truy cập |
|---|---|---|
| **Admin** | `auth`, `admin` | Toàn bộ `/admin/*`, báo cáo, phê duyệt |
| **Employee** | `auth`, `employee` | `/employee/*` — chỉ dữ liệu của mình |
| **Kiosk** | `verify.office.ip` | `/api/kiosk/*` — chỉ từ IP văn phòng |

---

## ⚙ Biến Môi Trường Quan Trọng

| Biến | Mô tả | Giá trị mặc định |
|---|---|---|
| `APP_URL` | URL ứng dụng | `http://localhost` |
| `DB_CONNECTION` | Loại database | `mysql` (Docker) / `sqlite` (local) |
| `DB_HOST` | Host database | `db` (Docker service name) |
| `DB_DATABASE` | Tên database | `laravel` |
| `QUEUE_CONNECTION` | Driver queue | `database` |
| `CACHE_STORE` | Driver cache | `database` |

---

## 🐛 Troubleshooting — Các Lỗi Thường Gặp

### Kiosk Helper App

| Lỗi | Nguyên nhân | Cách fix |
|---|---|---|
| `spawn airport ENOENT` | `airport` bị Apple xóa từ macOS Ventura trở đi | ✅ Đã fix: code tự dùng `system_profiler` trên macOS |
| `Thất bại: Không kết nối đúng mạng Wi-Fi` | SSID chưa có trong `hr.php` | Thêm tên WiFi chính xác vào `allowed_office_ssids` |
| `Phiên không hợp lệ` | `replaceState()` xóa token trước khi JS đọc | ✅ Đã fix: `replaceState` chạy trong `window.addEventListener('load')` |
| Token hết hạn quá nhanh | TTL quá ngắn | Tăng giây trong `Cache::put($key, true, now()->addSeconds(120))` |
| Không lấy được SSID khi dùng LAN cáp | LAN không có SSID | Dùng WiFi thay cho cáp mạng khi chạy Kiosk |

### Docker / Backend

| Lỗi | Nguyên nhân | Cách fix |
|---|---|---|
| Container `app` crash ngay sau khi up | DB chưa sẵn sàng khi migrate | `entrypoint.sh` tự retry, hoặc chạy lại `docker compose up` |
| `composer install` chưa chạy | Docker build không tự install | Chạy thủ công: `docker compose exec app composer install` |
| `php artisan migrate` thất bại | DB config sai trong `.env` | Kiểm tra `DB_HOST=db`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| Vite không hot-reload | Container `node` chưa up | Chạy: `docker compose up node` hoặc `docker compose exec node npm run dev` |

---

## 📝 Ghi Chú

> [!NOTE]
> **Kiosk Helper App** hỗ trợ đa nền tảng: **macOS** dùng `system_profiler SPAirPortDataType`, **Windows/Linux** dùng thư viện `node-wifi`. App tự phát hiện OS và chọn phương thức phù hợp.

> [!IMPORTANT]
> Sau khi chạy `php artisan storage:link`, các file ảnh đại diện của nhân viên sẽ được phục vụ tại `http://localhost:8000/storage/avatars/`.

> [!TIP]
> Để xem log realtime trong quá trình phát triển, dùng lệnh `php artisan pail` hoặc xem trong `storage/logs/laravel.log`.

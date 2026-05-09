# Báo cáo Review Hệ thống QLNS (Version Fixbug001)

## 📌 Tổng quan thay đổi
Bản cập nhật **Fixbug001** tập trung vào việc hiện đại hóa cơ chế tính lương, tách biệt các loại phụ cấp theo chuẩn thuế Việt Nam, xử lý chính xác chấm công ca đêm và hỗ trợ đa chế độ lương (Cố định, Theo giờ, Ghi đè).

---

## 🛠 Chi tiết các Module

### 1. Quản lý Nhân sự (Employees)
*   **Chức năng:** 
    *   Quản lý hồ sơ nhân sự, chức vụ, phòng ban.
    *   **Mới:** Cấu hình lương chi tiết (Lương cơ bản, Đơn giá giờ, Phụ cấp chịu thuế/miễn thuế, Bảo hiểm, Người phụ thuộc).
*   **Cách kiểm tra:**
    1.  Vào `Quản lý nhân viên` -> `Thêm mới/Chỉnh sửa`.
    2.  Kiểm tra các trường dữ liệu lương mới đã được lưu và hiển thị đúng chưa.
*   **Thiếu sót:** Chưa có quản lý Hợp đồng lao động và lưu trữ file tài liệu cá nhân.

### 2. Quản lý Chấm công & Kiosk (Attendance & FaceID)
*   **Chức năng:**
    *   Check-in/out qua FaceID.
    *   **Mới:** Logic ca đêm (Overnight) đã hoạt động chính xác, không còn lỗi so sánh giờ khi đi qua 00:00.
    *   Tự động tính giờ làm thực tế (trừ giờ nghỉ) và phân loại Đi trễ/Về sớm.
*   **Cách kiểm tra:**
    1.  Thực hiện chấm công cho nhân viên có ca làm việc xuyên đêm.
    2.  Kiểm tra số giờ làm thực tế tại trang Admin.
*   **Thiếu sót:** Chưa tách biệt giữa Nghỉ phép (có lương/không lương) và Vắng mặt.

### 3. Module Lương (Payroll - Core Update)
*   **Chức năng:**
    *   **Đa chế độ:** Tự động tính theo Ngày công (Lương cố định) hoặc Tổng giờ làm (Lương giờ).
    *   **Thuế TNCN:** Tự động tính thuế theo biểu lũy tiến 7 bậc sau khi trừ gia cảnh (11tr cá nhân, 4.4tr/người phụ thuộc).
    *   **Bảo hiểm:** Trừ 10.5% BHXH, BHYT, BHTN trên mức lương bảo hiểm cấu hình.
    *   **Tăng ca:** Tính hệ số 1.5 cho ngày làm dư hoặc giờ OT.
*   **Cách kiểm tra:**
    1.  Bấm `Tạo bảng lương` cho tháng hiện tại.
    2.  Kiểm tra chi tiết bảng lương để xem các khoản Thuế và Bảo hiểm có khớp với công thức không.
*   **Thiếu sót:** Thiếu chức năng xuất Phiếu lương (Payslip) PDF và gửi Email cho nhân viên.

### 4. Báo cáo (Reports)
*   **Chức năng:** Tổng hợp chi phí lương, phụ cấp, thuế và bảo hiểm toàn công ty theo tháng.
*   **Cách kiểm tra:** Vào `Báo cáo` -> `Báo cáo lương`.
*   **Thiếu sót:** Chưa có báo cáo biểu đồ trực quan và xuất file Excel.

---

## 📅 Cấu hình mặc định hệ thống
*   **Ngày công chuẩn:** 22 ngày/tháng (Nghỉ T7, CN).
*   **Giảm trừ cá nhân:** 11.000.000 VNĐ.
*   **Giảm trừ gia cảnh:** 4.400.000 VNĐ/người.
*   **Hệ số tăng ca:** 1.5.

## 🚀 Hướng dẫn vận hành sau cập nhật
1.  Chạy migration để cập nhật cấu trúc bảng: `php artisan migrate`.
2.  Cập nhật thông tin lương cho nhân viên cũ (vì các cột mới sẽ mặc định là 0 hoặc null).
3.  Tiến hành tạo bảng lương tháng để kiểm tra kết quả.

---
**Người thực hiện:** Antigravity AI Assistant
**Nhánh:** Fixbug001

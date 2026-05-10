# Báo cáo Review Hệ thống QLNS
Ngày review: 09/05/2026

## 1. Module Nhân sự (HRM)
- **Tự động hóa:** Mã nhân viên sinh ngẫu nhiên `QLNS-XXXXXX`.
- **Ràng buộc:** Không cho xóa Phòng ban/Chức vụ nếu còn nhân viên.
- **UX:** Popup xem nhanh danh sách và chi tiết nhân viên hoạt động mượt mà.

## 2. Module Chấm công (Attendance)
- **FaceID:** Tích hợp OTP Token chống giả mạo.
- **Dữ liệu:** Lưu cứng `work_schedule_id` khi check-in để bảo toàn lịch sử lương.
- **Audit:** Lưu `admin_id` khi có sự can thiệp thủ công từ quản trị viên.

## 3. Module Lương (Payroll)
- **Bảo hiểm:** Trích đóng BHXH, BHYT, BHTN theo tỷ lệ chuẩn 10.5%.
- **Thuế TNCN:** Tính theo biểu lũy tiến 7 bậc chính xác.
- **Nghỉ phép:** Tự động cộng 8h công cho các đơn nghỉ có lương đã duyệt.

## 4. Tổng kết kỹ thuật
- Không có lỗi cú pháp (Syntax error).
- Logic SQL tối ưu, có Index tại các cột quan trọng như `date`, `employee_id`.
- Hệ thống Toast và Modal xác nhận xóa hoạt động ổn định.

**Người thực hiện:** Antigravity AI Agent

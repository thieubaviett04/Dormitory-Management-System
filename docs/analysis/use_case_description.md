# Mô tả chi tiết Use Case (Use Case Description)

Dưới đây là mô tả chi tiết cho Use Case tiêu biểu của hệ thống.

## Use Case: UC-03: Đăng ký chỗ ở trực tuyến

| Thuộc tính | Mô tả |
|---|---|
| **Tác nhân chính** | Sinh viên |
| **Mục đích** | Cho phép sinh viên đăng ký chỗ ở ký túc xá qua mạng internet |
| **Tiền điều kiện** | Sinh viên đã đăng nhập vào hệ thống và đợt đăng ký chỗ ở đang được mở |
| **Kịch bản chính (Luồng cơ bản)** | 1. Sinh viên truy cập vào chức năng "Đăng ký chỗ ở".<br>2. Hệ thống hiển thị form điền thông tin (loại phòng mong muốn, thông tin cá nhân, diện ưu tiên).<br>3. Sinh viên nhập đầy đủ thông tin và nhấn "Gửi đơn".<br>4. Hệ thống kiểm tra tính hợp lệ của thông tin và lưu trạng thái đơn là "Chờ duyệt".<br>5. Hệ thống hiển thị thông báo gửi đơn thành công. |
| **Kịch bản ngoại lệ** | - **Lỗi thiếu thông tin:** Nếu sinh viên không điền các trường bắt buộc, hệ thống hiển thị thông báo lỗi và yêu cầu điền lại.<br>- **Đã hết hạn đăng ký:** Nếu đợt đăng ký đã đóng, hệ thống ẩn form đăng ký và thông báo "Thời gian đăng ký đã kết thúc". |
| **Hậu điều kiện** | Một đơn đăng ký mới được tạo trong cơ sở dữ liệu với trạng thái "Chờ duyệt". |

---

## Use Case: UC-08: Duyệt đơn đăng ký chỗ ở

| Thuộc tính | Mô tả |
|---|---|
| **Tác nhân chính** | Cán bộ quản lý |
| **Mục đích** | Phê duyệt đơn đăng ký của sinh viên để chuẩn bị xếp phòng |
| **Tiền điều kiện** | Cán bộ đã đăng nhập thành công và có đơn đăng ký ở trạng thái "Chờ duyệt" |
| **Kịch bản chính** | 1. Cán bộ truy cập danh sách "Đơn đăng ký chờ duyệt".<br>2. Cán bộ chọn đơn của sinh viên và xem thông tin chi tiết.<br>3. Cán bộ click "Phê duyệt" (hoặc "Từ chối" kèm lý do).<br>4. Hệ thống cập nhật trạng thái đơn thành "Đã phê duyệt" (hoặc "Đã từ chối").<br>5. Hệ thống gửi thông báo kết quả cho sinh viên qua email/hệ thống. |
| **Hậu điều kiện** | Đơn đăng ký chuyển trạng thái thành "Đã phê duyệt", kích hoạt bước xếp phòng tiếp theo. |

# Module Dịch vụ & Hóa đơn điện nước

## 1. Mô tả tính năng
Hỗ trợ ghi nhận chỉ số tiêu dùng điện, nước hàng tháng của từng phòng, tạo hóa đơn tự động và theo dõi tiến độ thanh toán của sinh viên.

## 2. Các chức năng chính
- **Nhập chỉ số điện nước:** Cho phép cán bộ chọn tòa nhà, chọn phòng và nhập chỉ số điện đầu/cuối, chỉ số nước đầu/cuối của tháng đó.
- **Tính toán chi phí:** Tự động nhân chỉ số tiêu thụ với đơn giá cấu hình sẵn trong hệ thống.
- **Tạo hóa đơn tổng hợp:** Gồm tiền phòng hàng tháng + tiền điện + tiền nước + các dịch vụ khác (nếu có, như mạng internet, dọn vệ sinh).
- **Thanh toán:**
  - Sinh viên xem hóa đơn trên cổng thông tin của mình.
  - Sinh viên chuyển khoản hoặc thanh toán tại quầy. Cán bộ cập nhật trạng thái hóa đơn thành `Đã thanh toán` sau khi nhận được tiền.

## 3. Cấu hình bảng giá dịch vụ
Quản trị viên có thể thay đổi đơn giá điện nước định kỳ.
- Ví dụ đơn giá:
  - Điện: 2,500đ / kWh
  - Nước: 10,000đ / m³

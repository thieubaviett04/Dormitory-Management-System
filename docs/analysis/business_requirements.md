# Đặc tả Nghiệp vụ (Business Requirements)

Dưới đây là các yêu cầu nghiệp vụ chính của hệ thống quản lý ký túc xá.

## 1. Yêu cầu chức năng (Functional Requirements)

### 1.1. Quản lý Cơ sở vật chất
- Hệ thống phải cho phép quản lý các khu nhà (Block), loại phòng (Room Type - ví dụ: phòng 4 người, phòng 8 người, phòng dịch vụ) và danh sách phòng (Room).
- Cho phép ghi nhận và theo dõi các trang thiết bị trong từng phòng (giường, tủ, điều hòa, quạt).

### 1.2. Đăng ký & Phân phòng
- Cho phép sinh viên đăng ký nguyện vọng ở ký túc xá trực tuyến theo đợt tuyển sinh hoặc đợt đăng ký định kỳ.
- Ban quản lý phê duyệt đơn đăng ký và thực hiện xếp phòng tự động hoặc thủ công.

### 1.3. Quản lý Hợp đồng & Hồ sơ sinh viên
- Tạo lập hợp đồng thuê phòng với thời hạn cụ thể (ví dụ: theo học kỳ hoặc theo năm học).
- Quản lý thông tin hồ sơ chi tiết của sinh viên nội trú (thông tin cá nhân, lớp, khoa, người giám hộ).

### 1.4. Quản lý Dịch vụ & Hóa đơn
- Ghi nhận chỉ số điện, nước hàng tháng của từng phòng.
- Tự động tính toán số tiền điện, nước tiêu thụ dựa trên đơn giá định sẵn.
- Phát hành hóa đơn phòng và hóa đơn dịch vụ hàng tháng. Cho phép cập nhật trạng thái thanh toán (Chưa thanh toán / Đã thanh toán).

## 2. Yêu cầu phi chức năng (Non-Functional Requirements)
- **Hiệu năng:** Thời gian phản hồi trang web dưới 2 giây.
- **Bảo mật:** Mã hóa mật khẩu người dùng, bảo vệ các route bằng middleware phân quyền.
- **Giao diện:** Tương thích với các thiết bị di động (Responsive) giúp sinh viên dễ dàng thao tác trên điện thoại.

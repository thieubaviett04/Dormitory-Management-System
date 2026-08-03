# Product Backlog

Tài liệu này quản lý danh sách các Epic, Feature và User Story của dự án Hệ thống Quản lý Ký túc xá. Các công việc chi tiết (Tasks) sẽ được phát triển trong Phase 2.

## Cấu trúc Phân cấp
`Epic` -> `Feature` -> `User Story` -> `Task`

---

## Epic 1: Quản lý Ký túc xá (Cơ sở vật chất)
### Feature 1.1: Quản lý dãy nhà (Blocks)
- **User Story:** Là quản trị viên, tôi muốn quản lý thông tin các tòa nhà/dãy nhà (Thêm, Sửa, Xóa, Xem) để phân chia khu vực lưu trú.
- **Tasks (Phase 2):**
  - Thiết kế Migration & Model `Block`
  - Viết API/Controller xử lý CRUD
  - Thiết kế màn hình quản lý dãy nhà

### Feature 1.2: Quản lý Phòng (Rooms)
- **User Story:** Là quản trị viên, tôi muốn quản lý danh sách phòng (Thêm, Sửa, Xóa, Xem, tìm kiếm theo trạng thái trống/đầy) để nắm bắt tình hình chỗ ở.
- **Tasks (Phase 2):**
  - Thiết kế Migration & Model `Room`
  - Xây dựng quan hệ `Room belongsTo Block`
  - Viết CRUD Room Controller
  - Thiết kế giao diện danh sách phòng

---

## Epic 2: Đăng ký chỗ ở & Phân phòng
### Feature 2.1: Đăng ký chỗ ở trực tuyến
- **User Story:** Là sinh viên, tôi muốn gửi yêu cầu đăng ký phòng trực tuyến để tiết kiệm thời gian làm thủ tục trực tiếp.
- **Tasks (Phase 2):**
  - Thiết kế Migration & Model `Registration`
  - Xây dựng form đăng ký phía Sinh viên
  - Xử lý gửi email xác nhận đăng ký

### Feature 2.2: Phê duyệt & Phân phòng
- **User Story:** Là ban quản lý, tôi muốn duyệt đơn đăng ký và xếp phòng cho sinh viên dựa trên loại phòng họ đăng ký và số giường còn trống.
- **Tasks (Phase 2):**
  - Viết logic tự động hoặc thủ công phân phòng
  - Cập nhật số lượng sinh viên hiện tại trong phòng

---

## Epic 3: Quản lý Hợp đồng & Thanh toán
### Feature 3.1: Quản lý Hợp đồng lưu trú
- **User Story:** Là ban quản lý, tôi muốn tạo và in hợp đồng lưu trú khi sinh viên nhận phòng để đảm bảo tính pháp lý.
- **Tasks (Phase 2):**
  - Thiết kế Migration & Model `Contract`
  - Xây dựng tính năng xuất PDF hợp đồng

### Feature 3.2: Quản lý Hóa đơn dịch vụ (Điện, Nước, Phòng)
- **User Story:** Là ban quản lý, tôi muốn tính toán và gửi hóa đơn hàng tháng cho từng phòng để thu phí dịch vụ.
- **Tasks (Phase 2):**
  - Thiết kế Migration & Model `Invoice`
  - Logic tính tiền điện, nước dựa trên chỉ số đầu và cuối

# Module Quản lý Ký túc xá (Khu nhà & Phòng)

## 1. Mô tả tính năng
Module này hỗ trợ Cán bộ quản lý thiết lập cấu trúc cơ sở vật chất của ký túc xá bao gồm các tòa nhà (Blocks) và phòng ở (Rooms) đi kèm thông tin chi tiết.

## 2. Giao diện dự kiến
- **Trang danh sách dãy nhà:** Hiển thị danh sách tòa nhà kèm số lượng phòng hiện có, giới tính áp dụng cho tòa đó.
- **Trang danh sách phòng:** Hiển thị danh sách phòng dưới dạng thẻ (Cards) hoặc bảng, hiển thị trực quan số giường trống/giường đã có sinh viên ở (ví dụ: `4/8`).
- **Trang chi tiết phòng:** Xem thông tin các sinh viên hiện đang ở trong phòng đó và danh sách trang thiết bị đi kèm phòng.

## 3. Quy trình nghiệp vụ chính
1. Tạo Block mới (Ví dụ: Block A, Block B).
2. Tạo các Loại phòng (Room Type) có giá tiền và số giường cụ thể.
3. Tạo Phòng (Room) gán vào Block tương ứng và chỉ định Loại phòng.
4. Khi phòng có sinh viên vào ở thông qua hợp đồng, cập nhật trường `current_occupancy` tự động.

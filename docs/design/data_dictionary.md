# Từ điển dữ liệu (Data Dictionary)

Định nghĩa chi tiết các kiểu dữ liệu, mục đích sử dụng và mô tả của các trường dữ liệu chính.

### 1. Thực thể: `users`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã định danh tài khoản tự sinh |
| `name` | VARCHAR(255) | Không | | Họ tên đầy đủ của người dùng |
| `email` | VARCHAR(255) | Không | | Email đăng nhập hệ thống (Unique) |
| `password` | VARCHAR(255) | Không | | Mật khẩu đã được mã hóa Bcrypt |
| `role` | VARCHAR(50) | Không | | Vai trò của tài khoản: `admin`, `manager`, `student` |

### 2. Thực thể: `students`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã định danh sinh viên tự sinh |
| `user_id` | BIGINT | Có | FK | Liên kết tới bảng `users` (nếu có tài khoản) |
| `student_code` | VARCHAR(50) | Không | | Mã số sinh viên (Unique) |
| `phone` | VARCHAR(20) | Có | | Số điện thoại liên lạc |
| `gender` | VARCHAR(10) | Không | | Giới tính: `male` (Nam), `female` (Nữ) |
| `dob` | DATE | Không | | Ngày tháng năm sinh |
| `class` | VARCHAR(100) | Không | | Lớp hành chính |
| `department` | VARCHAR(100) | Không | | Khoa / Ngành học |

### 3. Thực thể: `rooms`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã định danh phòng tự sinh |
| `block_id` | BIGINT | Không | FK | Liên kết tới dãy nhà `blocks` |
| `room_type_id` | BIGINT | Không | FK | Liên kết tới loại phòng `room_types` |
| `name` | VARCHAR(50) | Không | | Tên/Số phòng (ví dụ: `101`) |
| `status` | VARCHAR(50) | Không | | Trạng thái phòng: `available`, `full`, `maintenance` |
| `current_occupancy` | INT | Không | | Số lượng sinh viên thực tế đang ở trong phòng |

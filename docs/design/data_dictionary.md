# Từ điển dữ liệu (Data Dictionary)

Định nghĩa chi tiết các kiểu dữ liệu, mục đích sử dụng và mô tả của các trường dữ liệu chính.

### 1. Thực thể: `users`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã định danh tài khoản tự sinh |
| `name` | VARCHAR(255) | Không | | Họ tên đầy đủ của người dùng |
| `email` | VARCHAR(255) | Không | | Email đăng nhập hệ thống (Unique) |
| `password` | VARCHAR(255) | Không | | Mật khẩu đã được mã hóa Bcrypt |

### 2. Thực thể: `students`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã định danh sinh viên tự sinh |
| `student_code` | VARCHAR(20) | Không | | Mã số sinh viên (Unique) |
| `full_name` | VARCHAR(100) | Không | | Họ tên sinh viên |
| `email` | VARCHAR(255) | Không | | Email liên hệ (Unique) |
| `phone_number` | VARCHAR(15) | Có | | Số điện thoại liên lạc |
| `gender` | VARCHAR(10) | Không | | `male`, `female`, `other` |
| `date_of_birth` | DATE | Không | | Ngày tháng năm sinh |

### 3. Thực thể: `rooms`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã định danh phòng tự sinh |
| `building_id` | BIGINT | Không | FK | Liên kết tới tòa nhà `buildings` |
| `room_number` | VARCHAR(255) | Không | | Số phòng (ví dụ: `101`) |
| `floor` | SMALLINT | Không | | Tầng của phòng |
| `capacity` | SMALLINT | Không | | Sức chứa tối đa |
| `status` | VARCHAR(50) | Không | | Trạng thái phòng: `available`, `full`, `maintenance` |

### 4. Thực thể: `contracts`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã hợp đồng nội bộ |
| `contract_code` | VARCHAR(30) | Không | | Mã hợp đồng duy nhất dạng `HD-YYYY-NNNN` |
| `room_registration_id` | BIGINT | Không | FK | Đơn đã duyệt dùng để lập hợp đồng (Unique) |
| `student_id` | BIGINT | Không | FK | Sinh viên sở hữu hợp đồng |
| `start_date`, `end_date` | DATE | Không | | Thời hạn lưu trú |
| `monthly_rate` | DECIMAL(12,2) | Không | | Đơn giá mỗi tháng tại thời điểm ký |
| `status` | VARCHAR(50) | Không | | `active`, `expired`, `terminated` |

### 5. Thực thể: `allocations`
| Tên trường | Kiểu dữ liệu | Cho phép Null | Khóa | Mô tả |
|---|---|---|---|---|
| `id` | BIGINT | Không | PK | Mã lần phân giường |
| `contract_id` | BIGINT | Không | FK | Hợp đồng liên quan |
| `bed_id` | BIGINT | Không | FK | Giường được phân |
| `allocated_at` | TIMESTAMP | Không | | Thời điểm nhận giường |
| `released_at` | TIMESTAMP | Có | | Null khi sinh viên vẫn đang ở giường này |
| `release_reason` | VARCHAR(50) | Có | | Lý do chuyển hoặc trả giường |

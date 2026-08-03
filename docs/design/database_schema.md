# Lược đồ Cơ sở dữ liệu (Database Schema)

Lược đồ cơ sở dữ liệu vật lý dự kiến sử dụng cho cơ sở dữ liệu PostgreSQL.

## 1. Bảng `users`
- `id` (BIGINT, PK, Auto Increment)
- `name` (VARCHAR)
- `email` (VARCHAR, Unique)
- `password` (VARCHAR)
- `role` (VARCHAR) - giá trị: `admin`, `manager`, `student`
- `remember_token` (VARCHAR, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

## 2. Bảng `students`
- `id` (BIGINT, PK, Auto Increment)
- `user_id` (BIGINT, FK -> users.id, Nullable)
- `student_code` (VARCHAR, Unique)
- `phone` (VARCHAR, Nullable)
- `gender` (VARCHAR) - giá trị: `male`, `female`
- `dob` (DATE) - ngày sinh
- `class` (VARCHAR)
- `department` (VARCHAR)
- `created_at`, `updated_at` (TIMESTAMP)

## 3. Bảng `blocks`
- `id` (BIGINT, PK, Auto Increment)
- `name` (VARCHAR, Unique) - ví dụ: Tòa A1, Tòa B2
- `gender_target` (VARCHAR) - tòa dành cho nam hay nữ (`male`, `female`, `mixed`)
- `description` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

## 4. Bảng `room_types`
- `id` (BIGINT, PK, Auto Increment)
- `name` (VARCHAR) - ví dụ: Phòng VIP 4 giường, Phòng tiêu chuẩn 8 giường
- `capacity` (INT) - số lượng giường tối đa
- `price` (DECIMAL(10,2)) - đơn giá phòng/tháng
- `created_at`, `updated_at` (TIMESTAMP)

## 5. Bảng `rooms`
- `id` (BIGINT, PK, Auto Increment)
- `block_id` (BIGINT, FK -> blocks.id)
- `room_type_id` (BIGINT, FK -> room_types.id)
- `name` (VARCHAR) - số phòng (ví dụ: 101, 102)
- `status` (VARCHAR) - trạng thái (`available`, `full`, `maintenance`)
- `current_occupancy` (INT, Default 0) - sĩ số hiện tại
- `created_at`, `updated_at` (TIMESTAMP)

## 6. Bảng `accommodation_registrations`
- `id` (BIGINT, PK, Auto Increment)
- `student_id` (BIGINT, FK -> students.id)
- `room_type_id` (BIGINT, FK -> room_types.id)
- `status` (VARCHAR) - trạng thái (`pending`, `approved`, `rejected`)
- `rejected_reason` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

## 7. Bảng `contracts`
- `id` (BIGINT, PK, Auto Increment)
- `contract_code` (VARCHAR, Unique)
- `student_id` (BIGINT, FK -> students.id)
- `room_id` (BIGINT, FK -> rooms.id)
- `start_date` (DATE)
- `end_date` (DATE)
- `price` (DECIMAL(10,2)) - giá thuê thực tế tại thời điểm ký
- `status` (VARCHAR) - trạng thái (`active`, `expired`, `terminated`)
- `created_at`, `updated_at` (TIMESTAMP)

## 8. Bảng `utility_invoices`
- `id` (BIGINT, PK, Auto Increment)
- `room_id` (BIGINT, FK -> rooms.id)
- `billing_month` (DATE) - tháng xuất hóa đơn (lưu ngày đầu tháng)
- `electricity_start` (INT) - chỉ số điện đầu
- `electricity_end` (INT) - chỉ số điện cuối
- `water_start` (INT) - chỉ số nước đầu
- `water_end` (INT) - chỉ số nước cuối
- `electricity_cost` (DECIMAL(10,2))
- `water_cost` (DECIMAL(10,2))
- `room_cost` (DECIMAL(10,2))
- `total_amount` (DECIMAL(10,2))
- `status` (VARCHAR) - trạng thái (`unpaid`, `paid`)
- `paid_at` (TIMESTAMP, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

# Lược đồ Cơ sở dữ liệu (Database Schema)

Lược đồ cơ sở dữ liệu vật lý đang được sử dụng trên PostgreSQL. Migration trong `database/migrations` là nguồn sự thật cuối cùng khi tài liệu và source có khác biệt.

## 1. Bảng `users`
- `id` (BIGINT, PK, Auto Increment)
- `name` (VARCHAR)
- `email` (VARCHAR, Unique)
- `password` (VARCHAR)
- `remember_token` (VARCHAR, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

## 2. Bảng `students`
- `id` (BIGINT, PK, Auto Increment)
- `student_code` (VARCHAR, Unique)
- `full_name` (VARCHAR)
- `email` (VARCHAR, Unique)
- `phone_number` (VARCHAR, Nullable)
- `gender` (VARCHAR) - `male`, `female`, `other`
- `date_of_birth` (DATE)
- `created_at`, `updated_at` (TIMESTAMP)

## 3. Bảng `buildings`
- `id` (BIGINT, PK, Auto Increment)
- `code` (VARCHAR, Unique)
- `name` (VARCHAR)
- `floors` (INT)
- `gender_policy` (VARCHAR) - `male`, `female`, `mixed`
- `description` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

## 4. Bảng `rooms`
- `id` (BIGINT, PK, Auto Increment)
- `building_id` (BIGINT, FK -> buildings.id)
- `room_number` (VARCHAR)
- `floor` (INT)
- `capacity` (INT)
- `status` (VARCHAR) - trạng thái (`available`, `full`, `maintenance`)
- `created_at`, `updated_at` (TIMESTAMP)

## 5. Bảng `beds`
- `id` (BIGINT, PK, Auto Increment)
- `room_id` (BIGINT, FK -> rooms.id)
- `bed_number` (VARCHAR)
- `status` (VARCHAR) - `available`, `occupied`, `maintenance`
- `created_at`, `updated_at` (TIMESTAMP)

## 6. Bảng `room_registrations`
- `id` (BIGINT, PK, Auto Increment)
- `student_id` (BIGINT, FK -> students.id)
- `room_id` (BIGINT, FK -> rooms.id)
- `status` (VARCHAR) - `pending`, `approved`, `rejected`, `waitlist`, `cancelled`, `completed`
- `registered_at`, `reviewed_at`, `cancelled_at`, `completed_at` (TIMESTAMP, các mốc sau có thể Nullable)
- `reviewed_by`, `completed_by` (BIGINT, FK -> users.id, Nullable)
- `rejected_reason` (TEXT, Nullable)
- `cancellation_reason` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

## 7. Bảng `contracts`
- `id` (BIGINT, PK, Auto Increment)
- `contract_code` (VARCHAR, Unique)
- `room_registration_id` (BIGINT, FK -> room_registrations.id, Unique)
- `student_id` (BIGINT, FK -> students.id)
- `start_date` (DATE)
- `end_date` (DATE)
- `monthly_rate` (DECIMAL(12,2)) - giá thuê mỗi tháng tại thời điểm ký
- `status` (VARCHAR) - trạng thái (`active`, `expired`, `terminated`)
- `signed_at` (TIMESTAMP)
- `terminated_at` (TIMESTAMP, Nullable)
- `termination_reason` (TEXT, Nullable)
- `created_by` (BIGINT, FK -> users.id, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

Phòng và giường hiện tại không được lưu trực tiếp trên hợp đồng mà được truy vấn qua allocation đang hoạt động.

## 8. Bảng `allocations`
- `id` (BIGINT, PK, Auto Increment)
- `contract_id` (BIGINT, FK -> contracts.id)
- `bed_id` (BIGINT, FK -> beds.id)
- `allocated_at` (TIMESTAMP)
- `released_at` (TIMESTAMP, Nullable) - `NULL` nghĩa là allocation hiện tại
- `release_reason` (VARCHAR, Nullable) - `transferred`, `checked_out`, `contract_expired`, `contract_terminated`
- `allocated_by`, `released_by` (BIGINT, FK -> users.id, Nullable)
- `notes`, `release_notes` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

Partial unique index bảo đảm mỗi giường và mỗi hợp đồng chỉ có tối đa một allocation với `released_at IS NULL`.

## 9. Bảng `contract_renewals`
- `id` (BIGINT, PK, Auto Increment)
- `contract_id` (BIGINT, FK -> contracts.id)
- `previous_end_date` (DATE)
- `new_end_date` (DATE)
- `renewed_at` (TIMESTAMP)
- `renewed_by` (BIGINT, FK -> users.id, Nullable)
- `reason` (TEXT, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

## 10. Bảng `invoices`
- `id` (BIGINT, PK, Auto Increment)
- `invoice_code` (VARCHAR, Unique)
- `room_id` (BIGINT)
- `student_id` (BIGINT, Nullable)
- `billing_month` (DATE) - tháng xuất hóa đơn (lưu ngày đầu tháng)
- `total_amount` (DECIMAL(10,2))
- `status` (VARCHAR) - trạng thái (`unpaid`, `paid`)
- `paid_at` (TIMESTAMP, Nullable)
- `payment_method` (VARCHAR, Nullable)
- `created_at`, `updated_at` (TIMESTAMP)

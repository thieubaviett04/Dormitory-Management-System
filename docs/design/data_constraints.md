# Ràng buộc dữ liệu (Data Constraints)

Tổng hợp các ràng buộc dữ liệu chính nhằm bảo toàn tính toàn vẹn cơ sở dữ liệu.

## 1. Ràng buộc Khóa ngoại (Foreign Key Constraints)
- `students.user_id` -> `users.id` (ON DELETE SET NULL)
- `rooms.block_id` -> `blocks.id` (ON DELETE RESTRICT)
- `rooms.room_type_id` -> `room_types.id` (ON DELETE RESTRICT)
- `accommodation_registrations.student_id` -> `students.id` (ON DELETE CASCADE)
- `accommodation_registrations.room_type_id` -> `room_types.id` (ON DELETE RESTRICT)
- `contracts.student_id` -> `students.id` (ON DELETE CASCADE)
- `contracts.room_id` -> `rooms.id` (ON DELETE RESTRICT)
- `utility_invoices.room_id` -> `rooms.id` (ON DELETE RESTRICT)

## 2. Ràng buộc Miền giá trị (Domain/Check Constraints)
- `users.role` bắt buộc phải là một trong các giá trị: `admin`, `manager`, `student`.
- `students.gender` bắt buộc phải là: `male`, `female`.
- `blocks.gender_target` bắt buộc phải là: `male`, `female`, `mixed`.
- `rooms.status` bắt buộc phải là: `available`, `full`, `maintenance`.
- `accommodation_registrations.status` bắt buộc phải là: `pending`, `approved`, `rejected`.
- `contracts.status` bắt buộc phải là: `active`, `expired`, `terminated`.
- `utility_invoices.status` bắt buộc phải là: `unpaid`, `paid`.

## 3. Ràng buộc Logic bổ sung
- `rooms.current_occupancy` >= 0 và `rooms.current_occupancy` <= `room_types.capacity`.
- `contracts.end_date` phải lớn hơn `contracts.start_date`.
- `utility_invoices.electricity_end` >= `utility_invoices.electricity_start`.
- `utility_invoices.water_end` >= `utility_invoices.water_start`.
- `utility_invoices.total_amount` >= 0.

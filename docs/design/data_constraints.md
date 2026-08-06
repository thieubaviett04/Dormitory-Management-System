# Ràng buộc dữ liệu (Data Constraints)

Tổng hợp các ràng buộc dữ liệu chính nhằm bảo toàn tính toàn vẹn cơ sở dữ liệu.

## 1. Ràng buộc Khóa ngoại (Foreign Key Constraints)
- `rooms.building_id` -> `buildings.id` (ON DELETE CASCADE)
- `beds.room_id` -> `rooms.id` (ON DELETE CASCADE)
- `room_registrations.student_id` -> `students.id` (ON DELETE CASCADE)
- `room_registrations.room_id` -> `rooms.id` (ON DELETE RESTRICT)
- `room_registrations.reviewed_by`, `completed_by` -> `users.id` (ON DELETE SET NULL)
- `contracts.room_registration_id` -> `room_registrations.id` (ON DELETE RESTRICT, Unique)
- `contracts.student_id` -> `students.id` (ON DELETE RESTRICT)
- `allocations.contract_id` -> `contracts.id` (ON DELETE RESTRICT)
- `allocations.bed_id` -> `beds.id` (ON DELETE RESTRICT)
- `contract_renewals.contract_id` -> `contracts.id` (ON DELETE RESTRICT)

## 2. Ràng buộc Miền giá trị (Domain/Check Constraints)
- `students.gender` bắt buộc phải là: `male`, `female`, `other`.
- `buildings.gender_policy` bắt buộc phải là: `male`, `female`, `mixed`.
- `rooms.status` bắt buộc phải là: `available`, `full`, `maintenance`.
- `beds.status` bắt buộc phải là: `available`, `occupied`, `maintenance`.
- `room_registrations.status` bắt buộc phải là: `pending`, `approved`, `rejected`, `waitlist`, `cancelled`, `completed`.
- `contracts.status` bắt buộc phải là: `active`, `expired`, `terminated`.
- `allocations.release_reason` khi có giá trị phải là: `transferred`, `checked_out`, `contract_expired`, `contract_terminated`.
- `invoices.status` sử dụng `unpaid`, `paid` trong luồng nghiệp vụ hiện tại.

## 3. Ràng buộc Logic bổ sung
- Số allocation hoạt động trong một phòng không được vượt `rooms.capacity`.
- `contracts.end_date` phải lớn hơn `contracts.start_date`.
- `contracts.monthly_rate` phải lớn hơn hoặc bằng 0.
- Mỗi sinh viên chỉ có tối đa một hợp đồng `active`.
- Mỗi giường và mỗi hợp đồng chỉ có tối đa một allocation có `released_at IS NULL`.
- `allocations.released_at` phải rỗng hoặc không trước `allocated_at`.
- `contract_renewals.new_end_date` phải lớn hơn `previous_end_date`.
- `invoices.total_amount` >= 0 theo validation nghiệp vụ.

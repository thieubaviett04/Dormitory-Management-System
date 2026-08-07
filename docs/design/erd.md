# Sơ đồ quan hệ thực thể (ERD)

## Phạm vi

ERD dưới đây phản ánh các khóa ngoại và ràng buộc đang có trong migrations của Module 1–3: cơ sở vật chất, đăng ký chỗ ở, phân giường và hợp đồng. Đây là nguồn tham chiếu cho các thay đổi liên quan đến `Allocation` và `Contract`.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        string password
    }

    BUILDINGS {
        bigint id PK
        string code UK
        string name
        tinyint floors
        enum gender_policy
        text description
    }

    ROOMS {
        bigint id PK
        bigint building_id FK
        string room_number
        tinyint floor
        tinyint capacity
        enum status
    }

    BEDS {
        bigint id PK
        bigint room_id FK
        string bed_number
        enum status
    }

    STUDENTS {
        bigint id PK
        string student_code UK
        string full_name
        string email UK
        string phone_number
        enum gender
        date date_of_birth
    }

    ROOM_REGISTRATIONS {
        bigint id PK
        bigint student_id FK
        bigint room_id FK
        enum status
        timestamp registered_at
        timestamp reviewed_at
        bigint reviewed_by FK
        text rejected_reason
        timestamp cancelled_at
        text cancellation_reason
        timestamp completed_at
        bigint completed_by FK
    }

    CONTRACTS {
        bigint id PK
        string contract_code UK
        bigint room_registration_id FK
        bigint student_id FK
        date start_date
        date end_date
        decimal monthly_rate
        enum status
        timestamp signed_at
        timestamp terminated_at
        text termination_reason
        bigint created_by FK
    }

    ALLOCATIONS {
        bigint id PK
        bigint contract_id FK
        bigint bed_id FK
        timestamp allocated_at
        timestamp released_at
        enum release_reason
        bigint allocated_by FK
        bigint released_by FK
        text notes
        text release_notes
    }

    CONTRACT_RENEWALS {
        bigint id PK
        bigint contract_id FK
        date previous_end_date
        date new_end_date
        timestamp renewed_at
        bigint renewed_by FK
        text reason
    }

    CONTRACT_SEQUENCES {
        smallint year PK
        integer last_number
    }

    BUILDINGS ||--o{ ROOMS : contains
    ROOMS ||--o{ BEDS : contains
    STUDENTS ||--o{ ROOM_REGISTRATIONS : submits
    ROOMS ||--o{ ROOM_REGISTRATIONS : requested_for
    USERS o|--o{ ROOM_REGISTRATIONS : reviews
    USERS o|--o{ ROOM_REGISTRATIONS : completes
    ROOM_REGISTRATIONS ||--o| CONTRACTS : becomes
    STUDENTS ||--o{ CONTRACTS : signs
    USERS o|--o{ CONTRACTS : creates
    CONTRACTS ||--o{ ALLOCATIONS : has_history
    BEDS ||--o{ ALLOCATIONS : is_assigned_in
    USERS o|--o{ ALLOCATIONS : allocates
    USERS o|--o{ ALLOCATIONS : releases
    CONTRACTS ||--o{ CONTRACT_RENEWALS : has
    USERS o|--o{ CONTRACT_RENEWALS : renews
```

## Cách đọc luồng Module 3

```mermaid
flowchart LR
    student[Student] --> registration[Room registration]
    registration --> contract[Contract]
    contract --> allocation[Allocation history]
    allocation --> bed[Bed]
    bed --> room[Room]
    room --> building[Building]
    contract --> renewal[Contract renewal history]
```

- `room_registrations.room_id` là **phòng nguyện vọng** khi sinh viên đăng ký; không phải phòng ở hiện tại.
- `contracts` không có `room_id`. Phòng/giường hiện tại luôn được truy vấn qua allocation có `released_at = null`.
- Một `Contract` có nhiều `Allocation` để giữ lịch sử phân giường và chuyển phòng; chỉ một allocation được hoạt động tại một thời điểm.
- `CONTRACT_SEQUENCES` là bảng độc lập để sinh mã hợp đồng `HD-[Năm]-[Số thứ tự]`, không có khóa ngoại.

## Ràng buộc quan trọng

| Bảng | Ràng buộc |
| --- | --- |
| `rooms` | `(building_id, room_number)` là duy nhất. |
| `beds` | `(room_id, bed_number)` là duy nhất. |
| `room_registrations` | Tối đa một đơn `pending`, `waitlist` hoặc `approved` cho mỗi sinh viên. |
| `contracts` | `room_registration_id` là duy nhất; mỗi sinh viên tối đa một hợp đồng `active`. |
| `allocations` | Mỗi giường và mỗi hợp đồng tối đa một allocation có `released_at IS NULL`. |
| `contract_renewals` | `new_end_date` phải sau `previous_end_date`. |

## Các bảng ngoài phạm vi quan hệ khóa ngoại hoàn chỉnh

Các bảng dịch vụ/hóa đơn/vi phạm hiện có trong schema nhưng một số cột liên kết vẫn là `unsignedBigInteger`, chưa có foreign key vật lý: `utility_readings.room_id`, `invoices.room_id`, `invoices.student_id`, `invoice_items.service_type_id`, `violation_records.student_id` và `violation_records.recorded_by`.

Vì vậy, ERD không vẽ các liên kết đó như ràng buộc database. Hai quan hệ khóa ngoại thực tế của nhóm bảng này là:

```mermaid
erDiagram
    INVOICES ||--o{ INVOICE_ITEMS : contains
    VIOLATION_TYPES ||--o{ VIOLATION_RECORDS : classifies
```

Khi Module Dịch vụ/Hóa đơn hoặc Vi phạm được chuẩn hóa, cần bổ sung foreign key tương ứng trước khi mở rộng ERD chính.

# Sơ đồ quan hệ thực thể (ERD)

## Phạm vi

ERD dưới đây phản ánh các thực thể, khóa ngoại và ràng buộc logic của toàn bộ hệ thống. Các bảng thuộc Module Dịch vụ, Hóa đơn và Vi phạm được vẽ đầy đủ các quan hệ để thể hiện đúng logic nghiệp vụ, dù trong code một số cột hiện tại mới chỉ là `unsignedBigInteger`.

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

    SERVICE_TYPES {
        bigint id PK
        string name
        decimal price
        string unit
        text description
    }

    UTILITY_READINGS {
        bigint id PK
        bigint room_id FK
        date billing_month
        integer electricity_start
        integer electricity_end
        integer water_start
        integer water_end
        bigint recorded_by FK
    }

    INVOICES {
        bigint id PK
        string invoice_code UK
        bigint room_id FK
        bigint student_id FK
        date billing_month
        decimal total_amount
        enum status
        timestamp paid_at
        string payment_method
    }

    INVOICE_ITEMS {
        bigint id PK
        bigint invoice_id FK
        bigint service_type_id FK
        string item_name
        decimal quantity
        decimal price
        decimal subtotal
    }

    VIOLATION_TYPES {
        bigint id PK
        string name UK
        string severity
        decimal fine_amount
        text description
    }

    VIOLATION_RECORDS {
        bigint id PK
        bigint student_id FK
        bigint violation_type_id FK
        date record_date
        text description
        bigint recorded_by FK
        enum status
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

    ROOMS ||--o{ UTILITY_READINGS : has
    USERS o|--o{ UTILITY_READINGS : records
    ROOMS ||--o{ INVOICES : billed_for
    STUDENTS o|--o{ INVOICES : billed_to
    INVOICES ||--o{ INVOICE_ITEMS : contains
    SERVICE_TYPES o|--o{ INVOICE_ITEMS : categorized_as
    STUDENTS ||--o{ VIOLATION_RECORDS : commits
    VIOLATION_TYPES ||--o{ VIOLATION_RECORDS : classifies
    USERS o|--o{ VIOLATION_RECORDS : records
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

## Ràng buộc khóa ngoại của Dịch vụ, Hóa đơn và Vi phạm

Các bảng dịch vụ/hóa đơn/vi phạm hiện có trong schema nhưng một số cột liên kết vẫn là `unsignedBigInteger`, chưa có foreign key vật lý (như `utility_readings.room_id`, `invoices.room_id`, `invoices.student_id`, `invoice_items.service_type_id`, `violation_records.student_id` và `violation_records.recorded_by`).

Tuy nhiên, để phản ánh đúng logic nghiệp vụ, ERD ở trên vẽ tất cả các quan hệ này dưới dạng liên kết khóa ngoại hoàn chỉnh (nét liền) và đánh dấu FK đầy đủ. 

Khi Module Dịch vụ/Hóa đơn hoặc Vi phạm được chuẩn hóa, hệ thống sẽ cần bổ sung foreign key vật lý tương ứng trong database migrations để code đồng bộ hoàn toàn với thiết kế trên ERD.

# Cấu trúc Dự án (Project Structure)

Dự án sử dụng framework **Laravel** và tuân thủ cấu trúc mặc định, kết hợp với thư mục tài liệu `docs/` ở gốc.

## 1. Cấu trúc thư mục tổng quát
```text
Dormitory-Management-System/
├── app/                  # Chứa logic ứng dụng (Models, Controllers, Providers, Middleware...)
├── bootstrap/            # Chứa các file khởi chạy ứng dụng
├── config/               # Chứa toàn bộ cấu hình dự án
├── database/             # Chứa Migrations, Seeders và Factories
├── docs/                 # Thư mục chứa toàn bộ tài liệu của dự án (Phase 1)
│   ├── planning/         # Kế hoạch, roadmap, git flow, phân công, backlog
│   ├── analysis/         # Đặc tả nghiệp vụ, Use Case, Business Rules
│   ├── design/           # Thiết kế ERD, Schema, Data Dictionary
│   ├── modules/          # Mô tả chi tiết từng module chức năng
│   └── meetings/         # Nhật ký các buổi họp nhóm
├── public/               # Thư mục gốc web server, chứa tài nguyên tĩnh (images, css, js)
├── resources/            # Chứa giao diện (Views - Blade templates, CSS/JS gốc)
├── routes/               # Định nghĩa các tuyến đường (web.php, api.php...)
├── storage/              # Chứa các file log, session, cache, file upload
├── tests/                # Chứa các file unit/feature test
├── .env.example          # Tệp cấu hình môi trường mẫu
├── .gitignore            # Danh sách tệp/thư mục Git bỏ qua
├── composer.json         # Danh sách thư viện PHP quản lý bởi Composer
└── README.md             # Giới thiệu dự án và hướng dẫn chạy nhanh
```

## 2. Quy tắc đặt tên trong mã nguồn (Coding Conventions)
- **Controllers**: Đặt tên dạng PascalCase và có hậu tố `Controller` (ví dụ: `RoomController`, `ContractController`).
- **Models**: Đặt tên dạng PascalCase, số ít (ví dụ: `Room`, `Contract`, `Student`).
- **Migrations**: Đặt tên dạng snake_case, bắt đầu bằng hành động (ví dụ: `create_rooms_table`, `create_contracts_table`).
- **Views**: Đặt tên dạng snake_case, đặt trong thư mục tương ứng với Controller (ví dụ: `resources/views/rooms/index.blade.php`).
- **Routes**: Sử dụng RESTful resource route nếu có thể. Tên đường dẫn dạng kebab-case (ví dụ: `/accommodation-registrations`).

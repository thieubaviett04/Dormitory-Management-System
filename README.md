# 🚀 Dormitory Management System (Hệ thống Quản lý Ký túc xá)

Hệ thống Quản lý Ký túc xá là giải pháp phần mềm được phát triển trên nền tảng **Laravel** và **PostgreSQL** giúp quản lý tối ưu hóa hoạt động nội trú của sinh viên, từ khâu tiếp nhận đăng ký, phân phòng, quản lý hợp đồng đến tính hóa đơn dịch vụ hàng tháng.

---

## 🛠️ Công nghệ sử dụng
- **Backend Framework:** Laravel (PHP 8.2+)
- **Database:** PostgreSQL
- **Frontend:** HTML, Vanilla CSS, Vanilla JavaScript
- **Version Control:** Git & GitHub (Git Flow)

---

## 📂 Thư mục Tài liệu dự án (Documentation Index)

Dự án đã được cấu trúc sẵn bộ khung tài liệu chuẩn hóa tại thư mục [docs/](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs). Dưới đây là mục lục tài liệu chi tiết:

### 1. 📋 Kế hoạch & Quy trình (Planning)
- [Kế hoạch Dự án](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/planning/project_plan.md) - Mục tiêu, thời gian biểu và cột mốc quan trọng.
- [Lộ trình thực hiện](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/planning/roadmap.md) - Các pha phát triển chi tiết.
- [Phân công công việc](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/planning/assignment.md) - Phân chia vai trò của các thành viên trong nhóm.
- [Quy trình Git & Commit](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/planning/git_workflow.md) - Hướng dẫn đặt tên nhánh, commit message chuẩn.
- [Cấu trúc thư mục mã nguồn](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/planning/project_structure.md) - Quy định lập trình và cách bố trí mã nguồn.
- [Product Backlog](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/planning/product_backlog.md) - Danh sách Epic, Feature, User Story phục vụ chia Task.

### 2. 🔍 Phân tích Nghiệp vụ (Analysis)
- [Giới thiệu đề tài](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/analysis/introduction.md) - Khái quát bài toán và đối tượng sử dụng.
- [Đặc tả yêu cầu nghiệp vụ](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/analysis/business_requirements.md) - Các yêu cầu chức năng & phi chức năng.
- [Quy tắc nghiệp vụ (Business Rules)](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/analysis/business_rules.md) - Các quy luật và ràng buộc bắt buộc.
- [Sơ đồ Use Case](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/analysis/use_case.md) - Các tác nhân và danh mục Use Case.
- [Mô tả chi tiết Use Case](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/analysis/use_case_description.md) - Luồng xử lý chi tiết của các Use Case chính.

### 3. 📐 Thiết kế Hệ thống (Design)
- [Sơ đồ quan hệ thực thể (ERD)](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/design/erd.md) - Biểu đồ quan hệ giữa các bảng (sử dụng Mermaid).
- [Lược đồ Cơ sở dữ liệu (Database Schema)](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/design/database_schema.md) - Thiết kế các bảng vật lý PostgreSQL.
- [Từ điển dữ liệu (Data Dictionary)](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/design/data_dictionary.md) - Giải nghĩa chi tiết từng cột trong các bảng.
- [Ràng buộc dữ liệu](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/design/data_constraints.md) - Ràng buộc khóa ngoại, miền giá trị và logic.

### 4. 📦 Chi tiết các Module chức năng
- [Module Quản lý KTX](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/modules/ktx_management.md) - Quản lý dãy nhà, loại phòng và phòng.
- [Module Đăng ký chỗ ở](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/modules/accommodation_registration.md) - Đăng ký trực tuyến và quy trình duyệt đơn.
- [Module Hợp đồng lưu trú](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/modules/room_allocation_contracts.md) - Lập hợp đồng, phân phòng và xuất in PDF.
- [Module Dịch vụ & Hóa đơn](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/modules/utilities_invoices.md) - Tính tiền điện nước và cập nhật trạng thái thanh toán.

### 5. 👥 Biên bản Họp nhóm (Meetings)
- [Nhật ký họp nhóm](file:///d:/PHP-CN%20Web/Dormitory-Management-System/docs/meetings/meeting_logs.md) - Nhật ký ghi nhận các nội dung thảo luận.

---

## ⚡ Hướng dẫn cài đặt & Chạy nhanh (Quick Start)

### 1. Chuẩn bị môi trường
- Cài đặt **PHP (phiên bản 8.2 trở lên)**
- Cài đặt **Composer**
- Cài đặt hệ quản trị cơ sở dữ liệu **PostgreSQL** và tạo một database tên là `dormitory_management`

### 2. Cài đặt các thư viện phụ thuộc
Di chuyển vào thư mục dự án và chạy:
```bash
composer install
```

### 3. Thiết lập cấu hình môi trường
Hệ thống đã chuẩn bị sẵn file `.env.example`. Sao chép thành file `.env` bằng lệnh:
```bash
cp .env.example .env
```
Mở file `.env` và kiểm tra cấu hình kết nối PostgreSQL của bạn:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dormitory_management
DB_USERNAME=postgres
DB_PASSWORD=mật_khẩu_của_bạn
```

### 4. Tạo mã khóa ứng dụng (Application Key)
```bash
php artisan key:generate
```

### 5. Khởi chạy ứng dụng
Chạy server phát triển của Laravel:
```bash
php artisan serve
```
Truy cập ứng dụng tại địa chỉ: [http://localhost:8000](http://localhost:8000)

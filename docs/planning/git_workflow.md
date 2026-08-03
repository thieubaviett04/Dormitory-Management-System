# Quy trình Git & Quy ước Đóng góp (Git Workflow)

Quy trình này áp dụng thống nhất cho toàn bộ thành viên trong nhóm phát triển dự án.

## 1. Cấu trúc Nhánh (Branching Model)
- **`main`**: Nhánh chính chứa mã nguồn ổn định, đã kiểm thử kỹ càng. Chỉ được gộp từ nhánh `develop`.
- **`develop`**: Nhánh tích hợp chính để các thành viên gộp tính năng. Tất cả các nhánh tính năng đều tách ra từ `develop` và gộp về lại `develop`.
- **`feature/*`**: Các nhánh phát triển tính năng riêng biệt. 
  - Quy tắc đặt tên: `feature/module-name` (ví dụ: `feature/module-ktx`, `feature/module-registration`).

```text
main (nhánh chính, deploy)
  ↑ (Release Merge)
develop (nhánh tích hợp, dev chung)
  ↑ (Pull Request)
feature/* (nhánh phát triển cá nhân)
```

## 2. Quy ước Tên Nhánh (Branch Convention)
- Nhánh tính năng mới: `feature/module-name`
- Nhánh sửa lỗi: `bugfix/issue-name`
- Nhánh viết tài liệu: `docs/doc-name`
- Nhánh cấu hình/tối ưu: `refactor/config-name`

## 3. Quy ước Commit Message (Commit Convention)
Commit message phải tuân thủ định dạng sau:
`<type>(<scope>): <subject>`

### Các loại Type hợp lệ:
- **`feat`**: Tính năng mới (ví dụ: `feat(room): thêm giao diện danh sách phòng`)
- **`fix`**: Sửa lỗi (ví dụ: `fix(contract): sửa kiểm tra hợp đồng`)
- **`docs`**: Thay đổi về tài liệu (ví dụ: `docs(erd): cập nhật sơ đồ ERD`)
- **`style`**: Định dạng code, dấu phẩy, khoảng trắng... (không ảnh hưởng logic code)
- **`refactor`**: Tái cấu trúc mã nguồn (không thêm tính năng hay sửa lỗi)
- **`test`**: Thêm hoặc sửa các unit test
- **`chore`**: Cấu hình công cụ, thư viện, `.gitignore`... (ví dụ: `chore(env): cập nhật database config`)

## 4. Quy trình Đóng góp Mã nguồn (PR & Merge)
1. Kéo code mới nhất từ `develop` về local: `git pull origin develop`
2. Tạo nhánh tính năng mới: `git checkout -b feature/tên-tính-năng`
3. Thực hiện code và commit định kỳ với commit message chuẩn.
4. Đẩy nhánh lên GitHub: `git push origin feature/tên-tính-năng`
5. Tạo Pull Request (PR) từ `feature/tên-tính-năng` vào `develop`.
6. Chỉ định ít nhất 1 thành viên review code. Sau khi được duyệt, merge PR vào `develop`.

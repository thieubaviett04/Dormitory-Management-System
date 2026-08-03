# Quy tắc nghiệp vụ (Business Rules)

Tài liệu này định nghĩa các quy tắc và ràng buộc nghiệp vụ bắt buộc phải tuân thủ trong hệ thống.

## BR-01: Quy tắc xếp phòng
- Mỗi phòng có một sức chứa tối đa xác định (ví dụ: tối đa 4, 6 hoặc 8 sinh viên). 
- Hệ thống không được phép xếp thêm sinh viên vào phòng đã đạt trạng thái Đầy (Sĩ số hiện tại >= Sức chứa tối đa).
- Nam và Nữ sinh viên không được xếp chung vào cùng một phòng (trừ khi có cấu hình đặc biệt cho phòng gia đình, nếu có).

## BR-02: Quy tắc hợp đồng
- Sinh viên chỉ được phép ký hợp đồng lưu trú khi có đơn đăng ký đã được Ban quản lý phê duyệt.
- Một sinh viên tại một thời điểm chỉ có tối đa 1 hợp đồng lưu trú còn hiệu lực.

## BR-03: Quy tắc tính tiền điện nước
- Tiền điện/nước hàng tháng của một phòng được tính theo công thức:
  `Tiền tiêu thụ = (Chỉ số cuối - Chỉ số đầu) * Đơn giá`
- Chỉ số đầu của tháng này phải bằng hoặc lớn hơn chỉ số cuối của tháng liền kề trước đó.
- Hóa đơn dịch vụ sau khi tạo sẽ ở trạng thái "Chưa thanh toán". Khi sinh viên nộp tiền và thủ quỹ xác nhận, trạng thái sẽ chuyển thành "Đã thanh toán" và ghi nhận ngày thanh toán.

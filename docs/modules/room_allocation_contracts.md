# Module Phân phòng & Hợp đồng lưu trú

## 1. Mục đích

Module sử dụng đơn đăng ký `approved` để lập hợp đồng và phân một giường thuộc đúng phòng nguyện vọng cho sinh viên. `Contract` là đối tượng gốc; `Allocation` lưu toàn bộ lịch sử nhận giường, chuyển phòng và trả phòng.

## 2. Mô hình dữ liệu

- `contracts` liên kết duy nhất với `room_registrations` và lưu sinh viên, thời hạn, đơn giá tại thời điểm ký, trạng thái cùng thông tin thanh lý.
- `allocations` liên kết hợp đồng với giường. Bản ghi có `released_at = null` là phân giường hiện tại.
- `contract_renewals` lưu ngày hết hạn cũ và mới cho mỗi lần gia hạn.
- `contract_sequences` cấp số hợp đồng an toàn theo từng năm, định dạng `HD-[Năm]-[Số thứ tự]`.

Không lưu `room_id` trực tiếp trên hợp đồng. Phòng hiện tại được truy vấn qua `contract -> current allocation -> bed -> room`, nhờ đó chuyển phòng không làm mất lịch sử.

## 3. Trạng thái

Trạng thái hợp đồng:

- `active`: đang hiệu lực và phải có đúng một allocation hoạt động.
- `expired`: đã hết hạn tự nhiên.
- `terminated`: đã trả phòng hoặc thanh lý trước hạn.

Lý do kết thúc allocation:

- `transferred`
- `checked_out`
- `contract_expired`
- `contract_terminated`

## 4. Quy tắc tạo hợp đồng và phân giường

- Đơn phải ở trạng thái `approved` và chưa được dùng để lập hợp đồng.
- Sinh viên không được có hợp đồng `active` khác.
- Giường đầu tiên phải thuộc đúng phòng nguyện vọng của đơn.
- Phòng và giường không được bảo trì; giường phải trống.
- Số allocation hoạt động trong phòng không được vượt `rooms.capacity`.
- Giới tính sinh viên phải phù hợp `buildings.gender_policy`.
- Dù tòa nhà có chính sách `mixed`, một phòng không được có các allocation hoạt động khác giới tính.
- Tạo hợp đồng, tạo allocation, đổi trạng thái giường và tính lại trạng thái phòng phải nằm trong cùng transaction.

## 5. Chuyển phòng

Chuyển phòng đóng allocation hiện tại với lý do `transferred`, giải phóng giường cũ và tạo allocation mới. Hợp đồng không đổi. Giường mới không bắt buộc nằm trong phòng nguyện vọng ban đầu, nhưng vẫn phải vượt qua kiểm tra sức chứa, bảo trì và giới tính.

## 6. Gia hạn

Chỉ hợp đồng `active` được gia hạn. `new_end_date` phải sau `end_date` hiện tại. Mỗi lần gia hạn tạo một `contract_renewals` trước khi cập nhật ngày hết hạn trên hợp đồng.

## 7. Trả phòng, thanh lý và hết hạn

- Trả phòng/thanh lý đóng allocation hiện tại, giải phóng giường và chuyển hợp đồng sang `terminated` trong cùng transaction.
- Command `contracts:expire` xử lý hằng ngày các hợp đồng có `end_date` trước ngày hiện tại, chuyển sang `expired` và giải phóng giường.
- Khi hợp đồng `expired` hoặc `terminated`, đơn đăng ký liên quan chuyển từ `approved` sang `completed` và lưu thông tin hoàn tất.

## 8. API

| Method | Endpoint | Mục đích |
| --- | --- | --- |
| `GET` | `/contracts` | Danh sách hợp đồng |
| `GET` | `/contracts/eligible-registrations` | Đơn đủ điều kiện lập hợp đồng |
| `POST` | `/contracts` | Tạo hợp đồng và phân giường đầu tiên |
| `GET` | `/contracts/{contract}` | Chi tiết và lịch sử hợp đồng |
| `POST` | `/contracts/{contract}/transfers` | Chuyển phòng |
| `POST` | `/contracts/{contract}/renewals` | Gia hạn |
| `PATCH` | `/contracts/{contract}/terminate` | Trả phòng hoặc thanh lý |

API thành công trả `message` và `data`. Lỗi validation trả HTTP `422` với `message` và `errors` theo chuẩn Laravel.

## 9. Lưu ý vận hành

`allocations` là nguồn dữ liệu chính để xác định sinh viên đang ở đâu. `beds.status` và `rooms.status` được đồng bộ trong service để phục vụ giao diện. Không được tự gán giường thành `occupied` nếu không có allocation tương ứng.

## 10. Dữ liệu mẫu

`ContractSeeder` phụ thuộc vào dữ liệu tòa nhà, phòng và giường; `DatabaseSeeder` đã gọi đúng thứ tự này. Một cơ sở dữ liệu mới có thể được tạo dữ liệu mẫu bằng:

```bash
php artisan migrate:fresh --seed
```

Để chỉ tạo hoặc đồng bộ lại dữ liệu mẫu Module 3 trên cơ sở dữ liệu đã có sẵn cấu trúc KTX, chạy:

```bash
php artisan db:seed --class=ContractSeeder
```

Seeder tạo 5 tình huống: hợp đồng đang hiệu lực, đã chuyển giường, đã gia hạn, đã thanh lý và đã hết hạn. Các bản ghi Module 3 được nhận diện bằng mã `SEED-...`; có thể chạy lại mà không tạo trùng. Không dùng lệnh này trên dữ liệu vận hành nếu các giường mẫu đã được phân cho hợp đồng thật.

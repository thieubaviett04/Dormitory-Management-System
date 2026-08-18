{{-- File: resources/views/registrations/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Quản lý Đơn chờ duyệt')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-foreground">Quản lý Đơn đăng ký</h2>
                <p class="text-sm text-muted-foreground mt-1">Xem danh sách và xét duyệt đơn đăng ký phòng của sinh viên.
                </p>
            </div>
        </div>

        <!-- Data Table Container -->
        <div class="rounded-md border bg-card shadow-sm overflow-hidden">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground w-[80px]">ID Đơn
                            </th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Mã SV</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Họ Tên</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Nguyện vọng</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Trạng thái</th>
                            <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="[&_tr:last-child]:border-0">
                        <tr>
                            <td colspan="6" class="p-8 text-center text-muted-foreground">Đang tải dữ liệu...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value ?? '';
            return element.innerHTML;
        }

        async function fetchPending() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-muted-foreground"><i data-lucide="loader-2" class="h-6 w-6 animate-spin mx-auto"></i></td></tr>';
            if (typeof lucide !== 'undefined') lucide.createIcons();

            try {
                const response = await fetch('/registration/pending', { headers: { 'Accept': 'application/json' } });
                const result = await response.json();

                if (!response.ok) throw new Error(result.message || 'Không thể tải danh sách đơn.');

                tbody.innerHTML = '';
                if (!result.data || result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-muted-foreground">Tuyệt vời! Không có đơn đăng ký nào đang tồn đọng.</td></tr>';
                    return;
                }

                result.data.forEach(item => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b transition-colors hover:bg-muted/50';
                    tr.innerHTML = `
                        <td class="p-4 align-middle font-medium text-muted-foreground">#${item.id}</td>
                        <td class="p-4 align-middle font-medium">${escapeHtml(item.student.student_code)}</td>
                        <td class="p-4 align-middle">${escapeHtml(item.student.full_name)}</td>
                        <td class="p-4 align-middle font-semibold text-primary">
                            ${item.room ? `${escapeHtml(item.room.building?.code || '')} - Phòng ${item.room.room_number}` : `Phòng ID: ${item.room_id}`}
                        </td>
                        <td class="p-4 align-middle text-center">
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 border-transparent">Đang chờ</span>
                        </td>
                        <td class="p-4 align-middle text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button class="inline-flex h-8 items-center justify-center rounded-md bg-emerald-600 px-3 text-xs font-medium text-white shadow hover:bg-emerald-700 transition-colors" onclick="updateStatus(${item.id}, 'approved')">Duyệt</button>
                                <button class="inline-flex h-8 items-center justify-center rounded-md bg-orange-500 px-3 text-xs font-medium text-white shadow hover:bg-orange-600 transition-colors" onclick="updateStatus(${item.id}, 'waitlist')">Hàng chờ</button>
                                <button class="inline-flex h-8 items-center justify-center rounded-md bg-destructive px-3 text-xs font-medium text-destructive-foreground shadow hover:bg-destructive/90 transition-colors" onclick="updateStatus(${item.id}, 'rejected')">Từ chối</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (error) {
                tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-destructive font-medium"><i data-lucide="alert-circle" class="h-5 w-5 mx-auto mb-2"></i>${escapeHtml(error.message)}</td></tr>`;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }

        async function updateStatus(id, newStatus) {
            const actionNames = { approved: 'DUYỆT', rejected: 'TỪ CHỐI', waitlist: 'ĐƯA VÀO DANH SÁCH CHỜ' };
            const rejectedReason = newStatus === 'rejected' ? prompt('Nhập lý do từ chối (bắt buộc):') : null;

            if (newStatus === 'rejected' && (!rejectedReason || !rejectedReason.trim())) {
                alert('Lý do từ chối là bắt buộc.');
                return;
            }

            if (!confirm(`Bạn có chắc chắn muốn ${actionNames[newStatus]} đơn số #${id}?`)) return;

            try {
                const response = await fetch(`/registration/update/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ status: newStatus, rejected_reason: rejectedReason })
                });
                const result = await response.json();

                if (!response.ok) {
                    const validationMessages = Object.values(result.errors || {}).flat();
                    throw new Error(validationMessages[0] || result.message || 'Không thể cập nhật trạng thái.');
                }

                alert('✅ ' + result.message);
                fetchPending();
            } catch (error) {
                alert('❌ ' + (error.message || 'Có lỗi xảy ra khi cập nhật!'));
            }
        }

        fetchPending();
    </script>
@endsection
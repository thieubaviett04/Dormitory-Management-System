{{-- File: resources/views/registrations/status.blade.php --}}
@extends('layouts.app')

@section('title', 'Tra cứu Trạng thái Đơn')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-foreground">Tra Cứu Đơn Ký Túc Xá</h2>
            <p class="text-sm text-muted-foreground mt-1">Nhập ID Hệ thống của sinh viên để tra cứu trạng thái đơn đăng ký.
            </p>
        </div>

        <!-- Search Box -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6 space-y-4">
            <div class="flex gap-3">
                <div class="relative w-full">
                    <i data-lucide="search" class="absolute left-3 top-2.5 h-5 w-5 text-muted-foreground"></i>
                    <input type="number" id="student_id" placeholder="Ví dụ: 10, 11..."
                        class="flex h-10 w-full rounded-md border border-input bg-transparent pl-10 pr-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                </div>
                <button onclick="checkStatus()"
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground h-10 px-6 py-2 shadow hover:bg-primary/90 whitespace-nowrap transition-colors">
                    Tra Cứu Ngay
                </button>
            </div>
        </div>

        <!-- Results -->
        <div id="result" class="space-y-4"></div>
    </div>

    <script>
        const statusPresentation = {
            pending: ['Đang chờ duyệt', 'bg-amber-100 text-amber-800'],
            approved: ['Đã duyệt', 'bg-emerald-100 text-emerald-800'],
            rejected: ['Bị từ chối', 'bg-destructive/10 text-destructive'],
            waitlist: ['Danh sách chờ', 'bg-orange-100 text-orange-800'],
            cancelled: ['Đã hủy', 'bg-slate-100 text-slate-700']
        };

        function escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value ?? '';
            return element.innerHTML;
        }

        async function checkStatus() {
            const studentId = document.getElementById('student_id').value;
            if (!studentId) return alert("Vui lòng nhập ID Sinh viên!");

            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<div class="text-center p-6 text-muted-foreground"><i data-lucide="loader-2" class="h-6 w-6 animate-spin mx-auto mb-2"></i> Đang tìm kiếm...</div>';
            if (typeof lucide !== 'undefined') lucide.createIcons();

            try {
                const response = await fetch(`/registration/status/${studentId}`, { headers: { 'Accept': 'application/json' } });
                const result = await response.json();

                if (!response.ok) throw new Error(result.message || 'Không tìm thấy đơn đăng ký.');

                resultDiv.innerHTML = '';
                result.data.forEach(item => {
                    const [statusText, statusClass] = statusPresentation[item.status] || [item.status, 'bg-gray-100 text-gray-800'];
                    const canCancel = ['pending', 'waitlist'].includes(item.status);
                    const roomName = item.room ? `${item.room.building?.code || ''} - Phòng ${item.room.room_number}` : `ID: #${item.room_id}`;

                    const rejectedInfo = item.rejected_reason
                        ? `<div class="mt-3 p-3 bg-destructive/10 rounded-md text-sm text-destructive"><span class="font-bold">Lý do từ chối:</span> ${escapeHtml(item.rejected_reason)}</div>` : '';
                    const cancelInfo = item.cancellation_reason
                        ? `<div class="mt-3 p-3 bg-muted rounded-md text-sm text-muted-foreground"><span class="font-bold">Lý do hủy:</span> ${escapeHtml(item.cancellation_reason)}</div>` : '';

                    const cancelButton = canCancel
                        ? `<div class="mt-4 pt-4 border-t border-border flex justify-end"><button class="inline-flex h-9 items-center justify-center rounded-md border border-destructive bg-transparent px-4 text-sm font-medium text-destructive shadow-sm hover:bg-destructive hover:text-destructive-foreground transition-colors" onclick="cancelRegistration(${item.id})">Hủy đơn đăng ký này</button></div>` : '';

                    resultDiv.innerHTML += `
                        <div class="rounded-xl border bg-card p-6 shadow-sm relative overflow-hidden">
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-semibold text-lg">Đơn đăng ký #${item.id}</h3>
                                    <p class="text-sm text-muted-foreground mt-1">Gửi ngày: ${new Date(item.registered_at).toLocaleDateString('vi-VN')}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ${statusClass}">${escapeHtml(statusText)}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm mt-4">
                                <div>
                                    <span class="text-muted-foreground block mb-1">Sinh viên:</span>
                                    <span class="font-medium">${escapeHtml(item.student?.full_name)} (${escapeHtml(item.student?.student_code)})</span>
                                </div>
                                <div>
                                    <span class="text-muted-foreground block mb-1">Nguyện vọng vào:</span>
                                    <span class="font-medium text-primary">${escapeHtml(roomName)}</span>
                                </div>
                            </div>
                            ${rejectedInfo}
                            ${cancelInfo}
                            ${cancelButton}
                        </div>
                    `;
                });
            } catch (error) {
                resultDiv.innerHTML = `<div class="p-4 rounded-md bg-destructive/10 text-destructive text-center font-medium">${escapeHtml(error.message)}</div>`;
            }
        }

        async function cancelRegistration(registrationId) {
            if (!confirm(`Bạn có chắc chắn muốn hủy đơn đăng ký #${registrationId}? Hành động này không thể hoàn tác.`)) return;
            const cancellationReason = prompt('Nhập lý do hủy (không bắt buộc):') || null;

            try {
                const response = await fetch(`/registration/cancel/${registrationId}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ cancellation_reason: cancellationReason })
                });
                const result = await response.json();

                if (!response.ok) {
                    const validationMessages = Object.values(result.errors || {}).flat();
                    throw new Error(validationMessages[0] || result.message || 'Không thể hủy đơn.');
                }

                alert('✅ ' + result.message);
                checkStatus(); // Load lại kết quả
            } catch (error) {
                alert('❌ ' + (error.message || 'Có lỗi xảy ra khi hủy đơn!'));
            }
        }
    </script>
@endsection
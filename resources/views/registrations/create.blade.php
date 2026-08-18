{{-- File: resources/views/registrations/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Đăng ký Ký túc xá')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-foreground">Đăng Ký Chỗ Ở</h2>
            <p class="text-sm text-muted-foreground mt-1">Điền thông tin để đăng ký phòng ký túc xá.</p>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
            <form id="registerForm" class="space-y-4">
                <!-- Mã SV & Họ Tên -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Mã sinh viên:</label>
                        <input type="text" id="student_code" required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Họ và tên:</label>
                        <input type="text" id="full_name" required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm">
                    </div>
                </div>

                <!-- Các trường khác bạn bê từ HTML cũ sang... -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Email:</label>
                    <input type="email" id="email" required
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">ID Phòng nguyện vọng:</label>
                    <input type="number" id="room_id" required
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm">
                </div>

                <!-- Nút Lưu -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground h-10 px-4 py-2 hover:bg-primary/90">
                        <i data-lucide="send" class="mr-2 h-4 w-4"></i> Gửi Đăng Ký
                    </button>
                </div>
            </form>
            <div id="message" class="mt-4 text-center text-sm font-medium"></div>
        </div>
    </div>

    <!-- Giữ nguyên đoạn Script cũ của bạn ở dưới cùng để nó gọi API -->
    <script>
        document.getElementById('registerForm').addEventListener('submit', async function (e) {
            // ... (Copy y nguyên đoạn javascript từ file register.html cũ của bạn paste vào đây) ...
        });
    </script>
@endsection
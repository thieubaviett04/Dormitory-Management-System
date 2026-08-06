@extends('layouts.app')

@section('title', 'Thêm tòa nhà mới')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-2 text-sm font-medium text-muted-foreground">
        <a href="{{ route('buildings.index') }}" class="hover:text-foreground transition-colors">Quản lý Tòa nhà</a>
        <i data-lucide="chevron-right" class="h-4 w-4 text-border"></i>
        <span class="text-foreground">Thêm tòa nhà mới</span>
    </div>

    <div>
        <h2 class="text-2xl font-semibold tracking-tight text-foreground">Thêm tòa nhà mới</h2>
        <p class="text-sm text-muted-foreground mt-1">Điền thông tin bên dưới để tạo tòa nhà mới trong hệ thống.</p>
    </div>

    <!-- Main Card -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <form action="{{ route('buildings.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Validation Errors -->
            @if ($errors->any())
            <div class="rounded-md border border-destructive/50 bg-destructive/10 p-4">
                <div class="flex">
                    <i data-lucide="alert-circle" class="h-4 w-4 text-destructive mt-0.5 mr-2"></i>
                    <div>
                        <h3 class="text-sm font-medium text-destructive">Lỗi nhập liệu:</h3>
                        <div class="mt-2 text-sm text-destructive/80">
                            <ul role="list" class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <!-- Mã tòa nhà -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="code">Mã tòa nhà</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required placeholder="Ví dụ: TA, TB, T1..." class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>

                <!-- Tên tòa nhà -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="name">Tên tòa nhà</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Ví dụ: Tòa nhà A, Tòa nhà B..." class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>

                <!-- Số tầng -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="floors">Số tầng</label>
                    <input type="number" name="floors" id="floors" value="{{ old('floors') }}" required min="1" placeholder="Ví dụ: 5" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="gender_policy">Chính sách giới tính</label>
                    <select name="gender_policy" id="gender_policy" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="male" {{ old('gender_policy') === 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender_policy') === 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="mixed" {{ old('gender_policy', 'mixed') === 'mixed' ? 'selected' : '' }}>Hỗn hợp (phòng vẫn đồng nhất giới tính)</option>
                    </select>
                </div>

                <!-- Mô tả -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="description">Mô tả chi tiết</label>
                    <textarea name="description" id="description" rows="4" placeholder="Nhập ghi chú hoặc mô tả về tòa nhà này..." class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="border-t border-border pt-4 flex justify-end gap-3">
                <a href="{{ route('buildings.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    Hủy
                </a>
                <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                    <i data-lucide="save" class="mr-2 h-4 w-4"></i> Lưu lại
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

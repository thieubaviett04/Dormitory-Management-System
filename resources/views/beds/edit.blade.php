@extends('layouts.app')

@section('title', 'Sửa thông tin giường')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-2 text-sm font-medium text-muted-foreground">
        <a href="{{ route('beds.index') }}" class="hover:text-foreground transition-colors">Quản lý Giường</a>
        <i data-lucide="chevron-right" class="h-4 w-4 text-border"></i>
        <span class="text-foreground">Sửa giường</span>
    </div>

    <div>
        <h2 class="text-2xl font-semibold tracking-tight text-foreground">Sửa thông tin giường</h2>
        <p class="text-sm text-muted-foreground mt-1">Cập nhật thông tin chi tiết của giường ở này.</p>
    </div>

    <!-- Main Card -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <form action="{{ route('beds.update', $bed->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

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
                <!-- Chọn phòng -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="room_id">Thuộc phòng</label>
                    <select name="room_id" id="room_id" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id', $bed->room_id) == $room->id ? 'selected' : '' }}>
                            {{ $room->building->name ?? 'N/A' }} - Phòng {{ $room->room_number }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Số giường -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="bed_number">Số giường / Tên giường</label>
                    <input type="text" name="bed_number" id="bed_number" value="{{ old('bed_number', $bed->bed_number) }}" required placeholder="Ví dụ: G1, G2, 01, 02..." class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>

                <!-- Trạng thái -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="status">Trạng thái giường</label>
                    <select name="status" id="status" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        @if($hasActiveAllocation)
                            <option value="occupied" selected>Đang ở (quản lý bởi Module 3)</option>
                        @else
                            <option value="available" {{ old('status', $bed->status) == 'available' ? 'selected' : '' }}>Trống (Available)</option>
                            <option value="maintenance" {{ old('status', $bed->status) == 'maintenance' ? 'selected' : '' }}>Bảo trì (Maintenance)</option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="border-t border-border pt-4 flex justify-end gap-3">
                <a href="{{ route('beds.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    Hủy
                </a>
                <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                    <i data-lucide="save" class="mr-2 h-4 w-4"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

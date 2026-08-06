@extends('layouts.app')

@section('title', 'Sửa thông tin phòng')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-2 text-sm font-medium text-muted-foreground">
        <a href="{{ route('rooms.index') }}" class="hover:text-foreground transition-colors">Quản lý Phòng</a>
        <i data-lucide="chevron-right" class="h-4 w-4 text-border"></i>
        <span class="text-foreground">Sửa phòng</span>
    </div>

    <div>
        <h2 class="text-2xl font-semibold tracking-tight text-foreground">Sửa thông tin phòng</h2>
        <p class="text-sm text-muted-foreground mt-1">Cập nhật thông tin chi tiết của phòng ở này.</p>
    </div>

    <!-- Main Card -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <form action="{{ route('rooms.update', $room->id) }}" method="POST" class="p-6 space-y-6">
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
                <!-- Tòa nhà -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="building_id">Tòa nhà</label>
                    <select name="building_id" id="building_id" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        @foreach($buildings as $building)
                        <option value="{{ $building->id }}" {{ old('building_id', $room->building_id) == $building->id ? 'selected' : '' }}>
                            {{ $building->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Số phòng -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="room_number">Số phòng</label>
                    <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $room->room_number) }}" required placeholder="Ví dụ: 101, 102, 201..." class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>

                <!-- Tầng -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="floor">Tầng</label>
                    <input type="number" name="floor" id="floor" value="{{ old('floor', $room->floor) }}" required min="1" placeholder="Ví dụ: 1" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>

                <!-- Sức chứa -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="capacity">Sức chứa (số giường tối đa)</label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $room->capacity) }}" required min="1" placeholder="Ví dụ: 4" class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>

                <!-- Trạng thái -->
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="status">Trạng thái</label>
                    <select name="status" id="status" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                        <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Trống (Available)</option>
                        <option value="full" {{ old('status', $room->status) == 'full' ? 'selected' : '' }}>Đầy (Full)</option>
                        <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Bảo trì (Maintenance)</option>
                    </select>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="border-t border-border pt-4 flex justify-end gap-3">
                <a href="{{ route('rooms.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
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
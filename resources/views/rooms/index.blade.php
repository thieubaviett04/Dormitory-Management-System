@extends('layouts.app')

@section('title', 'Danh sách Phòng')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-foreground">Quản lý Phòng</h2>
            <p class="text-sm text-muted-foreground mt-1">Danh sách phòng ở thuộc các tòa nhà trong ký túc xá.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('rooms.create') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                <i data-lucide="plus" class="mr-2 h-4 w-4"></i> Thêm phòng mới
            </a>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
    <div class="relative w-full rounded-lg border border-emerald-500/50 bg-emerald-50/50 p-4 text-emerald-600 dark:border-emerald-500 [&>svg]:text-emerald-600">
        <i data-lucide="check-circle" class="absolute left-4 top-4 h-4 w-4"></i>
        <h5 class="mb-1 leading-none font-medium pl-7">Thành công</h5>
        <div class="text-sm [&_p]:leading-relaxed pl-7">{{ session('success') }}</div>
    </div>
    @endif

    <!-- Data Table Container -->
    <div class="rounded-md border bg-card shadow-sm overflow-hidden">
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground w-[80px]">ID</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Tòa nhà</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Số phòng</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Tầng</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Sức chứa (giường)</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Trạng thái</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground w-[150px]">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @forelse($rooms as $room)
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <td class="p-4 align-middle font-medium text-muted-foreground">#{{ $room->id }}</td>
                        <td class="p-4 align-middle font-semibold text-primary">
                            {{ $room->building->name ?? 'N/A' }}
                        </td>
                        <td class="p-4 align-middle font-bold">{{ $room->room_number }}</td>
                        <td class="p-4 align-middle text-center">{{ $room->floor }}</td>
                        <td class="p-4 align-middle text-center">{{ $room->capacity }}</td>
                        <td class="p-4 align-middle text-center">
                            @if($room->status == 'available')
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-emerald-100 text-emerald-800">
                                Trống (Available)
                            </span>
                            @elseif($room->status == 'full')
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-neutral-100 text-neutral-800">
                                Đầy (Full)
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-destructive/10 text-destructive">
                                Bảo trì (Maintenance)
                            </span>
                            @endif
                        </td>
                        <td class="p-4 align-middle text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('rooms.edit', $room->id) }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-8 px-3">
                                    <i data-lucide="edit-3" class="mr-1.5 h-3.5 w-3.5"></i> Sửa
                                </a>
                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa phòng này và toàn bộ giường thuộc về nó?')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-destructive text-destructive-foreground shadow hover:bg-destructive/90 h-8 px-3">
                                        <i data-lucide="trash-2" class="mr-1.5 h-3.5 w-3.5"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-muted-foreground">
                            Chưa có phòng nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
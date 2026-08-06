@extends('layouts.app')

@section('title', 'Danh sách Tòa nhà')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-foreground">Quản lý Tòa nhà</h2>
            <p class="text-sm text-muted-foreground mt-1">Xem và quản lý thông tin các tòa nhà ký túc xá.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('buildings.create') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                <i data-lucide="plus" class="mr-2 h-4 w-4"></i> Thêm tòa nhà mới
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
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground w-[120px]">Mã tòa nhà</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Tên tòa nhà</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground w-[120px]">Số tầng</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Mô tả</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground w-[150px]">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @forelse($buildings as $building)
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <td class="p-4 align-middle font-medium text-muted-foreground">#{{ $building->id }}</td>
                        <td class="p-4 align-middle font-semibold text-primary">{{ $building->code }}</td>
                        <td class="p-4 align-middle font-medium">{{ $building->name }}</td>
                        <td class="p-4 align-middle text-center font-medium">{{ $building->floors }}</td>
                        <td class="p-4 align-middle text-muted-foreground max-w-xs truncate" title="{{ $building->description }}">{{ $building->description ?: '—' }}</td>
                        <td class="p-4 align-middle text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('buildings.edit', $building->id) }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-8 px-3">
                                    <i data-lucide="edit-3" class="mr-1.5 h-3.5 w-3.5"></i> Sửa
                                </a>
                                <form action="{{ route('buildings.destroy', $building->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa tòa nhà này và toàn bộ phòng thuộc về nó?')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-destructive text-destructive-foreground shadow hover:bg-destructive/90 h-8 px-3">
                                        <i data-lucide="trash-2" class="mr-1.5 h-3.5 w-3.5"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-muted-foreground">
                            Chưa có tòa nhà nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
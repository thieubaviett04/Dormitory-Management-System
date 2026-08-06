@extends('layouts.app')

@section('title', 'Danh sách Giường')

@section('content')
@php
    $rooms = \App\Models\Room::with('building')->orderBy('room_number')->get();
@endphp
<div class="space-y-6" x-data="{
    showCreatePanel: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
    showEditPanel: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
    showDeleteModal: false,
    deleteActionUrl: '',
    deleteConfirmMessage: '',
    selectedBed: {
        id: '{{ old('bed_id') }}',
        room_id: '{{ old('room_id') }}',
        bed_number: '{{ old('bed_number') }}',
        status: '{{ old('status') }}'
    },
    openEdit(bed) {
        this.selectedBed = {
            id: bed.id,
            room_id: bed.room_id,
            bed_number: bed.bed_number,
            status: bed.status
        };
        this.showEditPanel = true;
    },
    confirmDelete(actionUrl, message) {
        this.deleteActionUrl = actionUrl;
        this.deleteConfirmMessage = message;
        this.showDeleteModal = true;
    }
}">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-foreground">Quản lý Giường</h2>
            <p class="text-sm text-muted-foreground mt-1">Danh sách giường trong các phòng ở ký túc xá.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <button @click="showCreatePanel = true" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                <i data-lucide="plus" class="mr-2 h-4 w-4"></i> Thêm giường mới
            </button>
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
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Phòng</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Số giường / Tên</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Trạng thái</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground w-[150px]">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @forelse($beds as $bed)
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <td class="p-4 align-middle font-medium text-muted-foreground">#{{ $bed->id }}</td>
                        <td class="p-4 align-middle font-medium">{{ $bed->room->building->name ?? 'N/A' }}</td>
                        <td class="p-4 align-middle font-bold text-primary">Phòng {{ $bed->room->room_number ?? 'N/A' }}</td>
                        <td class="p-4 align-middle font-medium">{{ $bed->bed_number }}</td>
                        <td class="p-4 align-middle text-center">
                            @if($bed->status == 'available')
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-emerald-100 text-emerald-800">
                                Trống (Available)
                            </span>
                            @elseif($bed->status == 'occupied')
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-blue-100 text-blue-800">
                                Đang ở (Occupied)
                            </span>
                            @else
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-destructive/10 text-destructive">
                                Bảo trì (Maintenance)
                            </span>
                            @endif
                        </td>
                        <td class="p-4 align-middle text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button @click="openEdit({{ json_encode($bed) }})" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-8 px-3">
                                    <i data-lucide="edit-3" class="mr-1.5 h-3.5 w-3.5"></i> Sửa
                                </button>
                                <button type="button" @click="confirmDelete('{{ route('beds.destroy', $bed->id) }}', 'Bạn có chắc chắn muốn xóa giường {{ $bed->bed_number }} thuộc phòng {{ $bed->room->room_number ?? 'N/A' }}?')" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-destructive text-destructive-foreground shadow hover:bg-destructive/90 h-8 px-3">
                                    <i data-lucide="trash-2" class="mr-1.5 h-3.5 w-3.5"></i> Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-muted-foreground">
                            Chưa có giường nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SLIDE-OVER CREATE BED                      -->
    <!-- ========================================== -->
    <template x-teleport="body">
        <div x-show="showCreatePanel" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <!-- Background backdrop -->
            <div x-show="showCreatePanel"
                 x-transition:enter="ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-sm"
                 @click="showCreatePanel = false"></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <div x-show="showCreatePanel"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             class="pointer-events-auto w-screen max-w-md">
                            
                            <!-- Panel content -->
                            <div class="flex h-full flex-col overflow-y-auto bg-background shadow-xl border-l border-border">
                                <div class="px-6 py-6 border-b border-border bg-muted/30">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h2 class="text-lg font-semibold leading-none tracking-tight">Thêm giường mới</h2>
                                            <p class="text-sm text-muted-foreground mt-2">Chọn phòng và điền thông tin giường mới.</p>
                                        </div>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button @click="showCreatePanel = false" type="button" class="relative rounded-md bg-background text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                                                <i data-lucide="x" class="h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('beds.store') }}" method="POST" class="flex-1 flex flex-col">
                                    @csrf
                                    <div class="flex-1 px-6 py-6 space-y-6">
                                        <!-- Validation Errors -->
                                        @if ($errors->any() && !old('_method'))
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
                                                <label class="text-sm font-medium leading-none" for="create_room_id">Thuộc phòng</label>
                                                <select name="room_id" id="create_room_id" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                                    <option value="">-- Chọn phòng ở --</option>
                                                    @foreach($rooms as $room)
                                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                        {{ $room->building->name ?? 'N/A' }} - Phòng {{ $room->room_number }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Số giường -->
                                            <div class="space-y-2">
                                                <label class="text-sm font-medium leading-none" for="create_bed_number">Số giường / Tên giường</label>
                                                <input type="text" name="bed_number" id="create_bed_number" value="{{ old('bed_number') }}" required placeholder="Ví dụ: G1, G2, 01, 02..." class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                                            </div>

                                            <!-- Trạng thái -->
                                            <div class="space-y-2">
                                                <label class="text-sm font-medium leading-none" for="create_status">Trạng thái giường</label>
                                                <select name="status" id="create_status" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Trống (Available)</option>
                                                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Đang ở (Occupied)</option>
                                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì (Maintenance)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Footer -->
                                    <div class="border-t border-border px-6 py-4 bg-muted/30 flex justify-end gap-3">
                                        <button type="button" @click="showCreatePanel = false" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                            Hủy
                                        </button>
                                        <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                                            <i data-lucide="save" class="mr-2 h-4 w-4"></i> Lưu lại
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- ========================================== -->
    <!-- SLIDE-OVER EDIT BED                        -->
    <!-- ========================================== -->
    <template x-teleport="body">
        <div x-show="showEditPanel" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <!-- Background backdrop -->
            <div x-show="showEditPanel"
                 x-transition:enter="ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-sm"
                 @click="showEditPanel = false"></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <div x-show="showEditPanel"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             class="pointer-events-auto w-screen max-w-md">
                            
                            <!-- Panel content -->
                            <div class="flex h-full flex-col overflow-y-auto bg-background shadow-xl border-l border-border">
                                <div class="px-6 py-6 border-b border-border bg-muted/30">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h2 class="text-lg font-semibold leading-none tracking-tight">Sửa giường</h2>
                                            <p class="text-sm text-muted-foreground mt-2">Cập nhật thông tin chi tiết của giường ở này.</p>
                                        </div>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button @click="showEditPanel = false" type="button" class="relative rounded-md bg-background text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                                                <i data-lucide="x" class="h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <form :action="'/beds/' + selectedBed.id" method="POST" class="flex-1 flex flex-col">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="bed_id" :value="selectedBed.id">
                                    <div class="flex-1 px-6 py-6 space-y-6">
                                        <!-- Validation Errors -->
                                        @if ($errors->any() && old('_method') === 'PUT')
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
                                                <label class="text-sm font-medium leading-none" for="edit_room_id">Thuộc phòng</label>
                                                <select name="room_id" id="edit_room_id" required x-model="selectedBed.room_id" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                                    @foreach($rooms as $room)
                                                    <option value="{{ $room->id }}">
                                                        {{ $room->building->name ?? 'N/A' }} - Phòng {{ $room->room_number }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Số giường -->
                                            <div class="space-y-2">
                                                <label class="text-sm font-medium leading-none" for="edit_bed_number">Số giường / Tên giường</label>
                                                <input type="text" name="bed_number" id="edit_bed_number" x-model="selectedBed.bed_number" required placeholder="Ví dụ: G1, G2, 01, 02..." class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                                            </div>

                                            <!-- Trạng thái -->
                                            <div class="space-y-2">
                                                <label class="text-sm font-medium leading-none" for="edit_status">Trạng thái giường</label>
                                                <select name="status" id="edit_status" required x-model="selectedBed.status" class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-ring">
                                                    <option value="available">Trống (Available)</option>
                                                    <option value="occupied">Đang ở (Occupied)</option>
                                                    <option value="maintenance">Bảo trì (Maintenance)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Footer -->
                                    <div class="border-t border-border px-6 py-4 bg-muted/30 flex justify-end gap-3">
                                        <button type="button" @click="showEditPanel = false" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                            Hủy
                                        </button>
                                        <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                                            <i data-lucide="save" class="mr-2 h-4 w-4"></i> Cập nhật
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- ========================================== -->
    <!-- CUSTOM DELETE CONFIRMATION MODAL           -->
    <!-- ========================================== -->
    <template x-teleport="body">
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-sm"
                 @click="showDeleteModal = false"></div>

            <!-- Modal Content Wrapper -->
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-background border border-border shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive sm:mx-0 sm:h-10 sm:w-10">
                        <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-foreground" id="modal-title">Xác nhận xóa</h3>
                        <div class="mt-2">
                            <p class="text-sm text-muted-foreground" x-text="deleteConfirmMessage"></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                    <button type="button" @click="showDeleteModal = false" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Hủy bỏ
                    </button>
                    <form :action="deleteActionUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-destructive text-destructive-foreground shadow hover:bg-destructive/90 h-9 px-4 py-2">
                            Xác nhận xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:initialized', () => {
        Alpine.effect(() => {
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 50);
        });
    });
</script>
@endsection
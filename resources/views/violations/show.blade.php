@extends('layouts.app')

@section('title', 'Chi tiết Biên bản Vi phạm')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Chi tiết Biên bản Vi phạm #{{ $record->id }}</h2>
            <p class="text-sm text-muted-foreground mt-1">Thông tin chi tiết về sự việc và hình thức xử lý.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('violation.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i> Quay lại
            </a>
            
            @if($record->status->value == 'pending' || $record->status == 'pending')
            <form action="{{ route('violation.resolve', $record->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn đánh dấu biên bản này là Đã giải quyết?')" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-emerald-600 text-white shadow hover:bg-emerald-700 h-9 px-4 py-2">
                    <i data-lucide="check-circle" class="mr-2 h-4 w-4"></i> Đánh dấu đã xử lý
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="md:col-span-2 space-y-6">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                <div class="p-6 border-b border-border">
                    <h3 class="font-semibold leading-none tracking-tight">Thông tin Vi phạm</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Loại vi phạm</p>
                            <p class="mt-1 text-base font-medium">{{ $record->violationType->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Mức độ</p>
                            <p class="mt-1">
                                @if($record->violationType->severity == 'high')
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-destructive/10 text-destructive">Nặng</span>
                                @elseif($record->violationType->severity == 'medium')
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800">Trung bình</span>
                                @else
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-primary/10 text-primary">Nhẹ</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Ngày ghi nhận</p>
                            <p class="mt-1 text-base">{{ $record->record_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Tiền phạt</p>
                            <p class="mt-1 text-base font-semibold text-destructive">{{ number_format($record->violationType->fine_amount) }} VNĐ</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-muted-foreground">Mô tả chi tiết</p>
                        <div class="mt-2 rounded-md bg-muted p-4">
                            <p class="text-sm leading-relaxed">{{ $record->description ?: 'Không có mô tả chi tiết.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Student Info -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                <div class="p-6 border-b border-border">
                    <h3 class="font-semibold leading-none tracking-tight">Người vi phạm</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="h-10 w-10 rounded-full bg-muted flex items-center justify-center">
                            <i data-lucide="user" class="h-5 w-5 text-muted-foreground"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium leading-none">Sinh viên #{{ $record->student_id }}</p>
                            <p class="text-sm text-muted-foreground">{{ $record->student->full_name ?? 'Chưa cập nhật tên' }}</p>
                        </div>
                    </div>
                    <!-- Có thể thêm thông tin phòng ở đây nếu models có liên kết -->
                </div>
            </div>

            <!-- Status Info -->
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
                <div class="p-6 border-b border-border">
                    <h3 class="font-semibold leading-none tracking-tight">Trạng thái</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground mb-2">Trạng thái xử lý</p>
                        @if($record->status->value == 'resolved' || $record->status == 'resolved')
                        <div class="inline-flex items-center rounded-md border px-2.5 py-1 text-sm font-semibold transition-colors border-transparent bg-emerald-100 text-emerald-800">
                            <i data-lucide="check-circle-2" class="mr-2 h-4 w-4"></i> Đã giải quyết
                        </div>
                        @else
                        <div class="inline-flex items-center rounded-md border px-2.5 py-1 text-sm font-semibold transition-colors border-transparent bg-destructive/10 text-destructive">
                            <i data-lucide="clock" class="mr-2 h-4 w-4"></i> Chờ xử lý
                        </div>
                        @endif
                    </div>
                    
                    <div class="pt-4 border-t border-border">
                        <p class="text-sm font-medium text-muted-foreground">Người lập biên bản</p>
                        <p class="mt-1 text-sm">{{ $record->recorder->name ?? 'Hệ thống' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

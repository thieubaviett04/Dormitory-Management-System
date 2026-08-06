@extends('layouts.app')

@section('title', 'Nhật ký Vi phạm Kỷ luật')

@section('content')
<div class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-destructive">Nhật ký Vi phạm Kỷ luật</h2>
            <p class="text-sm text-muted-foreground mt-1">Theo dõi và quản lý các biên bản vi phạm nội quy Ký túc xá.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-destructive text-destructive-foreground shadow hover:bg-destructive/90 h-9 px-4 py-2">
                <i data-lucide="alert-circle" class="mr-2 h-4 w-4"></i> Lập biên bản mới
            </button>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid gap-4 md:grid-cols-3">
        <!-- Card 1 -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Tổng số biên bản</h3>
                <i data-lucide="clipboard-list" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Chờ xử lý</h3>
                <i data-lucide="clock" class="h-4 w-4 text-destructive"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-destructive">{{ $stats['pending'] }}</div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Đã giải quyết</h3>
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-emerald-600">{{ $stats['resolved'] }}</div>
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="rounded-md border bg-card shadow-sm overflow-hidden">
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Ngày ghi nhận</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Sinh viên</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Lỗi vi phạm</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Mức độ</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Mô tả chi tiết</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Tiền phạt</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @forelse($records as $record)
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <td class="p-4 align-middle text-muted-foreground">{{ $record->record_date->format('d/m/Y') }}</td>
                        <td class="p-4 align-middle font-medium">SV #{{ $record->student_id }}</td>
                        <td class="p-4 align-middle font-medium">{{ $record->violationType->name }}</td>
                        <td class="p-4 align-middle text-center">
                            @if($record->violationType->severity == 'high')
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold transition-colors border-transparent bg-destructive/10 text-destructive">
                                <i data-lucide="flame" class="mr-1 h-3 w-3"></i> Nặng
                            </div>
                            @elseif($record->violationType->severity == 'medium')
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold transition-colors border-transparent bg-amber-100 text-amber-800">
                                <i data-lucide="alert-triangle" class="mr-1 h-3 w-3"></i> Trung bình
                            </div>
                            @else
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-semibold transition-colors border-transparent bg-primary/10 text-primary">
                                <i data-lucide="info" class="mr-1 h-3 w-3"></i> Nhẹ
                            </div>
                            @endif
                        </td>
                        <td class="p-4 align-middle text-muted-foreground max-w-xs truncate" title="{{ $record->description }}">{{ $record->description }}</td>
                        <td class="p-4 align-middle text-right font-semibold text-destructive">{{ number_format($record->violationType->fine_amount) }} đ</td>
                        <td class="p-4 align-middle text-center">
                            @if($record->status == 'resolved')
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors border-transparent bg-emerald-100 text-emerald-800">
                                Đã giải quyết
                            </div>
                            @else
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors border-transparent bg-destructive/10 text-destructive">
                                Chờ xử lý
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-muted-foreground">
                            Chưa có biên bản vi phạm nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
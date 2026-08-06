@extends('layouts.app')

@section('title', 'Hợp đồng lưu trú')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold">Hợp đồng lưu trú</h2>
            <p class="mt-1 text-sm text-muted-foreground">Theo dõi hợp đồng và vị trí giường hiện tại của sinh viên.</p>
        </div>
        <a href="{{ route('contracts.create') }}" class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90">
            <i data-lucide="plus" class="mr-2 h-4 w-4"></i>Lập hợp đồng
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-md border border-emerald-500/50 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-md border bg-card shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b bg-muted/40 text-left text-muted-foreground">
                    <tr>
                        <th class="p-4 font-medium">Mã hợp đồng</th>
                        <th class="p-4 font-medium">Sinh viên</th>
                        <th class="p-4 font-medium">Vị trí hiện tại</th>
                        <th class="p-4 font-medium">Thời hạn</th>
                        <th class="p-4 font-medium">Trạng thái</th>
                        <th class="p-4 text-right font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        @php($allocation = $contract->currentAllocation)
                        <tr class="border-b last:border-0 hover:bg-muted/30">
                            <td class="p-4 font-semibold text-primary">{{ $contract->contract_code }}</td>
                            <td class="p-4">
                                <div class="font-medium">{{ $contract->student->full_name }}</div>
                                <div class="text-xs text-muted-foreground">{{ $contract->student->student_code }}</div>
                            </td>
                            <td class="p-4">
                                @if($allocation)
                                    {{ $allocation->bed->room->building->code }} / Phòng {{ $allocation->bed->room->room_number }} / {{ $allocation->bed->bed_number }}
                                @else
                                    <span class="text-muted-foreground">Đã giải phóng</span>
                                @endif
                            </td>
                            <td class="p-4">{{ $contract->start_date->format('d/m/Y') }} – {{ $contract->end_date->format('d/m/Y') }}</td>
                            <td class="p-4">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $contract->status->value === 'active' ? 'bg-emerald-100 text-emerald-700' : ($contract->status->value === 'expired' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ['active' => 'Đang hiệu lực', 'expired' => 'Hết hạn', 'terminated' => 'Đã thanh lý'][$contract->status->value] }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('contracts.show', $contract) }}" class="inline-flex h-8 items-center rounded-md border px-3 font-medium hover:bg-accent">Chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-muted-foreground">Chưa có hợp đồng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

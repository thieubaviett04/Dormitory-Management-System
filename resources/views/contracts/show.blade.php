@extends('layouts.app')

@section('title', $contract->contract_code)

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <a href="{{ route('contracts.index') }}" class="text-sm text-muted-foreground hover:text-foreground">← Danh sách hợp đồng</a>
            <h2 class="mt-3 text-2xl font-semibold">{{ $contract->contract_code }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">{{ $contract->student->full_name }} · {{ $contract->student->student_code }}</p>
        </div>
        <span class="rounded-full px-3 py-1 text-sm font-medium {{ $contract->status->value === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
            {{ ['active' => 'Đang hiệu lực', 'expired' => 'Hết hạn', 'terminated' => 'Đã thanh lý'][$contract->status->value] }}
        </span>
    </div>

    @if(session('success'))<div class="rounded-md border border-emerald-500/50 bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="rounded-md border border-destructive/50 bg-destructive/10 p-4 text-sm text-destructive"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="grid gap-5 md:grid-cols-3">
        <div class="rounded-xl border bg-card p-5 shadow-sm"><div class="text-xs uppercase text-muted-foreground">Thời hạn</div><div class="mt-2 font-semibold">{{ $contract->start_date->format('d/m/Y') }} – {{ $contract->end_date->format('d/m/Y') }}</div></div>
        <div class="rounded-xl border bg-card p-5 shadow-sm"><div class="text-xs uppercase text-muted-foreground">Đơn giá/tháng</div><div class="mt-2 font-semibold">{{ number_format((float) $contract->monthly_rate, 0, ',', '.') }} ₫</div></div>
        <div class="rounded-xl border bg-card p-5 shadow-sm"><div class="text-xs uppercase text-muted-foreground">Vị trí hiện tại</div><div class="mt-2 font-semibold">@if($contract->currentAllocation){{ $contract->currentAllocation->bed->room->building->code }} / {{ $contract->currentAllocation->bed->room->room_number }} / {{ $contract->currentAllocation->bed->bed_number }}@else Đã giải phóng @endif</div></div>
    </div>

    @if($contract->status->value === 'active')
        <div class="grid gap-5 lg:grid-cols-3">
            <form action="{{ route('contracts.transfer', $contract) }}" method="POST" class="space-y-4 rounded-xl border bg-card p-5 shadow-sm">
                @csrf
                <h3 class="font-semibold">Chuyển phòng</h3>
                <select name="bed_id" required class="h-9 w-full rounded-md border bg-background px-3 text-sm">
                    <option value="">Chọn giường mới</option>
                    @foreach($availableBeds as $bed)<option value="{{ $bed->id }}">{{ $bed->room->building->code }} / {{ $bed->room->room_number }} / {{ $bed->bed_number }}</option>@endforeach
                </select>
                <textarea name="reason" required maxlength="1000" class="min-h-20 w-full rounded-md border bg-background p-3 text-sm" placeholder="Lý do chuyển phòng"></textarea>
                <button class="h-9 rounded-md border px-4 text-sm font-medium hover:bg-accent">Xác nhận chuyển</button>
            </form>

            <form action="{{ route('contracts.renew', $contract) }}" method="POST" class="space-y-4 rounded-xl border bg-card p-5 shadow-sm">
                @csrf
                <h3 class="font-semibold">Gia hạn hợp đồng</h3>
                <input type="date" name="new_end_date" min="{{ $contract->end_date->copy()->addDay()->toDateString() }}" required class="h-9 w-full rounded-md border bg-background px-3 text-sm">
                <textarea name="reason" maxlength="1000" class="min-h-20 w-full rounded-md border bg-background p-3 text-sm" placeholder="Lý do hoặc ghi chú"></textarea>
                <button class="h-9 rounded-md border px-4 text-sm font-medium hover:bg-accent">Gia hạn</button>
            </form>

            <form action="{{ route('contracts.terminate', $contract) }}" method="POST" class="space-y-4 rounded-xl border border-destructive/30 bg-card p-5 shadow-sm">
                @csrf
                @method('PATCH')
                <h3 class="font-semibold text-destructive">Trả phòng / thanh lý</h3>
                <select name="release_reason" class="h-9 w-full rounded-md border bg-background px-3 text-sm"><option value="checked_out">Trả phòng</option><option value="contract_terminated">Thanh lý hành chính</option></select>
                <textarea name="reason" required maxlength="1000" class="min-h-20 w-full rounded-md border bg-background p-3 text-sm" placeholder="Lý do"></textarea>
                <button onclick="return confirm('Xác nhận giải phóng giường và kết thúc hợp đồng?')" class="h-9 rounded-md bg-destructive px-4 text-sm font-medium text-destructive-foreground">Kết thúc hợp đồng</button>
            </form>
        </div>
    @endif

    <div class="rounded-xl border bg-card p-5 shadow-sm">
        <h3 class="mb-4 font-semibold">Lịch sử phân giường</h3>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="border-b text-left text-muted-foreground"><tr><th class="p-3">Vị trí</th><th class="p-3">Nhận giường</th><th class="p-3">Kết thúc</th><th class="p-3">Lý do</th></tr></thead><tbody>
            @foreach($contract->allocations as $allocation)<tr class="border-b last:border-0"><td class="p-3">{{ $allocation->bed->room->building->code }} / {{ $allocation->bed->room->room_number }} / {{ $allocation->bed->bed_number }}</td><td class="p-3">{{ $allocation->allocated_at->format('d/m/Y H:i') }}</td><td class="p-3">{{ $allocation->released_at?->format('d/m/Y H:i') ?? 'Hiện tại' }}</td><td class="p-3">{{ $allocation->release_notes ?? '—' }}</td></tr>@endforeach
        </tbody></table></div>
    </div>

    @if($contract->renewals->isNotEmpty())
        <div class="rounded-xl border bg-card p-5 shadow-sm"><h3 class="mb-4 font-semibold">Lịch sử gia hạn</h3><div class="space-y-2 text-sm">@foreach($contract->renewals as $renewal)<div class="rounded-md bg-muted/40 p-3">{{ $renewal->previous_end_date->format('d/m/Y') }} → <strong>{{ $renewal->new_end_date->format('d/m/Y') }}</strong> · {{ $renewal->reason ?: 'Không có ghi chú' }}</div>@endforeach</div></div>
    @endif
</div>
@endsection

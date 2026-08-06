@extends('layouts.app')

@section('title', 'Lập hợp đồng')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div>
        <a href="{{ route('contracts.index') }}" class="text-sm text-muted-foreground hover:text-foreground">← Danh sách hợp đồng</a>
        <h2 class="mt-3 text-2xl font-semibold">Đơn đủ điều kiện lập hợp đồng</h2>
        <p class="mt-1 text-sm text-muted-foreground">Chỉ hiển thị đơn đã duyệt, chưa được sử dụng để lập hợp đồng.</p>
    </div>

    @if($errors->any())
        <div class="rounded-md border border-destructive/50 bg-destructive/10 p-4 text-sm text-destructive">
            <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-5">
        @forelse($registrations as $registration)
            <form action="{{ route('contracts.store') }}" method="POST" class="rounded-xl border bg-card p-6 shadow-sm">
                @csrf
                <input type="hidden" name="room_registration_id" value="{{ $registration->id }}">

                <div class="grid gap-6 lg:grid-cols-[1fr_1.5fr]">
                    <div>
                        <div class="text-lg font-semibold">{{ $registration->student->full_name }}</div>
                        <div class="mt-1 text-sm text-muted-foreground">{{ $registration->student->student_code }} · {{ $registration->student->email }}</div>
                        <div class="mt-4 rounded-md bg-muted/50 p-3 text-sm">
                            Nguyện vọng: <strong>{{ $registration->room->building->code }} / Phòng {{ $registration->room->room_number }}</strong>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2 sm:col-span-2">
                            <label class="text-sm font-medium">Giường</label>
                            <select name="bed_id" required class="h-9 w-full rounded-md border bg-background px-3 text-sm">
                                <option value="">Chọn giường trống</option>
                                @foreach($registration->room->beds as $bed)
                                    <option value="{{ $bed->id }}">{{ $bed->bed_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Ngày bắt đầu</label>
                            <input type="date" name="start_date" value="{{ today()->toDateString() }}" max="{{ today()->toDateString() }}" required class="h-9 w-full rounded-md border bg-background px-3 text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Ngày kết thúc</label>
                            <input type="date" name="end_date" value="{{ today()->addMonths(5)->toDateString() }}" required class="h-9 w-full rounded-md border bg-background px-3 text-sm">
                        </div>
                        <div class="space-y-2 sm:col-span-2">
                            <label class="text-sm font-medium">Đơn giá mỗi tháng</label>
                            <input type="number" name="monthly_rate" min="0" step="1000" required class="h-9 w-full rounded-md border bg-background px-3 text-sm" placeholder="Ví dụ: 1200000">
                        </div>
                        <div class="sm:col-span-2 text-right">
                            <button type="submit" {{ $registration->room->beds->isEmpty() ? 'disabled' : '' }} class="inline-flex h-9 items-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground disabled:cursor-not-allowed disabled:opacity-50">
                                Tạo hợp đồng & phân giường
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @empty
            <div class="rounded-md border bg-card p-10 text-center text-muted-foreground">Không có đơn đã duyệt nào đang chờ lập hợp đồng.</div>
        @endforelse
    </div>
</div>
@endsection

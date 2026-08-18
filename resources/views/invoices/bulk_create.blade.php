@extends('layouts.app')

@section('title', 'Tạo hóa đơn đồng loạt')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Nhập số điện/nước đồng loạt</h2>
            <form id="month-form" method="GET" action="{{ route('invoice.bulk.create') }}" class="mt-2 flex items-center gap-2">
                <label class="text-sm font-medium text-muted-foreground">Kỳ chốt (Tháng/Năm):</label>
                <div x-data="{
                    open: false,
                    value: '{{ $month }}',
                    year: parseInt('{{ $month }}'.split('-')[0]),
                    month: parseInt('{{ $month }}'.split('-')[1]),
                    minYear: parseInt('{{ date('Y') }}'),
                    minMonth: parseInt('{{ date('m') }}'),
                    months: ['Thg 1', 'Thg 2', 'Thg 3', 'Thg 4', 'Thg 5', 'Thg 6', 'Thg 7', 'Thg 8', 'Thg 9', 'Thg 10', 'Thg 11', 'Thg 12'],
                    get formattedValue() {
                        return this.months[this.month - 1] + ', ' + this.year;
                    },
                    isMonthDisabled(index) {
                        if (this.year < this.minYear) return true;
                        if (this.year === this.minYear && (index + 1) < this.minMonth) return true;
                        return false;
                    },
                    selectMonth(index) {
                        if (this.isMonthDisabled(index)) return;
                        this.month = index + 1;
                        this.value = this.year + '-' + String(this.month).padStart(2, '0');
                        this.open = false;
                        $nextTick(() => { document.getElementById('month-form').submit(); });
                    }
                }" class="relative w-48" @click.away="open = false">
                    <input type="hidden" name="month" x-model="value">

                    <button type="button" @click="open = !open"
                        class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-1.5 text-sm font-medium text-foreground shadow-sm transition-colors hover:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                        <span x-text="formattedValue"></span>
                        <i data-lucide="calendar-days" class="h-4 w-4 opacity-50 transition-colors" :class="open ? 'text-primary opacity-100' : ''"></i>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-50 mt-1 w-[280px] rounded-md border border-border bg-popover p-3 text-popover-foreground shadow-md outline-none" style="display: none;">

                        <div class="flex items-center justify-between pt-1 pb-4">
                            <button type="button" @click="year--" class="h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent hover:text-accent-foreground transition-colors" :disabled="year <= minYear" :class="year <= minYear ? 'cursor-not-allowed opacity-20' : ''">
                                <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            </button>
                            <div class="text-sm font-medium" x-text="year"></div>
                            <button type="button" @click="year++" class="h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent hover:text-accent-foreground transition-colors">
                                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="(monthName, index) in months" :key="index">
                                <button type="button"
                                    @click="selectMonth(index)"
                                    :disabled="isMonthDisabled(index)"
                                    class="inline-flex h-9 items-center justify-center rounded-md text-sm transition-colors focus:outline-none"
                                    :class="[
                                        isMonthDisabled(index) ? 'opacity-30 cursor-not-allowed bg-transparent text-foreground' : 'hover:bg-accent hover:text-accent-foreground cursor-pointer',
                                        (!isMonthDisabled(index) && month === index + 1) ? 'bg-primary text-primary-foreground shadow hover:bg-primary hover:text-primary-foreground font-medium' : 'bg-transparent text-foreground'
                                    ]"
                                    x-text="monthName">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <a href="{{ route('invoice.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
            <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
    <div class="relative w-full rounded-lg border border-red-500/50 bg-red-50/50 p-4 text-red-600">
        <i data-lucide="alert-circle" class="absolute left-4 top-4 h-4 w-4"></i>
        <h5 class="mb-1 leading-none font-medium pl-7">Lỗi</h5>
        <div class="text-sm [&_p]:leading-relaxed pl-7">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="rounded-md border bg-card shadow-sm overflow-hidden" x-data="bulkForm()">
        <form action="{{ route('invoice.bulk.store') }}" method="POST" @submit="isSubmitting = true">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">

            <div class="p-4 border-b bg-muted/30 flex justify-between items-center">
                <p class="text-sm text-muted-foreground">Mẹo: Bạn có thể nhập nhanh từ trên xuống dưới. Các phòng bỏ trống sẽ bị bỏ qua.</p>
                <button type="submit" :disabled="isSubmitting" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                    <span x-show="!isSubmitting"><i data-lucide="save" class="mr-2 h-4 w-4 inline-block"></i> Lưu Hóa Đơn Đồng Loạt</span>
                    <span x-show="isSubmitting"><i data-lucide="loader-2" class="mr-2 h-4 w-4 inline-block animate-spin"></i> Đang xử lý...</span>
                </button>
            </div>

            <div class="w-full overflow-auto max-h-[60vh]">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b sticky top-0 bg-card z-10 shadow-sm">
                        <tr class="border-b transition-colors hover:bg-muted/50">
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground w-[150px]">Tòa nhà</th>
                            <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground w-[150px]">Phòng</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-blue-600 bg-blue-50/50">Điện số cũ</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-blue-600 bg-blue-50/50">Điện số mới</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-emerald-600 bg-emerald-50/50">Nước số cũ</th>
                            <th class="h-10 px-4 text-center align-middle font-medium text-emerald-600 bg-emerald-50/50">Nước số mới</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        @foreach($rooms as $room)
                        @php
                            $curr = $currentReadings->get($room->id);
                            $prev = $prevReadings->get($room->id);
                            
                            // Nếu đã nhập tháng này rồi, lấy số đầu/cuối của tháng này
                            // Nếu chưa, lấy số đầu = số cuối tháng trước
                            $eStart = $curr ? $curr->electricity_start : ($prev ? $prev->electricity_end : 0);
                            $wStart = $curr ? $curr->water_start : ($prev ? $prev->water_end : 0);
                            
                            $eEnd = $curr ? $curr->electricity_end : '';
                            $wEnd = $curr ? $curr->water_end : '';
                        @endphp
                        <tr class="border-b transition-colors hover:bg-muted/20">
                            <td class="p-2 px-4 align-middle font-medium">{{ $room->building->name }}</td>
                            <td class="p-2 px-4 align-middle">{{ $room->room_number }}</td>
                            
                            <!-- Điện -->
                            <td class="p-2 px-4 align-middle bg-blue-50/30">
                                <input type="number" name="readings[{{ $room->id }}][electricity_start]" value="{{ $eStart }}" class="flex h-9 w-24 mx-auto rounded-md border border-input bg-background/50 px-3 py-1 text-sm text-center shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" readonly tabindex="-1">
                            </td>
                            <td class="p-2 px-4 align-middle bg-blue-50/30">
                                <input type="number" name="readings[{{ $room->id }}][electricity_end]" value="{{ $eEnd }}" min="{{ $eStart }}" placeholder="0" class="flex h-9 w-28 mx-auto rounded-md border-2 border-blue-200 bg-background px-3 py-1 text-sm text-center shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 font-bold text-blue-700">
                            </td>

                            <!-- Nước -->
                            <td class="p-2 px-4 align-middle bg-emerald-50/30">
                                <input type="number" name="readings[{{ $room->id }}][water_start]" value="{{ $wStart }}" class="flex h-9 w-24 mx-auto rounded-md border border-input bg-background/50 px-3 py-1 text-sm text-center shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" readonly tabindex="-1">
                            </td>
                            <td class="p-2 px-4 align-middle bg-emerald-50/30">
                                <input type="number" name="readings[{{ $room->id }}][water_end]" value="{{ $wEnd }}" min="{{ $wStart }}" placeholder="0" class="flex h-9 w-28 mx-auto rounded-md border-2 border-emerald-200 bg-background px-3 py-1 text-sm text-center shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 font-bold text-emerald-700">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    function bulkForm() {
        return {
            isSubmitting: false
        }
    }
</script>
@endsection

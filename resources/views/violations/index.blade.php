@extends('layouts.app')

@section('title', 'Nhật ký Vi phạm Kỷ luật')

@section('content')
<div x-data="violationManager()" class="space-y-6">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-destructive">Nhật ký Vi phạm Kỷ luật</h2>
            <p class="text-sm text-muted-foreground mt-1">Theo dõi và quản lý các biên bản vi phạm nội quy Ký túc xá.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <button @click="showCreatePanel = true" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-destructive text-destructive-foreground shadow hover:bg-destructive/90 h-9 px-4 py-2">
                <i data-lucide="alert-circle" class="mr-2 h-4 w-4"></i> Lập biên bản mới
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-emerald-50 p-4 border border-emerald-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="check-circle" class="h-5 w-5 text-emerald-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

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
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Mô tả chi tiết</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Tiền phạt</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Trạng thái</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @forelse($records as $record)
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <td class="p-4 align-middle text-muted-foreground">{{ $record->record_date->format('d/m/Y') }}</td>
                        <td class="p-4 align-middle font-medium">{{ $record->student->full_name ?? 'Chưa cập nhật tên' }}</td>
                        <td class="p-4 align-middle font-medium">{{ $record->violationType->name }}</td>
                        <td class="p-4 align-middle text-muted-foreground max-w-xs truncate" title="{{ $record->description }}">{{ $record->description }}</td>
                        <td class="p-4 align-middle text-right font-semibold text-destructive">{{ number_format($record->violationType->fine_amount) }} đ</td>
                        <td class="p-4 align-middle text-center">
                            @if($record->status->value == 'resolved' || $record->status == 'resolved')
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors border-transparent bg-emerald-100 text-emerald-800">
                                Đã giải quyết
                            </div>
                            @else
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors border-transparent bg-destructive/10 text-destructive">
                                Chờ xử lý
                            </div>
                            @endif
                        </td>
                        <td class="p-4 align-middle text-right">
                            <button @click="openDetailPanel({{ $record->toJson() }})" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-muted h-8 w-8 text-muted-foreground hover:text-foreground">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-muted-foreground">
                            Chưa có biên bản vi phạm nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE PANEL -->
    <template x-teleport="body">
    <div x-show="showCreatePanel" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="showCreatePanel" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showCreatePanel = false"></div>
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="showCreatePanel" 
                     x-transition:enter="transform transition ease-in-out duration-300" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-300" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="pointer-events-auto w-screen max-w-md">
                    
                    <div class="flex h-full flex-col bg-background shadow-xl border-l border-border">
                        <!-- Header -->
                        <div class="px-6 py-6 border-b border-border bg-muted/30">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold leading-none tracking-tight" id="slide-over-title">Lập Biên bản Vi phạm</h2>
                                    <p class="text-sm text-muted-foreground mt-2">Ghi nhận vi phạm kỷ luật mới đối với sinh viên.</p>
                                </div>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" class="relative rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 p-1.5 transition-colors focus:outline-none" @click="showCreatePanel = false; $dispatch('reset-create-form')">
                                        <i data-lucide="x" class="h-5 w-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <form action="{{ route('violation.store') }}" method="POST" class="flex-1 flex flex-col overflow-hidden"
                            x-data="{
                                clientErrors: [],
                                showServerErrors: {{ $errors->any() ? 'true' : 'false' }},
                                validateAndSubmit(e) {
                                    this.clientErrors = [];
                                    const studentId = this.$el.querySelector('[name=student_id]').value;
                                    const typeId = this.$el.querySelector('[name=violation_type_id]').value;
                                    const recordDate = this.$el.querySelector('[name=record_date]').value;
                                    const desc = this.$el.querySelector('[name=description]').value;
                                    
                                    const errors = [];
                                    if (!studentId) errors.push('Vui lòng chọn sinh viên vi phạm.');
                                    if (!typeId) errors.push('Vui lòng chọn lỗi vi phạm.');
                                    if (!recordDate) errors.push('Vui lòng chọn ngày vi phạm.');
                                    if (recordDate && new Date(recordDate) > new Date()) errors.push('Ngày vi phạm không thể là ngày trong tương lai.');
                                    if (desc.length > 1000) errors.push('Mô tả không được vượt quá 1000 ký tự.');
                                    
                                    if (errors.length > 0) {
                                        this.clientErrors = errors;
                                        e.preventDefault();
                                    }
                                }
                            }"
                            @submit="validateAndSubmit($event)"
                            @reset-create-form.window="clientErrors = []; showServerErrors = false; $el.reset()">
                            @csrf
                            
                            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">
                                <!-- Errors Display -->
                                @if ($errors->any())
                                <div x-show="showServerErrors" class="p-3 rounded-md bg-red-50 text-red-600 text-sm border border-red-200">
                                    <div class="flex items-center font-medium mb-1">
                                        <i data-lucide="alert-circle" class="h-4 w-4 mr-2"></i> Lỗi nhập liệu
                                    </div>
                                    <ul class="list-disc pl-5 space-y-1 text-xs">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div x-show="clientErrors.length > 0" class="p-3 rounded-md bg-red-50 text-red-600 text-sm border border-red-200" style="display:none">
                                    <div class="flex items-center font-medium mb-1">
                                        <i data-lucide="alert-circle" class="h-4 w-4 mr-2"></i> Lỗi nhập liệu
                                    </div>
                                    <ul class="list-disc pl-5 space-y-1 text-xs">
                                        <template x-for="err in clientErrors" :key="err">
                                            <li x-text="err"></li>
                                        </template>
                                    </ul>
                                </div>

                                <div class="space-y-4">
                                    <!-- Sinh viên -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium leading-none">Sinh viên vi phạm <span class="text-destructive">*</span></label>
                                        <div x-data="{ open: false, search: '', selected: '{{ old('student_id') }}', selectedText: '-- Vui lòng chọn sinh viên --' }" x-init="
                                            @foreach($students as $student)
                                                if (selected == '{{ $student->id }}') selectedText = 'SV #{{ $student->id }} - {{ $student->full_name ?? 'Chưa cập nhật tên' }}';
                                            @endforeach
                                        " class="relative w-full">
                                            <input type="hidden" name="student_id" x-model="selected">
                                            <button type="button" @click="open = !open" @click.away="open = false"
                                                class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all hover:border-primary/50">
                                                <span x-text="selectedText" :class="selected === '' ? 'text-muted-foreground' : 'text-foreground'"></span>
                                                <i data-lucide="chevron-down" class="h-4 w-4 opacity-50 transition-transform duration-200" :class="open ? 'rotate-180 opacity-100' : ''"></i>
                                            </button>

                                            <div x-show="open"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute z-50 mt-1 max-h-60 w-full overflow-hidden flex flex-col rounded-md border border-border bg-popover text-popover-foreground shadow-md outline-none" style="display: none;">
                                                <div class="flex items-center border-b border-border px-3">
                                                    <i data-lucide="search" class="mr-2 h-4 w-4 shrink-0 opacity-50"></i>
                                                    <input type="text" x-model="search" class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50" placeholder="Tìm sinh viên...">
                                                </div>
                                                <div class="overflow-y-auto p-1 max-h-[180px]">
                                                    @foreach ($students as $student)
                                                    <div x-show="search === '' || 'SV #{{ $student->id }} - {{ mb_strtolower($student->full_name ?? 'Chưa cập nhật tên', 'UTF-8') }}'.toLowerCase().includes(search.toLowerCase())"
                                                        @click="selected = '{{ $student->id }}'; selectedText = 'SV #{{ $student->id }} - {{ $student->full_name ?? 'Chưa cập nhật tên' }}'; open = false; search = ''"
                                                        class="relative flex w-full cursor-pointer select-none items-center rounded-sm py-2 pl-8 pr-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground transition-colors"
                                                        :class="selected == '{{ $student->id }}' ? 'bg-accent/50 text-accent-foreground font-medium' : ''">
                                                        <span x-show="selected == '{{ $student->id }}'" class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
                                                            <i data-lucide="check" class="h-4 w-4 text-primary"></i>
                                                        </span>
                                                        SV #{{ $student->id }} - {{ $student->full_name ?? 'Chưa cập nhật tên' }}
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lỗi vi phạm -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium leading-none">Loại vi phạm <span class="text-destructive">*</span></label>
                                        <div x-data="{ open: false, search: '', selected: '{{ old('violation_type_id') }}', selectedText: '-- Vui lòng chọn loại vi phạm --' }" x-init="
                                            @foreach($violationTypes as $type)
                                                if (selected == '{{ $type->id }}') selectedText = '{{ $type->name }} (Phạt: {{ number_format($type->fine_amount) }}đ)';
                                            @endforeach
                                        " class="relative w-full">
                                            <input type="hidden" name="violation_type_id" x-model="selected">
                                            <button type="button" @click="open = !open" @click.away="open = false"
                                                class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all hover:border-primary/50">
                                                <span x-text="selectedText" :class="selected === '' ? 'text-muted-foreground' : 'text-foreground'"></span>
                                                <i data-lucide="chevron-down" class="h-4 w-4 opacity-50 transition-transform duration-200" :class="open ? 'rotate-180 opacity-100' : ''"></i>
                                            </button>

                                            <div x-show="open"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute z-50 mt-1 max-h-60 w-full overflow-hidden flex flex-col rounded-md border border-border bg-popover text-popover-foreground shadow-md outline-none" style="display: none;">
                                                <div class="flex items-center border-b border-border px-3">
                                                    <i data-lucide="search" class="mr-2 h-4 w-4 shrink-0 opacity-50"></i>
                                                    <input type="text" x-model="search" class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50" placeholder="Tìm loại vi phạm...">
                                                </div>
                                                <div class="overflow-y-auto p-1 max-h-[180px]">
                                                    @foreach ($violationTypes as $type)
                                                    <div x-show="search === '' || '{{ mb_strtolower($type->name, 'UTF-8') }}'.toLowerCase().includes(search.toLowerCase())"
                                                        @click="selected = '{{ $type->id }}'; selectedText = '{{ $type->name }} (Phạt: {{ number_format($type->fine_amount) }}đ)'; open = false; search = ''"
                                                        class="relative flex w-full cursor-pointer select-none items-center rounded-sm py-2 pl-8 pr-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground transition-colors"
                                                        :class="selected == '{{ $type->id }}' ? 'bg-accent/50 text-accent-foreground font-medium' : ''">
                                                        <span x-show="selected == '{{ $type->id }}'" class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
                                                            <i data-lucide="check" class="h-4 w-4 text-primary"></i>
                                                        </span>
                                                        {{ $type->name }} (Phạt: {{ number_format($type->fine_amount) }}đ)
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ngày vi phạm -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium leading-none">Ngày vi phạm <span class="text-destructive">*</span></label>
                                        <div x-data="{
                                            open: false,
                                            value: '{{ old('record_date', date('Y-m-d')) }}',
                                            month: new Date('{{ old('record_date', date('Y-m-d')) }}').getMonth(),
                                            year: new Date('{{ old('record_date', date('Y-m-d')) }}').getFullYear(),
                                            days: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                                            months: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                                            get formattedValue() {
                                                if (!this.value) return '-- Chọn ngày --';
                                                const d = new Date(this.value);
                                                return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
                                            },
                                            get blankDays() {
                                                let days = [];
                                                let firstDay = new Date(this.year, this.month, 1).getDay();
                                                for (let i = 0; i < firstDay; i++) {
                                                    days.push(i);
                                                }
                                                return days;
                                            },
                                            get monthDays() {
                                                let days = [];
                                                let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                                                for (let i = 1; i <= daysInMonth; i++) {
                                                    days.push(i);
                                                }
                                                return days;
                                            },
                                            isSelectedDate(day) {
                                                if (!this.value) return false;
                                                const d = new Date(this.value);
                                                return d.getDate() === day && d.getMonth() === this.month && d.getFullYear() === this.year;
                                            },
                                            selectDate(day) {
                                                this.value = this.year + '-' + ('0' + (this.month + 1)).slice(-2) + '-' + ('0' + day).slice(-2);
                                                this.open = false;
                                            }
                                        }" class="relative w-full" @click.away="open = false">
                                            <input type="hidden" name="record_date" x-model="value">
                                            <button type="button" @click="open = !open"
                                                class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all hover:border-primary/50">
                                                <span x-text="formattedValue" :class="value === '' ? 'text-muted-foreground' : 'text-foreground'"></span>
                                                <i data-lucide="calendar-days" class="h-4 w-4 opacity-50 transition-transform duration-200" :class="open ? 'text-primary' : ''"></i>
                                            </button>

                                            <div x-show="open"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute z-50 mt-1 p-3 w-64 rounded-md border border-border bg-popover text-popover-foreground shadow-md outline-none" style="display: none;">
                                                <div class="flex items-center justify-between pb-3">
                                                    <button type="button" @click="month--; if(month < 0) { month = 11; year--; }" class="h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent hover:text-accent-foreground transition-colors">
                                                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                                    </button>
                                                    <div class="text-sm font-medium" x-text="months[month] + ', ' + year"></div>
                                                    <button type="button" @click="month++; if(month > 11) { month = 0; year++; }" class="h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 inline-flex items-center justify-center rounded-md border border-border hover:bg-accent hover:text-accent-foreground transition-colors">
                                                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                                    </button>
                                                </div>
                                                
                                                <div class="grid grid-cols-7 gap-1 text-center mb-1">
                                                    <template x-for="day in days">
                                                        <div class="text-[10px] uppercase text-muted-foreground font-medium" x-text="day"></div>
                                                    </template>
                                                </div>
                                                
                                                <div class="grid grid-cols-7 gap-1">
                                                    <template x-for="blank in blankDays">
                                                        <div class="h-8 w-8"></div>
                                                    </template>
                                                    <template x-for="day in monthDays">
                                                        <button type="button" @click="selectDate(day)"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md text-sm transition-colors focus:outline-none hover:bg-accent hover:text-accent-foreground font-normal"
                                                            :class="isSelectedDate(day) ? 'bg-primary text-primary-foreground font-medium shadow hover:bg-primary hover:text-primary-foreground' : 'text-foreground'"
                                                            x-text="day">
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mô tả chi tiết -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium leading-none">Mô tả chi tiết sự việc</label>
                                        <textarea name="description" rows="4" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all hover:border-primary/50" placeholder="Nhập mô tả chi tiết...">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-border px-6 py-4 bg-muted/30 flex justify-end gap-3 mt-auto">
                                <button type="button" @click="showCreatePanel = false; $dispatch('reset-create-form')" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                    Hủy
                                </button>
                                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                                    Lưu biên bản
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </template>

    <!-- DETAIL PANEL -->
    <template x-teleport="body">
    <div x-show="showDetailPanel" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="showDetailPanel" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="showDetailPanel = false"></div>
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="showDetailPanel" 
                     x-transition:enter="transform transition ease-in-out duration-300" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-300" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="pointer-events-auto w-screen max-w-md">
                    
                    <div class="flex h-full flex-col overflow-y-scroll bg-card py-6 shadow-xl border-l border-border">
                        <div class="px-4 sm:px-6 flex items-start justify-between border-b border-border pb-4">
                            <h2 class="text-lg font-semibold leading-6 text-foreground" id="slide-over-title">Chi tiết Biên bản</h2>
                            <button type="button" class="relative rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 p-1.5 transition-colors focus:outline-none" @click="showDetailPanel = false">
                                <i data-lucide="x" class="h-5 w-5"></i>
                            </button>
                        </div>
                        
                        <div class="relative mt-6 flex-1 px-4 sm:px-6">
                            <template x-if="currentRecord">
                                <div class="space-y-6">
                                    <!-- Record Info -->
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-sm font-medium text-muted-foreground">Sinh viên vi phạm</p>
                                            <p class="mt-1 font-medium text-foreground" x-text="'SV #' + currentRecord.student_id + ' - ' + (currentRecord.student?.full_name || 'Chưa cập nhật tên')"></p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm font-medium text-muted-foreground">Loại vi phạm</p>
                                            <p class="mt-1 font-medium text-destructive" x-text="currentRecord.violation_type?.name"></p>
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-muted-foreground">Tiền phạt</p>
                                                <p class="mt-1 font-bold text-destructive">
                                                    <span x-text="new Intl.NumberFormat('vi-VN').format(currentRecord.violation_type?.fine_amount || 0)"></span> đ
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-muted-foreground">Trạng thái</p>
                                                <div class="mt-1">
                                                    <span x-show="currentRecord.status === 'resolved'" class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                        Đã giải quyết
                                                    </span>
                                                    <span x-show="currentRecord.status === 'pending'" class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-semibold bg-destructive/10 text-destructive">
                                                        Chờ xử lý
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium text-muted-foreground">Mô tả chi tiết</p>
                                            <div class="mt-2 rounded-md bg-muted p-3 text-sm">
                                                <p x-text="currentRecord.description || 'Không có mô tả chi tiết'"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Form Action (Only if pending) -->
                                    <div x-show="currentRecord.status === 'pending'" class="mt-8 pt-6 border-t border-border">
                                        <h3 class="text-sm font-medium text-foreground mb-4">Xử lý biên bản</h3>
                                        <form :action="'/violations/' + currentRecord.id + '/resolve'" method="POST" class="space-y-4">
                                            @csrf
                                            @method('PATCH')
                                            
                                            <div class="space-y-2">
                                                <label class="text-sm font-medium text-foreground">Phương thức thanh toán phạt</label>
                                                <div x-data="{ open: false, selected: 'cash', selectedText: 'Tiền mặt' }" class="relative w-full">
                                                    <input type="hidden" name="payment_method" x-model="selected">
                                                    <button type="button" @click="open = !open" @click.away="open = false"
                                                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all hover:border-primary/50">
                                                        <span x-text="selectedText" :class="selected === '' ? 'text-muted-foreground' : 'text-foreground'"></span>
                                                        <i data-lucide="chevron-down" class="h-4 w-4 opacity-50 transition-transform duration-200" :class="open ? 'rotate-180 opacity-100' : ''"></i>
                                                    </button>

                                                    <div x-show="open"
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md border border-border bg-popover text-popover-foreground shadow-md outline-none" style="display: none;">
                                                        <div class="p-1">
                                                            <template x-for="option in [
                                                                {value: 'cash', label: 'Tiền mặt'},
                                                                {value: 'bank_transfer', label: 'Chuyển khoản ngân hàng'},
                                                                {value: 'momo', label: 'Ví điện tử MoMo'},
                                                                {value: 'other', label: 'Khác'}
                                                            ]" :key="option.value">
                                                                <div @click="selected = option.value; selectedText = option.label; open = false"
                                                                    class="relative flex w-full cursor-pointer select-none items-center rounded-sm py-2 pl-8 pr-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground transition-colors"
                                                                    :class="selected == option.value ? 'bg-accent/50 text-accent-foreground font-medium' : ''">
                                                                    <span x-show="selected == option.value" class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
                                                                        <i data-lucide="check" class="h-4 w-4 text-primary"></i>
                                                                    </span>
                                                                    <span x-text="option.label"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-[11px] text-muted-foreground mt-1">Chọn phương thức mà sinh viên đã dùng để nộp phạt.</p>
                                            </div>

                                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 h-10 px-4 py-2 mt-4">
                                                <i data-lucide="check-circle" class="mr-2 h-4 w-4"></i> Xác nhận đã xử lý
                                            </button>
                                        </form>
                                    </div>
                                    
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>

<script>
    function violationManager() {
        return {
            showCreatePanel: false,
            showDetailPanel: false,
            currentRecord: null,
            
            openDetailPanel(record) {
                this.currentRecord = record;
                this.showDetailPanel = true;
                
                // Re-initialize icons inside the template if needed
                setTimeout(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                }, 100);
            }
        }
    }
</script>
@endsection
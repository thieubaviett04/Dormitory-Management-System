@extends('layouts.app')

@section('title', 'Quản lý Hóa đơn Điện & Nước')

@section('content')
<div class="space-y-6" x-data="{ 
    showCreatePanel: {{ $errors->any() ? 'true' : 'false' }},
    showDetailPanel: false, 
    selectedInvoice: null,
    invoicesData: {{ json_encode($invoices) }},
    openDetail(id) {
        this.selectedInvoice = this.invoicesData.find(i => i.id == id);
        this.showDetailPanel = true;
    },
    formatDate(dateString) {
        if(!dateString) return '—';
        const d = new Date(dateString);
        return d.toLocaleDateString('vi-VN', {day: '2-digit', month: '2-digit', year: 'numeric'}) + ' ' + d.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
    },
    formatMonth(dateString) {
        if(!dateString) return '—';
        const d = new Date(dateString);
        return 'Tháng ' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
    },
    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }
}">

    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Điện & Nước</h2>
            <p class="text-sm text-muted-foreground mt-1">Quản lý chỉ số và hóa đơn dịch vụ ký túc xá.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <!-- Form Lọc theo tháng -->
            <form method="GET" action="{{ route('invoice.index') }}" class="flex items-center space-x-2">
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                    <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" onchange="this.form.submit()" class="flex h-9 w-full rounded-md border border-input bg-transparent pl-9 pr-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring cursor-pointer">
                </div>
            </form>

            <button @click="showCreatePanel = true" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                <i data-lucide="plus" class="mr-2 h-4 w-4"></i> Tạo hóa đơn
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

    <!-- Stats Cards Grid -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Card 1 -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Tổng hóa đơn</h3>
                <i data-lucide="receipt" class="h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Đã thanh toán</h3>
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-emerald-600">{{ $stats['paid'] }}</div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Chưa thanh toán</h3>
                <i data-lucide="clock" class="h-4 w-4 text-amber-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-amber-500">{{ $stats['unpaid'] }}</div>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Doanh thu</h3>
                <i data-lucide="wallet" class="h-4 w-4 text-blue-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_revenue']) }} <span class="text-lg underline">đ</span></div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="rounded-md border bg-card shadow-sm overflow-hidden">
        <div class="relative w-full overflow-auto">
            <table class="w-full caption-bottom text-sm">
                <thead class="[&_tr]:border-b">
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Mã Hóa đơn</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Phòng</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Kỳ đóng tiền</th>
                        <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Tổng tiền</th>
                        <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Trạng thái</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Ngày thanh toán</th>
                        <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Phương thức</th>
                        <th class="h-10 px-4 align-middle font-medium text-muted-foreground"></th>
                    </tr>
                </thead>
                <tbody class="[&_tr:last-child]:border-0">
                    @forelse($invoices as $invoice)
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <td class="p-4 align-middle font-medium">{{ $invoice->invoice_code }}</td>
                        <td class="p-4 align-middle text-primary font-medium">Phòng {{ $invoice->room_id + 100 }}</td>
                        <td class="p-4 align-middle text-muted-foreground">Tháng {{ $invoice->billing_month->format('m/Y') }}</td>
                        <td class="p-4 align-middle text-right font-medium">{{ number_format($invoice->total_amount) }} đ</td>
                        <td class="p-4 align-middle text-center">
                            @if($invoice->status == 'paid')
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors border-transparent bg-emerald-100 text-emerald-800">
                                Đã thanh toán
                            </div>
                            @else
                            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors border-transparent bg-amber-100 text-amber-800">
                                Chưa thanh toán
                            </div>
                            @endif
                        </td>
                        <td class="p-4 align-middle text-muted-foreground">
                            {{ $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="p-4 align-middle text-muted-foreground">
                            @if($invoice->payment_method == 'bank_transfer')
                                Chuyển khoản
                            @elseif($invoice->payment_method == 'cash')
                                Tiền mặt
                            @else
                                —
                            @endif
                        </td>
                        <td class="p-4 align-middle text-right">
                            <button @click="openDetail({{ $invoice->id }})" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-muted-foreground">
                            Không có hóa đơn nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SLIDE-OVER CREATE INVOICE                  -->
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
                                            <h2 class="text-lg font-semibold leading-none tracking-tight">Ghi chỉ số & Tạo hóa đơn</h2>
                                            <p class="text-sm text-muted-foreground mt-2">Nhập chỉ số điện nước sử dụng để hệ thống tự sinh hóa đơn.</p>
                                        </div>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button @click="showCreatePanel = false" type="button" class="relative rounded-md bg-background text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                                                <i data-lucide="x" class="h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('invoice.store') }}" method="POST" class="flex-1 flex flex-col">
                                    @csrf
                                    <div class="flex-1 px-6 py-6 space-y-6">
                                        <!-- Hiển thị thông báo khi lỗi Validation -->
                                        @if ($errors->any())
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
                                            <div class="space-y-2">
                                                <label class="text-sm font-medium leading-none">Phòng áp dụng</label>
                                                <select name="room_id" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring">
                                                    <option value="">-- Chọn phòng --</option>
                                                    @for($i = 101; $i <= 103; $i++)
                                                        <option value="{{ $i - 100 }}" {{ old('room_id') == ($i - 100) ? 'selected' : '' }}>Phòng {{ $i }}</option>
                                                    @endfor
                                                    @for($i = 201; $i <= 203; $i++)
                                                        <option value="{{ $i - 100 }}" {{ old('room_id') == ($i - 100) ? 'selected' : '' }}>Phòng {{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-sm font-medium leading-none">Kỳ đóng tiền</label>
                                                <input type="month" name="billing_month" value="{{ old('billing_month', date('Y-m')) }}" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring cursor-pointer">
                                            </div>
                                        </div>

                                        <div class="border-t border-border pt-6 space-y-4">
                                            <h3 class="font-medium text-sm text-foreground flex items-center gap-2">
                                                <i data-lucide="zap" class="h-4 w-4 text-amber-500"></i> Chỉ số điện (kWh)
                                            </h3>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label class="text-xs text-muted-foreground">Chỉ số đầu</label>
                                                    <input type="number" name="electricity_start" value="{{ old('electricity_start') }}" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="text-xs text-muted-foreground">Chỉ số cuối</label>
                                                    <input type="number" name="electricity_end" value="{{ old('electricity_end') }}" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-t border-border pt-6 space-y-4">
                                            <h3 class="font-medium text-sm text-foreground flex items-center gap-2">
                                                <i data-lucide="droplet" class="h-4 w-4 text-blue-500"></i> Chỉ số nước (m³)
                                            </h3>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label class="text-xs text-muted-foreground">Chỉ số đầu</label>
                                                    <input type="number" name="water_start" value="{{ old('water_start') }}" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                                </div>
                                                <div class="space-y-2">
                                                    <label class="text-xs text-muted-foreground">Chỉ số cuối</label>
                                                    <input type="number" name="water_end" value="{{ old('water_end') }}" required class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Footer -->
                                    <div class="border-t border-border px-6 py-4 bg-muted/30 flex justify-end gap-3">
                                        <button type="button" @click="showCreatePanel = false" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                            Hủy
                                        </button>
                                        <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                                            <i data-lucide="save" class="mr-2 h-4 w-4"></i> Lưu hóa đơn
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
    <!-- SLIDE-OVER DETAIL INVOICE                  -->
    <!-- ========================================== -->
    <template x-teleport="body">
        <div x-show="showDetailPanel" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div x-show="showDetailPanel"
                 x-transition:enter="ease-in-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in-out duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-sm"
                 @click="showDetailPanel = false"></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <div x-show="showDetailPanel"
                             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             class="pointer-events-auto w-screen max-w-lg">
                            
                            <div class="flex h-full flex-col overflow-y-auto bg-background shadow-xl border-l border-border" x-show="selectedInvoice">
                                <!-- Header -->
                                <div class="px-6 py-6 border-b border-border bg-muted/30">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="p-2 bg-primary/10 rounded-md text-primary">
                                                <i data-lucide="receipt" class="h-5 w-5"></i>
                                            </div>
                                            <div>
                                                <h2 class="text-lg font-semibold leading-none tracking-tight">Chi tiết Hóa đơn</h2>
                                                <p class="text-sm font-medium text-primary mt-1.5" x-text="'Mã: ' + selectedInvoice?.invoice_code"></p>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button @click="showDetailPanel = false" type="button" class="relative rounded-md bg-background text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring">
                                                <i data-lucide="x" class="h-4 w-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Body -->
                                <div class="flex-1 px-6 py-6 space-y-6">
                                    <!-- Info Grid -->
                                    <div class="grid grid-cols-2 gap-4 bg-muted/50 p-4 rounded-xl border border-border/50">
                                        <div>
                                            <p class="text-xs font-medium text-muted-foreground mb-1">Đối tượng áp dụng</p>
                                            <p class="text-sm font-semibold" x-text="'Phòng ' + (parseInt(selectedInvoice?.room_id) + 100)"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-muted-foreground mb-1">Kỳ đóng tiền</p>
                                            <p class="text-sm font-semibold" x-text="formatMonth(selectedInvoice?.billing_month)"></p>
                                        </div>
                                        <div class="col-span-2 pt-4 border-t border-border">
                                            <p class="text-xs font-medium text-muted-foreground mb-2">Trạng thái thanh toán</p>
                                            <template x-if="selectedInvoice?.status === 'paid'">
                                                <div class="flex items-center gap-3">
                                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-emerald-100 text-emerald-800">
                                                        Đã thanh toán
                                                    </div>
                                                    <p class="text-xs font-medium text-muted-foreground" x-text="formatDate(selectedInvoice?.paid_at) + (selectedInvoice?.payment_method === 'bank_transfer' ? ' (Chuyển khoản)' : ' (Tiền mặt)')"></p>
                                                </div>
                                            </template>
                                            <template x-if="selectedInvoice?.status !== 'paid'">
                                                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-amber-100 text-amber-800">
                                                    Chưa thanh toán
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Items Table -->
                                    <div>
                                        <h3 class="text-sm font-medium mb-3">Chi tiết các khoản thu</h3>
                                        <div class="rounded-md border border-border">
                                            <table class="w-full text-sm">
                                                <thead class="bg-muted/50 border-b border-border">
                                                    <tr>
                                                        <th class="h-9 px-4 text-left align-middle font-medium text-muted-foreground text-xs">Khoản thu</th>
                                                        <th class="h-9 px-4 text-center align-middle font-medium text-muted-foreground text-xs">SL</th>
                                                        <th class="h-9 px-4 text-right align-middle font-medium text-muted-foreground text-xs">Đơn giá</th>
                                                        <th class="h-9 px-4 text-right align-middle font-medium text-muted-foreground text-xs">Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template x-for="item in selectedInvoice?.items" :key="item.id">
                                                        <tr class="border-b transition-colors hover:bg-muted/50 last:border-0">
                                                            <td class="p-3 align-middle font-medium text-xs" x-text="item.item_name"></td>
                                                            <td class="p-3 align-middle text-center text-muted-foreground text-xs" x-text="item.quantity"></td>
                                                            <td class="p-3 align-middle text-right text-muted-foreground text-xs" x-text="formatCurrency(item.price)"></td>
                                                            <td class="p-3 align-middle text-right font-medium text-xs" x-text="formatCurrency(item.subtotal)"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="flex items-center justify-between p-4 bg-primary/5 rounded-xl border border-primary/20">
                                        <span class="font-medium text-sm">Tổng thanh toán</span>
                                        <span class="text-xl font-bold text-primary" x-text="formatCurrency(selectedInvoice?.total_amount)"></span>
                                    </div>
                                </div>

                                <!-- Action Footer -->
                                <div class="px-6 py-4 border-t border-border bg-muted/30 space-y-3">
                                    <!-- Form thanh toán (nếu chưa thanh toán) -->
                                    <template x-if="selectedInvoice?.status !== 'paid'">
                                        <form :action="'/invoices/' + selectedInvoice?.id + '/pay'" method="POST" class="space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div class="space-y-2">
                                                <label class="text-xs font-medium text-muted-foreground">Xác nhận thanh toán thủ công</label>
                                                <select name="payment_method" required class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm ring-offset-background focus:outline-none focus:ring-1 focus:ring-ring">
                                                    <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                                    <option value="cash">Tiền mặt tại quầy</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-emerald-600 text-white shadow hover:bg-emerald-600/90 h-9 px-4 py-2">
                                                <i data-lucide="check" class="mr-2 h-4 w-4"></i> Đánh dấu Đã thanh toán
                                            </button>
                                        </form>
                                    </template>

                                    <!-- Nút In ấn -->
                                    <a :href="'/invoices/' + selectedInvoice?.id" target="_blank" class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                                        <i data-lucide="printer" class="mr-2 h-4 w-4"></i> Xuất hóa đơn & In ấn
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<!-- Đợi AlpineJS khởi tạo xong mới render Lucide cho các component bên trong Alpine -->
<script>
    document.addEventListener('alpine:initialized', () => {
        Alpine.effect(() => {
            // Chạy lại createIcons sau khi DOM bên trong Alpine được update
            setTimeout(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 50);
        });
    });
</script>
@endsection
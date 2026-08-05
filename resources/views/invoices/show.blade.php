@extends('layouts.app')

@section('title', 'Chi tiết Hóa đơn ' . $invoice->invoice_code)

@section('content')
<div class="flex items-center justify-center p-4">
    <div class="bg-card text-card-foreground w-full max-w-2xl rounded-xl border border-border shadow-sm p-10">
        <!-- Header -->
        <header class="flex justify-between items-start border-b border-border pb-8 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-primary/10 p-2.5 rounded-lg text-primary">
                    <i data-lucide="building-2" class="h-6 w-6"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight">KÝ TÚC XÁ TLU</h2>
                    <p class="text-muted-foreground text-sm mt-0.5">Hóa đơn dịch vụ hàng tháng</p>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold tracking-tight text-primary">{{ $invoice->invoice_code }}</h1>
                <p class="text-muted-foreground text-sm mt-0.5">Kỳ: Tháng {{ $invoice->billing_month->format('m/Y') }}</p>
            </div>
        </header>

        <!-- Information Info -->
        <section class="grid grid-cols-2 gap-8 mb-10">
            <div class="space-y-1">
                <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Đối tượng thanh toán</h3>
                <p class="font-medium text-lg">Phòng số: {{ $invoice->room_id + 100 }}</p>
                <p class="text-muted-foreground text-sm">Đại diện ID: {{ $invoice->student_id ?? 'Chưa xác định' }}</p>
            </div>
            <div class="text-right space-y-1">
                <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Trạng thái</h3>
                @if($invoice->status == 'paid')
                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-emerald-100 text-emerald-800">
                    <i data-lucide="check-circle-2" class="mr-1.5 h-3 w-3"></i> ĐÃ THANH TOÁN
                </div>
                <p class="text-xs text-muted-foreground mt-2">Lúc: {{ $invoice->paid_at->format('d/m/Y H:i') }}</p>
                <p class="text-xs text-muted-foreground">PT: {{ $invoice->payment_method == 'bank_transfer' ? 'CHUYỂN KHOẢN' : 'TIỀN MẶT' }}</p>
                @else
                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-amber-100 text-amber-800">
                    <i data-lucide="clock" class="mr-1.5 h-3 w-3"></i> CHƯA THANH TOÁN
                </div>
                @endif
            </div>
        </section>

        <!-- Table Billing Items -->
        <section class="mb-10">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-border text-muted-foreground">
                        <th class="py-3 text-left font-semibold">Mô tả khoản thu</th>
                        <th class="py-3 text-center font-semibold">Số lượng</th>
                        <th class="py-3 text-right font-semibold">Đơn giá</th>
                        <th class="py-3 text-right font-semibold">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($invoice->items as $item)
                    <tr class="transition-colors hover:bg-muted/50">
                        <td class="py-4 font-medium">{{ $item->item_name }}</td>
                        <td class="py-4 text-center text-muted-foreground">{{ number_format($item->quantity, 0) }}</td>
                        <td class="py-4 text-right text-muted-foreground">{{ number_format($item->price) }} đ</td>
                        <td class="py-4 text-right font-semibold">{{ number_format($item->subtotal) }} đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <!-- Total Box -->
        <section class="flex justify-end border-t border-border pt-6">
            <div class="text-right">
                <p class="text-sm font-medium text-muted-foreground mb-1">Tổng cộng tiền thanh toán</p>
                <p class="text-3xl font-bold tracking-tight text-primary">{{ number_format($invoice->total_amount) }} <span class="text-xl underline">đ</span></p>
            </div>
        </section>

        <!-- Actions -->
        <footer class="mt-12 flex justify-between print:hidden">
            <a href="{{ route('invoice.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-10 px-6 py-2">
                <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i> Quay lại
            </a>
            <button onclick="window.print()" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-10 px-6 py-2">
                <i data-lucide="printer" class="mr-2 h-4 w-4"></i> In hóa đơn
            </button>
        </footer>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .bg-card, .bg-card * {
            visibility: visible;
        }
        .bg-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
            box-shadow: none;
        }
    }
</style>
@endsection
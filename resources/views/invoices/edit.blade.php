@extends('layouts.app')

@section('title', 'Sửa chỉ số Hóa đơn')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Sửa chỉ số điện nước</h2>
            <p class="text-sm text-muted-foreground mt-1">Hóa đơn: {{ $invoice->invoice_code }} - Phòng: {{ $invoice->room->room_number }}</p>
        </div>
        <a href="{{ route('invoice.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
            <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
    <div class="relative w-full rounded-lg border border-red-500/50 bg-red-50/50 p-4 text-red-600">
        <i data-lucide="alert-circle" class="absolute left-4 top-4 h-4 w-4"></i>
        <h5 class="mb-1 leading-none font-medium pl-7">Có lỗi xảy ra</h5>
        <div class="text-sm [&_p]:leading-relaxed pl-7">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Điện -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-blue-600 flex items-center">
                        <i data-lucide="zap" class="mr-2 h-5 w-5"></i> Chỉ số Điện
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-muted-foreground">Số đầu (Không thể sửa)</label>
                            <input type="number" value="{{ $reading->electricity_start }}" class="flex h-10 w-full rounded-md border border-input bg-muted px-3 py-2 text-sm ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 text-muted-foreground font-semibold" readonly tabindex="-1">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Số cuối (Mới)</label>
                            <input type="number" name="electricity_end" value="{{ old('electricity_end', $reading->electricity_end) }}" min="{{ $reading->electricity_start + 1 }}" required class="flex h-10 w-full rounded-md border-2 border-blue-200 bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50 font-bold text-blue-700">
                        </div>
                    </div>
                </div>

                <div class="w-full h-px bg-border"></div>

                <!-- Nước -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-emerald-600 flex items-center">
                        <i data-lucide="droplets" class="mr-2 h-5 w-5"></i> Chỉ số Nước
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-muted-foreground">Số đầu (Không thể sửa)</label>
                            <input type="number" value="{{ $reading->water_start }}" class="flex h-10 w-full rounded-md border border-input bg-muted px-3 py-2 text-sm ring-offset-background disabled:cursor-not-allowed disabled:opacity-50 text-muted-foreground font-semibold" readonly tabindex="-1">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Số cuối (Mới)</label>
                            <input type="number" name="water_end" value="{{ old('water_end', $reading->water_end) }}" min="{{ $reading->water_start + 1 }}" required class="flex h-10 w-full rounded-md border-2 border-emerald-200 bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:cursor-not-allowed disabled:opacity-50 font-bold text-emerald-700">
                        </div>
                    </div>
                </div>

                <div class="rounded-md bg-amber-50 p-4 mt-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="info" class="h-5 w-5 text-amber-400"></i>
                        </div>
                        <div class="ml-3 flex-1 md:flex md:justify-between">
                            <p class="text-sm text-amber-700">Lưu ý: Sau khi lưu, hệ thống sẽ tự động tính toán lại số tiền tiêu thụ và cập nhật tổng cộng của hóa đơn này.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end p-6 pt-0 space-x-2">
                <a href="{{ route('invoice.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                    <i data-lucide="save" class="mr-2 h-4 w-4"></i> Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

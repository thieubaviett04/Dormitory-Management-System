@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Bảng điều khiển</h1>
        <p class="text-muted-foreground mt-2">Xin chào {{ Auth::user()->name }}, chào mừng trở lại hệ thống quản lý KTX!</p>
    </div>

    <!-- Stats Grid (Exactly 4 cards) -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        
        <!-- Students -->
        <div class="rounded-xl border bg-card text-card-foreground shadow transition-all hover:shadow-md">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Tổng Sinh Viên</h3>
                <i data-lucide="users" class="h-4 w-4 text-blue-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ number_format($stats['students']) }}</div>
                <p class="text-xs text-muted-foreground mt-1">Đang lưu trú trong KTX</p>
            </div>
        </div>

        <!-- Available Beds -->
        <div class="rounded-xl border bg-card text-card-foreground shadow transition-all hover:shadow-md">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Giường Trống</h3>
                <i data-lucide="bed" class="h-4 w-4 text-emerald-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ number_format($stats['beds_available']) }} / {{ number_format($stats['beds']) }}</div>
                <p class="text-xs text-muted-foreground mt-1">Số giường có thể sắp xếp</p>
            </div>
        </div>

        <!-- Pending Registrations -->
        <div class="rounded-xl border bg-card text-card-foreground shadow transition-all hover:shadow-md">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Đơn Chờ Duyệt</h3>
                <i data-lucide="file-text" class="h-4 w-4 text-amber-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ number_format($stats['registrations_pending']) }}</div>
                <p class="text-xs text-muted-foreground mt-1">Đơn đăng ký chỗ ở mới</p>
            </div>
        </div>

        <!-- Unpaid Invoices -->
        <div class="rounded-xl border bg-card text-card-foreground shadow transition-all hover:shadow-md">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium text-muted-foreground">Hóa Đơn Chưa Thu</h3>
                <i data-lucide="receipt" class="h-4 w-4 text-rose-500"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ number_format($stats['invoices_unpaid']) }}</div>
                <p class="text-xs text-muted-foreground mt-1">Hóa đơn điện/nước/phòng</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid gap-4 md:grid-cols-2">
        
        <!-- Occupancy Chart -->
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="flex flex-col space-y-1.5 p-6 border-b border-border/50">
                <h3 class="font-semibold leading-none tracking-tight">Tỉ lệ lấp đầy KTX</h3>
                <p class="text-sm text-muted-foreground">Tình trạng sử dụng giường hiện tại</p>
            </div>
            <div class="p-6 pt-6 flex justify-center items-center h-72">
                <canvas id="bedsChart"></canvas>
            </div>
        </div>

        <!-- Invoices Chart -->
        <div class="rounded-xl border bg-card text-card-foreground shadow">
            <div class="flex flex-col space-y-1.5 p-6 border-b border-border/50">
                <h3 class="font-semibold leading-none tracking-tight">Trạng thái Hóa đơn</h3>
                <p class="text-sm text-muted-foreground">Thống kê thanh toán hóa đơn</p>
            </div>
            <div class="p-6 pt-6 flex justify-center items-center h-72">
                <canvas id="invoicesChart"></canvas>
            </div>
        </div>

    </div>
</div>

<!-- Thêm thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Truyền dữ liệu an toàn để tránh lỗi Editor Linter -->
<script id="dashboard-chart-data" type="application/json">
    {!! json_encode($chartData) !!}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Dữ liệu từ Backend
        const chartData = JSON.parse(document.getElementById('dashboard-chart-data').textContent);

        // Cấu hình Chart.js chung
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b'; // Tailwind slate-500
        
        // 1. Biểu đồ Giường
        const ctxBeds = document.getElementById('bedsChart').getContext('2d');
        new Chart(ctxBeds, {
            type: 'doughnut',
            data: {
                labels: ['Đang sử dụng', 'Giường trống'],
                datasets: [{
                    data: [chartData.beds.occupied, chartData.beds.available],
                    backgroundColor: [
                        '#3b82f6', // blue-500
                        '#e2e8f0', // slate-200
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true }
                    }
                }
            }
        });

        // 2. Biểu đồ Hóa đơn
        const ctxInvoices = document.getElementById('invoicesChart').getContext('2d');
        new Chart(ctxInvoices, {
            type: 'doughnut',
            data: {
                labels: ['Đã thanh toán', 'Chưa thanh toán'],
                datasets: [{
                    data: [chartData.invoices.paid, chartData.invoices.unpaid],
                    backgroundColor: [
                        '#10b981', // emerald-500
                        '#f43f5e', // rose-500
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true }
                    }
                }
            }
        });
    });
</script>
@endsection
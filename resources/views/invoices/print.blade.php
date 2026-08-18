<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Hóa Đơn - {{ $invoice->invoice_code }}</title>
    <!-- Use Tailwind via CDN for quick styling of print view -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4;
                margin: 15mm;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
        }
    </style>
</head>
<body class="bg-gray-100 py-8 print:bg-white print:py-0">
    
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 shadow-sm border border-gray-200 print:shadow-none print:border-0 print:p-0 relative">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-gray-300 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-bold uppercase tracking-wider text-gray-800">Ban Quản Lý Ký Túc Xá</h1>
                <p class="text-sm mt-1 text-gray-600">Trường Đại học Công nghệ thông tin</p>
                <p class="text-sm text-gray-600">SĐT: 0123.456.789 | Email: ktx@university.edu.vn</p>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-bold text-gray-800 uppercase tracking-widest">Hóa Đơn</h2>
                <p class="text-sm text-gray-500 mt-2 font-medium">Mã: {{ $invoice->invoice_code }}</p>
                <p class="text-sm text-gray-500">Ngày lập: {{ $invoice->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Info -->
        <div class="flex justify-between mb-8">
            <div class="w-1/2">
                <h3 class="text-sm font-bold uppercase text-gray-500 mb-2 border-b border-gray-200 inline-block pb-1">Thông tin thanh toán</h3>
                <p class="font-bold text-lg">{{ $invoice->room ? 'Phòng ' . $invoice->room->room_number : 'N/A' }}</p>
                <p class="text-sm text-gray-700">Tòa nhà: {{ $invoice->room ? $invoice->room->building->name : 'N/A' }}</p>
                <p class="text-sm text-gray-700">Kỳ thanh toán: <span class="font-bold">Tháng {{ $invoice->billing_month->format('m/Y') }}</span></p>
            </div>
            <div class="w-1/2 text-right">
                <h3 class="text-sm font-bold uppercase text-gray-500 mb-2 border-b border-gray-200 inline-block pb-1">Trạng thái</h3>
                @if($invoice->status == \App\Enums\InvoiceStatus::Paid)
                <p class="text-xl font-bold text-green-600 uppercase border-2 border-green-600 px-3 py-1 inline-block mt-1 transform -rotate-6">Đã Thanh Toán</p>
                <p class="text-sm text-gray-600 mt-2">Ngày TT: {{ $invoice->paid_at->format('d/m/Y H:i') }}</p>
                @else
                <p class="text-xl font-bold text-red-600 uppercase border-2 border-red-600 px-3 py-1 inline-block mt-1 transform -rotate-6">Chưa Thanh Toán</p>
                @endif
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-left mb-8 border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-800">
                    <th class="py-3 px-4 font-bold text-sm text-gray-800">STT</th>
                    <th class="py-3 px-4 font-bold text-sm text-gray-800">Khoản thu / Dịch vụ</th>
                    <th class="py-3 px-4 text-center font-bold text-sm text-gray-800">Số lượng</th>
                    <th class="py-3 px-4 text-right font-bold text-sm text-gray-800">Đơn giá</th>
                    <th class="py-3 px-4 text-right font-bold text-sm text-gray-800">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr class="border-b border-gray-200">
                    <td class="py-3 px-4 text-sm">{{ $index + 1 }}</td>
                    <td class="py-3 px-4 text-sm font-medium">{{ $item->item_name }}</td>
                    <td class="py-3 px-4 text-center text-sm">{{ $item->quantity }}</td>
                    <td class="py-3 px-4 text-right text-sm">{{ number_format($item->price) }} đ</td>
                    <td class="py-3 px-4 text-right text-sm font-bold">{{ number_format($item->subtotal) }} đ</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="py-4"></td>
                    <td class="py-4 px-4 text-right font-bold text-lg border-t-2 border-gray-800">Tổng cộng:</td>
                    <td class="py-4 px-4 text-right font-bold text-xl border-t-2 border-gray-800">{{ number_format($invoice->total_amount) }} đ</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="flex justify-between mt-12 px-8">
            <div class="text-center">
                <p class="font-bold text-sm mb-16">Người nộp tiền</p>
                <p class="text-sm italic text-gray-500">(Ký, ghi rõ họ tên)</p>
            </div>
            <div class="text-center">
                <p class="font-bold text-sm mb-16">Người lập phiếu</p>
                <p class="text-sm italic text-gray-500">(Ký, ghi rõ họ tên)</p>
            </div>
        </div>

        <div class="text-center mt-16 pt-6 border-t border-gray-300 text-xs text-gray-500">
            Hóa đơn in từ Hệ thống Quản lý Ký Túc Xá lúc {{ now()->format('d/m/Y H:i:s') }}
        </div>

    </div>

    <!-- Print controls -->
    <div class="fixed top-4 right-4 flex space-x-2 no-print">
        <button onclick="window.close()" class="px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 transition">Đóng</button>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            In ngay
        </button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>

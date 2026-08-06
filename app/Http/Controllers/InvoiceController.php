<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\UtilityReading;
use App\Models\ServiceType;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('items')->orderBy('created_at', 'desc');

        // Filter by month
        $monthFilter = $request->input('month', date('Y-m'));
        if ($monthFilter) {
            $query->where('billing_month', $monthFilter . '-01');
        }

        $invoices = $query->get();

        // Tính toán số liệu thống kê thực tế dựa trên danh sách đã lọc
        $stats = [
            'total' => $invoices->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'unpaid' => $invoices->where('status', 'unpaid')->count(),
            'total_revenue' => $invoices->where('status', 'paid')->sum('total_amount'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }


    public function show($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer|min:1',
            'billing_month' => 'required|date_format:Y-m',
            'electricity_start' => 'required|numeric|min:0',
            'electricity_end' => 'required|numeric|gte:electricity_start',
            'water_start' => 'required|numeric|min:0',
            'water_end' => 'required|numeric|gte:water_start',
        ], [
            'electricity_end.gte' => 'Chỉ số điện mới không được nhỏ hơn chỉ số điện cũ.',
            'water_end.gte' => 'Chỉ số nước mới không được nhỏ hơn chỉ số nước cũ.',
        ]);

        $monthDate = $request->billing_month . '-01';

        $reading = UtilityReading::updateOrCreate(
            [
                'room_id' => $request->room_id,
                'billing_month' => $monthDate
            ],
            [
                'electricity_start' => $request->electricity_start,
                'electricity_end' => $request->electricity_end,
                'water_start' => $request->water_start,
                'water_end' => $request->water_end,
                'recorded_by' => 1
            ]
        );

        $electricityUsed = $request->electricity_end - $request->electricity_start;
        $waterUsed = $request->water_end - $request->water_start;

        $electricPrice = ServiceType::find(1)?->unit_price ?? 2500;
        $waterPrice = ServiceType::find(2)?->unit_price ?? 10000;

        $electricSubtotal = $electricityUsed * $electricPrice;
        $waterSubtotal = $waterUsed * $waterPrice;

        $roomPrice = 1500000;
        $internetPrice = 150000;

        $totalAmount = $electricSubtotal + $waterSubtotal + $roomPrice + $internetPrice;

        $invoiceCode = 'HD-' . $request->room_id . '-' . date('Ym', strtotime($monthDate)) . '-' . rand(1000, 9999);

        $invoice = Invoice::create([
            'invoice_code' => $invoiceCode,
            'room_id' => $request->room_id,
            'student_id' => null,
            'billing_month' => $monthDate,
            'total_amount' => $totalAmount,
            'status' => 'unpaid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_type_id' => 1,
            'item_name' => "Tiền điện tiêu thụ ({$electricityUsed} kWh)",
            'quantity' => $electricityUsed,
            'price' => $electricPrice,
            'subtotal' => $electricSubtotal
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_type_id' => 2,
            'item_name' => "Tiền nước tiêu thụ ({$waterUsed} m³)",
            'quantity' => $waterUsed,
            'price' => $waterPrice,
            'subtotal' => $waterSubtotal
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_type_id' => null,
            'item_name' => "Tiền thuê phòng lưu trú",
            'quantity' => 1,
            'price' => $roomPrice,
            'subtotal' => $roomPrice
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_type_id' => null,
            'item_name' => "Phí dịch vụ Internet",
            'quantity' => 1,
            'price' => $internetPrice,
            'subtotal' => $internetPrice
        ]);

        return redirect()->route('invoice.index')->with('success', "Đã ghi nhận chỉ số và tạo thành công hóa đơn {$invoiceCode}!");
    }

    /**
     * Chức năng 3: Xác nhận thanh toán hóa đơn thủ công từ Cán bộ quản lý
     */
    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,cash',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('invoice.index')->with('success', "Đã xác nhận thanh toán thành công cho hóa đơn {$invoice->invoice_code}!");
    }
}

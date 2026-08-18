<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\UtilityReading;
use App\Models\ServiceType;
use App\Models\InvoiceItem;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['items', 'room.building'])->orderBy('created_at', 'desc');

        // Filter by month
        $monthFilter = $request->input('month', date('Y-m'));
        if ($monthFilter) {
            $query->where('billing_month', $monthFilter . '-01');
        }

        $invoices = $query->get();

        $readings = UtilityReading::whereIn('room_id', $invoices->pluck('room_id'))
            ->get()
            ->keyBy(function($item) {
                return $item->room_id . '_' . $item->billing_month;
            });

        foreach ($invoices as $invoice) {
            $key = $invoice->room_id . '_' . $invoice->billing_month;
            $invoice->reading = $readings->get($key);
        }

        // Tính toán số liệu thống kê thực tế dựa trên danh sách đã lọc
        $stats = [
            'total' => $invoices->count(),
            'paid' => $invoices->where('status', InvoiceStatus::Paid)->count(),
            'unpaid' => $invoices->where('status', InvoiceStatus::Unpaid)->count(),
            'total_revenue' => $invoices->where('status', InvoiceStatus::Paid)->sum('total_amount'),
        ];

        $rooms = Room::with('building')->orderBy('building_id')->orderBy('room_number')->get();

        return view('invoices.index', compact('invoices', 'stats', 'rooms'));
    }


    public function show($id)
    {
        $invoice = Invoice::with(['items', 'room.building'])->findOrFail($id);

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
            'electricity_end' => 'required|numeric|gt:0|gt:electricity_start',
            'water_start' => 'required|numeric|min:0',
            'water_end' => 'required|numeric|gt:0|gt:water_start',
        ], [
            'room_id.required' => 'Vui lòng chọn phòng áp dụng.',
            'room_id.min'      => 'Vui lòng chọn phòng áp dụng.',
            'electricity_end.gt'  => 'Chỉ số điện cuối phải lớn hơn chỉ số đầu và khác 0.',
            'water_end.gt'        => 'Chỉ số nước cuối phải lớn hơn chỉ số đầu và khác 0.',
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
            'status' => InvoiceStatus::Unpaid,
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
            'payment_method' => ['required', new \Illuminate\Validation\Rules\Enum(PaymentMethod::class)]
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
            'payment_method' => PaymentMethod::from($request->payment_method),
        ]);

        return redirect()->route('invoice.index')->with('success', "Đã xác nhận thanh toán thành công cho hóa đơn {$invoice->invoice_code}!");
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['items', 'room.building'])->findOrFail($id);
        
        // Lấy chỉ số đã lưu để hiển thị lại
        $reading = UtilityReading::where('room_id', $invoice->room_id)
                    ->where('billing_month', $invoice->billing_month)
                    ->first();

        return view('invoices.edit', compact('invoice', 'reading'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'electricity_end' => 'required|numeric|gt:0',
            'water_end' => 'required|numeric|gt:0',
        ], [
            'electricity_end.gt'  => 'Chỉ số điện cuối phải lớn hơn 0.',
            'water_end.gt'        => 'Chỉ số nước cuối phải lớn hơn 0.',
        ]);

        $invoice = Invoice::with(['items'])->findOrFail($id);
        
        $reading = UtilityReading::where('room_id', $invoice->room_id)
                    ->where('billing_month', $invoice->billing_month)
                    ->firstOrFail();
        
        if ($request->electricity_end <= $reading->electricity_start) {
            return back()->withErrors(['electricity_end' => 'Chỉ số cuối phải lớn hơn chỉ số đầu ('.$reading->electricity_start.').']);
        }
        if ($request->water_end <= $reading->water_start) {
            return back()->withErrors(['water_end' => 'Chỉ số cuối phải lớn hơn chỉ số đầu ('.$reading->water_start.').']);
        }

        // Cập nhật UtilityReading
        $reading->update([
            'electricity_end' => $request->electricity_end,
            'water_end' => $request->water_end,
        ]);

        $electricityUsed = $request->electricity_end - $reading->electricity_start;
        $waterUsed = $request->water_end - $reading->water_start;

        $electricPrice = ServiceType::find(1)?->unit_price ?? 2500;
        $waterPrice = ServiceType::find(2)?->unit_price ?? 10000;

        $electricSubtotal = $electricityUsed * $electricPrice;
        $waterSubtotal = $waterUsed * $waterPrice;

        // Cập nhật lại các InvoiceItems
        $elecItem = $invoice->items()->where('service_type_id', 1)->first();
        if ($elecItem) {
            $elecItem->update([
                'item_name' => "Tiền điện tiêu thụ ({$electricityUsed} kWh)",
                'quantity' => $electricityUsed,
                'subtotal' => $electricSubtotal
            ]);
        }

        $waterItem = $invoice->items()->where('service_type_id', 2)->first();
        if ($waterItem) {
            $waterItem->update([
                'item_name' => "Tiền nước tiêu thụ ({$waterUsed} m³)",
                'quantity' => $waterUsed,
                'subtotal' => $waterSubtotal
            ]);
        }

        // Cập nhật tổng tiền hóa đơn
        $totalAmount = $invoice->items()->sum('subtotal');
        $invoice->update(['total_amount' => $totalAmount]);

        return redirect()->route('invoice.index')->with('success', "Đã cập nhật chỉ số điện nước cho hóa đơn {$invoice->invoice_code}.");
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status == InvoiceStatus::Paid) {
            return redirect()->route('invoice.index')->withErrors(['error' => 'Không thể xóa hóa đơn đã thanh toán để đảm bảo tính toàn vẹn dữ liệu doanh thu.']);
        }
        
        // Xóa các item chi tiết trước (để tránh lỗi foreign key nếu không dùng cascade delete trên CSDL)
        $invoice->items()->delete();
        
        // Xóa bảng ghi chỉ số điện nước tháng đó
        UtilityReading::where('room_id', $invoice->room_id)
            ->where('billing_month', $invoice->billing_month)
            ->delete();

        // Cuối cùng xóa hóa đơn
        $invoice->delete();

        return redirect()->route('invoice.index')->with('success', 'Đã xóa hóa đơn thành công.');
    }

    public function print($id)
    {
        $invoice = Invoice::with(['items', 'room.building', 'student'])->findOrFail($id);
        return view('invoices.print', compact('invoice'));
    }

    public function bulkCreate(Request $request)
    {
        $month = $request->input('month', date('Y-m'));
        $monthDate = $month . '-01';
        $prevMonthDate = date('Y-m-d', strtotime('-1 month', strtotime($monthDate)));

        $rooms = Room::with('building')->orderBy('building_id')->orderBy('room_number')->get();
        
        // Lấy chỉ số tháng hiện tại (nếu đã nhập rải rác một vài phòng rồi)
        $currentReadings = UtilityReading::where('billing_month', $monthDate)->get()->keyBy('room_id');
        
        // Lấy chỉ số tháng trước để lấy ra số ĐẦU (chính là số cuối của tháng trước)
        $prevReadings = UtilityReading::where('billing_month', $prevMonthDate)->get()->keyBy('room_id');

        return view('invoices.bulk_create', compact('rooms', 'month', 'currentReadings', 'prevReadings'));
    }

    public function bulkStore(Request $request)
    {
        $monthDate = $request->input('month') . '-01';
        $readings = $request->input('readings', []);

        if (empty($readings)) {
            return back()->withErrors(['Dữ liệu nhập không hợp lệ. Vui lòng nhập ít nhất một phòng.']);
        }

        // Bọc toàn bộ vào transaction: Nếu có lỗi ở phòng bất kỳ, sẽ huỷ (rollback) toàn bộ để làm lại
        \Illuminate\Support\Facades\DB::transaction(function () use ($monthDate, $readings) {
            $electricPrice = ServiceType::find(1)?->unit_price ?? 2500;
            $waterPrice = ServiceType::find(2)?->unit_price ?? 10000;
            $roomPrice = 1500000;
            $internetPrice = 150000;

            foreach ($readings as $roomId => $data) {
                // Bỏ qua các phòng không điền đầy đủ cả điện và nước cuối
                if (empty($data['electricity_end']) || empty($data['water_end'])) {
                    continue;
                }

                $electricityStart = $data['electricity_start'] ?? 0;
                $waterStart = $data['water_start'] ?? 0;
                $electricityEnd = $data['electricity_end'];
                $waterEnd = $data['water_end'];

                // Bỏ qua nếu số cuối nhỏ hơn số đầu (tránh âm tiền)
                if ($electricityEnd <= $electricityStart || $waterEnd <= $waterStart) {
                    continue; 
                }

                // Cập nhật hoặc thêm mới UtilityReading
                UtilityReading::updateOrCreate(
                    [
                        'room_id' => $roomId,
                        'billing_month' => $monthDate
                    ],
                    [
                        'electricity_start' => $electricityStart,
                        'electricity_end' => $electricityEnd,
                        'water_start' => $waterStart,
                        'water_end' => $waterEnd,
                        'recorded_by' => Auth::id() ?? 1
                    ]
                );

                $electricityUsed = $electricityEnd - $electricityStart;
                $waterUsed = $waterEnd - $waterStart;

                $electricSubtotal = $electricityUsed * $electricPrice;
                $waterSubtotal = $waterUsed * $waterPrice;
                $totalAmount = $electricSubtotal + $waterSubtotal + $roomPrice + $internetPrice;

                // Tìm xem hóa đơn tháng này của phòng này đã có chưa
                $invoice = Invoice::firstOrNew([
                    'room_id' => $roomId,
                    'billing_month' => $monthDate,
                ]);

                // Nếu chưa có thì sinh mã mới
                if (!$invoice->exists) {
                    $invoice->invoice_code = 'HD-' . $roomId . '-' . date('Ym', strtotime($monthDate)) . '-' . rand(1000, 9999);
                    $invoice->status = InvoiceStatus::Unpaid;
                }
                
                $invoice->total_amount = $totalAmount;
                $invoice->save();

                // Xóa các chi tiết cũ và tạo mới để an toàn cập nhật lại
                $invoice->items()->delete();

                InvoiceItem::insert([
                    ['invoice_id' => $invoice->id, 'service_type_id' => 1, 'item_name' => "Tiền điện tiêu thụ ({$electricityUsed} kWh)", 'quantity' => $electricityUsed, 'price' => $electricPrice, 'subtotal' => $electricSubtotal, 'created_at' => now(), 'updated_at' => now()],
                    ['invoice_id' => $invoice->id, 'service_type_id' => 2, 'item_name' => "Tiền nước tiêu thụ ({$waterUsed} m³)", 'quantity' => $waterUsed, 'price' => $waterPrice, 'subtotal' => $waterSubtotal, 'created_at' => now(), 'updated_at' => now()],
                    ['invoice_id' => $invoice->id, 'service_type_id' => null, 'item_name' => "Tiền thuê phòng lưu trú", 'quantity' => 1, 'price' => $roomPrice, 'subtotal' => $roomPrice, 'created_at' => now(), 'updated_at' => now()],
                    ['invoice_id' => $invoice->id, 'service_type_id' => null, 'item_name' => "Phí dịch vụ Internet", 'quantity' => 1, 'price' => $internetPrice, 'subtotal' => $internetPrice, 'created_at' => now(), 'updated_at' => now()]
                ]);
            }
        });

        return redirect()->route('invoice.index')->with('success', 'Đã xử lý tạo và cập nhật hóa đơn đồng loạt thành công!');
    }
}

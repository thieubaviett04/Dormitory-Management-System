<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo hóa đơn chính cho Phòng 101 (Tháng 8/2026)
        $invoice1 = Invoice::create([
            'invoice_code' => 'HD-202608-0001',
            'room_id' => 1,
            'student_id' => 10,
            'billing_month' => '2026-08-01',
            'total_amount' => 2025000.00,
            'status' => 'unpaid',
        ]);

        // Chi tiết tiền cho hóa đơn 1
        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'service_type_id' => 1, // Điện (150 kWh x 2500)
            'item_name' => 'Tiền điện sinh hoạt tiêu thụ (150 kWh)',
            'quantity' => 150.00,
            'price' => 2500.00,
            'subtotal' => 375000.00
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'service_type_id' => 2, // Nước (12 m3 x 10000)
            'item_name' => 'Tiền nước sinh hoạt tiêu thụ (12 m3)',
            'quantity' => 12.00,
            'price' => 10000.00,
            'subtotal' => 120000.00
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'service_type_id' => 3, // Mạng
            'item_name' => 'Tiền gói cước Internet phòng 101',
            'quantity' => 1.00,
            'price' => 150000.00,
            'subtotal' => 150000.00
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'service_type_id' => null, // Tiền phòng cố định
            'item_name' => 'Tiền thuê phòng lưu trú hàng tháng',
            'quantity' => 1.00,
            'price' => 1380000.00,
            'subtotal' => 1380000.00
        ]);


        // 2. Tạo hóa đơn chính cho Phòng 102 (Đã thanh toán)
        $invoice2 = Invoice::create([
            'invoice_code' => 'HD-202608-0002',
            'room_id' => 2,
            'student_id' => 11,
            'billing_month' => '2026-08-01',
            'total_amount' => 1880000.00,
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'bank_transfer'
        ]);

        // Chi tiết tiền cho hóa đơn 2
        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'service_type_id' => 1,
            'item_name' => 'Tiền điện sinh hoạt tiêu thụ (120 kWh)',
            'quantity' => 120.00,
            'price' => 2500.00,
            'subtotal' => 300000.00
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'service_type_id' => 2,
            'item_name' => 'Tiền nước sinh hoạt tiêu thụ (8 m3)',
            'quantity' => 8.00,
            'price' => 10000.00,
            'subtotal' => 80000.00
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'service_type_id' => null,
            'item_name' => 'Tiền thuê phòng lưu trú hàng tháng',
            'quantity' => 1.00,
            'price' => 1500000.00,
            'subtotal' => 1500000.00
        ]);
    }
}

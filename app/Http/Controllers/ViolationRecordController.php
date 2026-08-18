<?php

namespace App\Http\Controllers;

use App\Models\ViolationRecord;
use App\Models\Student;
use App\Models\ViolationType;
use App\Enums\ViolationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViolationRecordController extends Controller
{
    public function index()
    {
        $records = ViolationRecord::with(['violationType', 'student', 'recorder'])->latest()->get();

        $stats = [
            'total' => $records->count(),
            'pending' => $records->where('status', ViolationStatus::Pending)->count(),
            'resolved' => $records->where('status', ViolationStatus::Resolved)->count(),
        ];

        $students = Student::all();
        $violationTypes = ViolationType::all();

        return view('violations.index', compact('records', 'stats', 'students', 'violationTypes'));
    }

    public function create()
    {
        $students = Student::all();
        $violationTypes = ViolationType::all();
        return view('violations.create', compact('students', 'violationTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'record_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:1000',
        ], [
            'student_id.required' => 'Vui lòng chọn sinh viên vi phạm.',
            'student_id.exists' => 'Sinh viên không tồn tại trong hệ thống.',
            'violation_type_id.required' => 'Vui lòng chọn loại vi phạm.',
            'violation_type_id.exists' => 'Loại vi phạm không tồn tại.',
            'record_date.required' => 'Vui lòng nhập ngày vi phạm.',
            'record_date.date' => 'Ngày vi phạm không hợp lệ.',
            'record_date.before_or_equal' => 'Ngày vi phạm không được lớn hơn ngày hiện tại.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $validated['status'] = ViolationStatus::Pending;
        $validated['recorded_by'] = Auth::id() ?? 1; // Fallback to 1 if no auth

        ViolationRecord::create($validated);

        return redirect()->route('violation.index')->with('success', 'Đã lập biên bản vi phạm thành công.');
    }

    public function show($id)
    {
        $record = ViolationRecord::with(['student', 'violationType', 'recorder'])->findOrFail($id);
        return view('violations.show', compact('record'));
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'nullable|string'
        ]);

        $record = ViolationRecord::findOrFail($id);
        $record->status = ViolationStatus::Resolved;
        
        // Save payment method if field exists, or we just log it if we create the field later
        // Assuming we will add a payment_method column via migration
        if (\Schema::hasColumn('violation_records', 'payment_method')) {
            $record->payment_method = $request->input('payment_method');
        }
        
        $record->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái biên bản thành Đã giải quyết.');
    }
}

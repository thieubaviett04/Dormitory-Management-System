<?php

namespace App\Http\Controllers;

use App\Models\ViolationRecord;
use App\Enums\ViolationStatus;
use Illuminate\Http\Request;

class ViolationRecordController extends Controller
{
    public function index()
    {
        $records = ViolationRecord::with('violationType')->orderBy('record_date', 'desc')->get();

        $stats = [
            'total' => $records->count(),
            'pending' => $records->where('status', ViolationStatus::Pending)->count(),
            'resolved' => $records->where('status', ViolationStatus::Resolved)->count(),
        ];

        return view('violations.index', compact('records', 'stats'));
    }
}

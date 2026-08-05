<?php

namespace App\Http\Controllers;

use App\Models\ViolationRecord;
use Illuminate\Http\Request;

class ViolationRecordController extends Controller
{
    public function index()
    {
        $records = ViolationRecord::with('violationType')->orderBy('record_date', 'desc')->get();

        $stats = [
            'total' => $records->count(),
            'pending' => $records->where('status', 'pending')->count(),
            'resolved' => $records->where('status', 'resolved')->count(),
        ];

        return view('violations.index', compact('records', 'stats'));
    }
}

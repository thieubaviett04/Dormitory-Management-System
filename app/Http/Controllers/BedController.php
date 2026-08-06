<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

// use App\Models\Building;

class BedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beds = Bed::with('room.building')
            ->orderBy('id', 'desc')
            ->get();

        return view('beds.index', compact('beds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rooms = Room::with('building')
            ->orderBy('room_number')
            ->get();

        return view('beds.create', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|max:20',
            'status' => 'required|in:available,maintenance',
        ]);

        Bed::create([
            'room_id' => $request->room_id,
            'bed_number' => $request->bed_number,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('beds.index')
            ->with('success', 'Thêm giường thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bed = Bed::findOrFail($id);

        $rooms = Room::with('building')
            ->orderBy('room_number')
            ->get();
        $hasActiveAllocation = $bed->allocations()->active()->exists();

        return view('beds.edit', compact('bed', 'rooms', 'hasActiveAllocation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bed = Bed::findOrFail($id);

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|max:20',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $hasActiveAllocation = $bed->allocations()->active()->exists();
        if ($bed->allocations()->exists() && (int) $request->room_id !== $bed->room_id) {
            throw ValidationException::withMessages([
                'room_id' => 'Không thể chuyển giường sang phòng khác vì đã có lịch sử phân giường.',
            ]);
        }

        if ($hasActiveAllocation && $request->status !== 'occupied') {
            throw ValidationException::withMessages([
                'status' => 'Giường đang được phân; hãy trả phòng hoặc chuyển phòng trước.',
            ]);
        }

        if (! $hasActiveAllocation && $request->status === 'occupied') {
            throw ValidationException::withMessages([
                'status' => 'Chỉ Module 3 được đánh dấu giường đang có người ở.',
            ]);
        }

        $bed->update([
            'room_id' => $request->room_id,
            'bed_number' => $request->bed_number,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('beds.index')
            ->with('success', 'Cập nhật giường thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bed = Bed::findOrFail($id);

        if ($bed->allocations()->exists()) {
            throw ValidationException::withMessages([
                'bed' => 'Không thể xóa giường đã có lịch sử phân giường.',
            ]);
        }

        $bed->delete();

        return redirect()
            ->route('beds.index')
            ->with('success', 'Xóa giường thành công.');
    }
}

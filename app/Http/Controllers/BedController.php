<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Room;
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
            'status' => 'required|in:available,occupied,maintenance',
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

        return view('beds.edit', compact('bed', 'rooms'));
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

        $bed->delete();

        return redirect()
            ->route('beds.index')
            ->with('success', 'Xóa giường thành công.');
    }
}

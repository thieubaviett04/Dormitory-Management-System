<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Building;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with('building')
            ->orderBy('id', 'desc')
            ->get();

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buildings = Building::orderBy('name')->get();

        return view('rooms.create', compact('buildings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => 'required|max:20',
            'floor' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,full,maintenance',
        ]);

        Room::create([
            'building_id' => $request->building_id,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Thêm phòng thành công.');
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
        $room = Room::findOrFail($id);
        $buildings = Building::orderBy('name')->get();

        return view('rooms.edit', compact('room', 'buildings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => 'required|max:20',
            'floor' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,full,maintenance',
        ]);

        $room->update([
            'building_id' => $request->building_id,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Cập nhật phòng thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);

        $room->delete();

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Xóa phòng thành công.');
    }
}

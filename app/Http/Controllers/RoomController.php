<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $building = Building::find($request->building_id);

        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => [
                'required',
                'max:20',
                'regex:/^\d{3,}$/',
                \Illuminate\Validation\Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('building_id', $request->building_id);
                }),
            ],
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,full,maintenance',
        ], [
            'room_number.unique' => 'Số phòng này đã tồn tại trong tòa nhà đã chọn.',
            'room_number.regex' => 'Số phòng phải là chữ số và có ít nhất 3 ký tự (ví dụ: 101, 205, 1001).',
        ]);

        // Tự động tính tầng từ số phòng
        $floor = intdiv((int) $request->room_number, 100);

        if ($building && $floor > $building->floors) {
            return back()->withInput()->withErrors([
                'room_number' => 'Số phòng "' . $request->room_number . '" ứng với tầng ' . $floor . ', vượt quá số tầng của tòa nhà (' . $building->floors . ' tầng).',
            ]);
        }

        Room::create([
            'building_id' => $request->building_id,
            'room_number' => $request->room_number,
            'floor' => $floor,
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
        $building = Building::find($request->building_id);

        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => [
                'required',
                'max:20',
                'regex:/^\d{3,}$/',
                \Illuminate\Validation\Rule::unique('rooms')->where(function ($query) use ($request) {
                    return $query->where('building_id', $request->building_id);
                })->ignore($room->id),
            ],
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,full,maintenance',
        ], [
            'room_number.unique' => 'Số phòng này đã tồn tại trong tòa nhà đã chọn.',
            'room_number.regex' => 'Số phòng phải là chữ số và có ít nhất 3 ký tự (ví dụ: 101, 205, 1001).',
        ]);

        // Tự động tính tầng từ số phòng
        $floor = intdiv((int) $request->room_number, 100);

        if ($building && $floor > $building->floors) {
            return back()->withInput()->withErrors([
                'room_number' => 'Số phòng "' . $request->room_number . '" ứng với tầng ' . $floor . ', vượt quá số tầng của tòa nhà (' . $building->floors . ' tầng).',
            ]);
        }

        $activeAllocationCount = $room->allocations()->active()->count();
        if ((int) $request->capacity < $activeAllocationCount) {
            throw ValidationException::withMessages([
                'capacity' => 'Sức chứa không được nhỏ hơn số sinh viên đang ở trong phòng.',
            ]);
        }

        if ($request->status === 'maintenance' && $activeAllocationCount > 0) {
            throw ValidationException::withMessages([
                'status' => 'Phải chuyển hoặc trả hết giường trước khi đưa phòng vào bảo trì.',
            ]);
        }

        $room->update([
            'building_id' => $request->building_id,
            'room_number' => $request->room_number,
            'floor' => $floor,
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

        if ($room->allocations()->exists()) {
            throw ValidationException::withMessages([
                'room' => 'Không thể xóa phòng đã có lịch sử phân giường.',
            ]);
        }

        $room->delete();

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Xóa phòng thành công.');
    }
}

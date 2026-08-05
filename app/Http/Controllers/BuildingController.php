<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Building;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buildings = Building::orderBy('id', 'desc')->get();

        return view('buildings.index', compact('buildings'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('buildings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|max:20|unique:buildings,code',
            'name' => 'required|max:255',
            'floors' => 'required|integer|min:1',
            'description' => 'nullable|max:500',
        ]);

        Building::create($request->only([
            'code',
            'name',
            'floors',
            'description',
        ]));

        return redirect()
            ->route('buildings.index')
            ->with('success', 'Thêm tòa nhà thành công.');
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
        $building = Building::findOrFail($id);

        return view('buildings.edit', compact('building'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $building = Building::findOrFail($id);

        $request->validate([
            'code' => 'required|max:20|unique:buildings,code,' . $building->id,
            'name' => 'required|max:255',
            'floors' => 'required|integer|min:1',
            'description' => 'nullable|max:500',
        ]);

        $building->update($request->only([
            'code',
            'name',
            'floors',
            'description',
        ]));

        return redirect()
            ->route('buildings.index')
            ->with('success', 'Cập nhật tòa nhà thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $building = Building::findOrFail($id);

        $building->delete();

        return redirect()
            ->route('buildings.index')
            ->with('success', 'Xóa tòa nhà thành công.');
    }
}

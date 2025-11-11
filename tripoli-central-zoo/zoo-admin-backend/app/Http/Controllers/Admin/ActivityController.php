<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Animal;
use App\Models\Facility;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['animal', 'facility'])
            ->orderBy('start_time', 'desc')
            ->paginate(20);
        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        $animals = Animal::orderBy('name')->get();
        $facilities = Facility::orderBy('name')->get();
        return view('admin.activities.create', compact('animals', 'facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'status' => 'required|in:scheduled,cancelled,completed',
            'animal_id' => 'nullable|exists:animals,id',
            'facility_id' => 'nullable|exists:facilities,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'duration_minutes' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'requires_booking' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        Activity::create($validated);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity created successfully.');
    }

    public function edit(Activity $activity)
    {
        $animals = Animal::orderBy('name')->get();
        $facilities = Facility::orderBy('name')->get();
        return view('admin.activities.edit', compact('activity', 'animals', 'facilities'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'location' => 'nullable|string',
            'status' => 'required|in:scheduled,cancelled,completed',
            'animal_id' => 'nullable|exists:animals,id',
            'facility_id' => 'nullable|exists:facilities,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'duration_minutes' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'requires_booking' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $activity->update($validated);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}

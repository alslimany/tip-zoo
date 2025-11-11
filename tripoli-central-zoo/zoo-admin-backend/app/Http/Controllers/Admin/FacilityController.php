<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::with('category')->orderBy('display_order')->paginate(20);
        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $types = $this->getFacilityTypes();
        return view('admin.facilities.create', compact('categories', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:open,closed,maintenance',
            'location_x' => 'nullable|numeric',
            'location_y' => 'nullable|numeric',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'capacity' => 'nullable|integer',
            'is_accessible' => 'boolean',
            'display_order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opening_hours' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('facilities', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        Facility::create($validated);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility created successfully.');
    }

    public function edit(Facility $facility)
    {
        $categories = Category::orderBy('name')->get();
        $types = $this->getFacilityTypes();
        return view('admin.facilities.edit', compact('facility', 'categories', 'types'));
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'required|in:open,closed,maintenance',
            'location_x' => 'nullable|numeric',
            'location_y' => 'nullable|numeric',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'capacity' => 'nullable|integer',
            'is_accessible' => 'boolean',
            'display_order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opening_hours' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($facility->image_url) {
                $oldPath = str_replace('/storage/', '', $facility->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('facilities', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $facility->update($validated);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility updated successfully.');
    }

    public function destroy(Facility $facility)
    {
        // Delete image if exists
        if ($facility->image_url) {
            $path = str_replace('/storage/', '', $facility->image_url);
            Storage::disk('public')->delete($path);
        }

        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Facility deleted successfully.');
    }

    private function getFacilityTypes()
    {
        return [
            'restroom' => 'Restroom',
            'dining' => 'Dining',
            'gift_shop' => 'Gift Shop',
            'information' => 'Information Center',
            'first_aid' => 'First Aid',
            'parking' => 'Parking',
        ];
    }
}

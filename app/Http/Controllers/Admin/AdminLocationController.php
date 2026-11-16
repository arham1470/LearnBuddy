<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\PhysicalLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLocationController extends Controller
{
    /**
     * Display a listing of physical campus locations across Sri Lanka.
     */
    public function index(Request $request): View
    {
        $query = PhysicalLocation::withCount('batches')
            ->orderBy('province')
            ->orderBy('city');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $locations = $query->paginate(15)->withQueryString();

        return view('admin.locations.index', compact('locations', 'search'));
    }

    /**
     * Show the form for creating a new campus location.
     */
    public function create(): View
    {
        $provinces = PhysicalLocation::PROVINCES;
        $districts = PhysicalLocation::DISTRICTS;

        return view('admin.locations.create', compact('provinces', 'districts'));
    }

    /**
     * Store a newly created campus location.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        PhysicalLocation::create($data);

        return redirect()->route('admin.locations.index')
            ->with('status', 'Campus location created successfully!');
    }

    /**
     * Show the form for editing the specified campus location.
     */
    public function edit(PhysicalLocation $location): View
    {
        $provinces = PhysicalLocation::PROVINCES;
        $districts = PhysicalLocation::DISTRICTS;

        return view('admin.locations.edit', compact('location', 'provinces', 'districts'));
    }

    /**
     * Update the specified campus location.
     */
    public function update(UpdateLocationRequest $request, PhysicalLocation $location): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $location->update($data);

        return redirect()->route('admin.locations.index')
            ->with('status', 'Campus location updated successfully!');
    }

    /**
     * Remove the specified campus location.
     */
    public function destroy(PhysicalLocation $location): RedirectResponse
    {
        if ($location->batches()->exists()) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete this campus location because course batches are scheduled at it.');
        }

        $name = $location->name;
        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('status', "Campus location '{$name}' has been safely deleted.");
    }
}

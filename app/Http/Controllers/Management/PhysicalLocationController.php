<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\PhysicalLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PhysicalLocationController extends Controller
{
    /**
     * Display a listing of physical campus locations across Sri Lanka.
     */
    public function index(\Illuminate\Http\Request $request): View|RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.locations.index');
        }

        Gate::authorize('viewAny', PhysicalLocation::class);

        $locations = PhysicalLocation::withCount('batches')
            ->orderBy('province')
            ->orderBy('city')
            ->get();

        return view('management.locations.index', compact('locations'));
    }

    /**
     * Show form to create a new physical location.
     */
    public function create(\Illuminate\Http\Request $request): View|RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.locations.create');
        }

        Gate::authorize('create', PhysicalLocation::class);

        $provinces = PhysicalLocation::PROVINCES;
        $districts = PhysicalLocation::DISTRICTS;

        return view('management.locations.create', compact('provinces', 'districts'));
    }

    /**
     * Store a newly created physical location.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Gate::authorize('create', PhysicalLocation::class);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        PhysicalLocation::create($data);

        return redirect()->route('management.locations.index')
            ->with('status', 'Physical campus location added successfully.');
    }

    /**
     * Show form to edit an existing physical location.
     */
    public function edit(\Illuminate\Http\Request $request, PhysicalLocation $location): View|RedirectResponse
    {
        if ($request->user()?->isAdmin()) {
            return redirect()->route('admin.locations.edit', $location);
        }

        Gate::authorize('update', $location);

        $provinces = PhysicalLocation::PROVINCES;
        $districts = PhysicalLocation::DISTRICTS;

        return view('management.locations.edit', compact('location', 'provinces', 'districts'));
    }

    /**
     * Update an existing physical location.
     */
    public function update(UpdateLocationRequest $request, PhysicalLocation $location): RedirectResponse
    {
        Gate::authorize('update', $location);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $location->update($data);

        return redirect()->route('management.locations.index')
            ->with('status', 'Physical campus location updated successfully.');
    }

    /**
     * Delete a physical location.
     */
    public function destroy(PhysicalLocation $location): RedirectResponse
    {
        Gate::authorize('delete', $location);

        if ($location->batches()->count() > 0) {
            return back()->with('error', 'Cannot delete a campus location that has associated course batches.');
        }

        $location->delete();

        return redirect()->route('management.locations.index')
            ->with('status', 'Physical location deleted successfully.');
    }
}

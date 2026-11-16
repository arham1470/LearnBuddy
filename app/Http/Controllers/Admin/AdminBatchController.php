<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseBatch;
use App\Models\PhysicalLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBatchController extends Controller
{
    public function index(Request $request): View
    {
        $query = CourseBatch::with(['course', 'physicalLocation', 'enrollments'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('batch_name', 'like', "%{$search}%")
                  ->orWhere('batch_code', 'like', "%{$search}%");
        }

        $batches = $query->paginate(15)->withQueryString();
        $locations = PhysicalLocation::where('is_active', true)->get();

        return view('admin.batches.index', compact('batches', 'locations'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Assignment::with(['course', 'submissions'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $assignments = $query->paginate(15)->withQueryString();
        $pendingSubmissionsCount = AssignmentSubmission::where('status', 'submitted')->count();

        return view('admin.assignments.index', compact('assignments', 'pendingSubmissionsCount'));
    }
}

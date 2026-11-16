<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminEnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Enrollment::with(['user', 'course'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('course', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $enrollments = $query->paginate(15)->withQueryString();

        return view('admin.enrollments.index', compact('enrollments'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountApprovedMail;
use App\Models\InstructorApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class AdminApprovalController extends Controller
{
    /**
     * Display a listing of pending user accounts and instructor applications awaiting admin approval.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'all'); // 'all', 'instructors', 'students'

        $query = User::query()
            ->with(['roles', 'latestInstructorApplication'])
            ->where(function ($q) {
                // Either unapproved users with verified email
                $q->where(function ($sub) {
                    $sub->where('is_approved', false)
                        ->whereNotNull('email_verified_at');
                })
                // Or any user that has a pending instructor application
                ->orWhereHas('instructorApplications', function ($sub) {
                    $sub->where('status', 'pending');
                });
            })
            ->latest('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($tab === 'instructors') {
            $query->where(function ($q) {
                $q->whereHas('instructorApplications', fn ($sub) => $sub->where('status', 'pending'))
                  ->orWhereHas('roles', fn ($sub) => $sub->where('name', 'instructor'));
            });
        } elseif ($tab === 'students') {
            $query->whereDoesntHave('instructorApplications', fn ($sub) => $sub->where('status', 'pending'))
                  ->whereHas('roles', fn ($sub) => $sub->where('name', 'student'));
        }

        $pendingUsers = $query->paginate(15)->withQueryString();

        $stats = [
            'pending_count' => User::where(function ($q) {
                $q->where(fn ($sub) => $sub->where('is_approved', false)->whereNotNull('email_verified_at'))
                  ->orWhereHas('instructorApplications', fn ($sub) => $sub->where('status', 'pending'));
            })->count(),
            'pending_instructors' => InstructorApplication::where('status', 'pending')->count(),
            'pending_students' => User::where('is_approved', false)
                ->whereNotNull('email_verified_at')
                ->whereDoesntHave('instructorApplications', fn ($sub) => $sub->where('status', 'pending'))
                ->count(),
            'approved_today' => User::where('is_approved', true)->whereDate('approved_at', today())->count(),
            'total_approved' => User::where('is_approved', true)->count(),
        ];

        return view('admin.approvals.index', [
            'users' => $pendingUsers,
            'search' => $search,
            'tab' => $tab,
            'stats' => $stats,
        ]);
    }

    /**
     * Approve a pending user account / instructor application.
     */
    public function approve(Request $request, User $user): RedirectResponse
    {
        // Check for any pending instructor application
        $application = $user->latestInstructorApplication;

        if ($application && $application->isPending()) {
            $application->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
            ]);

            // Assign instructor role if not already assigned
            if (! $user->isInstructor()) {
                $user->assignRole('instructor');
            }
        }

        $user->approve($request->user());

        try {
            Mail::to($user->email)->send(new AccountApprovedMail($user));
        } catch (Throwable $e) {
            Log::error('Failed to send account approval email: '.$e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        $successMsg = $application && $application->status === 'approved'
            ? "Instructor application for '{$user->name}' has been approved. They now have full access to create courses and manage batches."
            : "User '{$user->name}' has been successfully approved.";

        return redirect()->route('admin.approvals.index', ['tab' => $request->input('tab', 'all')])
            ->with('status', $successMsg);
    }

    /**
     * Reject a pending user registration or instructor application.
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        $name = $user->name;
        $application = $user->latestInstructorApplication;

        if ($application && $application->isPending()) {
            $application->update([
                'status' => 'rejected',
                'admin_notes' => $request->input('admin_notes', 'Application rejected by platform administrator.'),
                'reviewed_by' => $request->user()?->id,
                'reviewed_at' => now(),
            ]);

            // If user already had an approved student account, don't delete them; just reject their instructor application
            if ($user->isApproved() || $user->enrollments()->exists() || $user->orders()->exists()) {
                return redirect()->route('admin.approvals.index')
                    ->with('status', "Instructor application for '{$name}' was rejected. Their student account remains active.");
            }
        }

        $user->forceFill([
            'is_active' => false,
        ])->save();

        $user->delete();

        return redirect()->route('admin.approvals.index')
            ->with('status', "Registration for '{$name}' has been rejected and removed.");
    }
}

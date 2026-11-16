<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountApprovedMail;
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
     * Display a listing of pending user accounts awaiting admin approval.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = User::query()
            ->where('is_approved', false)
            ->whereNotNull('email_verified_at')
            ->latest('email_verified_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $pendingUsers = $query->paginate(15)->withQueryString();

        $stats = [
            'pending_count' => User::where('is_approved', false)->whereNotNull('email_verified_at')->count(),
            'approved_today' => User::where('is_approved', true)->whereDate('approved_at', today())->count(),
            'total_approved' => User::where('is_approved', true)->count(),
        ];

        return view('admin.approvals.index', [
            'users' => $pendingUsers,
            'search' => $search,
            'stats' => $stats,
        ]);
    }

    /**
     * Approve a pending user account.
     */
    public function approve(Request $request, User $user): RedirectResponse
    {
        if ($user->isApproved()) {
            return redirect()->route('admin.approvals.index')
                ->with('status', "User '{$user->name}' is already approved.");
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

        return redirect()->route('admin.approvals.index')
            ->with('status', "User '{$user->name}' has been successfully approved.");
    }

    /**
     * Reject and delete/deactivate a pending user registration.
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        $name = $user->name;

        $user->forceFill([
            'is_active' => false,
        ])->save();

        $user->delete();

        return redirect()->route('admin.approvals.index')
            ->with('status', "Registration for '{$name}' has been rejected and removed.");
    }
}

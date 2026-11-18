<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all registered users with search and role filters.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->role($role);
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total_users' => User::count(),
            'instructors_count' => User::whereHas('roles', fn ($q) => $q->where('name', 'instructor'))->count(),
            'students_count' => User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->count(),
            'admins_count' => User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count(),
            'pending_approvals' => User::where('is_approved', false)->whereNotNull('email_verified_at')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show the form for creating a new user (Instructor, Student, or Admin).
     */
    public function create(): View
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active', true);
        $isApproved = $request->boolean('is_approved', true);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'is_active' => $isActive,
            'is_approved' => $isApproved,
            'approved_at' => $isApproved ? now() : null,
            'approved_by' => $isApproved ? auth()->id() : null,
            'email_verified_at' => now(), // Admin created users are pre-verified
            'locale' => 'en',
            'timezone' => 'Asia/Colombo',
        ]);

        $user->assignRole($validated['role']);

        $roleLabel = ucfirst($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('status', "User '{$user->name}' created successfully with role {$roleLabel}.");
    }

    /**
     * Show the form for editing an existing user.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $currentRole = $user->roles->first()?->name ?? 'student';

        return view('admin.users.edit', compact('user', 'roles', 'currentRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');
        $isApproved = $request->boolean('is_approved');

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'is_active' => $isActive,
            'is_approved' => $isApproved,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($isApproved && ! $user->is_approved) {
            $updateData['approved_at'] = now();
            $updateData['approved_by'] = auth()->id();
        }

        $user->update($updateData);

        // Sync updated role
        $user->roles()->sync([]);
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')
            ->with('status', "User '{$user->name}' updated successfully.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own active administrator account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', "User account for '{$userName}' has been deleted.");
    }
}

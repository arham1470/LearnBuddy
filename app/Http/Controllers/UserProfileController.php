<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    /**
     * Display the specified user profile.
     * Enforces least-privilege policy to prevent Insecure Direct Object Reference (IDOR).
     */
    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        return view('profile.show', [
            'user' => $user->load('roles'),
        ]);
    }
}

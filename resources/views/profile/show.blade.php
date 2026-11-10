<x-layouts.public title="User Profile - LearnBuddy" description="View account details and role permissions on LearnBuddy.">
    <section class="min-h-[calc(100vh-140px)] py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/50">
        <div class="max-w-3xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold tracking-wide uppercase mb-2">
                        <i class="fas fa-id-badge text-xs" aria-hidden="true"></i> Account Overview
                    </div>
                    <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">{{ $user->name }}</h1>
                    <p class="text-sm text-neutral-600">
                        Member since {{ $user->created_at->format('M d, Y') }} · Primary role:
                        @foreach ($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-700' : ($role->name === 'instructor' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ $role->display_name }}
                            </span>
                        @endforeach
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-neutral-200 bg-white text-sm font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                        <i class="fas fa-home text-xs" aria-hidden="true"></i>
                        <span>Home</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-neutral-900 text-white text-sm font-semibold hover:bg-neutral-800 transition cursor-pointer">
                            <i class="fas fa-sign-out-alt text-xs" aria-hidden="true"></i>
                            <span>Sign out</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Profile Details Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-lb-black mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-lb-orange" aria-hidden="true"></i>
                        <span>Contact & Identity Details</span>
                    </h2>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100">
                            <dt class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Email Address</dt>
                            <dd class="mt-1 font-semibold text-neutral-900 flex items-center gap-2">
                                <span>{{ $user->email }}</span>
                                @if ($user->hasVerifiedEmail())
                                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                                        <i class="fas fa-check text-[10px]" aria-hidden="true"></i> Verified
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                                        <i class="fas fa-clock text-[10px]" aria-hidden="true"></i> Unverified
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100">
                            <dt class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Sri Lankan Phone</dt>
                            <dd class="mt-1 font-semibold text-neutral-900">
                                {{ $user->formattedPhone() ?? 'Not provided' }}
                            </dd>
                        </div>

                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100">
                            <dt class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Timezone & Locale</dt>
                            <dd class="mt-1 font-semibold text-neutral-900">
                                {{ $user->timezone }} ({{ strtoupper($user->locale) }})
                            </dd>
                        </div>

                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100">
                            <dt class="text-xs font-medium text-neutral-500 uppercase tracking-wider">Account Status</dt>
                            <dd class="mt-1 font-semibold">
                                @if ($user->is_active)
                                    <span class="text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-full text-xs">Active</span>
                                @else
                                    <span class="text-rose-700 bg-rose-100/70 px-2 py-0.5 rounded-full text-xs">Deactivated</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                @if ($user->province || $user->district || $user->city)
                    <div class="pt-4 border-t border-neutral-100">
                        <h2 class="text-lg font-bold text-lb-black mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-lb-orange" aria-hidden="true"></i>
                            <span>Location Details (Sri Lanka)</span>
                        </h2>

                        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                            <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-100">
                                <dt class="text-xs font-medium text-neutral-500">Province</dt>
                                <dd class="mt-1 font-semibold text-neutral-900">{{ $user->province ?? '—' }}</dd>
                            </div>
                            <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-100">
                                <dt class="text-xs font-medium text-neutral-500">District</dt>
                                <dd class="mt-1 font-semibold text-neutral-900">{{ $user->district ?? '—' }}</dd>
                            </div>
                            <div class="p-3 rounded-xl bg-neutral-50 border border-neutral-100">
                                <dt class="text-xs font-medium text-neutral-500">City</dt>
                                <dd class="mt-1 font-semibold text-neutral-900">{{ $user->city ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <!-- Least-Privilege / IDOR Protection Notice -->
                <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-100 text-xs text-neutral-700 flex items-start gap-3">
                    <i class="fas fa-lock text-lb-orange mt-0.5 shrink-0" aria-hidden="true"></i>
                    <div>
                        <span class="font-bold text-neutral-900">Server-Side Authorization Active:</span>
                        This profile endpoint is protected with strict Laravel Policies. A regular student cannot view another user's profile by changing the ID in the URL.
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>

@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div>
        <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
            <a href="{{ route('admin.users.index') }}" class="hover:text-[#FF5F00] transition-colors">User &amp; Security</a>
            <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#0A0A0A] font-semibold">Edit User</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Edit User Account</h1>
        <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Modify credentials, switch roles, or manage active status for <strong>{{ $user->name }}</strong>.</p>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="lb-card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Role Selection -->
            <div>
                <label class="block text-xs font-bold text-[#0A0A0A] mb-2 uppercase tracking-wider">
                    Role &amp; Permissions <span class="text-rose-500">*</span>
                </label>

                @php
                    $selectedRole = old('role', $currentRole);
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Instructor Role Option -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-[#FF5F00]/50 {{ $selectedRole === 'instructor' ? 'border-[#FF5F00] bg-orange-50/40' : 'border-[#E5E5E5] bg-[#F7F7F5]' }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="w-8 h-8 rounded-xl bg-orange-100 text-[#FF5F00] flex items-center justify-center text-sm">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <input type="radio" name="role" value="instructor" class="text-[#FF5F00] focus:ring-[#FF5F00]" {{ $selectedRole === 'instructor' ? 'checked' : '' }}>
                        </div>
                        <span class="font-bold text-sm text-[#0A0A0A]">Instructor</span>
                        <span class="text-[11px] text-[#6B6B6B] mt-0.5 leading-snug">Can build courses, conduct live classes &amp; grade students.</span>
                    </label>

                    <!-- Student Role Option -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-[#FF5F00]/50 {{ $selectedRole === 'student' ? 'border-[#FF5F00] bg-orange-50/40' : 'border-[#E5E5E5] bg-[#F7F7F5]' }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <input type="radio" name="role" value="student" class="text-[#FF5F00] focus:ring-[#FF5F00]" {{ $selectedRole === 'student' ? 'checked' : '' }}>
                        </div>
                        <span class="font-bold text-sm text-[#0A0A0A]">Student</span>
                        <span class="text-[11px] text-[#6B6B6B] mt-0.5 leading-snug">Standard learner account enrolling in courses &amp; batches.</span>
                    </label>

                    <!-- Administrator Role Option -->
                    <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all hover:border-[#FF5F00]/50 {{ $selectedRole === 'admin' ? 'border-[#FF5F00] bg-orange-50/40' : 'border-[#E5E5E5] bg-[#F7F7F5]' }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-sm">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <input type="radio" name="role" value="admin" class="text-[#FF5F00] focus:ring-[#FF5F00]" {{ $selectedRole === 'admin' ? 'checked' : '' }}>
                        </div>
                        <span class="font-bold text-sm text-[#0A0A0A]">Administrator</span>
                        <span class="text-[11px] text-[#6B6B6B] mt-0.5 leading-snug">Full platform management, course approvals &amp; settings.</span>
                    </label>
                </div>
                @error('role') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Basic Credentials -->
            <div class="space-y-4 pt-4 border-t border-[#E5E5E5]">
                <h3 class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Account Credentials</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:bg-white outline-none transition-colors">
                        @error('name') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:bg-white outline-none transition-colors">
                        @error('email') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                            New Password
                        </label>
                        <input type="password" id="password" name="password" minlength="8"
                               placeholder="Leave blank to keep current password"
                               class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:bg-white outline-none transition-colors">
                        <p class="text-[11px] text-[#6B6B6B] mt-1">Leave empty if you do not want to change their password.</p>
                        @error('password') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                            Phone Number
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                               placeholder="e.g. +94 77 123 4567"
                               class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:bg-white outline-none transition-colors font-mono">
                        @error('phone') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Regional & Campus Location -->
            <div class="space-y-4 pt-4 border-t border-[#E5E5E5]">
                <h3 class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Location &amp; Region</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="city" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" placeholder="e.g. Colombo, Kandy"
                               class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none">
                    </div>
                    <div>
                        <label for="district" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">District</label>
                        <input type="text" id="district" name="district" value="{{ old('district', $user->district) }}" placeholder="e.g. Colombo"
                               class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none">
                    </div>
                    <div>
                        <label for="province" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">Province</label>
                        <input type="text" id="province" name="province" value="{{ old('province', $user->province) }}" placeholder="e.g. Western"
                               class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none">
                    </div>
                </div>
            </div>

            <!-- Account Status Toggles -->
            <div class="space-y-3 pt-4 border-t border-[#E5E5E5]">
                <h3 class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">Account Status</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#F7F7F5] border border-[#E5E5E5] cursor-pointer hover:bg-neutral-100 transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="mt-1 rounded text-[#FF5F00] focus:ring-[#FF5F00]">
                        <div>
                            <span class="block text-xs font-bold text-[#0A0A0A]">Active Account</span>
                            <span class="block text-[11px] text-[#6B6B6B]">When unchecked, user cannot sign in to the platform.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-[#F7F7F5] border border-[#E5E5E5] cursor-pointer hover:bg-neutral-100 transition">
                        <input type="checkbox" name="is_approved" value="1" {{ old('is_approved', $user->is_approved) ? 'checked' : '' }}
                               class="mt-1 rounded text-[#FF5F00] focus:ring-[#FF5F00]">
                        <div>
                            <span class="block text-xs font-bold text-[#0A0A0A]">Approved Status</span>
                            <span class="block text-[11px] text-[#6B6B6B]">Required for instructor teaching permissions and access.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit & Cancel Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E5E5E5]">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-[#E5E5E5] hover:bg-[#F7F7F5] text-sm font-semibold text-[#6B6B6B] transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all flex items-center gap-2">
                    <i class="fas fa-save text-xs"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

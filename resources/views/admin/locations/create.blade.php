@extends('layouts.admin')

@section('title', 'Add Campus Location')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div>
        <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
            <a href="{{ route('admin.batches.index') }}" class="hover:text-[#FF5F00] transition-colors">Campus &amp; Batches</a>
            <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('admin.locations.index') }}" class="hover:text-[#FF5F00] transition-colors">Campuses</a>
            <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#0A0A0A] font-semibold">New Campus</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Add Campus Location</h1>
        <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Register a new physical training center for in-person classroom batches.</p>
    </div>

    <!-- Form Card -->
    <div class="lb-card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.locations.store') }}" class="space-y-5">
            @csrf

            <!-- Name & Code -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Campus Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. LearnBuddy Colombo Central Campus"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                    @error('name') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="code" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Campus Code <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required
                           placeholder="e.g. CMB-01"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors font-mono uppercase">
                    @error('code') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Province, District, City -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="province" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Province <span class="text-rose-500">*</span>
                    </label>
                    <select id="province" name="province" required
                            class="w-full px-3 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] outline-none transition-colors">
                        <option value="">Select Province</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov }}" {{ old('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                    @error('province') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="district" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        District <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="district" name="district" value="{{ old('district') }}" required
                           placeholder="e.g. Colombo, Kandy"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                    @error('district') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="city" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        City <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" required
                           placeholder="e.g. Kollupitiya, Peradeniya"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                    @error('city') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Address Lines -->
            <div>
                <label for="address_line_1" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                    Address Line 1 <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="address_line_1" name="address_line_1" value="{{ old('address_line_1') }}" required
                       placeholder="e.g. No. 120, Galle Road"
                       class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                @error('address_line_1') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="address_line_2" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Address Line 2 (Optional)
                    </label>
                    <input type="text" id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}"
                           placeholder="e.g. Level 3, Technology Tower"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                </div>
                <div>
                    <label for="postal_code" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Postal Code
                    </label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                           placeholder="e.g. 00300"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                </div>
            </div>

            <!-- Contact Phone & Room Capacity -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="contact_phone" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Contact Phone
                    </label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
                           placeholder="e.g. +94 11 234 5678"
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                </div>
                <div>
                    <label for="max_room_capacity" class="block text-xs font-bold text-[#0A0A0A] mb-1.5 uppercase tracking-wider">
                        Room Seating Capacity <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="max_room_capacity" name="max_room_capacity" value="{{ old('max_room_capacity', '30') }}" min="1" max="500" required
                           class="w-full px-4 py-2.5 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-sm text-[#0A0A0A] focus:border-[#FF5F00] focus:ring-1 focus:ring-[#FF5F00] outline-none transition-colors">
                    @error('max_room_capacity') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Active Status -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-[#FF5F00] focus:ring-[#FF5F00] border-[#E5E5E5]">
                    <span class="text-sm font-semibold text-[#0A0A0A]">Active Campus (Available for course batches)</span>
                </label>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-[#E5E5E5] flex items-center justify-between">
                <a href="{{ route('admin.locations.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-[#E5E5E5] text-sm font-semibold text-[#6B6B6B] hover:text-[#0A0A0A] hover:bg-[#F7F7F5] transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#e65500] text-white text-sm font-bold shadow-md shadow-orange-500/25 transition-all">
                    Save Campus
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

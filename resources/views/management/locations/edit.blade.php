<x-layouts.public title="Edit Campus: {{ $location->name }} - LearnBuddy" description="Edit Sri Lankan physical campus details.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.locations.index') }}" class="hover:text-lb-orange">Campus Locations</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">Edit {{ $location->name }}</span>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold">Please correct the following errors:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">Edit Campus Location</h1>
                    <p class="text-xs text-neutral-500 mt-1">Code: <strong class="text-neutral-700 font-mono">{{ $location->code }}</strong></p>
                </div>

                <form action="{{ route('management.locations.update', $location) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Campus / Centre Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $location->name) }}"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="code" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Unique Location Code <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="code"
                                id="code"
                                value="{{ old('code', $location->code) }}"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange uppercase font-mono"
                            >
                        </div>
                    </div>

                    <!-- Sri Lankan Administrative Hierarchy -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="province" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Province <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="province"
                                id="province"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange bg-white"
                            >
                                @foreach ($provinces as $prov)
                                    <option value="{{ $prov }}" {{ old('province', $location->province) === $prov ? 'selected' : '' }}>
                                        {{ $prov }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="district" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                District <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="district"
                                id="district"
                                value="{{ old('district', $location->district) }}"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                City / Town <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="city"
                                id="city"
                                value="{{ old('city', $location->city) }}"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <!-- Address Lines -->
                    <div class="space-y-3">
                        <div>
                            <label for="address_line_1" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Address Line 1 <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="address_line_1"
                                id="address_line_1"
                                value="{{ old('address_line_1', $location->address_line_1) }}"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="address_line_2" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Address Line 2 (Floor / Building / Unit)
                            </label>
                            <input
                                type="text"
                                name="address_line_2"
                                id="address_line_2"
                                value="{{ old('address_line_2', $location->address_line_2) }}"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <!-- Contact & Capacity Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="postal_code" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Postal Code
                            </label>
                            <input
                                type="text"
                                name="postal_code"
                                id="postal_code"
                                value="{{ old('postal_code', $location->postal_code) }}"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Campus Phone
                            </label>
                            <input
                                type="text"
                                name="contact_phone"
                                id="contact_phone"
                                value="{{ old('contact_phone', $location->contact_phone) }}"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="max_room_capacity" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Room Capacity (Seats) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="max_room_capacity"
                                id="max_room_capacity"
                                value="{{ old('max_room_capacity', $location->max_room_capacity) }}"
                                min="1"
                                max="500"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="google_map_embed_url" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Google Maps Embed Link (Optional)
                        </label>
                        <input
                            type="text"
                            name="google_map_embed_url"
                            id="google_map_embed_url"
                            value="{{ old('google_map_embed_url', $location->google_map_embed_url) }}"
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >
                    </div>

                    <div class="pt-4 border-t border-neutral-100">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }} class="rounded border-neutral-300 text-lb-orange focus:ring-lb-orange">
                            <span>Campus is Active</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-neutral-100 flex items-center justify-end gap-3">
                        <a
                            href="{{ route('management.locations.index') }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            Update Campus Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>

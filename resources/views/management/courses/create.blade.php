<x-layouts.public title="Create Course - LearnBuddy" description="Author and configure a new course draft.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumbs -->
            <div class="mb-4 flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">New Course Draft</span>
            </div>

            <!-- Page Title -->
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Create Course Draft</h1>
                <p class="text-sm text-neutral-600 mt-1">
                    Enter course details, learning objectives, instructor assignments, and delivery modes.
                </p>
            </div>

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold">Please resolve the following errors:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('management.courses.store') }}" enctype="multipart/form-data" class="space-y-8" novalidate>
                @csrf

                <!-- Section 1: Core Details -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-5">
                    <div class="border-b border-neutral-100 pb-4">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-lb-orange" aria-hidden="true"></i>
                            <span>1. Core Course Information</span>
                        </h2>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                            Course Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="e.g. Modern Full-Stack Web Development with Laravel & Vue"
                            class="w-full px-4 py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @error('title') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                        >
                        @error('title')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label for="subtitle" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                            Short Subtitle / Tagline
                        </label>
                        <input
                            id="subtitle"
                            type="text"
                            name="subtitle"
                            value="{{ old('subtitle') }}"
                            placeholder="e.g. Master backend architecture, APIs, and real-world database design in Sri Lanka."
                            class="w-full px-4 py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >
                    </div>

                    <!-- Category & Difficulty Level -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                                Category <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="category_id"
                                name="category_id"
                                required
                                class="w-full px-4 py-3 rounded-2xl border text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @error('category_id') border-rose-400 @else border-neutral-200 bg-neutral-50/30 @enderror"
                            >
                                <option value="">Select a category...</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="difficulty_level" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                                Difficulty Level <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="difficulty_level"
                                name="difficulty_level"
                                required
                                class="w-full px-4 py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            >
                                <option value="all_levels" {{ old('difficulty_level') === 'all_levels' ? 'selected' : '' }}>All Levels</option>
                                <option value="beginner" {{ old('difficulty_level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty_level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty_level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                    </div>

                    <!-- Language & Duration Hours -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="language" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                                Instruction Language <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="language"
                                type="text"
                                name="language"
                                value="{{ old('language', 'English') }}"
                                required
                                placeholder="English, Sinhala, Tamil..."
                                class="w-full px-4 py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            >
                        </div>

                        <div>
                            <label for="duration_hours" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                                Estimated Duration (Hours) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="duration_hours"
                                type="number"
                                step="0.5"
                                min="0"
                                name="duration_hours"
                                value="{{ old('duration_hours', '12.0') }}"
                                required
                                class="w-full px-4 py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                            Detailed Course Description <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            required
                            placeholder="Provide a comprehensive syllabus overview, teaching methodology, and industry relevance..."
                            class="w-full px-4 py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @error('description') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thumbnail Image Upload -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                            Course Thumbnail Image
                        </label>
                        <input
                            id="thumbnail"
                            type="file"
                            name="thumbnail"
                            accept="image/jpeg,image/png,image/webp"
                            class="w-full px-4 py-2.5 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-100 file:text-lb-orange hover:file:bg-orange-200 cursor-pointer"
                        >
                        <p class="mt-1 text-xs text-neutral-500">Max size: 2MB. Formats: JPEG, PNG, or WebP. 16:9 ratio recommended.</p>
                        @error('thumbnail')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section 2: Authorized Instructors -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                    <div class="border-b border-neutral-100 pb-4">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-chalkboard-teacher text-lb-orange" aria-hidden="true"></i>
                            <span>2. Authorised Faculty & Instructors</span>
                        </h2>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <p class="text-xs text-neutral-500">Select which instructors are permitted to author and manage content for this course:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            @foreach ($instructors as $inst)
                                <label class="flex items-center gap-3 p-3 rounded-2xl border border-neutral-200 hover:border-neutral-300 hover:bg-neutral-50/60 cursor-pointer transition select-none">
                                    <input
                                        type="checkbox"
                                        name="instructor_ids[]"
                                        value="{{ $inst->id }}"
                                        {{ in_array($inst->id, old('instructor_ids', [auth()->id()])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-lb-orange focus:ring-lb-orange"
                                    >
                                    <div>
                                        <p class="text-xs font-bold text-neutral-900">{{ $inst->name }}</p>
                                        <p class="text-[11px] text-neutral-500">{{ $inst->email }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200 flex items-center gap-3">
                            <i class="fas fa-user-check text-emerald-600 text-lg" aria-hidden="true"></i>
                            <div>
                                <p class="text-xs font-bold text-neutral-900">{{ auth()->user()->name }} (Primary Lead Instructor)</p>
                                <p class="text-[11px] text-neutral-500">You will be designated as the primary authorized instructor for this course.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Section 3: Learning Outcomes & Prerequisites -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-5">
                    <div class="border-b border-neutral-100 pb-4">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-list-check text-lb-orange" aria-hidden="true"></i>
                            <span>3. Learning Outcomes & Prerequisites</span>
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-neutral-800 mb-1.5">
                                Key Learning Outcomes (1 per line)
                            </label>
                            <textarea
                                name="learning_outcomes[]"
                                rows="4"
                                placeholder="Build robust RESTful APIs&#10;Design normalized MySQL schemas&#10;Implement secure auth with 2FA"
                                class="w-full px-4 py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            >{{ old('learning_outcomes.0') }}</textarea>
                            <p class="mt-1 text-[11px] text-neutral-500">Key skills students will acquire upon completion.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-neutral-800 mb-1.5">
                                Course Prerequisites (1 per line)
                            </label>
                            <textarea
                                name="prerequisites[]"
                                rows="4"
                                placeholder="Basic knowledge of HTML & CSS&#10;Basic object-oriented programming concepts"
                                class="w-full px-4 py-3 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            >{{ old('prerequisites.0') }}</textarea>
                            <p class="mt-1 text-[11px] text-neutral-500">Prior knowledge or equipment required.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Delivery Options & Sri Lankan Pricing -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6" x-data="{
                    online: {{ in_array('online', old('delivery_modes', ['online'])) ? 'true' : 'false' }},
                    physical: {{ in_array('physical', old('delivery_modes', [])) ? 'true' : 'false' }},
                    hybrid: {{ in_array('hybrid', old('delivery_modes', [])) ? 'true' : 'false' }}
                }">
                    <div class="border-b border-neutral-100 pb-4">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-boxes-stacked text-lb-orange" aria-hidden="true"></i>
                            <span>4. Multi-Delivery Modes & Independent Pricing (LKR)</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-1">
                            LearnBuddy allows a single course record to offer Online, Physical Classroom, or Both delivery options with independent prices and capacity rules.
                        </p>
                    </div>

                    <!-- Delivery Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-start gap-3 p-4 rounded-2xl border border-neutral-200 cursor-pointer select-none transition" :class="online ? 'border-lb-orange bg-orange-50/40' : 'hover:bg-neutral-50'">
                            <input
                                type="checkbox"
                                name="delivery_modes[]"
                                value="online"
                                x-model="online"
                                class="w-4 h-4 mt-0.5 rounded text-lb-orange focus:ring-lb-orange"
                            >
                            <div>
                                <span class="text-sm font-bold text-neutral-900">Online Self-Paced</span>
                                <p class="text-[11px] text-neutral-500 mt-0.5">Recorded lessons, resources, and online certificates.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 rounded-2xl border border-neutral-200 cursor-pointer select-none transition" :class="physical ? 'border-lb-orange bg-orange-50/40' : 'hover:bg-neutral-50'">
                            <input
                                type="checkbox"
                                name="delivery_modes[]"
                                value="physical"
                                x-model="physical"
                                class="w-4 h-4 mt-0.5 rounded text-lb-orange focus:ring-lb-orange"
                            >
                            <div>
                                <span class="text-sm font-bold text-neutral-900">Physical Classroom</span>
                                <p class="text-[11px] text-neutral-500 mt-0.5">Campus venues, scheduled batches, and seat capacity limits.</p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-4 rounded-2xl border border-neutral-200 cursor-pointer select-none transition" :class="hybrid ? 'border-lb-orange bg-orange-50/40' : 'hover:bg-neutral-50'">
                            <input
                                type="checkbox"
                                name="delivery_modes[]"
                                value="hybrid"
                                x-model="hybrid"
                                class="w-4 h-4 mt-0.5 rounded text-lb-orange focus:ring-lb-orange"
                            >
                            <div>
                                <span class="text-sm font-bold text-neutral-900">Hybrid Cohort</span>
                                <p class="text-[11px] text-neutral-500 mt-0.5">Live video sessions + weekend campus practicals.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Online Configuration Fields -->
                    <div x-show="online" x-transition class="p-5 rounded-2xl bg-neutral-50/80 border border-neutral-200 space-y-4">
                        <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-globe text-blue-600" aria-hidden="true"></i> Online Delivery Configuration
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                    Price in LKR (Enter 0 for Free)
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-neutral-500 pointer-events-none">LKR</span>
                                    <input
                                        type="number"
                                        step="100"
                                        min="0"
                                        name="online_price"
                                        value="{{ old('online_price', '5000') }}"
                                        class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-neutral-200 bg-white text-xs font-bold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    >
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                    Access Period (Days, leave empty for Lifetime)
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    name="online_access_days"
                                    value="{{ old('online_access_days') }}"
                                    placeholder="e.g. 365"
                                    class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-white text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Physical Classroom Configuration Fields -->
                    <div x-show="physical" x-transition class="p-5 rounded-2xl bg-neutral-50/80 border border-neutral-200 space-y-4">
                        <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-building text-amber-600" aria-hidden="true"></i> Physical Classroom Configuration
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                    Price in LKR
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-neutral-500 pointer-events-none">LKR</span>
                                    <input
                                        type="number"
                                        step="100"
                                        min="0"
                                        name="physical_price"
                                        value="{{ old('physical_price', '15000') }}"
                                        class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-neutral-200 bg-white text-xs font-bold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                    Max Seat Capacity per Batch
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    name="physical_max_capacity"
                                    value="{{ old('physical_max_capacity', '25') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-white text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                    Campus Location
                                </label>
                                <select
                                    name="physical_location_id"
                                    class="w-full px-3 py-2.5 rounded-xl border border-neutral-200 bg-white text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                                    <option value="">Select campus / venue...</option>
                                    @foreach ($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ (string) old('physical_location_id') === (string) $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }} ({{ $loc->city }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Hybrid Configuration Fields -->
                    <div x-show="hybrid" x-transition class="p-5 rounded-2xl bg-neutral-50/80 border border-neutral-200 space-y-4">
                        <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-layer-group text-purple-600" aria-hidden="true"></i> Hybrid Cohort Configuration
                        </h3>
                        <div>
                            <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                Hybrid Price in LKR
                            </label>
                            <div class="relative max-w-xs">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-neutral-500 pointer-events-none">LKR</span>
                                <input
                                    type="number"
                                    step="100"
                                    min="0"
                                    name="hybrid_price"
                                    value="{{ old('hybrid_price', '12000') }}"
                                    class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-neutral-200 bg-white text-xs font-bold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center justify-between pt-4">
                    <a
                        href="{{ route('management.courses.index') }}"
                        class="px-6 py-3 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-100 transition"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="px-8 py-3.5 rounded-full bg-lb-orange text-white text-sm font-semibold hover:bg-[#E65500] shadow-sm transition cursor-pointer"
                    >
                        Create Course Draft
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.public>

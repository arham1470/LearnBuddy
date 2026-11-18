<x-layouts.public title="Edit {{ $course->title }} - LearnBuddy" description="Edit course curriculum, settings, and delivery options.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumbs -->
            <div class="mb-4 flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.show', $course) }}" class="hover:text-lb-orange truncate max-w-xs">{{ $course->title }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Edit</span>
            </div>

            <!-- Page Title -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Edit Course</h1>
                    <p class="text-sm text-neutral-600 mt-1">
                        Status: <span class="font-bold uppercase text-xs">{{ $course->status }}</span>
                    </p>
                </div>

                <a href="{{ route('management.courses.show', $course) }}" class="px-4 py-2 rounded-full border border-neutral-200 bg-white text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition">
                    View Course Details
                </a>
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

            @php
                $canEditCritical = auth()->user()->can('updateCriticalFields', $course);
            @endphp

            @if ($course->isPublished())
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-3">
                    <i class="fas fa-check-circle text-emerald-600 text-base mt-0.5 shrink-0"></i>
                    <div>
                        <p class="font-bold">This Course is Published &amp; Live</p>
                        <p class="text-emerald-800 mt-0.5 leading-relaxed">
                            @if ($canEditCritical)
                                You have Administrator privileges and may edit all fields including titles and pricing.
                            @else
                                As an instructor, you can update lessons, video lectures, assignments, quizzes, batches, descriptions, learning outcomes, and media. Core title and base pricing tiers are locked to safeguard existing student enrollments.
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('management.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-8" novalidate>
                @csrf
                @method('PUT')

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
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="title" class="block text-sm font-semibold text-neutral-800">
                                Course Title <span class="text-rose-500">*</span>
                            </label>
                            @if (! $canEditCritical)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold">
                                    <i class="fas fa-lock text-[9px]"></i>
                                    <span>Locked for Published Course</span>
                                </span>
                            @endif
                        </div>
                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title', $course->title) }}"
                            required
                            @if (! $canEditCritical) readonly @endif
                            class="w-full px-4 py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @if(! $canEditCritical) bg-neutral-100 text-neutral-600 cursor-not-allowed border-neutral-200 @elseif($errors->has('title')) border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @endif"
                        >
                        @if (! $canEditCritical)
                            <p class="text-[11px] text-neutral-500 mt-1">Course titles are locked once published to preserve enrollment records and digital certificates. Contact an administrator for title changes.</p>
                        @endif
                    </div>

                    <!-- Slug -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="slug" class="block text-sm font-semibold text-neutral-800">
                                URL Slug
                            </label>
                            @if (! $canEditCritical)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold">
                                    <i class="fas fa-lock text-[9px]"></i>
                                    <span>Permanent URL</span>
                                </span>
                            @endif
                        </div>
                        <input
                            id="slug"
                            type="text"
                            name="slug"
                            value="{{ old('slug', $course->slug) }}"
                            @if (! $canEditCritical) readonly @endif
                            class="w-full px-4 py-3 rounded-2xl border text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @if(! $canEditCritical) bg-neutral-100 text-neutral-600 cursor-not-allowed border-neutral-200 @else border-neutral-200 bg-neutral-50/30 @endif"
                        >
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
                            value="{{ old('subtitle', $course->subtitle) }}"
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
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string) old('category_id', $course->category_id) === (string) $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
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
                                <option value="all_levels" {{ old('difficulty_level', $course->difficulty_level) === 'all_levels' ? 'selected' : '' }}>All Levels</option>
                                <option value="beginner" {{ old('difficulty_level', $course->difficulty_level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty_level', $course->difficulty_level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty_level', $course->difficulty_level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
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
                                value="{{ old('language', $course->language) }}"
                                required
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
                                value="{{ old('duration_hours', $course->duration_hours) }}"
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
                            class="w-full px-4 py-3 rounded-2xl border text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @error('description') border-rose-400 bg-rose-50/20 @else border-neutral-200 bg-neutral-50/30 @enderror"
                        >{{ old('description', $course->description) }}</textarea>
                    </div>

                    <!-- Thumbnail Image Upload -->
                    <div>
                        <label for="thumbnail" class="block text-sm font-semibold text-neutral-800 mb-1.5">
                            Change Course Thumbnail
                        </label>
                        @if ($course->thumbnail_url)
                            <div class="mb-3 flex items-center gap-4">
                                <img
                                    src="{{ $course->thumbnail_url }}"
                                    alt="Current thumbnail"
                                    class="w-24 h-16 rounded-xl object-cover border border-neutral-200"
                                    onerror="this.style.display='none';"
                                >
                                <span class="text-xs text-neutral-500">Current active thumbnail</span>
                            </div>
                        @endif
                        <input
                            id="thumbnail"
                            type="file"
                            name="thumbnail"
                            accept="image/jpeg,image/png,image/webp"
                            class="w-full px-4 py-2.5 rounded-2xl border border-neutral-200 bg-neutral-50/30 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-100 file:text-lb-orange hover:file:bg-orange-200 cursor-pointer"
                        >
                    </div>
                </div>

                <!-- Section 2: Instructors (Admin only edit) -->
                @if (auth()->user()->isAdmin())
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-4">
                        <div class="border-b border-neutral-100 pb-4">
                            <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-chalkboard-teacher text-lb-orange" aria-hidden="true"></i>
                                <span>2. Authorised Faculty & Instructors</span>
                            </h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            @php
                                $assignedInstructorIds = $course->instructors->pluck('id')->all();
                            @endphp
                            @foreach ($instructors as $inst)
                                <label class="flex items-center gap-3 p-3 rounded-2xl border border-neutral-200 hover:border-neutral-300 hover:bg-neutral-50/60 cursor-pointer transition select-none">
                                    <input
                                        type="checkbox"
                                        name="instructor_ids[]"
                                        value="{{ $inst->id }}"
                                        {{ in_array($inst->id, old('instructor_ids', $assignedInstructorIds)) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-lb-orange focus:ring-lb-orange"
                                    >
                                    <div>
                                        <p class="text-xs font-bold text-neutral-900">{{ $inst->name }}</p>
                                        <p class="text-[11px] text-neutral-500">{{ $inst->email }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Section 3: Delivery Options & Pricing -->
                @php
                    $activeModes = $course->deliveryOptions->pluck('delivery_mode')->all();
                    $onlineOpt = $course->deliveryOptions->firstWhere('delivery_mode', 'online');
                    $physicalOpt = $course->deliveryOptions->firstWhere('delivery_mode', 'physical');
                    $hybridOpt = $course->deliveryOptions->firstWhere('delivery_mode', 'hybrid');
                @endphp
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6" x-data="{
                    online: {{ in_array('online', old('delivery_modes', $activeModes)) ? 'true' : 'false' }},
                    physical: {{ in_array('physical', old('delivery_modes', $activeModes)) ? 'true' : 'false' }},
                    hybrid: {{ in_array('hybrid', old('delivery_modes', $activeModes)) ? 'true' : 'false' }}
                }">
                    <div class="border-b border-neutral-100 pb-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-boxes-stacked text-lb-orange" aria-hidden="true"></i>
                                <span>3. Multi-Delivery Modes &amp; Pricing (LKR)</span>
                            </h2>
                            @if (! $canEditCritical)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold">
                                    <i class="fas fa-lock text-[9px]"></i>
                                    <span>Pricing Locked</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    @if (! $canEditCritical)
                        <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
                            <i class="fas fa-lock text-amber-600 text-base mt-0.5 shrink-0"></i>
                            <div>
                                <p class="font-bold">Base Pricing &amp; Delivery Modes are Locked for Published Courses</p>
                                <p class="text-[11px] text-amber-800 mt-0.5 leading-relaxed">
                                    Base delivery modes and pricing cannot be altered directly once published to protect existing student enrollments. If you need to introduce new batch pricing or adjust tiers, please coordinate with a platform administrator.
                                </p>
                            </div>
                        </div>

                        {{-- Hidden inputs ensure validation passes and existing modes remain untouched --}}
                        @foreach ($activeModes as $mode)
                            <input type="hidden" name="delivery_modes[]" value="{{ $mode }}">
                        @endforeach
                        @if ($onlineOpt)
                            <input type="hidden" name="online_price" value="{{ $onlineOpt->price }}">
                            <input type="hidden" name="online_access_days" value="{{ $onlineOpt->access_period_days }}">
                        @endif
                        @if ($physicalOpt)
                            <input type="hidden" name="physical_price" value="{{ $physicalOpt->price }}">
                            <input type="hidden" name="physical_max_capacity" value="{{ $physicalOpt->max_capacity }}">
                            <input type="hidden" name="physical_location_id" value="{{ $physicalOpt->batches()->first()?->location_id }}">
                        @endif
                        @if ($hybridOpt)
                            <input type="hidden" name="hybrid_price" value="{{ $hybridOpt->price }}">
                        @endif
                    @endif

                    <!-- Delivery Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 @if(! $canEditCritical) opacity-70 pointer-events-none @endif">
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
                                <p class="text-[11px] text-neutral-500 mt-0.5">On-demand recorded lessons and resources.</p>
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
                                <p class="text-[11px] text-neutral-500 mt-0.5">Campus venues and scheduled batches.</p>
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
                                <p class="text-[11px] text-neutral-500 mt-0.5">Live sessions + classroom practicals.</p>
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
                                        value="{{ old('online_price', $onlineOpt?->price ?? '0') }}"
                                        @if(! $canEditCritical) readonly @endif
                                        class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-bold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @if(! $canEditCritical) bg-neutral-100 text-neutral-600 cursor-not-allowed @else bg-white @endif"
                                    >
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                    Access Period (Days)
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    name="online_access_days"
                                    value="{{ old('online_access_days', $onlineOpt?->access_period_days) }}"
                                    @if(! $canEditCritical) readonly @endif
                                    class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @if(! $canEditCritical) bg-neutral-100 text-neutral-600 cursor-not-allowed @else bg-white @endif"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Physical Configuration Fields -->
                    <div x-show="physical" x-transition class="p-5 rounded-2xl bg-neutral-50/80 border border-neutral-200 space-y-4">
                        <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-building text-amber-600" aria-hidden="true"></i> Physical Classroom Configuration
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                        value="{{ old('physical_price', $physicalOpt?->price ?? '15000') }}"
                                        @if(! $canEditCritical) readonly @endif
                                        class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-bold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @if(! $canEditCritical) bg-neutral-100 text-neutral-600 cursor-not-allowed @else bg-white @endif"
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
                                    value="{{ old('physical_max_capacity', $physicalOpt?->max_capacity ?? '30') }}"
                                    @if(! $canEditCritical) readonly @endif
                                    class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @if(! $canEditCritical) bg-neutral-100 text-neutral-600 cursor-not-allowed @else bg-white @endif"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Hybrid Configuration Fields -->
                    <div x-show="hybrid" x-transition class="p-5 rounded-2xl bg-neutral-50/80 border border-neutral-200 space-y-4">
                        <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fas fa-layer-group text-purple-600" aria-hidden="true"></i> Hybrid Cohort Configuration
                        </h3>
                        <div class="max-w-xs">
                            <label class="block text-xs font-semibold text-neutral-700 mb-1">
                                Hybrid Price in LKR
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-neutral-500 pointer-events-none">LKR</span>
                                <input
                                    type="number"
                                    step="100"
                                    min="0"
                                    name="hybrid_price"
                                    value="{{ old('hybrid_price', $hybridOpt?->price ?? '12000') }}"
                                    @if(! $canEditCritical) readonly @endif
                                    class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-neutral-200 text-xs font-bold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange @if(! $canEditCritical) bg-neutral-100 text-neutral-600 cursor-not-allowed @else bg-white @endif"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-4">
                    <a
                        href="{{ route('management.courses.show', $course) }}"
                        class="px-6 py-3 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-100 transition"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="px-8 py-3.5 rounded-full bg-lb-orange text-white text-sm font-semibold hover:bg-[#E65500] shadow-sm transition cursor-pointer"
                    >
                        Save Course Changes
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.public>

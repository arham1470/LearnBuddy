<x-layouts.public title="Schedule Live Class - {{ $course->title }} - LearnBuddy" description="Schedule an interactive live video class session.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.live-classes.index', $course) }}" class="hover:text-lb-orange">Live Classes</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Schedule Session</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold uppercase tracking-wide mb-2">
                    <i class="fas fa-calendar-plus text-xs" aria-hidden="true"></i> Live Video Class
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                    Schedule Interactive Class Session
                </h1>
                <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                    Set up an interactive live session for <strong>{{ $course->title }}</strong> using secure meeting rooms.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('management.courses.live-classes.store', $course) }}" class="space-y-6">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Session Title / Topic <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="e.g. Live Q&A Workshop: Laravel Eloquent Optimization & Indexing"
                            class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            required
                        >
                        @error('title')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description / Agenda -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Session Agenda &amp; Learning Objectives
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Provide a clear agenda, prerequisites, or topics to prepare in advance..."
                            class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Host Instructor & Target Audience -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="instructor_id" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Host Instructor <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="instructor_id"
                                name="instructor_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
                                @foreach ($instructors as $inst)
                                    <option value="{{ $inst->id }}" {{ old('instructor_id', auth()->id()) == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }} ({{ $inst->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="batch_id" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Target Batch (Optional)
                            </label>
                            <select
                                id="batch_id"
                                name="batch_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            >
                                <option value="">-- All Active Course Enrolments --</option>
                                @foreach ($batches as $b)
                                    <option value="{{ $b->id }}" {{ old('batch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->batch_code }} &bull; {{ $b->batch_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-neutral-400 mt-1">Leave empty to include all enrolled students in the course.</p>
                            @error('batch_id')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Scheduled Timings (Asia/Colombo) -->
                    <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-neutral-700 uppercase tracking-wider">
                                Class Timing &amp; Duration <span class="text-rose-500">*</span>
                            </span>
                            <span class="text-[10px] font-bold text-neutral-500 uppercase bg-neutral-200 px-2 py-0.5 rounded">
                                Timezone: Asia/Colombo
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="scheduled_start_time" class="block text-[11px] font-semibold text-neutral-600 mb-1">
                                    Start Date &amp; Time <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    id="scheduled_start_time"
                                    name="scheduled_start_time"
                                    value="{{ old('scheduled_start_time', now()->addDay()->setHour(18)->setMinute(0)->format('Y-m-d\TH:i')) }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    required
                                >
                                @error('scheduled_start_time')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="scheduled_end_time" class="block text-[11px] font-semibold text-neutral-600 mb-1">
                                    End Date &amp; Time <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    id="scheduled_end_time"
                                    name="scheduled_end_time"
                                    value="{{ old('scheduled_end_time', now()->addDay()->setHour(20)->setMinute(0)->format('Y-m-d\TH:i')) }}"
                                    class="w-full px-3 py-2 rounded-xl border border-neutral-200 bg-white text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                    required
                                >
                                @error('scheduled_end_time')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Access Permissions & Security -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="access_permission" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Access Permissions <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="access_permission"
                                name="access_permission"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                                required
                            >
                                <option value="enrolled_students" {{ old('access_permission', 'enrolled_students') === 'enrolled_students' ? 'selected' : '' }}>
                                    All Active Enrolled Students
                                </option>
                                <option value="batch_students_only" {{ old('access_permission') === 'batch_students_only' ? 'selected' : '' }}>
                                    Target Batch Enrollees Only
                                </option>
                                <option value="instructors_only" {{ old('access_permission') === 'instructors_only' ? 'selected' : '' }}>
                                    Instructors &amp; Faculty Only (Dry Run)
                                </option>
                            </select>
                            @error('access_permission')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meeting_password" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Meeting Passcode (Optional)
                            </label>
                            <input
                                type="text"
                                id="meeting_password"
                                name="meeting_password"
                                value="{{ old('meeting_password') }}"
                                placeholder="Optional extra passcode"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            >
                            <p class="text-[10px] text-neutral-400 mt-1">Never shared with unauthorised students.</p>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="pt-4 flex items-center justify-between border-t border-neutral-100">
                        <a
                            href="{{ route('management.courses.live-classes.index', $course) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition cursor-pointer"
                        >
                            Schedule Live Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>

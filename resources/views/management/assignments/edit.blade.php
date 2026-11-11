<x-layouts.public title="Edit Assignment: {{ $assignment->title }} - LearnBuddy" description="Edit assignment details and requirements.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.assignments.index', $course) }}" class="hover:text-lb-orange">Assignments</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold truncate">Edit {{ $assignment->title }}</span>
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
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">Edit Assignment</h1>
                    <p class="text-xs text-neutral-500 mt-1">Course: <strong class="text-neutral-700">{{ $course->title }}</strong></p>
                </div>

                <form action="{{ route('management.courses.assignments.update', [$course, $assignment]) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="title" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Assignment Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title', $assignment->title) }}"
                            required
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Short Summary
                        </label>
                        <input
                            type="text"
                            name="description"
                            id="description"
                            value="{{ old('description', $assignment->description) }}"
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >
                    </div>

                    <div>
                        <label for="instructions" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Detailed Instructions &amp; Requirements
                        </label>
                        <textarea
                            name="instructions"
                            id="instructions"
                            rows="6"
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >{{ old('instructions', $assignment->instructions) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="max_points" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Maximum Marks <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="max_points"
                                id="max_points"
                                value="{{ old('max_points', $assignment->max_points) }}"
                                min="1"
                                max="1000"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="passing_points" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Passing Marks
                            </label>
                            <input
                                type="number"
                                name="passing_points"
                                id="passing_points"
                                value="{{ old('passing_points', $assignment->passing_points) }}"
                                min="1"
                                max="1000"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="due_date" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Due Date (SL Time)
                            </label>
                            <input
                                type="datetime-local"
                                name="due_date"
                                id="due_date"
                                value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="allowed_file_types" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Allowed Formats
                            </label>
                            <input
                                type="text"
                                name="allowed_file_types"
                                id="allowed_file_types"
                                value="{{ old('allowed_file_types', $assignment->allowed_file_types) }}"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="max_file_size_mb" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Max Size (MB) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="max_file_size_mb"
                                id="max_file_size_mb"
                                value="{{ old('max_file_size_mb', $assignment->max_file_size_mb) }}"
                                min="1"
                                max="100"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $assignment->is_published) ? 'checked' : '' }} class="rounded border-neutral-300 text-lb-orange focus:ring-lb-orange">
                            <span>Publish Assignment</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                            <input type="checkbox" name="allow_late_submissions" value="1" {{ old('allow_late_submissions', $assignment->allow_late_submissions) ? 'checked' : '' }} class="rounded border-neutral-300 text-lb-orange focus:ring-lb-orange">
                            <span>Allow late submissions after due date</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-neutral-100 flex items-center justify-end gap-3">
                        <a
                            href="{{ route('management.courses.assignments.index', $course) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            Update Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>

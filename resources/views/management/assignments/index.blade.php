<x-layouts.public title="Course Assignments (Management) - LearnBuddy" description="Manage course assignments, due dates, and student submissions.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.show', $course) }}" class="hover:text-lb-orange truncate">{{ $course->title }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Assignments</span>
            </div>

            <!-- Flash Alert -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">
                        Course Assignments
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Create, review and grade assignments for <strong class="text-neutral-700">{{ $course->title }}</strong>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('management.courses.show', $course) }}"
                        class="px-4 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition"
                    >
                        Back to Course
                    </a>

                    <a
                        href="{{ route('management.courses.assignments.create', $course) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                    >
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        <span>New Assignment</span>
                    </a>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                @if ($assignments->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-4">Title</th>
                                    <th class="p-4">Due Date</th>
                                    <th class="p-4">Max Points</th>
                                    <th class="p-4">Submissions</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($assignments as $assignment)
                                    <tr class="hover:bg-neutral-50/50">
                                        <td class="p-4">
                                            <div class="font-bold text-neutral-900 text-sm">{{ $assignment->title }}</div>
                                            <div class="text-[11px] text-neutral-400">
                                                Allowed: {{ $assignment->allowed_file_types }} (Max {{ $assignment->max_file_size_mb }}MB)
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            @if ($assignment->due_date)
                                                <span class="font-semibold text-neutral-800">{{ $assignment->due_date->timezone('Asia/Colombo')->format('M d, Y · h:i A') }}</span>
                                                @if ($assignment->isPastDue())
                                                    <span class="block text-[10px] text-rose-600 font-bold">Past Due</span>
                                                @endif
                                            @else
                                                <span class="text-neutral-400">No deadline</span>
                                            @endif
                                        </td>
                                        <td class="p-4 font-bold text-neutral-800">
                                            {{ $assignment->max_points }} pts
                                        </td>
                                        <td class="p-4">
                                            <a
                                                href="{{ route('management.courses.assignments.submissions', [$course, $assignment]) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-lb-orange font-bold hover:bg-orange-100 transition"
                                            >
                                                <i class="fas fa-inbox text-[10px]" aria-hidden="true"></i>
                                                <span>{{ $assignment->submissions_count }} submissions</span>
                                            </a>
                                        </td>
                                        <td class="p-4">
                                            @if ($assignment->is_published)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                    Published
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-600">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <a
                                                href="{{ route('management.courses.assignments.edit', [$course, $assignment]) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl border border-neutral-300 text-neutral-700 hover:bg-neutral-50 font-semibold"
                                            >
                                                Edit
                                            </a>

                                            <button
                                                type="button"
                                                data-item="{{ $assignment->title }}"
                                                data-action="{{ route('management.courses.assignments.destroy', [$course, $assignment]) }}"
                                                onclick="confirmDialog({
                                                    title: 'Delete Assignment',
                                                    itemName: this.dataset.item,
                                                    message: 'Are you sure you want to delete this assignment? All associated student submissions and grading records will be deleted.',
                                                    action: this.dataset.action,
                                                    method: 'DELETE',
                                                    confirmText: 'Delete Assignment',
                                                    confirmVariant: 'danger'
                                                })"
                                                class="inline-flex items-center px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-semibold cursor-pointer"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto text-xl">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-sm font-bold text-neutral-800">No Assignments Created Yet</h3>
                        <p class="text-xs text-neutral-500 max-w-sm mx-auto">
                            Add assignments to evaluate student projects, practical coursework, and homework.
                        </p>
                        <a
                            href="{{ route('management.courses.assignments.create', $course) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            <span>Create First Assignment</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>

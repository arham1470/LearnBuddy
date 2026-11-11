<x-layouts.public :title="'Announcements: ' . $course->title . ' · LearnBuddy'">
    <!-- HEADER -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-10 sm:pb-14 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6">
            <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('courses.index') }}" class="hover:text-lb-orange transition">Courses</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('courses.show', $course) }}" class="hover:text-lb-orange transition truncate max-w-xs">{{ $course->title }}</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">Announcements</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <x-badge variant="orange" icon="fas fa-bullhorn">
                        Course Bulletin Board
                    </x-badge>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-lb-black">
                        Course <span class="text-lb-orange">Announcements</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-600">
                        Official updates, schedule changes, and notices from your course instructors.
                    </p>
                </div>

                <a
                    href="{{ route('learning.index', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-neutral-200 text-neutral-700 hover:text-lb-orange text-xs font-bold transition shadow-sm self-start sm:self-auto"
                >
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Learning</span>
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="py-8 sm:py-14 bg-lb-off-white min-h-[600px]">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Instructor Announcement Composer -->
            @if ($canManage)
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                    <div class="border-b border-neutral-100 pb-3 flex items-center justify-between">
                        <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-bullhorn text-lb-orange"></i>
                            <span>Post an Announcement to Enrolled Students</span>
                        </h2>
                        <span class="text-[11px] font-semibold text-neutral-400">Asia/Colombo Timestamps</span>
                    </div>

                    <form action="{{ route('courses.announcements.store', $course) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-neutral-800">
                                Announcement Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                required
                                value="{{ old('title') }}"
                                placeholder="e.g. Workshop schedule change / New assignment posted"
                                class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-neutral-800">
                                Detailed Message Content <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                name="content"
                                rows="4"
                                required
                                placeholder="Type your message to students here..."
                                class="w-full text-xs sm:text-sm rounded-xl border-neutral-300 focus:border-lb-orange focus:ring-lb-orange px-3.5 py-2.5"
                            >{{ old('content') }}</textarea>
                        </div>

                        <div class="flex items-center justify-between flex-wrap gap-3 pt-2">
                            <label class="inline-flex items-center gap-2 text-xs font-bold text-neutral-700 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="is_pinned"
                                    value="1"
                                    class="w-4 h-4 text-lb-orange rounded focus:ring-lb-orange"
                                    {{ old('is_pinned') ? 'checked' : '' }}
                                >
                                <span><i class="fas fa-thumbtack text-amber-500 mr-1"></i> Pin this announcement to top</span>
                            </label>

                            <button
                                type="submit"
                                class="px-6 py-2.5 rounded-xl bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-bold transition shadow-sm cursor-pointer"
                            >
                                <i class="fas fa-paper-plane mr-1.5"></i> Publish & Queue Notifications
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Announcements Feed -->
            @if ($announcements->count() === 0)
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-12 text-center max-w-md mx-auto space-y-3">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 text-lb-orange flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-comment-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-neutral-900">No announcements yet</h3>
                    <p class="text-xs text-neutral-500">
                        The instructor has not posted any announcements for this course yet.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($announcements as $announcement)
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4 relative {{ $announcement->is_pinned ? 'ring-2 ring-amber-400/50 bg-amber-50/20' : '' }}">
                            <!-- Top Meta -->
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-lb-peach text-lb-orange flex items-center justify-center font-black text-sm shrink-0 border border-orange-200">
                                        {{ strtoupper(substr($announcement->author?->name ?? 'Instructor', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-bold text-xs sm:text-sm text-neutral-900">{{ $announcement->author?->name ?? 'Course Instructor' }}</span>
                                            @if ($announcement->is_pinned)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                    <i class="fas fa-thumbtack text-amber-600 mr-1"></i> Pinned
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-neutral-400 mt-0.5">
                                            {{ $announcement->publishedAtColombo()->format('d M Y, h:i A') }} (Asia/Colombo)
                                        </p>
                                    </div>
                                </div>

                                @if (auth()->user()->isAdmin() || auth()->id() === $announcement->user_id)
                                    <form action="{{ route('courses.announcements.destroy', ['course' => $course, 'announcement' => $announcement]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="text-neutral-400 hover:text-rose-600 text-xs transition p-1 cursor-pointer"
                                            title="Delete announcement"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Title & Content -->
                            <div class="space-y-2 border-t border-neutral-100 pt-3">
                                <h3 class="text-sm sm:text-base font-bold text-neutral-900">
                                    {{ $announcement->title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-neutral-700 leading-relaxed whitespace-pre-line">
                                    {{ $announcement->content }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($announcements->hasPages())
                    <div class="p-4 bg-white rounded-2xl border border-neutral-200">
                        {{ $announcements->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>
</x-layouts.public>

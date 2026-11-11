<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $lesson->title }} · {{ $course->title }} · LearnBuddy</title>

    <!-- Brand Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Hls.js Streaming Engine (fallback to native HLS where supported) -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{
        curriculumOpen: false,
        activeTab: 'overview',
        isCompleted: {{ $lessonProgress->is_completed ? 'true' : 'false' }},
        courseProgress: {{ $progressPercentage }},
        noteContent: '{{ addslashes($lessonProgress->notes ?? '') }}',
        savingNote: false,
        noteSavedMessage: false,
        submittingProgress: false,
        loadingPlayback: true,
        playbackData: null,
        hlsReady: false,

        init() {
            this.fetchPlaybackData();
        },

        async fetchPlaybackData() {
            this.loadingPlayback = true;
            try {
                const response = await fetch('{{ route('learning.playback', [$course, $lesson]) }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    this.playbackData = await response.json();
                    if (this.playbackData.status === 'ready' && this.playbackData.playback_url) {
                        this.initHlsPlayer(this.playbackData.playback_url);
                    }
                } else {
                    this.playbackData = { status: 'error', message: 'Unable to authorize playback session.' };
                }
            } catch (err) {
                this.playbackData = { status: 'error', message: 'Connection error while contacting streaming server.' };
            } finally {
                this.loadingPlayback = false;
            }
        },

        initHlsPlayer(url) {
            this.$nextTick(() => {
                const video = document.getElementById('lb-video-element');
                if (!video) return;

                if (window.Hls && window.Hls.isSupported()) {
                    const hls = new window.Hls({
                        enableWorker: true,
                        lowLatencyMode: false,
                    });
                    hls.loadSource(url);
                    hls.attachMedia(video);
                    hls.on(window.Hls.Events.MANIFEST_PARSED, () => {
                        this.hlsReady = true;
                    });
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = url;
                    this.hlsReady = true;
                }
            });
        },

        async toggleCompletion() {
            this.submittingProgress = true;
            const newStatus = !this.isCompleted;
            try {
                const response = await fetch('{{ route('learning.progress', [$course, $lesson]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ is_completed: newStatus ? 1 : 0 })
                });

                if (response.ok) {
                    const data = await response.json();
                    this.isCompleted = data.is_completed;
                    this.courseProgress = data.course_progress_percentage;
                    // Update progress bar width dynamically
                    const progressBar = document.getElementById('course-progress-bar');
                    if (progressBar) {
                        progressBar.style.width = this.courseProgress + '%';
                    }
                    const progressText = document.getElementById('course-progress-text');
                    if (progressText) {
                        progressText.textContent = this.courseProgress + '%';
                    }
                } else {
                    // Fallback to standard form submission if AJAX fails
                    document.getElementById('fallback-progress-form').submit();
                }
            } catch (err) {
                document.getElementById('fallback-progress-form').submit();
            } finally {
                this.submittingProgress = false;
            }
        },

        async saveNote() {
            this.savingNote = true;
            try {
                const response = await fetch('{{ route('learning.progress', [$course, $lesson]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ notes: this.noteContent })
                });

                if (response.ok) {
                    this.noteSavedMessage = true;
                    setTimeout(() => { this.noteSavedMessage = false; }, 3000);
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.savingNote = false;
            }
        }
    }"
    class="h-full flex flex-col font-sans antialiased text-neutral-100 selection:bg-lb-orange selection:text-white"
>

    <!-- Fallback hidden form for non-JS/POST requests -->
    <form id="fallback-progress-form" action="{{ route('learning.progress', [$course, $lesson]) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="is_completed" :value="isCompleted ? 0 : 1">
    </form>

    <!-- Top Learning Theater Navigation Bar -->
    <header class="bg-neutral-950 border-b border-neutral-800 shrink-0 z-30 sticky top-0">
        <div class="px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            
            <!-- Left: Back link and Course Title -->
            <div class="flex items-center gap-3 min-w-0">
                <a
                    href="{{ route('student.dashboard') }}"
                    class="w-9 h-9 rounded-full bg-neutral-800 hover:bg-neutral-700 text-neutral-300 hover:text-white flex items-center justify-center transition shrink-0"
                    title="Return to Student Dashboard"
                >
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>

                <div class="truncate">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-orange-500/10 text-lb-orange border border-orange-500/20">
                            {{ $course->category->name ?? 'Course' }}
                        </span>
                        <a href="{{ route('courses.show', $course) }}" class="text-xs text-neutral-400 hover:text-neutral-200 truncate hidden sm:inline">
                            {{ $course->title }}
                        </a>
                    </div>
                    <h1 class="text-sm sm:text-base font-bold text-white truncate leading-tight">
                        {{ $lesson->title }}
                    </h1>
                </div>
            </div>

            <!-- Center / Right: Progress Bar & Controls -->
            <div class="flex items-center gap-3 sm:gap-6 shrink-0">
                
                <!-- Progress Percentage Indicator -->
                <div class="hidden md:flex items-center gap-3">
                    <div class="text-right">
                        <div class="text-[11px] text-neutral-400 uppercase tracking-wider font-semibold">Your Progress</div>
                        <div id="course-progress-text" class="text-xs font-bold text-lb-orange">{{ $progressPercentage }}%</div>
                    </div>
                    <div class="w-28 h-2 bg-neutral-800 rounded-full overflow-hidden">
                        <div
                            id="course-progress-bar"
                            class="h-full bg-gradient-to-r from-orange-500 to-amber-400 rounded-full transition-all duration-500"
                            style="width: {{ $progressPercentage }}%;"
                        ></div>
                    </div>
                </div>

                <!-- Navigation: Previous / Next Lesson -->
                <div class="flex items-center gap-1.5">
                    @if ($previousLesson)
                        <a
                            href="{{ route('learning.show', [$course, $previousLesson]) }}"
                            class="px-3 py-1.5 rounded-lg bg-neutral-800 hover:bg-neutral-700 text-neutral-200 text-xs font-medium flex items-center gap-1.5 transition"
                            title="Previous: {{ $previousLesson->title }}"
                        >
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            <span class="hidden sm:inline">Prev</span>
                        </a>
                    @else
                        <button
                            disabled
                            class="px-3 py-1.5 rounded-lg bg-neutral-900 text-neutral-600 text-xs font-medium cursor-not-allowed flex items-center gap-1.5"
                        >
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            <span class="hidden sm:inline">Prev</span>
                        </button>
                    @endif

                    @if ($nextLesson)
                        <a
                            href="{{ route('learning.show', [$course, $nextLesson]) }}"
                            class="px-3 py-1.5 rounded-lg bg-neutral-800 hover:bg-neutral-700 text-neutral-200 text-xs font-medium flex items-center gap-1.5 transition"
                            title="Next: {{ $nextLesson->title }}"
                        >
                            <span class="hidden sm:inline">Next</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    @else
                        <button
                            disabled
                            class="px-3 py-1.5 rounded-lg bg-neutral-900 text-neutral-600 text-xs font-medium cursor-not-allowed flex items-center gap-1.5"
                        >
                            <span class="hidden sm:inline">Next</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </button>
                    @endif
                </div>

                <!-- Mobile / Desktop Curriculum Drawer Toggle -->
                <button
                    @click="curriculumOpen = !curriculumOpen"
                    class="px-3 py-1.5 rounded-lg bg-lb-orange/15 text-lb-orange border border-lb-orange/30 hover:bg-lb-orange/25 text-xs font-semibold flex items-center gap-2 transition"
                >
                    <i class="fa-solid fa-list-ul"></i>
                    <span class="hidden sm:inline">Curriculum</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-lb-orange text-white font-bold leading-none">
                        {{ count($completedLessonIds) }}/{{ $accessibleLessons->count() }}
                    </span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Learning Layout Workspace -->
    <div class="flex-grow flex flex-col lg:flex-row overflow-hidden relative">

        <!-- Left / Center: Video Player & Learning Tabs -->
        <div class="flex-grow flex flex-col overflow-y-auto bg-neutral-900">
            
            <!-- Video & Primary Stage Area -->
            <div class="w-full bg-black p-2 sm:p-4 md:p-6 flex justify-center">
                <div class="w-full max-w-5xl">
                    
                    <!-- 16:9 Responsive Video Stage Container -->
                    <div class="relative aspect-video w-full rounded-2xl overflow-hidden bg-neutral-950 border border-neutral-800 shadow-2xl flex flex-col justify-between group">
                        
                        <!-- Top Video Overlay Details -->
                        <div class="absolute top-0 inset-x-0 p-4 bg-gradient-to-b from-black/80 via-black/40 to-transparent z-20 flex items-center justify-between opacity-90 group-hover:opacity-100 transition">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-lb-orange text-white uppercase">
                                    {{ $lesson->type ?? 'Video' }}
                                </span>
                                <span class="text-xs text-neutral-300 font-medium">
                                    {{ $lesson->section->title ?? 'Section' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Dynamic Student Security Watermark -->
                                <div
                                    x-show="playbackData?.status === 'ready'"
                                    class="pointer-events-none select-none text-[10px] font-mono text-white/50 bg-black/40 px-2 py-0.5 rounded backdrop-blur-sm border border-white/10"
                                >
                                    <i class="fa-solid fa-shield-halved text-[9px] mr-1 text-lb-orange"></i>
                                    <span x-text="playbackData?.watermark?.student_email || '{{ auth()->user()->email }}'"></span>
                                </div>

                                @if (! $lesson->is_published)
                                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                        <i class="fa-solid fa-lock text-[10px] mr-1"></i> Unpublished Preview
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- 1. Loading State -->
                        <div x-show="loadingPlayback" class="my-auto text-center px-4 py-8 z-10">
                            <div class="w-10 h-10 border-2 border-lb-orange/30 border-t-lb-orange rounded-full animate-spin mx-auto mb-3"></div>
                            <p class="text-xs text-neutral-400 font-medium">Authorizing secure stream session...</p>
                        </div>

                        <!-- 2. Active Ready HLS Video Player -->
                        <template x-if="!loadingPlayback && playbackData?.status === 'ready' && playbackData?.playback_url">
                            <div class="w-full h-full relative z-10 flex items-center justify-center bg-black">
                                <video
                                    id="lb-video-element"
                                    class="w-full h-full object-contain"
                                    controls
                                    playsinline
                                    preload="metadata"
                                    controlsList="nodownload"
                                ></video>
                            </div>
                        </template>

                        <!-- 3. Processing State -->
                        <div x-show="!loadingPlayback && playbackData?.status === 'processing'" class="my-auto text-center px-4 py-8 z-10" x-cloak>
                            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 uppercase tracking-wider mb-2 inline-block">
                                Video Encoding In Progress
                            </span>
                            <h2 class="text-sm sm:text-base font-bold text-white mb-2">{{ $lesson->title }}</h2>
                            <p class="text-xs text-neutral-400 max-w-md mx-auto" x-text="playbackData?.message"></p>
                        </div>

                        <!-- 4. Provider Unconfigured State (Honest Boundary) -->
                        <div x-show="!loadingPlayback && playbackData?.status === 'unconfigured'" class="my-auto text-center px-4 py-8 z-10" x-cloak>
                            <div class="w-14 h-14 rounded-2xl bg-orange-500/10 text-lb-orange flex items-center justify-center mx-auto mb-3 text-xl">
                                <i class="fa-solid fa-video"></i>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-800 text-neutral-300 uppercase tracking-wider mb-2 inline-block">
                                Streaming Integration Boundary
                            </span>
                            <h2 class="text-sm sm:text-base font-bold text-white mb-1.5">{{ $lesson->title }}</h2>
                            <p class="text-xs text-neutral-400 max-w-lg mx-auto leading-relaxed mb-3" x-text="playbackData?.message"></p>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-neutral-900 border border-neutral-700/60 text-[11px] text-neutral-400">
                                <i class="fa-solid fa-key text-amber-400"></i>
                                <span>Set MUX_TOKEN_ID &amp; MUX_SIGNING_KEY_ID in .env for signed streaming.</span>
                            </div>
                        </div>

                        <!-- 5. Errored State -->
                        <div x-show="!loadingPlayback && playbackData?.status === 'errored'" class="my-auto text-center px-4 py-8 z-10" x-cloak>
                            <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30 uppercase tracking-wider mb-2 inline-block">
                                Processing Failure
                            </span>
                            <p class="text-xs text-neutral-400 max-w-md mx-auto" x-text="playbackData?.message"></p>
                        </div>

                        <!-- 6. None / Standard Simulation -->
                        <div x-show="!loadingPlayback && (playbackData?.status === 'none' || !playbackData)" class="my-auto text-center px-4 py-8 z-10">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-lb-orange/90 text-white flex items-center justify-center mx-auto mb-4 shadow-xl hover:scale-105 hover:bg-lb-orange transition cursor-pointer">
                                <i class="fa-solid fa-play text-xl sm:text-2xl ml-1"></i>
                            </div>

                            <h2 class="text-base sm:text-xl font-bold text-white mb-2 max-w-lg mx-auto leading-snug">
                                {{ $lesson->title }}
                            </h2>

                            <p class="text-xs sm:text-sm text-neutral-400 max-w-md mx-auto mb-3">
                                {{ $lesson->duration_minutes ? $lesson->duration_minutes . ' minutes of instruction' : 'Lecture notes and resources ready below' }}
                            </p>

                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-neutral-900/90 border border-neutral-700/60 text-[11px] text-neutral-400">
                                <i class="fa-solid fa-circle-info text-lb-orange"></i>
                                <span>Attach video via course management or use lecture materials below.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Watermark Security Notice -->
                    <div class="mt-2 px-2 flex items-center justify-between text-[11px] text-neutral-500">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-neutral-400"></i>
                            <span>Anti-Leak Protection: Dynamic watermarking is active to identify subscriber accounts. Note: No web software can completely prevent external screen recording.</span>
                        </div>
                    </div>

                    <!-- Lesson Action Bar (Complete Toggle, Next, Status) -->
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-neutral-950 rounded-2xl border border-neutral-800">
                        <div class="flex items-center gap-3">
                            <div
                                :class="isCompleted ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-neutral-800 text-neutral-400 border-neutral-700'"
                                class="w-10 h-10 rounded-xl border flex items-center justify-center text-base transition-colors shrink-0"
                            >
                                <i :class="isCompleted ? 'fa-solid fa-check' : 'fa-regular fa-circle-check'"></i>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Lesson Status</div>
                                <div class="text-sm font-bold text-white" x-text="isCompleted ? 'Completed' : 'In Progress'">
                                    {{ $lessonProgress->is_completed ? 'Completed' : 'In Progress' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Toggle Completion Button -->
                            <button
                                @click="toggleCompletion()"
                                :disabled="submittingProgress"
                                type="button"
                                :class="isCompleted ? 'bg-neutral-800 hover:bg-neutral-700 text-neutral-200 border-neutral-700' : 'bg-lb-orange hover:bg-lb-orange-dark text-white border-transparent shadow-lg shadow-orange-500/20'"
                                class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-semibold border flex items-center justify-center gap-2 transition disabled:opacity-50"
                            >
                                <i :class="isCompleted ? 'fa-solid fa-rotate-left' : 'fa-solid fa-check'"></i>
                                <span x-text="isCompleted ? 'Mark as Incomplete' : 'Mark as Complete'"></span>
                            </button>

                            @if ($nextLesson)
                                <a
                                    href="{{ route('learning.show', [$course, $nextLesson]) }}"
                                    class="px-4 py-2.5 rounded-full bg-neutral-800 hover:bg-neutral-700 text-white text-xs sm:text-sm font-semibold flex items-center gap-2 transition"
                                >
                                    <span>Next Lesson</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Content Tabs & Details Workspace -->
            <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-6 flex-grow">
                
                <!-- Tab Headers -->
                <div class="border-b border-neutral-800 flex items-center gap-2 overflow-x-auto no-scrollbar mb-6">
                    <button
                        @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'border-lb-orange text-lb-orange font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-200 font-medium'"
                        class="pb-3 px-3 border-b-2 text-sm flex items-center gap-2 whitespace-nowrap transition"
                    >
                        <i class="fa-solid fa-book-open text-xs"></i>
                        <span>Overview & Content</span>
                    </button>

                    <button
                        @click="activeTab = 'notes'"
                        :class="activeTab === 'notes' ? 'border-lb-orange text-lb-orange font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-200 font-medium'"
                        class="pb-3 px-3 border-b-2 text-sm flex items-center gap-2 whitespace-nowrap transition"
                    >
                        <i class="fa-solid fa-pencil text-xs"></i>
                        <span>My Notes</span>
                        <span x-show="noteContent && noteContent.trim().length > 0" class="w-2 h-2 rounded-full bg-lb-orange"></span>
                    </button>

                    <button
                        @click="activeTab = 'resources'"
                        :class="activeTab === 'resources' ? 'border-lb-orange text-lb-orange font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-200 font-medium'"
                        class="pb-3 px-3 border-b-2 text-sm flex items-center gap-2 whitespace-nowrap transition"
                    >
                        <i class="fa-solid fa-paperclip text-xs"></i>
                        <span>Resources</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] bg-neutral-800 text-neutral-300 font-bold">
                            {{ $lesson->resources->count() }}
                        </span>
                    </button>

                    <a
                        href="{{ route('courses.announcements.index', $course) }}"
                        class="pb-3 px-3 border-b-2 border-transparent text-neutral-400 hover:text-neutral-200 font-medium text-sm flex items-center gap-2 whitespace-nowrap transition"
                    >
                        <i class="fa-solid fa-bullhorn text-xs"></i>
                        <span>Announcements</span>
                        @if ($course->announcements()->count() > 0)
                            <span class="px-1.5 py-0.5 rounded text-[10px] bg-orange-500/20 text-lb-orange font-bold">
                                {{ $course->announcements()->count() }}
                            </span>
                        @endif
                    </a>

                    <button
                        @click="activeTab = 'discussions'"
                        :class="activeTab === 'discussions' ? 'border-lb-orange text-lb-orange font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-200 font-medium'"
                        class="pb-3 px-3 border-b-2 text-sm flex items-center gap-2 whitespace-nowrap transition opacity-75"
                    >
                        <i class="fa-solid fa-comments text-xs"></i>
                        <span>Discussions</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-neutral-800 text-neutral-400 uppercase font-mono">Phase 12</span>
                    </button>

                    <button
                        @click="activeTab = 'assignments'"
                        :class="activeTab === 'assignments' ? 'border-lb-orange text-lb-orange font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-200 font-medium'"
                        class="pb-3 px-3 border-b-2 text-sm flex items-center gap-2 whitespace-nowrap transition"
                    >
                        <i class="fa-solid fa-file-lines text-xs"></i>
                        <span>Assignments</span>
                        @if ($course->publishedAssignments->count() > 0)
                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-orange-500/20 text-lb-orange font-mono font-bold">{{ $course->publishedAssignments->count() }}</span>
                        @endif
                    </button>

                    <button
                        @click="activeTab = 'quizzes'"
                        :class="activeTab === 'quizzes' ? 'border-lb-orange text-lb-orange font-bold' : 'border-transparent text-neutral-400 hover:text-neutral-200 font-medium'"
                        class="pb-3 px-3 border-b-2 text-sm flex items-center gap-2 whitespace-nowrap transition"
                    >
                        <i class="fa-solid fa-circle-question text-xs"></i>
                        <span>Quizzes</span>
                        @if ($course->publishedQuizzes->count() > 0)
                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-purple-500/20 text-purple-400 font-mono font-bold">{{ $course->publishedQuizzes->count() }}</span>
                        @endif
                    </button>

                    <a
                        href="{{ route('courses.live-classes.index', $course->slug) }}"
                        class="pb-3 px-3 border-b-2 border-transparent text-neutral-400 hover:text-blue-400 font-medium text-sm flex items-center gap-2 whitespace-nowrap transition"
                        title="View Live Interactive Classes"
                    >
                        <i class="fa-solid fa-video text-xs text-blue-500"></i>
                        <span>Live Classes</span>
                        @if ($course->upcomingLiveClasses->count() > 0)
                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-blue-500/20 text-blue-400 font-mono font-bold">{{ $course->upcomingLiveClasses->count() }}</span>
                        @endif
                    </a>
                </div>

                <!-- Tab 1: Overview & Reading Content -->
                <div x-show="activeTab === 'overview'" class="space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">{{ $lesson->title }}</h3>
                        <p class="text-sm text-neutral-400">
                            Part of module: <span class="text-neutral-200 font-medium">{{ $lesson->section->title ?? 'Curriculum' }}</span>
                        </p>
                    </div>

                    @if ($lesson->content)
                        <div class="prose prose-invert max-w-none text-neutral-300 text-sm leading-relaxed p-6 bg-neutral-950 rounded-2xl border border-neutral-800">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    @else
                        <div class="p-6 bg-neutral-950 rounded-2xl border border-neutral-800 text-neutral-400 text-sm">
                            <p class="mb-2">No written lecture transcript provided for this lesson.</p>
                            <p class="text-xs text-neutral-500">Watch the video demonstration above and refer to the attached downloadable resources and your personal notes.</p>
                        </div>
                    @endif

                    <!-- Instructor info box -->
                    <div class="p-4 bg-neutral-950 rounded-2xl border border-neutral-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-lb-orange/20 text-lb-orange flex items-center justify-center font-bold">
                                {{ substr($course->primaryInstructor()?->name ?? 'I', 0, 1) }}
                            </div>
                            <div>
                                <div class="text-xs text-neutral-400">Course Instructor</div>
                                <div class="text-sm font-semibold text-white">{{ $course->primaryInstructor()?->name ?? 'Course Instructor' }}</div>
                            </div>
                        </div>

                        <a href="{{ route('courses.show', $course) }}" class="text-xs text-lb-orange hover:underline font-medium">
                            View Syllabus &rarr;
                        </a>
                    </div>
                </div>

                <!-- Tab 2: My Personal Notes -->
                <div x-show="activeTab === 'notes'" x-cloak class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white">My Lesson Notes</h3>
                            <p class="text-xs text-neutral-400">Your notes are private to you and saved to your LearnBuddy student profile.</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span x-show="noteSavedMessage" x-transition class="text-xs font-semibold text-emerald-400 flex items-center gap-1">
                                <i class="fa-solid fa-check"></i> Notes saved!
                            </span>
                            <button
                                @click="saveNote()"
                                :disabled="savingNote"
                                type="button"
                                class="px-4 py-2 rounded-full bg-lb-orange hover:bg-lb-orange-dark text-white text-xs font-semibold flex items-center gap-1.5 transition disabled:opacity-50"
                            >
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span x-text="savingNote ? 'Saving...' : 'Save Notes'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <textarea
                            x-model="noteContent"
                            rows="10"
                            placeholder="Write your takeaways, code snippets, or key concepts from this lesson here..."
                            class="w-full bg-neutral-950 border border-neutral-800 rounded-2xl p-4 text-sm text-neutral-100 placeholder-neutral-500 focus:outline-none focus:border-lb-orange focus:ring-1 focus:ring-lb-orange resize-y"
                        ></textarea>
                    </div>
                </div>

                <!-- Tab 3: Lesson Resources / Attachments -->
                <div x-show="activeTab === 'resources'" x-cloak class="space-y-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Lesson Resources</h3>
                        <p class="text-xs text-neutral-400">Download supplementary source files, worksheets, or reference slides.</p>
                    </div>

                    @if ($lesson->resources && $lesson->resources->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ($lesson->resources as $res)
                                <div class="p-4 bg-neutral-950 rounded-2xl border border-neutral-800 flex items-center justify-between gap-3 hover:border-neutral-700 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-lb-orange flex items-center justify-center shrink-0">
                                            @if ($res->file_type === 'pdf')
                                                <i class="fa-solid fa-file-pdf text-base"></i>
                                            @elseif ($res->file_type === 'zip')
                                                <i class="fa-solid fa-file-zipper text-base"></i>
                                            @elseif ($res->file_type === 'code')
                                                <i class="fa-solid fa-code text-base"></i>
                                            @else
                                                <i class="fa-solid fa-file text-base"></i>
                                            @endif
                                        </div>
                                        <div class="truncate">
                                            <div class="text-sm font-semibold text-white truncate">{{ $res->title }}</div>
                                            <div class="text-xs text-neutral-400 flex items-center gap-2">
                                                <span>{{ strtoupper($res->file_type) }}</span>
                                                @if ($res->file_size_bytes)
                                                    <span>&bull;</span>
                                                    <span>{{ $res->formattedFileSize() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <a
                                        href="{{ route('learning.resources.download', [$course, $lesson, $res]) }}"
                                        target="_blank"
                                        class="px-3 py-1.5 rounded-lg bg-neutral-800 hover:bg-neutral-700 text-neutral-200 text-xs font-semibold shrink-0 transition"
                                    >
                                        <i class="fa-solid fa-download mr-1"></i> Get
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 bg-neutral-950 rounded-2xl border border-neutral-800 text-center">
                            <div class="w-12 h-12 rounded-xl bg-neutral-900 text-neutral-500 flex items-center justify-center mx-auto mb-3 text-lg">
                                <i class="fa-solid fa-paperclip"></i>
                            </div>
                            <h4 class="text-sm font-bold text-neutral-200 mb-1">No Attachments</h4>
                            <p class="text-xs text-neutral-400">This lesson does not contain supplementary file downloads.</p>
                        </div>
                    @endif
                </div>

                <!-- Tab 4: Discussions (Phase 12 Placeholder) -->
                <div x-show="activeTab === 'discussions'" x-cloak class="p-8 bg-neutral-950 rounded-2xl border border-neutral-800 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-orange-500/10 text-lb-orange flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-800 text-neutral-300 uppercase tracking-wider mb-2 inline-block">
                        Phase 12 Milestone
                    </span>
                    <h4 class="text-base font-bold text-white mb-2">Classroom Discussions Coming Soon</h4>
                    <p class="text-xs text-neutral-400 max-w-md mx-auto leading-relaxed">
                        Interactive lesson discussions, peer question threads, and tutor replies will be unlocked in Phase 12.
                    </p>
                </div>

                <!-- Tab 5: Assignments -->
                <div x-show="activeTab === 'assignments'" x-cloak class="space-y-4">
                    @if ($course->publishedAssignments->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($course->publishedAssignments as $assign)
                                @php
                                    $userSub = $assign->getUserSubmission(auth()->user());
                                @endphp
                                <div class="p-5 bg-neutral-900/90 rounded-2xl border border-neutral-800 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h4 class="text-sm font-bold text-white">{{ $assign->title }}</h4>
                                            <p class="text-xs text-neutral-400 mt-0.5 line-clamp-2">{{ $assign->description }}</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-neutral-800 text-neutral-300 shrink-0">
                                            {{ $assign->max_points }} pts
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-neutral-800 text-xs">
                                        <span class="text-neutral-400">
                                            @if ($assign->due_date)
                                                Due {{ $assign->due_date->timezone('Asia/Colombo')->format('M d, Y') }}
                                            @else
                                                No deadline
                                            @endif
                                        </span>

                                        <div class="flex items-center gap-2">
                                            @if ($userSub)
                                                @if ($userSub->isGraded())
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400">
                                                        Graded: {{ $userSub->points_awarded }}/{{ $assign->max_points }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400">Submitted</span>
                                                @endif
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-neutral-800 text-neutral-400">Pending</span>
                                            @endif

                                            <a
                                                href="{{ route('courses.assignments.show', [$course, $assign]) }}"
                                                class="px-3 py-1.5 rounded-xl bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs transition"
                                            >
                                                {{ $userSub ? 'View' : 'Submit' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 bg-neutral-950 rounded-2xl border border-neutral-800 text-center">
                            <i class="fa-solid fa-file-lines text-2xl text-neutral-600 mb-2 block"></i>
                            <h4 class="text-sm font-bold text-white mb-1">No Assignments Yet</h4>
                            <p class="text-xs text-neutral-400">No coursework has been posted for this course yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Tab 6: Quizzes -->
                <div x-show="activeTab === 'quizzes'" x-cloak class="space-y-4">
                    @if ($course->publishedQuizzes->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($course->publishedQuizzes as $q)
                                @php
                                    $best = $q->getUserBestAttempt(auth()->user());
                                @endphp
                                <div class="p-5 bg-neutral-900/90 rounded-2xl border border-neutral-800 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h4 class="text-sm font-bold text-white">{{ $q->title }}</h4>
                                            <p class="text-xs text-neutral-400 mt-0.5 line-clamp-2">{{ $q->description }}</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-500/20 text-purple-400 shrink-0">
                                            {{ $q->questions->count() }} Qs · {{ $q->totalMarks() }} pts
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-neutral-800 text-xs">
                                        <span class="text-neutral-400">
                                            {{ $q->time_limit_minutes ? $q->time_limit_minutes . 'm limit' : 'Untimed' }} · Pass: {{ $q->passing_percentage }}%
                                        </span>

                                        <div class="flex items-center gap-2">
                                            @if ($best)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $best->is_passed ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                                    Best: {{ $best->percentage }}%
                                                </span>
                                            @endif

                                            <a
                                                href="{{ route('courses.quizzes.show', [$course, $q]) }}"
                                                class="px-3 py-1.5 rounded-xl bg-purple-600 text-white hover:bg-purple-700 font-bold text-xs transition"
                                            >
                                                {{ $best ? 'Details' : 'Take Quiz' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 bg-neutral-950 rounded-2xl border border-neutral-800 text-center">
                            <i class="fa-solid fa-circle-question text-2xl text-neutral-600 mb-2 block"></i>
                            <h4 class="text-sm font-bold text-white mb-1">No Quizzes Yet</h4>
                            <p class="text-xs text-neutral-400">No knowledge check quizzes have been created for this course yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Collapsible Curriculum Drawer Sidebar -->
        <aside
            :class="curriculumOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 right-0 z-40 w-80 sm:w-96 bg-neutral-950 border-l border-neutral-800 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:z-auto shrink-0 shadow-2xl lg:shadow-none"
        >
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-neutral-800 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-sm font-bold text-white">Course Curriculum</h3>
                    <div class="text-xs text-neutral-400 mt-0.5">
                        {{ $accessibleLessons->count() }} lessons total &bull; <span class="text-lb-orange font-semibold">{{ count($completedLessonIds) }} completed</span>
                    </div>
                </div>

                <button
                    @click="curriculumOpen = false"
                    class="lg:hidden w-8 h-8 rounded-lg bg-neutral-800 text-neutral-300 hover:text-white flex items-center justify-center"
                >
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Curriculum Accordion Sections List -->
            <div class="flex-grow overflow-y-auto divide-y divide-neutral-900/60 p-2 space-y-2">
                @foreach ($sections as $sectionIndex => $sec)
                    @php
                        $secLessons = $sec->lessons;
                        $secCompletedCount = $secLessons->filter(fn ($l) => in_array($l->id, $completedLessonIds))->count();
                        $isCurrentSection = $secLessons->contains('id', $lesson->id);
                    @endphp

                    <div x-data="{ open: {{ $isCurrentSection ? 'true' : 'true' }} }" class="bg-neutral-900/60 rounded-xl overflow-hidden border border-neutral-800/80">
                        <!-- Section Accordion Header -->
                        <button
                            @click="open = !open"
                            type="button"
                            class="w-full px-3.5 py-3 flex items-center justify-between text-left hover:bg-neutral-800/40 transition"
                        >
                            <div class="min-w-0 pr-2">
                                <div class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">
                                    Section {{ $sectionIndex + 1 }}
                                </div>
                                <div class="text-xs sm:text-sm font-bold text-white truncate">
                                    {{ $sec->title }}
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-[11px] font-mono text-neutral-400">
                                    {{ $secCompletedCount }}/{{ $secLessons->count() }}
                                </span>
                                <i :class="open ? 'rotate-180' : ''" class="fa-solid fa-chevron-down text-xs text-neutral-400 transition-transform"></i>
                            </div>
                        </button>

                        <!-- Lessons List within Section -->
                        <div x-show="open" class="divide-y divide-neutral-800/50 bg-neutral-950/40">
                            @foreach ($secLessons as $l)
                                @php
                                    $isThisLessonActive = (int) $l->id === (int) $lesson->id;
                                    $isThisLessonCompleted = in_array($l->id, $completedLessonIds);
                                @endphp

                                <a
                                    href="{{ route('learning.show', [$course, $l]) }}"
                                    class="px-3.5 py-2.5 flex items-center justify-between gap-3 text-xs transition {{ $isThisLessonActive ? 'bg-orange-500/15 border-l-4 border-lb-orange text-white' : 'hover:bg-neutral-800/30 text-neutral-300' }}"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <!-- Completion / Active Indicator -->
                                        @if ($isThisLessonCompleted)
                                            <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-[10px]">
                                                <i class="fa-solid fa-check"></i>
                                            </span>
                                        @elseif ($isThisLessonActive)
                                            <span class="w-5 h-5 rounded-full bg-lb-orange text-white flex items-center justify-center shrink-0 text-[10px]">
                                                <i class="fa-solid fa-play ml-0.5"></i>
                                            </span>
                                        @else
                                            <span class="w-5 h-5 rounded-full border border-neutral-700 text-neutral-600 flex items-center justify-center shrink-0 text-[10px]">
                                                <i class="fa-solid fa-circle text-[6px]"></i>
                                            </span>
                                        @endif

                                        <div class="truncate">
                                            <div class="font-medium truncate {{ $isThisLessonActive ? 'text-white font-bold' : '' }}">
                                                {{ $l->title }}
                                            </div>
                                            <div class="text-[10px] text-neutral-400 flex items-center gap-1.5">
                                                <span>{{ $l->duration_minutes ? $l->duration_minutes . 'm' : '10m' }}</span>
                                                @if ($l->is_preview)
                                                    <span class="px-1 py-0.2 rounded bg-neutral-800 text-neutral-300 text-[9px]">Preview</span>
                                                @endif
                                                @if (! $l->is_published)
                                                    <span class="px-1 py-0.2 rounded bg-amber-500/20 text-amber-300 text-[9px]">Draft</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if ($isThisLessonActive)
                                        <span class="text-[10px] font-bold text-lb-orange uppercase shrink-0">Playing</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- Mobile Backdrop for Curriculum Drawer -->
        <div
            x-show="curriculumOpen"
            @click="curriculumOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 z-30 lg:hidden"
        ></div>
    </div>
</body>
</html>

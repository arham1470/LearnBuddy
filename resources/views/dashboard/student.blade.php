<x-layouts.public title="Student Learning Dashboard · LearnBuddy">
    <!-- DASHBOARD BANNER -->
    <section class="bg-neutral-900 text-white pt-8 sm:pt-12 pb-12 sm:pb-16 rounded-b-[32px] sm:rounded-b-[48px] relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-lb-orange/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-[1140px] mx-auto px-4 sm:px-6 relative z-10">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-neutral-400 mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
                <span class="text-neutral-600">/</span>
                <span class="text-neutral-300 font-semibold">Student Dashboard</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/10 text-lb-orange border border-white/10">
                            <i class="fas fa-graduation-cap"></i> Student Learning Portal
                        </span>
                        <span class="text-xs text-neutral-400 hidden sm:inline">&bull; Asia/Colombo Timezone</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                        Ayubowan, <span class="text-lb-orange">{{ $user->name }}</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-300 max-w-xl">
                        Track your enrolled courses, campus classroom batches, laboratory intakes, and upcoming academic milestones.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('courses.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-bold transition shadow-md cursor-pointer"
                    >
                        <i class="fas fa-search text-xs"></i>
                        <span>Explore Courses</span>
                    </a>

                    <a
                        href="{{ route('profile.show', $user) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition"
                    >
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8 pt-6 border-t border-neutral-800">
                <!-- Stat 1 -->
                <div class="p-4 rounded-2xl bg-neutral-800/80 border border-neutral-700/80">
                    <span class="text-[11px] font-semibold text-neutral-400 block uppercase tracking-wider">Active Courses</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1">{{ $activeEnrollments->count() }}</div>
                    <span class="text-[10px] text-emerald-400 font-medium">Instant access granted</span>
                </div>

                <!-- Stat 2 -->
                <div class="p-4 rounded-2xl bg-neutral-800/80 border border-neutral-700/80">
                    <span class="text-[11px] font-semibold text-neutral-400 block uppercase tracking-wider">Physical Batches</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1">{{ $physicalEnrollments->count() }}</div>
                    <span class="text-[10px] text-amber-400 font-medium">Campus lab seats</span>
                </div>

                <!-- Stat 3 -->
                <div class="p-4 rounded-2xl bg-neutral-800/80 border border-neutral-700/80">
                    <span class="text-[11px] font-semibold text-neutral-400 block uppercase tracking-wider">Pending Payment</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1">{{ $pendingEnrollments->count() }}</div>
                    <span class="text-[10px] text-orange-400 font-medium">Reserved seats</span>
                </div>

                <!-- Stat 4 -->
                <div class="p-4 rounded-2xl bg-neutral-800/80 border border-neutral-700/80">
                    <span class="text-[11px] font-semibold text-neutral-400 block uppercase tracking-wider">Certificates</span>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1">0</div>
                    <span class="text-[10px] text-neutral-400">Available upon completion</span>
                </div>
            </div>
        </div>
    </section>

    <!-- DASHBOARD MAIN CONTENT -->
    <section class="py-10 sm:py-16 bg-lb-off-white min-h-[700px]">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6 space-y-10">
            <!-- Flash Message Alerts -->
            @if (session('success'))
                <x-alert type="success" dismissible="true">
                    {{ session('success') }}
                </x-alert>
            @endif

            @if (session('info'))
                <x-alert type="info" dismissible="true">
                    {{ session('info') }}
                </x-alert>
            @endif

            <!-- 1. CONTINUE LEARNING HERO CARD (If active enrollment exists) -->
            @if ($continueLearning)
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 overflow-hidden relative">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-3 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-orange-100 text-lb-orange">
                                    Continue Learning
                                </span>
                                <span class="text-xs text-neutral-400">{{ $continueLearning->course->category?->name }}</span>
                            </div>

                            <h2 class="text-lg sm:text-2xl font-bold text-neutral-900 leading-snug">
                                {{ $continueLearning->course->title }}
                            </h2>

                            <p class="text-xs text-neutral-500 line-clamp-2 max-w-2xl leading-relaxed">
                                {{ $continueLearning->course->subtitle ?: $continueLearning->course->description }}
                            </p>

                            <!-- Progress indicator -->
                            @php
                                $continueProgress = $continueLearning->course->calculateProgressPercentageFor(auth()->user());
                            @endphp
                            <div class="space-y-1.5 max-w-md pt-2">
                                <div class="flex justify-between text-xs font-semibold text-neutral-600">
                                    <span>Course Progress</span>
                                    <span class="text-lb-orange font-bold">{{ $continueProgress }}% Completed</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-neutral-100 overflow-hidden">
                                    <div class="h-full bg-lb-orange rounded-full transition-all duration-300" style="width: {{ $continueProgress }}%;"></div>
                                </div>
                                <p class="text-[10px] text-neutral-400">
                                    Interactive lesson streaming, note-taking, and progress tracking.
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 flex flex-col gap-2">
                            <a
                                href="{{ route('learning.index', $continueLearning->course) }}"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-bold transition shadow-sm"
                            >
                                <i class="fas fa-play text-xs text-lb-orange"></i>
                                <span>Continue Learning</span>
                            </a>

                            <a
                                href="{{ route('enrollments.confirmation', $continueLearning) }}"
                                class="inline-flex items-center justify-center text-center text-xs text-neutral-500 hover:text-lb-orange transition py-1"
                            >
                                View Enrollment Receipt
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. PENDING PAYMENT RESERVATIONS (If any) -->
            @if ($pendingEnrollments->isNotEmpty())
                <div class="bg-amber-50/70 rounded-3xl border border-amber-200 p-6 sm:p-8 space-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-amber-600 text-base"></i>
                        <h2 class="text-sm sm:text-base font-bold text-amber-900">
                            Seat Reservations Awaiting Payment Verification ({{ $pendingEnrollments->count() }})
                        </h2>
                    </div>
                    <p class="text-xs text-amber-800 leading-relaxed max-w-2xl">
                        Your seats for the following courses are reserved. In accordance with the project development plan, payment verification activates in Phase 13.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        @foreach ($pendingEnrollments as $pEnr)
                            <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-sm flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[10px] font-mono font-bold text-neutral-500 uppercase">{{ $pEnr->enrollment_code }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800">Pending Payment</span>
                                    </div>
                                    <h3 class="text-xs sm:text-sm font-bold text-neutral-900">{{ $pEnr->course->title }}</h3>
                                    <p class="text-xs text-neutral-500 mt-1">{{ $pEnr->deliveryOption?->title }}</p>
                                </div>

                                <div class="pt-3 border-t border-neutral-100 flex items-center justify-between">
                                    <span class="text-xs font-extrabold text-neutral-900">{{ $pEnr->formattedPrice() }}</span>
                                    <a href="{{ route('enrollments.confirmation', $pEnr) }}" class="text-xs text-lb-orange font-semibold hover:underline">
                                        View Receipt &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 3. ENROLLED COURSES LIST -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-neutral-200 pb-3">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-book-open text-lb-orange" aria-hidden="true"></i>
                            <span>My Enrolled Courses</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">Active curriculums with immediate access.</p>
                    </div>

                    <a href="{{ route('courses.index') }}" class="text-xs font-semibold text-lb-orange hover:underline">
                        Browse More Courses &rarr;
                    </a>
                </div>

                @if ($activeEnrollments->isEmpty())
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-12 text-center space-y-3">
                        <div class="w-14 h-14 rounded-full bg-orange-50 text-lb-orange flex items-center justify-center mx-auto text-xl">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="text-base font-bold text-neutral-900">No active course enrollments yet</h3>
                        <p class="text-xs text-neutral-500 max-w-sm mx-auto leading-relaxed">
                            Discover Sri Lanka's premier online software courses and physical campus intakes to start your learning journey.
                        </p>
                        <div class="pt-2">
                            <x-button as="a" href="{{ route('courses.index') }}" variant="primary">
                                Explore Published Courses
                            </x-button>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($activeEnrollments as $enr)
                            <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-lb-sm hover:shadow-lb-md transition overflow-hidden flex flex-col justify-between">
                                <div>
                                    <!-- Thumbnail -->
                                    <div class="h-36 bg-gradient-to-tr from-neutral-900 to-neutral-800 flex items-center justify-center relative overflow-hidden">
                                        @if ($enr->course->thumbnail_url)
                                            <img src="{{ asset('storage/' . $enr->course->thumbnail_url) }}" alt="{{ $enr->course->title }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="{{ $enr->course->category?->icon ?? 'fas fa-graduation-cap' }} text-3xl text-neutral-400"></i>
                                        @endif

                                        <span class="absolute top-2.5 left-2.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $enr->delivery_mode === 'online' ? 'bg-blue-600 text-white' : 'bg-amber-600 text-white' }}">
                                            {{ $enr->delivery_mode }}
                                        </span>
                                    </div>

                                    <div class="p-4 space-y-2">
                                        <span class="text-[10px] text-neutral-400 uppercase font-semibold">{{ $enr->course->category?->name }}</span>
                                        <h3 class="text-xs sm:text-sm font-bold text-neutral-900 line-clamp-2 leading-snug">
                                            {{ $enr->course->title }}
                                        </h3>
                                        <p class="text-[11px] text-neutral-500">
                                            Enrolled: {{ $enr->enrolled_at ? $enr->enrolled_at->format('d M Y') : 'Active' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="p-4 pt-2 border-t border-neutral-100 flex items-center justify-between">
                                    <span class="text-[10px] font-mono text-neutral-400">{{ $enr->enrollment_code }}</span>
                                    <a
                                        href="{{ route('learning.index', $enr->course) }}"
                                        class="text-xs font-bold text-lb-orange hover:underline flex items-center gap-1"
                                    >
                                        <span>Start Learning</span>
                                        <i class="fas fa-play text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 4. PHYSICAL CAMPUS SCHEDULE -->
            <div class="space-y-4">
                <div class="border-b border-neutral-200 pb-3">
                    <h2 class="text-base sm:text-lg font-bold text-neutral-900 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-lb-orange" aria-hidden="true"></i>
                        <span>Physical Campus Classroom Schedule</span>
                    </h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Sri Lankan in-person laboratory intakes and weekend lectures.</p>
                </div>

                @if ($physicalEnrollments->isEmpty())
                    <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-8 text-center text-xs text-neutral-500 space-y-1">
                        <i class="fas fa-building text-neutral-300 text-2xl mb-1 block"></i>
                        <p class="font-medium text-neutral-800">No physical classroom batches enrolled.</p>
                        <p class="text-neutral-400">You are currently taking self-paced online courses. You can enroll in in-person campus labs from the course catalogue.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($physicalEnrollments as $pEnr)
                            @php $b = $pEnr->batch; @endphp
                            <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-lb-sm p-5 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-neutral-900">{{ $b->batch_name }}</span>
                                    <span class="text-[10px] font-mono font-bold bg-amber-50 text-amber-800 px-2 py-0.5 rounded border border-amber-200">
                                        {{ $b->batch_code }}
                                    </span>
                                </div>

                                <p class="text-xs text-neutral-600 font-semibold">{{ $pEnr->course->title }}</p>

                                @if ($b->location)
                                    <div class="text-xs text-neutral-600 space-y-0.5 pt-1 border-t border-neutral-100">
                                        <p class="font-medium text-neutral-900"><i class="fas fa-building text-lb-orange mr-1"></i> {{ $b->location->name }}</p>
                                        <p class="text-neutral-500">{{ $b->location->address_line_1 }}, {{ $b->location->city }} ({{ $b->location->district }} District)</p>
                                    </div>
                                @endif

                                <div class="pt-2 flex flex-wrap items-center gap-4 text-[11px] text-neutral-500 border-t border-neutral-100">
                                    @if ($b->start_date)
                                        <span>Starts: <strong>{{ $b->start_date->format('d M Y') }}</strong></span>
                                    @endif
                                    @if ($b->end_date)
                                        <span>Ends: <strong>{{ $b->end_date->format('d M Y') }}</strong></span>
                                    @endif
                                    <span>Timezone: <strong>Asia/Colombo</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 5. UPCOMING LIVE CLASSES & ACADEMIC ACTIVITY (HONEST EMPTY STATES) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upcoming Live Classes -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 space-y-3">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-video text-blue-500"></i>
                            <span>Upcoming Live Classes</span>
                        </h3>
                        <span class="text-[10px] font-bold text-neutral-400 uppercase">Phase 11</span>
                    </div>
                    <div class="py-6 text-center text-xs text-neutral-400 space-y-2">
                        <i class="fas fa-calendar-times text-2xl text-neutral-300"></i>
                        <p class="text-neutral-600 font-medium">No live interactive classes scheduled.</p>
                        <p class="text-[11px] leading-relaxed">
                            Live video workshops scheduled by your instructors will appear here with calendar links in Phase 11.
                        </p>
                    </div>
                </div>

                <!-- Pending Assignments & Quizzes -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 space-y-3">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-tasks text-purple-500"></i>
                            <span>Assignments &amp; Quizzes</span>
                        </h3>
                        <span class="text-[10px] font-bold text-neutral-400 uppercase">Phases 14-15</span>
                    </div>
                    <div class="py-6 text-center text-xs text-neutral-400 space-y-2">
                        <i class="fas fa-clipboard-check text-2xl text-neutral-300"></i>
                        <p class="text-neutral-600 font-medium">No pending submissions.</p>
                        <p class="text-[11px] leading-relaxed">
                            Graded assignments and quiz attempts will be managed in upcoming assessment phases.
                        </p>
                    </div>
                </div>

                <!-- Verifiable Certificates -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 space-y-3">
                    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                        <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-certificate text-emerald-500"></i>
                            <span>Earned Certificates</span>
                        </h3>
                        <span class="text-[10px] font-bold text-neutral-400 uppercase">Phase 16</span>
                    </div>
                    <div class="py-6 text-center text-xs text-neutral-400 space-y-2">
                        <i class="fas fa-award text-2xl text-neutral-300"></i>
                        <p class="text-neutral-600 font-medium">0 Certificates earned.</p>
                        <p class="text-[11px] leading-relaxed">
                            Certificates with public verification hashes will be issued once course lessons and assessments are completed in Phase 16.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 6. RECENT ACTIVITY & NOTIFICATIONS -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                <div class="border-b border-neutral-100 pb-3">
                    <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                        <i class="fas fa-bell text-lb-orange"></i>
                        <span>Recent Account Activity &amp; Notifications</span>
                    </h3>
                </div>

                @if ($recentActivities->isEmpty())
                    <p class="text-xs text-neutral-400 py-4 text-center">No recent activity on your learner account.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($recentActivities as $act)
                            <div class="p-3.5 rounded-xl bg-neutral-50/80 border border-neutral-100 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-orange-50 text-lb-orange flex items-center justify-center font-bold text-xs shrink-0">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <div>
                                        <p class="font-bold text-neutral-800">
                                            Enrollment {{ $act->isActive() ? 'Activated' : 'Reserved' }}: {{ $act->course->title }}
                                        </p>
                                        <p class="text-[11px] text-neutral-500">
                                            Ref: {{ $act->enrollment_code }} &bull; Mode: {{ ucfirst($act->delivery_mode) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right text-[11px] text-neutral-400">
                                    <span>{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>

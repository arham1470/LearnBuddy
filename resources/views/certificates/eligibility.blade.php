<x-layouts.public :title="'Certificate Eligibility: ' . $course->title . ' · LearnBuddy'">
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
                <span class="font-semibold text-neutral-800">Certificate Eligibility</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <x-badge variant="orange" icon="fas fa-certificate">
                        Academic Credentials
                    </x-badge>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-lb-black">
                        Certificate <span class="text-lb-orange">Eligibility</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-600">
                        Track your lesson progress, assignments, and quiz completion requirements for {{ $course->title }}.
                    </p>
                </div>

                <a
                    href="{{ route('learning.index', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-neutral-200 text-neutral-700 hover:text-lb-orange text-xs font-bold transition shadow-sm self-start sm:self-auto"
                >
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Course</span>
                </a>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="py-8 sm:py-14 bg-lb-off-white min-h-[600px]">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 space-y-8">

            @if ($certificate)
                <!-- ALREADY ISSUED BANNER -->
                <div class="bg-emerald-50 border-2 border-emerald-200 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shrink-0 shadow">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-200/60 px-2 py-0.5 rounded-full">
                                Issued & Verified
                            </span>
                            <h2 class="text-lg sm:text-xl font-bold text-neutral-900">
                                Official Certificate Earned!
                            </h2>
                            <p class="text-xs text-neutral-600">
                                Credential ID: <strong class="font-mono text-neutral-900">{{ $certificate->certificate_code }}</strong>
                                &bull; Issued: {{ $certificate->issue_date->format('d M Y') }}
                                @if ($certificate->final_grade)
                                    &bull; Grade: <strong>{{ $certificate->formattedGrade() }}</strong>
                                @endif
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('certificates.show', $certificate) }}"
                        class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm transition shadow flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer"
                    >
                        <i class="fas fa-file-pdf"></i> View & Print Certificate
                    </a>
                </div>
            @elseif ($eligibility['is_eligible'])
                <!-- ELIGIBLE TO CLAIM BANNER -->
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-lb-orange/40 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6 shadow-md">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-lb-orange text-white flex items-center justify-center text-2xl shrink-0 shadow">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-orange-800 bg-orange-200/60 px-2 py-0.5 rounded-full">
                                100% Course Completed
                            </span>
                            <h2 class="text-lg sm:text-xl font-bold text-neutral-900">
                                You Are Eligible for Your Certificate!
                            </h2>
                            <p class="text-xs text-neutral-600">
                                All curriculum lessons, graded assignments, and quizzes have been completed.
                                Final Calculated Grade: <strong class="text-lb-orange">{{ number_format($eligibility['final_grade'], 1) }}%</strong>
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('certificates.claim', $course) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-lb-orange hover:bg-lb-orange-hover text-white font-black text-xs sm:text-sm transition shadow-md flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer"
                        >
                            <i class="fas fa-award"></i> Claim Certificate of Completion
                        </button>
                    </form>
                </div>
            @else
                <!-- INELIGIBLE / PROGRESS BANNER -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-neutral-900">Requirements Remaining</h2>
                            <p class="text-xs text-neutral-500">
                                Complete the remaining criteria below to unlock your verifiable digital certificate.
                            </p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 space-y-1.5">
                        <span class="font-bold block">Remaining items required for certificate issuance:</span>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach ($eligibility['missing_requirements'] as $req)
                                <li>{{ $req }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- COMPLETION BREAKDOWN CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Lessons Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Lessons</span>
                        <span class="w-8 h-8 rounded-xl bg-orange-100 text-lb-orange flex items-center justify-center text-xs">
                            <i class="fas fa-play-circle"></i>
                        </span>
                    </div>

                    <div class="space-y-1">
                        <div class="text-2xl font-black text-neutral-900">
                            {{ $eligibility['completed_lessons'] }} <span class="text-sm font-semibold text-neutral-400">/ {{ $eligibility['total_lessons'] }}</span>
                        </div>
                        <p class="text-xs text-neutral-500">
                            {{ $eligibility['lessons_percentage'] }}% lessons completed
                        </p>
                    </div>

                    <div class="w-full h-2.5 bg-neutral-100 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-lb-orange rounded-full transition-all duration-500"
                            style="width: {{ $eligibility['lessons_percentage'] }}%;"
                        ></div>
                    </div>
                </div>

                <!-- Assignments Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Assignments</span>
                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xs">
                            <i class="fas fa-clipboard-check"></i>
                        </span>
                    </div>

                    <div class="space-y-1">
                        <div class="text-2xl font-black text-neutral-900">
                            {{ $eligibility['completed_assignments'] }} <span class="text-sm font-semibold text-neutral-400">/ {{ $eligibility['total_assignments'] }}</span>
                        </div>
                        <p class="text-xs text-neutral-500">
                            @if ($eligibility['total_assignments'] === 0)
                                No required assignments in this course
                            @else
                                {{ $eligibility['completed_assignments'] === $eligibility['total_assignments'] ? 'All assignments completed & graded' : 'Pending grading or submission' }}
                            @endif
                        </p>
                    </div>

                    <div class="w-full h-2.5 bg-neutral-100 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-blue-600 rounded-full transition-all duration-500"
                            style="width: {{ $eligibility['total_assignments'] > 0 ? ($eligibility['completed_assignments'] / $eligibility['total_assignments']) * 100 : 100 }}%;"
                        ></div>
                    </div>
                </div>

                <!-- Quizzes Card -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Quizzes</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                            <i class="fas fa-award"></i>
                        </span>
                    </div>

                    <div class="space-y-1">
                        <div class="text-2xl font-black text-neutral-900">
                            {{ $eligibility['passed_quizzes'] }} <span class="text-sm font-semibold text-neutral-400">/ {{ $eligibility['total_quizzes'] }}</span>
                        </div>
                        <p class="text-xs text-neutral-500">
                            @if ($eligibility['total_quizzes'] === 0)
                                No quizzes configured in this course
                            @else
                                {{ $eligibility['passed_quizzes'] === $eligibility['total_quizzes'] ? 'All quizzes passed successfully' : 'Pending quiz pass attempts' }}
                            @endif
                        </p>
                    </div>

                    <div class="w-full h-2.5 bg-neutral-100 rounded-full overflow-hidden">
                        <div
                            class="h-full bg-emerald-500 rounded-full transition-all duration-500"
                            style="width: {{ $eligibility['total_quizzes'] > 0 ? ($eligibility['passed_quizzes'] / $eligibility['total_quizzes']) * 100 : 100 }}%;"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- SECURITY & INTEGRITY DISCLOSURE -->
            <div class="p-4 rounded-2xl bg-neutral-100/80 border border-neutral-200 text-[11px] text-neutral-600 space-y-1">
                <p class="font-bold text-neutral-800">
                    <i class="fas fa-shield-alt text-emerald-600 mr-1"></i> Tamper-Proof Credential Policy:
                </p>
                <p>
                    LearnBuddy certificates are issued strictly through automated server-side verification of student progress, submissions, and quiz scoring. Each certificate includes a unique, non-guessable credential identifier and an immutable cryptographic SHA-256 verification hash to prevent forgery and unauthorized duplicate issuance.
                </p>
            </div>

        </div>
    </section>
</x-layouts.public>

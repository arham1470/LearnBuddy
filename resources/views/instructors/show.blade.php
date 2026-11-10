<x-layouts.public :title="$instructor->name . ' · Instructor Profile · LearnBuddy'">
    <!-- INSTRUCTOR PROFILE HERO -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-12 sm:pb-16 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('courses.index') }}" class="hover:text-lb-orange transition">Instructors</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">{{ $instructor->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 sm:gap-8">
                <!-- Large Avatar -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white shadow-lb-md text-lb-orange flex items-center justify-center font-extrabold text-3xl sm:text-4xl shrink-0 border-4 border-white">
                    {{ strtoupper(substr($instructor->name, 0, 2)) }}
                </div>

                <!-- Instructor Meta -->
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-neutral-900 text-white shadow-sm">
                            <i class="fas fa-certificate text-lb-orange text-[10px]" aria-hidden="true"></i>
                            <span>Verified Instructor</span>
                        </span>

                        @if ($instructor->isAdmin())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-lb-orange">
                                Platform Administrator
                            </span>
                        @endif

                        @if ($instructor->city || $instructor->province)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-white text-neutral-700 shadow-sm border border-neutral-200">
                                <i class="fas fa-map-marker-alt text-lb-orange text-xs"></i>
                                <span>{{ $instructor->city ? $instructor->city . ', ' : '' }}{{ $instructor->province }} Province</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-lb-black">
                        {{ $instructor->name }}
                    </h1>

                    <p class="text-xs sm:text-sm text-neutral-600 max-w-2xl leading-relaxed">
                        {{ $instructor->bio ?: 'Dedicated software engineer and accredited instructor passionate about delivering high-impact learning for Sri Lankan students.' }}
                    </p>

                    <!-- Instructor Quick Stats -->
                    <div class="flex items-center gap-6 pt-2 text-xs font-medium text-neutral-600">
                        <div>
                            <span class="font-extrabold text-neutral-900 text-sm">{{ $publishedCourses->count() }}</span>
                            <span class="text-neutral-500">Published {{ Str::plural('Course', $publishedCourses->count()) }}</span>
                        </div>
                        <div class="border-l border-neutral-300 pl-6">
                            <span class="font-extrabold text-neutral-900 text-sm">{{ $publishedCourses->sum(fn ($c) => $c->sections->sum(fn ($s) => $s->lessons->count())) }}</span>
                            <span class="text-neutral-500">Curriculum Lessons</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PUBLISHED COURSES LIST -->
    <section class="py-12 sm:py-16 bg-lb-off-white min-h-[500px]">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6 space-y-6">
            <div class="flex items-center justify-between border-b border-neutral-200 pb-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-900">
                        Courses Taught by {{ $instructor->name }}
                    </h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Explore active batches, physical classroom intakes, and online self-paced courses.</p>
                </div>

                <a href="{{ route('courses.index') }}" class="text-xs font-semibold text-lb-orange hover:underline hidden sm:inline">
                    Browse All Catalog &rarr;
                </a>
            </div>

            @if ($publishedCourses->isEmpty())
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-neutral-100 flex items-center justify-center mx-auto text-neutral-400">
                        <i class="fas fa-book-open text-base"></i>
                    </div>
                    <h3 class="text-base font-bold text-neutral-900">No published courses right now</h3>
                    <p class="text-xs text-neutral-500 max-w-sm mx-auto">
                        This instructor is actively developing upcoming curriculum modules. Check back soon for new course releases.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($publishedCourses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>

<x-layouts.public title="LearnBuddy · Smart Learning Platform Sri Lanka">
    <!-- HERO SECTION -->
    <section class="bg-lb-peach pt-8 sm:pt-14 pb-16 sm:pb-24 rounded-b-[32px] sm:rounded-b-[48px] overflow-hidden">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-14">
                <!-- Hero Left Copy -->
                <div class="flex-1 max-w-xl text-center lg:text-left">
                    <x-badge variant="white" icon="fas fa-star text-lb-orange" class="mb-4">
                        eLearning Platform
                    </x-badge>

                    <h1 class="text-4xl sm:text-5xl lg:text-[3.8rem] font-extrabold tracking-tight text-lb-black leading-[1.1] mb-5">
                        Smart Learning<br>
                        Deeper &amp; More<br>
                        <span class="text-lb-orange">-Amazing</span>
                    </h1>

                    <p class="text-neutral-600 text-sm sm:text-base leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0">
                        Sri Lanka's premier destination for verified online and physical classroom learning. Gain real-world software, design, and business skills with recognized instructors.
                    </p>

                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4">
                        <x-button
                            as="a"
                            href="{{ route('courses.index') }}"
                            variant="primary"
                            size="lg"
                            icon="fas fa-arrow-right"
                        >
                            Explore Courses
                        </x-button>

                        <button
                            type="button"
                            @click="$dispatch('open-modal', 'search-modal')"
                            class="inline-flex items-center gap-2.5 font-semibold text-sm text-neutral-800 hover:text-lb-orange transition py-2 px-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded-full cursor-pointer"
                        >
                            <span class="w-10 h-10 rounded-full bg-white shadow-lb-sm flex items-center justify-center text-lb-orange shrink-0">
                                <i class="fas fa-search text-xs" aria-hidden="true"></i>
                            </span>
                            <span>Search Curriculum</span>
                        </button>
                    </div>
                </div>

                <!-- Hero Right Image with Teal Angle Block -->
                <div class="flex-1 w-full max-w-md lg:max-w-none flex items-center justify-center relative mt-4 lg:mt-0">
                    <div class="relative w-full max-w-[420px] sm:max-w-[480px]">
                        <!-- Decorative Teal Tilt Box -->
                        <div class="hidden sm:block absolute top-4 -right-4 w-[320px] sm:w-[380px] h-[320px] sm:h-[380px] bg-[#1ABC9C] rounded-[36px] rotate-[10deg] -z-0"></div>

                        <!-- Hero Student Image -->
                        @if (file_exists(public_path('images/hero.png')))
                            <img
                                src="{{ asset('images/hero.png') }}"
                                alt="LearnBuddy Student Learning"
                                class="relative z-10 w-full h-auto object-contain drop-shadow-2xl mx-auto"
                                loading="eager"
                            >
                        @else
                            <div class="relative z-10 w-full h-80 bg-white/60 rounded-3xl flex items-center justify-center border border-white">
                                <span class="text-neutral-500 font-semibold">LearnBuddy Hero Graphic</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT & STATS SECTION -->
    <section id="about" class="py-16 sm:py-24 bg-white text-center">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <div class="max-w-3xl mx-auto mb-14 sm:mb-18 space-y-3">
                <x-badge variant="white" icon="fas fa-circle text-[8px] text-lb-orange">
                    Who We Are
                </x-badge>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-lb-black leading-snug">
                    We are passionate about <span class="text-lb-orange font-extrabold">empowering learners</span> across Sri Lanka with high-quality, accessible &amp; engaging education.
                </h2>
            </div>

            <!-- Stats Counter Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-12 max-w-3xl mx-auto">
                <div class="space-y-1">
                    <div class="text-4xl sm:text-5xl font-extrabold text-lb-black tracking-tight">
                        25<span class="text-lb-orange">+</span>
                    </div>
                    <p class="text-xs sm:text-sm text-neutral-500 leading-snug">
                        Years of Education &amp;<br>Industry Experience
                    </p>
                </div>
                <div class="space-y-1">
                    <div class="text-4xl sm:text-5xl font-extrabold text-lb-black tracking-tight">
                        56k<span class="text-lb-orange">+</span>
                    </div>
                    <p class="text-xs sm:text-sm text-neutral-500 leading-snug">
                        Students Enrolled in<br>Diverse Programs
                    </p>
                </div>
                <div class="space-y-1">
                    <div class="text-4xl sm:text-5xl font-extrabold text-lb-black tracking-tight">
                        170<span class="text-lb-orange">+</span>
                    </div>
                    <p class="text-xs sm:text-sm text-neutral-500 leading-snug">
                        Experienced Mentors<br>&amp; Industry Practitioners
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- COURSES DISCOVERY PREVIEW SECTION -->
    <section id="courses" class="py-16 sm:py-24 bg-lb-off-white">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <!-- Section Header & Filter Bar -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <x-badge variant="orange" icon="fas fa-book-open" class="mb-2">
                        Our Courses
                    </x-badge>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-lb-black">
                        Explore Our Courses
                    </h2>
                </div>

                <!-- Fast Filter Input -->
                <form action="{{ route('courses.index') }}" method="GET" class="w-full md:w-auto">
                    <div class="flex items-center bg-white p-1.5 rounded-full shadow-lb-sm border border-neutral-200/80 max-w-md">
                        <input
                            type="text"
                            name="q"
                            placeholder="Search Courses..."
                            class="w-full sm:w-56 px-4 py-1.5 text-xs sm:text-sm bg-transparent border-0 focus:outline-none placeholder-neutral-400"
                        >
                        <select name="category" class="hidden sm:block text-xs bg-transparent border-l border-neutral-200 pl-3 pr-2 py-1 text-neutral-600 focus:outline-none cursor-pointer">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button
                            type="submit"
                            class="w-9 h-9 rounded-full bg-lb-orange text-white flex items-center justify-center shrink-0 hover:bg-lb-orange-hover transition cursor-pointer"
                            aria-label="Submit search"
                        >
                            <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Course Cards Grid (Responsive: 1 col on mobile, 2 col on tablet, 3 col on desktop) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($featuredCourses as $course)
                    <x-course-card :course="$course" />
                @empty
                    <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-neutral-200/80 p-8">
                        <div class="w-12 h-12 rounded-full bg-orange-50 text-lb-orange flex items-center justify-center mx-auto mb-3 text-xl">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="text-base font-bold text-neutral-900 mb-1">No Published Courses Yet</h3>
                        <p class="text-xs text-neutral-500 max-w-sm mx-auto">
                            Check back soon as our verified instructors are currently preparing upcoming curriculums.
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- See All Action -->
            <div class="mt-12 text-center">
                <x-button
                    as="a"
                    href="{{ route('courses.index') }}"
                    variant="teal"
                    size="md"
                    icon="fas fa-arrow-right"
                >
                    Explore All Published Courses
                </x-button>
            </div>
        </div>
    </section>

    <!-- CATEGORIES SECTION -->
    <section id="categories" class="py-16 sm:py-24 bg-lb-peach">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <div class="text-center max-w-xl mx-auto mb-12 space-y-2">
                <x-badge variant="orange" icon="fas fa-layer-group">
                    Top Categories
                </x-badge>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-lb-black">
                    Choose Your Favorite Course<br>From Top Categories
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($categories as $cat)
                    <a href="{{ route('courses.index', ['category' => $cat->slug]) }}" class="bg-white p-6 rounded-2xl shadow-lb-sm flex items-start gap-4 hover:-translate-y-1 transition duration-300 group border border-transparent hover:border-orange-200">
                        <div class="w-12 h-12 rounded-full bg-orange-50 group-hover:bg-lb-orange group-hover:text-white transition text-lb-orange flex items-center justify-center shrink-0 text-xl">
                            <i class="{{ $cat->icon ?: 'fas fa-graduation-cap' }}"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-neutral-900 group-hover:text-lb-orange transition mb-1">{{ $cat->name }}</h3>
                            <p class="text-xs text-neutral-500 mb-2 line-clamp-2 leading-relaxed">{{ $cat->description }}</p>
                            <span class="text-xs font-bold text-lb-orange">+{{ $cat->published_courses_count }} Courses</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center text-xs text-neutral-400 py-6">
                        No active categories.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- FAQ SECTION (Alpine.js Accordion with 45deg rotation) -->
    <section id="faq" class="py-16 sm:py-24 bg-white">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">
                <!-- FAQ Left -->
                <div class="lg:col-span-5 space-y-3">
                    <x-badge variant="orange" icon="fas fa-question-circle">
                        Questions
                    </x-badge>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-lb-black">
                        Frequently Asked Questions
                    </h2>
                    <p class="text-sm text-neutral-600 leading-relaxed max-w-md">
                        Have questions about physical classroom schedules, LKR payments, or course certificates? Browse our key answers below.
                    </p>
                </div>

                <!-- FAQ Right Accordion (Alpine.js) -->
                <div
                    x-data="{ active: 0 }"
                    class="lg:col-span-7 space-y-3"
                >
                    <!-- Item 1 -->
                    <div class="border border-neutral-200 rounded-2xl overflow-hidden transition-colors" :class="{ 'border-lb-orange bg-orange-50/20': active === 0 }">
                        <button
                            type="button"
                            @click="active = active === 0 ? null : 0"
                            class="w-full px-5 py-4 flex items-center justify-between text-left font-semibold text-sm sm:text-base text-neutral-900 focus:outline-none cursor-pointer"
                        >
                            <span>How do physical classroom batches work in Sri Lanka?</span>
                            <span class="w-7 h-7 rounded-full bg-white shadow-lb-sm flex items-center justify-center text-lb-orange shrink-0 transition-transform duration-300" :class="{ 'rotate-45 text-red-500': active === 0 }">
                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            </span>
                        </button>
                        <div x-show="active === 0" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                            Physical batches are held at verified campuses (such as Colombo Central Campus). When you select a physical delivery option, you reserve a seat in an scheduled intake with attendance tracking and on-site mentoring.
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="border border-neutral-200 rounded-2xl overflow-hidden transition-colors" :class="{ 'border-lb-orange bg-orange-50/20': active === 1 }">
                        <button
                            type="button"
                            @click="active = active === 1 ? null : 1"
                            class="w-full px-5 py-4 flex items-center justify-between text-left font-semibold text-sm sm:text-base text-neutral-900 focus:outline-none cursor-pointer"
                        >
                            <span>What payment methods are supported?</span>
                            <span class="w-7 h-7 rounded-full bg-white shadow-lb-sm flex items-center justify-center text-lb-orange shrink-0 transition-transform duration-300" :class="{ 'rotate-45 text-red-500': active === 1 }">
                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            </span>
                        </button>
                        <div x-show="active === 1" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                            We support local Sri Lankan payments in LKR through major credit/debit cards (Visa, Mastercard), local payment gateways, and direct bank transfer slips with automated verification.
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="border border-neutral-200 rounded-2xl overflow-hidden transition-colors" :class="{ 'border-lb-orange bg-orange-50/20': active === 2 }">
                        <button
                            type="button"
                            @click="active = active === 2 ? null : 2"
                            class="w-full px-5 py-4 flex items-center justify-between text-left font-semibold text-sm sm:text-base text-neutral-900 focus:outline-none cursor-pointer"
                        >
                            <span>Can I learn on mobile devices?</span>
                            <span class="w-7 h-7 rounded-full bg-white shadow-lb-sm flex items-center justify-center text-lb-orange shrink-0 transition-transform duration-300" :class="{ 'rotate-45 text-red-500': active === 2 }">
                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            </span>
                        </button>
                        <div x-show="active === 2" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                            Yes! Every LearnBuddy page, video player, and interactive quiz is mobile-first and tested from small 360px phones up to tablets and desktops.
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="border border-neutral-200 rounded-2xl overflow-hidden transition-colors" :class="{ 'border-lb-orange bg-orange-50/20': active === 3 }">
                        <button
                            type="button"
                            @click="active = active === 3 ? null : 3"
                            class="w-full px-5 py-4 flex items-center justify-between text-left font-semibold text-sm sm:text-base text-neutral-900 focus:outline-none cursor-pointer"
                        >
                            <span>Will I receive a verifiable certificate?</span>
                            <span class="w-7 h-7 rounded-full bg-white shadow-lb-sm flex items-center justify-center text-lb-orange shrink-0 transition-transform duration-300" :class="{ 'rotate-45 text-red-500': active === 3 }">
                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            </span>
                        </button>
                        <div x-show="active === 3" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                            Upon fulfilling course requirements, quizzes, and assignment pass marks, students receive a tamper-evident digital certificate with a public QR verification hash.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION SECTION (Reference UI Style) -->
    <section class="py-16 sm:py-20 bg-lb-off-white">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Box 1 -->
                <div class="bg-white p-8 sm:p-12 rounded-3xl border border-neutral-200/80 shadow-lb-sm flex flex-col justify-between">
                    <div class="space-y-3 mb-6">
                        <i class="fas fa-chalkboard-teacher text-3xl text-lb-orange" aria-hidden="true"></i>
                        <h3 class="text-2xl font-bold text-lb-black">Do You Want to Teach Here?</h3>
                        <p class="text-sm text-neutral-600 leading-relaxed">
                            Share your expertise with thousands of Sri Lankan students. Create online courses or lead weekend classroom batches at our partner campuses.
                        </p>
                    </div>
                    <div>
                        <x-button as="a" href="{{ url('/design-system') }}" variant="dark">
                            Instructor Guide
                        </x-button>
                    </div>
                </div>

                <!-- Box 2 (Teal) -->
                <div class="bg-lb-teal text-white p-8 sm:p-12 rounded-3xl shadow-lb-sm flex flex-col justify-between">
                    <div class="space-y-3 mb-6">
                        <i class="fas fa-user-graduate text-3xl text-white" aria-hidden="true"></i>
                        <h3 class="text-2xl font-bold">Do You Want to Learn Here?</h3>
                        <p class="text-sm text-white/90 leading-relaxed">
                            Accelerate your career with accredited, modern courses. Discover in-demand programming, design, and software engineering batches.
                        </p>
                    </div>
                    <div>
                        <x-button as="a" href="{{ route('courses.index') }}" variant="dark">
                            Join Now
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>

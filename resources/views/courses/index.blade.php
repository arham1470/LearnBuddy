<x-layouts.public title="Course Catalogue · LearnBuddy Sri Lanka">
    <!-- CATALOGUE HERO & SEARCH HEADER -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-12 sm:pb-16 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">Course Catalogue</span>
            </nav>

            <div class="max-w-2xl">
                <x-badge variant="orange" icon="fas fa-search" class="mb-3">
                    Curriculum Discovery
                </x-badge>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-lb-black leading-tight mb-3">
                    Explore Courses in <span class="text-lb-orange">Sri Lanka</span>
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">
                    Verified online streams, physical campus laboratory batches in Colombo and Kandy, and dual-mode professional curriculums.
                </p>
            </div>

            <!-- Top Search Input Bar -->
            <div class="mt-8 max-w-3xl">
                <form action="{{ route('courses.index') }}" method="GET" class="bg-white p-2 rounded-2xl sm:rounded-full shadow-lb-sm border border-neutral-200/80 flex flex-col sm:flex-row items-center gap-2">
                    <!-- Preserve existing non-search filters -->
                    @if (request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if (request('delivery_mode')) <input type="hidden" name="delivery_mode" value="{{ request('delivery_mode') }}"> @endif
                    @if (request('language')) <input type="hidden" name="language" value="{{ request('language') }}"> @endif
                    @if (request('difficulty')) <input type="hidden" name="difficulty" value="{{ request('difficulty') }}"> @endif
                    @if (request('price')) <input type="hidden" name="price" value="{{ request('price') }}"> @endif
                    @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    <div class="relative flex-1 w-full flex items-center">
                        <i class="fas fa-search text-neutral-400 pl-4 pr-3 text-sm" aria-hidden="true"></i>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search by title, topics, Laravel, UI/UX, database..."
                            class="w-full bg-transparent border-0 text-xs sm:text-sm focus:outline-none focus:ring-0 placeholder-neutral-400 py-2.5"
                        >
                        @if (request('q'))
                            <a href="{{ route('courses.index', request()->except('q')) }}" class="pr-3 text-neutral-400 hover:text-neutral-700 text-xs" title="Clear search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-semibold transition shrink-0 cursor-pointer shadow-sm"
                    >
                        Search Courses
                    </button>
                </form>

                <!-- Category Quick Pills -->
                <div class="flex items-center gap-2 mt-4 overflow-x-auto pb-1 no-overflow-x scrollbar-none text-xs">
                    <span class="text-neutral-400 font-semibold shrink-0">Popular:</span>
                    <a
                        href="{{ route('courses.index') }}"
                        class="px-3 py-1 rounded-full whitespace-nowrap transition {{ !request('category') ? 'bg-neutral-900 text-white font-semibold' : 'bg-white text-neutral-600 hover:bg-orange-50 border border-neutral-200' }}"
                    >
                        All Categories
                    </a>
                    @foreach ($categories as $cat)
                        <a
                            href="{{ route('courses.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                            class="px-3 py-1 rounded-full whitespace-nowrap transition {{ request('category') === $cat->slug ? 'bg-lb-orange text-white font-semibold' : 'bg-white text-neutral-600 hover:bg-orange-50 border border-neutral-200' }}"
                        >
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- CATALOGUE BODY: SIDEBAR + RESULTS -->
    <section class="py-10 sm:py-16 bg-lb-off-white min-h-[600px]" x-data="{ mobileFiltersOpen: false }">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <!-- Mobile Filters Toggle & Results Summary Row -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-neutral-200/80">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="mobileFiltersOpen = !mobileFiltersOpen"
                        class="lg:hidden inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-neutral-200 text-xs font-bold text-neutral-800 shadow-sm hover:border-neutral-300 transition cursor-pointer"
                    >
                        <i class="fas fa-sliders-h text-lb-orange" aria-hidden="true"></i>
                        <span>Filter Courses</span>
                        @if ($activeFiltersCount > 0)
                            <span class="w-5 h-5 rounded-full bg-lb-orange text-white text-[10px] flex items-center justify-center font-bold">
                                {{ $activeFiltersCount }}
                            </span>
                        @endif
                    </button>

                    <p class="text-xs text-neutral-600 font-medium">
                        Showing <span class="font-bold text-neutral-900">{{ $courses->total() }}</span> published {{ Str::plural('course', $courses->total()) }}
                    </p>
                </div>

                <!-- Sort Control -->
                <form action="{{ route('courses.index') }}" method="GET" class="flex items-center gap-2 text-xs">
                    @foreach (request()->except(['sort', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach

                    <label for="sort-select" class="text-neutral-500 font-medium whitespace-nowrap hidden sm:inline">Sort by:</label>
                    <select
                        id="sort-select"
                        name="sort"
                        onchange="this.form.submit()"
                        class="bg-white border border-neutral-200 rounded-full px-4 py-2 text-xs font-semibold text-neutral-800 focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer shadow-sm"
                    >
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest Releases</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="duration_asc" {{ $sort === 'duration_asc' ? 'selected' : '' }}>Duration: Shortest</option>
                        <option value="duration_desc" {{ $sort === 'duration_desc' ? 'selected' : '' }}>Duration: Longest</option>
                    </select>
                </form>
            </div>

            <!-- Active Filter Chips -->
            @if ($activeFiltersCount > 0)
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="text-xs font-bold text-neutral-500 uppercase tracking-wider mr-1">Active:</span>

                    @if (request('q'))
                        <a href="{{ route('courses.index', request()->except(['q', 'page'])) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold hover:bg-orange-200 transition">
                            <span>Keyword: "{{ request('q') }}"</span>
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif

                    @if (request('category'))
                        @php $activeCat = $categories->firstWhere('slug', request('category')); @endphp
                        <a href="{{ route('courses.index', request()->except(['category', 'page'])) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-neutral-200 text-neutral-800 text-xs font-semibold hover:bg-neutral-300 transition">
                            <span>Category: {{ $activeCat->name ?? request('category') }}</span>
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif

                    @if (request('delivery_mode'))
                        <a href="{{ route('courses.index', request()->except(['delivery_mode', 'page'])) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold hover:bg-blue-200 transition">
                            <span>Mode: {{ ucfirst(request('delivery_mode')) }}</span>
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif

                    @if (request('price'))
                        <a href="{{ route('courses.index', request()->except(['price', 'page'])) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold hover:bg-emerald-200 transition">
                            <span>Price: {{ ucfirst(request('price')) }}</span>
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif

                    @if (request('language'))
                        <a href="{{ route('courses.index', request()->except(['language', 'page'])) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold hover:bg-purple-200 transition">
                            <span>Language: {{ request('language') }}</span>
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif

                    @if (request('difficulty'))
                        <a href="{{ route('courses.index', request()->except(['difficulty', 'page'])) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-neutral-200 text-neutral-800 text-xs font-semibold hover:bg-neutral-300 transition">
                            <span>Level: {{ ucfirst(str_replace('_', ' ', request('difficulty'))) }}</span>
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    @endif

                    <a href="{{ route('courses.index') }}" class="text-xs text-neutral-500 hover:text-rose-600 underline ml-2 transition">
                        Clear all
                    </a>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- SIDEBAR FILTERS (Desktop & Mobile Drawer) -->
                <aside
                    class="lg:col-span-3"
                    :class="{ 'fixed inset-0 z-50 bg-neutral-900/60 backdrop-blur-sm flex justify-start': mobileFiltersOpen, 'hidden lg:block': !mobileFiltersOpen }"
                >
                    <div
                        class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 space-y-6 w-full lg:w-auto max-w-sm lg:max-w-none overflow-y-auto max-h-[90vh] lg:max-h-none"
                        @click.outside="mobileFiltersOpen = false"
                    >
                        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
                            <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                                <i class="fas fa-filter text-lb-orange text-xs" aria-hidden="true"></i>
                                <span>Filter By</span>
                            </h2>

                            @if ($activeFiltersCount > 0)
                                <a href="{{ route('courses.index') }}" class="text-[11px] font-semibold text-rose-600 hover:underline">
                                    Reset
                                </a>
                            @endif

                            <button
                                type="button"
                                @click="mobileFiltersOpen = false"
                                class="lg:hidden p-1 text-neutral-400 hover:text-neutral-700"
                                aria-label="Close filters"
                            >
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>

                        <form action="{{ route('courses.index') }}" method="GET" class="space-y-6">
                            @if (request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                            @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                            <!-- Category Filter -->
                            <div class="space-y-2.5">
                                <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider block">Category</span>
                                <div class="space-y-1.5">
                                    <label class="flex items-center justify-between text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                            <span>All Categories</span>
                                        </div>
                                    </label>
                                    @foreach ($categories as $cat)
                                        <label class="flex items-center justify-between text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="radio"
                                                    name="category"
                                                    value="{{ $cat->slug }}"
                                                    {{ request('category') === $cat->slug ? 'checked' : '' }}
                                                    onchange="this.form.submit()"
                                                    class="text-lb-orange focus:ring-lb-orange"
                                                >
                                                <span>{{ $cat->name }}</span>
                                            </div>
                                            <span class="text-[11px] font-semibold text-neutral-400">({{ $cat->published_courses_count }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Delivery Mode Filter -->
                            <div class="pt-4 border-t border-neutral-100 space-y-2.5">
                                <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider block">Delivery Mode</span>
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="delivery_mode" value="" {{ !request('delivery_mode') ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>All Delivery Modes</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="delivery_mode" value="online" {{ request('delivery_mode') === 'online' ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>Online Self-Paced</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="delivery_mode" value="physical" {{ request('delivery_mode') === 'physical' ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>Physical Classroom Batch</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Price Filter -->
                            <div class="pt-4 border-t border-neutral-100 space-y-2.5">
                                <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider block">Tuition Price</span>
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="price" value="" {{ !request('price') ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>All Prices</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="price" value="free" {{ request('price') === 'free' ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>Free Courses</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="price" value="paid" {{ request('price') === 'paid' ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>Paid (LKR)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Language Filter -->
                            @if ($availableLanguages->isNotEmpty())
                                <div class="pt-4 border-t border-neutral-100 space-y-2.5">
                                    <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider block">Language</span>
                                    <div class="space-y-1.5">
                                        <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                            <input type="radio" name="language" value="" {{ !request('language') ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                            <span>All Languages</span>
                                        </label>
                                        @foreach ($availableLanguages as $lang)
                                            <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                                <input type="radio" name="language" value="{{ $lang }}" {{ request('language') === $lang ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                                <span>{{ $lang }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Difficulty Level Filter -->
                            <div class="pt-4 border-t border-neutral-100 space-y-2.5">
                                <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider block">Difficulty</span>
                                <div class="space-y-1.5">
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="difficulty" value="" {{ !request('difficulty') ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>All Levels</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="difficulty" value="beginner" {{ request('difficulty') === 'beginner' ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>Beginner</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="difficulty" value="intermediate" {{ request('difficulty') === 'intermediate' ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>Intermediate</span>
                                    </label>
                                    <label class="flex items-center gap-2 text-xs text-neutral-700 hover:text-lb-orange cursor-pointer py-1">
                                        <input type="radio" name="difficulty" value="advanced" {{ request('difficulty') === 'advanced' ? 'checked' : '' }} onchange="this.form.submit()" class="text-lb-orange focus:ring-lb-orange">
                                        <span>Advanced</span>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </aside>

                <!-- MAIN COURSES GRID -->
                <main class="lg:col-span-9 space-y-8">
                    @if ($courses->isEmpty())
                        <!-- Empty Search/Filter State -->
                        <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-10 sm:p-16 text-center space-y-4">
                            <div class="w-16 h-16 rounded-full bg-orange-50 text-lb-orange flex items-center justify-center mx-auto text-2xl shadow-inner">
                                <i class="fas fa-search-minus" aria-hidden="true"></i>
                            </div>
                            <h3 class="text-lg font-bold text-neutral-900">No matching published courses</h3>
                            <p class="text-xs sm:text-sm text-neutral-500 max-w-md mx-auto leading-relaxed">
                                We couldn't find any courses matching your specific search query or active filter combination.
                            </p>
                            <div class="pt-2">
                                <a
                                    href="{{ route('courses.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold transition cursor-pointer"
                                >
                                    <i class="fas fa-undo text-xs"></i>
                                    <span>Reset All Filters</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Course Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach ($courses as $course)
                                <x-course-card :course="$course" />
                            @endforeach
                        </div>

                        <!-- Pagination Links -->
                        <div class="pt-4 flex justify-center">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>
</x-layouts.public>

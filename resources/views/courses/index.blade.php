<x-layouts.public title="Course Catalogue · LearnBuddy Sri Lanka">
@php
    $currentCategory = $categories->firstWhere('slug', request('category'));
    $selectedCategoryName = $currentCategory ? $currentCategory->name : 'All Categories';
    $selectedCategoryIcon = $currentCategory ? ($currentCategory->icon ?: 'fas fa-layer-group') : 'fas fa-th-large';
@endphp

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
                <form action="{{ route('courses.index') }}" method="GET" class="bg-white p-2 sm:p-2.5 rounded-2xl shadow-lb-sm border border-neutral-200/80 flex flex-col sm:flex-row items-center gap-2">
                    <!-- Preserve existing non-search filters -->
                    @if (request('delivery_mode')) <input type="hidden" name="delivery_mode" value="{{ request('delivery_mode') }}"> @endif
                    @if (request('language')) <input type="hidden" name="language" value="{{ request('language') }}"> @endif
                    @if (request('difficulty')) <input type="hidden" name="difficulty" value="{{ request('difficulty') }}"> @endif
                    @if (request('price')) <input type="hidden" name="price" value="{{ request('price') }}"> @endif
                    @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    <!-- 1. Text Input -->
                    <div class="relative flex-1 w-full min-w-0">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                            <i class="fas fa-search text-sm" aria-hidden="true"></i>
                        </div>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search by title, topics, Laravel, UI/UX, database..."
                            class="w-full bg-transparent border-0 text-xs sm:text-sm placeholder-neutral-400 py-2.5 pl-10 pr-10 focus:outline-none focus:ring-0 focus-visible:outline-none !outline-none !ring-0 !border-0"
                            style="outline: none !important; box-shadow: none !important; border: none !important;"
                        >
                        @if (request('q'))
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <a href="{{ route('courses.index', request()->except('q')) }}" class="text-neutral-400 hover:text-neutral-700 text-xs p-1 rounded-full transition" title="Clear search keyword">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Subtle Divider on Desktop -->
                    <div class="hidden sm:block h-7 w-px bg-neutral-200/80 shrink-0"></div>

                    <!-- 2. Category Selector Dropdown -->
                    <div
                        x-data="{
                            open: false,
                            selectedSlug: @js(request('category', '')),
                            selectedName: @js($selectedCategoryName),
                            selectedIcon: @js($selectedCategoryIcon),
                            selectCategory(slug, name, icon) {
                                this.selectedSlug = slug;
                                this.selectedName = name;
                                this.selectedIcon = icon;
                                this.open = false;
                            }
                        }"
                        @click.outside="open = false"
                        @keydown.escape.window="open = false"
                        class="relative w-full sm:w-auto shrink-0"
                    >
                        <!-- Hidden input for category submission -->
                        <input type="hidden" name="category" :value="selectedSlug">

                        <!-- Trigger Button -->
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full sm:w-auto bg-neutral-50 hover:bg-neutral-100/80 text-xs border border-neutral-200 hover:border-neutral-300 rounded-xl px-3.5 py-2.5 text-neutral-700 inline-flex items-center justify-between gap-2.5 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer shadow-xs min-w-[150px]"
                            :class="{ 'border-lb-orange ring-1 ring-lb-orange bg-orange-50/40': open }"
                            aria-haspopup="listbox"
                            :aria-expanded="open.toString()"
                        >
                            <div class="flex items-center gap-2 truncate">
                                <i :class="selectedIcon" class="text-neutral-400 text-xs shrink-0" aria-hidden="true"></i>
                                <span class="font-medium text-neutral-800 truncate" x-text="selectedName"></span>
                            </div>
                            <i
                                class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0 ml-1"
                                :class="{ 'rotate-180 text-lb-orange': open }"
                                aria-hidden="true"
                            ></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1 sm:translate-y-1 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-1 sm:translate-y-1 sm:scale-95"
                            class="w-full sm:w-72 absolute left-0 sm:left-auto sm:right-0 top-full mt-1.5 rounded-2xl bg-white shadow-xl border border-neutral-100 p-1.5 z-50 text-xs"
                            role="listbox"
                        >
                            <div class="px-2.5 py-1.5 text-[10px] font-bold text-neutral-400 uppercase tracking-wider flex items-center justify-between border-b border-neutral-100 mb-1">
                                <span>Filter by Category</span>
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="sm:hidden text-neutral-400 hover:text-neutral-600 p-0.5"
                                    aria-label="Close category list"
                                >
                                    <i class="fas fa-times text-[10px]" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="max-h-56 overflow-y-auto space-y-0.5 overscroll-contain">
                                <!-- All Categories Option -->
                                <button
                                    type="button"
                                    @click="selectCategory('', 'All Categories', 'fas fa-th-large')"
                                    class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl transition text-left cursor-pointer group active:scale-[0.99]"
                                    :class="selectedSlug === '' ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
                                    role="option"
                                    :aria-selected="(selectedSlug === '').toString()"
                                >
                                    <div class="flex items-center gap-2.5 truncate">
                                        <span class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition" :class="selectedSlug === '' ? 'bg-orange-100 text-lb-orange' : 'bg-neutral-100 text-neutral-500 group-hover:bg-white group-hover:text-neutral-800'">
                                            <i class="fas fa-th-large text-[11px]" aria-hidden="true"></i>
                                        </span>
                                        <span class="truncate font-medium">All Categories</span>
                                    </div>
                                    <i class="fas fa-check text-xs text-lb-orange shrink-0 ml-2" x-show="selectedSlug === ''" aria-hidden="true"></i>
                                </button>

                                @foreach ($categories as $cat)
                                    <button
                                        type="button"
                                        @click="selectCategory(@js($cat->slug), @js($cat->name), @js($cat->icon ?: 'fas fa-layer-group'))"
                                        class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl transition text-left cursor-pointer group active:scale-[0.99]"
                                        :class="selectedSlug === @js($cat->slug) ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
                                        role="option"
                                        :aria-selected="(selectedSlug === @js($cat->slug)).toString()"
                                    >
                                        <div class="flex items-center gap-2.5 truncate">
                                            <span class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition" :class="selectedSlug === @js($cat->slug) ? 'bg-orange-100 text-lb-orange' : 'bg-neutral-100 text-neutral-500 group-hover:bg-white group-hover:text-neutral-800'">
                                                <i class="{{ $cat->icon ?: 'fas fa-layer-group' }} text-[11px]" aria-hidden="true"></i>
                                            </span>
                                            <span class="truncate font-medium">{{ $cat->name }}</span>
                                        </div>
                                        <i class="fas fa-check text-xs text-lb-orange shrink-0 ml-2" x-show="selectedSlug === @js($cat->slug)" aria-hidden="true"></i>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 3. Search Submit Button -->
                    <button
                        type="submit"
                        class="w-full sm:w-auto bg-lb-orange hover:bg-lb-orange-hover text-white rounded-xl px-6 py-2.5 text-xs font-bold transition shrink-0 cursor-pointer shadow-xs flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-search text-xs"></i>
                        <span>Search</span>
                    </button>
                </form>
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
                @php
                    $sortLabels = [
                        'newest' => 'Newest Releases',
                        'price_asc' => 'Price: Low to High',
                        'price_desc' => 'Price: High to Low',
                        'duration_asc' => 'Duration: Shortest',
                        'duration_desc' => 'Duration: Longest',
                    ];
                    $currentSortLabel = $sortLabels[$sort ?? 'newest'] ?? 'Newest Releases';
                @endphp
                <form action="{{ route('courses.index') }}" method="GET" class="flex items-center gap-2 text-xs">
                    @foreach (request()->except(['sort', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach

                    <label for="sort-select" class="text-neutral-500 font-medium whitespace-nowrap hidden sm:inline">Sort by:</label>

                    <!-- Native select for standard form submission and selector compatibility -->
                    <select
                        id="sort-select"
                        name="sort"
                        onchange="this.form.submit()"
                        class="sr-only"
                        tabindex="-1"
                        aria-hidden="true"
                    >
                        <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Newest Releases</option>
                        <option value="price_asc" {{ ($sort ?? '') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ ($sort ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="duration_asc" {{ ($sort ?? '') === 'duration_asc' ? 'selected' : '' }}>Duration: Shortest</option>
                        <option value="duration_desc" {{ ($sort ?? '') === 'duration_desc' ? 'selected' : '' }}>Duration: Longest</option>
                    </select>

                    <!-- Custom Dropdown Menu Component -->
                    <div
                        x-data="{
                            open: false,
                            selected: @js($sort ?? 'newest'),
                            selectedLabel: @js($currentSortLabel),
                            choose(val, label) {
                                this.selected = val;
                                this.selectedLabel = label;
                                this.open = false;
                                const selectEl = document.getElementById('sort-select');
                                if (selectEl) {
                                    selectEl.value = val;
                                    selectEl.form.submit();
                                }
                            }
                        }"
                        @click.outside="open = false"
                        @keydown.escape.window="open = false"
                        class="relative inline-block text-left"
                    >
                        <!-- Pill-shaped Trigger matching search bar -->
                        <button
                            type="button"
                            @click="open = !open"
                            class="bg-white hover:bg-neutral-50 border border-neutral-200 hover:border-neutral-300 rounded-full px-4 py-2 text-xs font-semibold text-neutral-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer shadow-sm inline-flex items-center gap-2 select-none"
                            :class="{ 'border-lb-orange ring-1 ring-lb-orange bg-orange-50/20': open }"
                            aria-haspopup="listbox"
                            :aria-expanded="open.toString()"
                        >
                            <span x-text="selectedLabel">{{ $currentSortLabel }}</span>
                            <i
                                class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0"
                                :class="{ 'rotate-180 text-lb-orange': open }"
                                aria-hidden="true"
                            ></i>
                        </button>

                        <!-- Custom Menu Component with Rounded Corners, Drop Shadow & Soft Orange / Dark Gray States -->
                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                            class="w-48 sm:w-52 absolute right-0 top-full mt-1.5 rounded-2xl bg-white shadow-xl border border-neutral-100 p-1.5 z-50 text-xs"
                            role="listbox"
                        >
                            <div class="px-2.5 py-1.5 text-[10px] font-bold text-neutral-400 uppercase tracking-wider border-b border-neutral-100 mb-1">
                                Sort Courses
                            </div>

                            <div class="space-y-0.5">
                                <button
                                    type="button"
                                    @click="choose('newest', 'Newest Releases')"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl transition text-left cursor-pointer group active:scale-[0.99]"
                                    :class="selected === 'newest' ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
                                    role="option"
                                    :aria-selected="(selected === 'newest').toString()"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fas fa-sparkles text-[10px]" :class="selected === 'newest' ? 'text-lb-orange' : 'text-neutral-400 group-hover:text-neutral-700'" aria-hidden="true"></i>
                                        <span class="truncate">Newest Releases</span>
                                    </div>
                                    <i class="fas fa-check text-xs text-lb-orange shrink-0 ml-2" x-show="selected === 'newest'" aria-hidden="true"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="choose('price_asc', 'Price: Low to High')"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl transition text-left cursor-pointer group active:scale-[0.99]"
                                    :class="selected === 'price_asc' ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
                                    role="option"
                                    :aria-selected="(selected === 'price_asc').toString()"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fas fa-sort-amount-down-alt text-[10px]" :class="selected === 'price_asc' ? 'text-lb-orange' : 'text-neutral-400 group-hover:text-neutral-700'" aria-hidden="true"></i>
                                        <span class="truncate">Price: Low to High</span>
                                    </div>
                                    <i class="fas fa-check text-xs text-lb-orange shrink-0 ml-2" x-show="selected === 'price_asc'" aria-hidden="true"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="choose('price_desc', 'Price: High to Low')"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl transition text-left cursor-pointer group active:scale-[0.99]"
                                    :class="selected === 'price_desc' ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
                                    role="option"
                                    :aria-selected="(selected === 'price_desc').toString()"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fas fa-sort-amount-up-alt text-[10px]" :class="selected === 'price_desc' ? 'text-lb-orange' : 'text-neutral-400 group-hover:text-neutral-700'" aria-hidden="true"></i>
                                        <span class="truncate">Price: High to Low</span>
                                    </div>
                                    <i class="fas fa-check text-xs text-lb-orange shrink-0 ml-2" x-show="selected === 'price_desc'" aria-hidden="true"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="choose('duration_asc', 'Duration: Shortest')"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl transition text-left cursor-pointer group active:scale-[0.99]"
                                    :class="selected === 'duration_asc' ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
                                    role="option"
                                    :aria-selected="(selected === 'duration_asc').toString()"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fas fa-hourglass-start text-[10px]" :class="selected === 'duration_asc' ? 'text-lb-orange' : 'text-neutral-400 group-hover:text-neutral-700'" aria-hidden="true"></i>
                                        <span class="truncate">Duration: Shortest</span>
                                    </div>
                                    <i class="fas fa-check text-xs text-lb-orange shrink-0 ml-2" x-show="selected === 'duration_asc'" aria-hidden="true"></i>
                                </button>

                                <button
                                    type="button"
                                    @click="choose('duration_desc', 'Duration: Longest')"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl transition text-left cursor-pointer group active:scale-[0.99]"
                                    :class="selected === 'duration_desc' ? 'bg-orange-50 text-lb-orange font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
                                    role="option"
                                    :aria-selected="(selected === 'duration_desc').toString()"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fas fa-hourglass-end text-[10px]" :class="selected === 'duration_desc' ? 'text-lb-orange' : 'text-neutral-400 group-hover:text-neutral-700'" aria-hidden="true"></i>
                                        <span class="truncate">Duration: Longest</span>
                                    </div>
                                    <i class="fas fa-check text-xs text-lb-orange shrink-0 ml-2" x-show="selected === 'duration_desc'" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
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

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
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
                        <!-- Course Grid (Auto-fill Responsive Grid with Uniform Card Heights) -->
                        <div class="grid gap-6" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
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

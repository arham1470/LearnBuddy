@php
    $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    $currentCategory = $categories->firstWhere('slug', request('category'));
    $selectedCategoryName = $currentCategory ? $currentCategory->name : 'All Categories';
    $selectedCategoryIcon = $currentCategory ? ($currentCategory->icon ?: 'fas fa-layer-group') : 'fas fa-th-large';
@endphp

<div
    x-data
    @keydown.window="if ((($event.ctrlKey || $event.metaKey) && $event.key.toLowerCase() === 'k')) { $event.preventDefault(); $dispatch('open-modal', 'search-modal'); }"
>
<x-modal name="search-modal" maxWidth="lg">
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
            <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Search Courses</span>
            <div class="flex items-center gap-2">
                <span class="hidden sm:inline text-xs text-neutral-400">Press <kbd class="px-1.5 py-0.5 bg-neutral-100 rounded border text-[10px]">ESC</kbd> to close</span>
                <button
                    type="button"
                    @click="$dispatch('close-modal', 'search-modal')"
                    class="sm:hidden text-neutral-400 hover:text-neutral-700 p-1 rounded-lg transition"
                    aria-label="Close search"
                >
                    <i class="fas fa-times text-sm" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('courses.index') }}" method="GET">
            <div class="flex flex-col sm:flex-row gap-2 bg-neutral-50 p-2 rounded-2xl border border-neutral-200">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                        <i class="fas fa-search text-sm" aria-hidden="true"></i>
                    </div>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search web dev, Laravel, UI/UX..."
                        class="w-full bg-transparent border-0 text-sm placeholder-neutral-400 py-2.5 sm:py-2 pl-10 pr-3 focus:outline-none focus:ring-0 focus-visible:outline-none !outline-none !ring-0 !border-0"
                        style="outline: none !important; box-shadow: none !important; border: none !important;"
                        autofocus
                    >
                </div>

                <div
                    x-data="{
                        open: false,
                        selectedSlug: @js(request('category', '')),
                        selectedName: @js($selectedCategoryName),
                        selectedIcon: @js($selectedCategoryIcon)
                    }"
                    @click.outside="open = false"
                    @keydown.escape.window="open = false"
                    class="relative flex flex-wrap sm:flex-nowrap items-center gap-2"
                >
                    <!-- Hidden input for standard GET form submission -->
                    <input type="hidden" name="category" :value="selectedSlug">

                    <!-- Trigger Button -->
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex-1 sm:flex-initial bg-white hover:bg-neutral-50 text-xs border border-neutral-200 hover:border-neutral-300 rounded-xl px-3 py-2 text-neutral-700 inline-flex items-center justify-between gap-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange cursor-pointer shadow-xs min-w-0"
                        :class="{ 'border-lb-orange ring-1 ring-lb-orange': open }"
                        aria-haspopup="listbox"
                        :aria-expanded="open.toString()"
                    >
                        <div class="flex items-center gap-2 truncate">
                            <i :class="selectedIcon" class="text-neutral-400 text-xs shrink-0" aria-hidden="true"></i>
                            <span class="font-medium text-neutral-800 truncate sm:whitespace-nowrap" x-text="selectedName"></span>
                        </div>
                        <i
                            class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0 ml-1"
                            :class="{ 'rotate-180 text-lb-orange': open }"
                            aria-hidden="true"
                        ></i>
                    </button>

                    <!-- Search Button -->
                    <button
                        type="submit"
                        class="bg-lb-orange hover:bg-lb-orange-hover text-white rounded-xl px-4 py-2 text-xs font-semibold transition shrink-0 cursor-pointer shadow-xs"
                    >
                        Search
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
                        class="w-full sm:w-72 sm:absolute sm:right-0 sm:top-full mt-1.5 rounded-xl sm:rounded-2xl bg-white shadow-xs sm:shadow-xl border border-neutral-200 sm:border-neutral-100 p-1.5 z-50 text-xs"
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

                        <div class="max-h-52 sm:max-h-60 overflow-y-auto space-y-0.5 overscroll-contain">
                            <!-- All Categories Option -->
                            <button
                                type="button"
                                @click="selectedSlug = ''; selectedName = 'All Categories'; selectedIcon = 'fas fa-th-large'; open = false"
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
                                    @click="selectedSlug = @js($cat->slug); selectedName = @js($cat->name); selectedIcon = @js($cat->icon ?: 'fas fa-layer-group'); open = false"
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
            </div>
        </form>

        <div class="pt-2">
            <span class="text-xs font-semibold text-neutral-500">Popular Searches:</span>
            <div class="flex flex-wrap gap-1.5 mt-2">
                <a href="{{ route('courses.index', ['q' => 'Laravel']) }}" class="px-2.5 py-1 bg-neutral-100 hover:bg-orange-50 hover:text-lb-orange rounded-full text-xs text-neutral-600 transition">
                    Laravel
                </a>
                <a href="{{ route('courses.index', ['delivery_mode' => 'physical']) }}" class="px-2.5 py-1 bg-neutral-100 hover:bg-orange-50 hover:text-lb-orange rounded-full text-xs text-neutral-600 transition">
                    Physical Batches
                </a>
                <a href="{{ route('courses.index', ['price' => 'free']) }}" class="px-2.5 py-1 bg-neutral-100 hover:bg-orange-50 hover:text-lb-orange rounded-full text-xs text-neutral-600 transition">
                    Free Courses
                </a>
                <a href="{{ route('courses.index', ['language' => 'Sinhala']) }}" class="px-2.5 py-1 bg-neutral-100 hover:bg-orange-50 hover:text-lb-orange rounded-full text-xs text-neutral-600 transition">
                    සිංහල පාඨමාලා
                </a>
            </div>
        </div>
    </div>
</x-modal>
</div>

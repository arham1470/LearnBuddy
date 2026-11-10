<x-layouts.public title="Course Management - LearnBuddy" description="Manage curriculum, publication status, delivery modes, and batches.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-7xl mx-auto">
            <!-- Breadcrumbs / Scope Indicator -->
            <div class="mb-4 flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('home') }}" class="hover:text-lb-orange">Home</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Course Management</span>
            </div>

            <!-- Page Header -->
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold tracking-wide uppercase mb-2">
                        <i class="fas fa-layer-group text-xs" aria-hidden="true"></i> Faculty & Administration
                    </div>
                    <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Courses & Curriculum</h1>
                    <p class="text-sm text-neutral-600 mt-1">
                        Author, review, configure delivery modes, and publish courses across Sri Lanka.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @can('create', \App\Models\Category::class)
                        <a
                            href="{{ route('management.categories.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full border border-neutral-300 bg-white text-xs font-semibold text-neutral-700 hover:bg-neutral-50 transition shadow-sm"
                        >
                            <i class="fas fa-tags text-neutral-400" aria-hidden="true"></i>
                            <span>Categories</span>
                        </a>
                    @endcan

                    <a
                        href="{{ route('management.courses.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white text-xs font-semibold hover:bg-[#E65500] transition shadow-sm"
                    >
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        <span>Create New Course</span>
                    </a>
                </div>
            </div>

            <!-- Status Flash Messages -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Filters & Search Toolbar -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-4 sm:p-5 mb-8">
                <form method="GET" action="{{ route('management.courses.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search Input -->
                    <div class="lg:col-span-2 relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                            <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        </span>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search by title, subtitle, or slug..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select
                            name="status"
                            class="w-full px-3 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="review" {{ request('status') === 'review' ? 'selected' : '' }}>Under Review</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select
                            name="category_id"
                            class="w-full px-3 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) request('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-neutral-900 hover:bg-neutral-800 text-white text-xs font-semibold transition cursor-pointer text-center"
                        >
                            Filter
                        </button>
                        @if (request()->hasAny(['q', 'status', 'category_id', 'delivery_mode']))
                            <a
                                href="{{ route('management.courses.index') }}"
                                class="py-2.5 px-3 rounded-xl border border-neutral-200 hover:bg-neutral-100 text-neutral-600 text-xs font-semibold transition text-center"
                                title="Reset filters"
                            >
                                <i class="fas fa-undo" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Course Collection (Responsive Table & Card views) -->
            @if ($courses->isEmpty())
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-3xl bg-orange-50 text-lb-orange flex items-center justify-center text-2xl mb-4">
                        <i class="fas fa-book-open" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-lg font-bold text-lb-black">No courses found</h3>
                    <p class="text-sm text-neutral-500 mt-1 max-w-sm mx-auto">
                        @if (request()->hasAny(['q', 'status', 'category_id', 'delivery_mode']))
                            No courses match your active filter criteria. Try resetting filters.
                        @else
                            You have not authored or been assigned to any courses yet.
                        @endif
                    </p>
                    <div class="mt-6">
                        <a
                            href="{{ route('management.courses.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white text-xs font-semibold hover:bg-[#E65500] transition"
                        >
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            <span>Create Course</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-neutral-100 bg-neutral-50/80 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-4 sm:px-6">Course</th>
                                    <th class="py-3.5 px-4">Category</th>
                                    <th class="py-3.5 px-4">Delivery Modes</th>
                                    <th class="py-3.5 px-4">Status</th>
                                    <th class="py-3.5 px-4">Starting Price</th>
                                    <th class="py-3.5 px-4 text-right pr-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                @foreach ($courses as $course)
                                    <tr class="hover:bg-neutral-50/50 transition">
                                        <!-- Course Title & Subtitle -->
                                        <td class="py-4 px-4 sm:px-6">
                                            <div class="flex items-center gap-3">
                                                @if ($course->thumbnail_url)
                                                    <img src="{{ $course->thumbnail_url }}" alt="" class="w-12 h-12 rounded-xl object-cover shrink-0 border border-neutral-100">
                                                @else
                                                    <div class="w-12 h-12 rounded-xl bg-orange-100 text-lb-orange flex items-center justify-center shrink-0 text-base font-bold">
                                                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <a href="{{ route('management.courses.show', $course) }}" class="font-bold text-neutral-900 hover:text-lb-orange transition line-clamp-1">
                                                        {{ $course->title }}
                                                    </a>
                                                    <p class="text-xs text-neutral-500 line-clamp-1">
                                                        {{ $course->subtitle ?? 'No subtitle provided' }}
                                                    </p>
                                                    <div class="flex items-center gap-2 mt-1 text-[11px] text-neutral-400">
                                                        <span>{{ $course->duration_hours }}h</span>
                                                        <span>•</span>
                                                        <span>{{ ucfirst($course->difficulty_level) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Category -->
                                        <td class="py-4 px-4 text-xs font-medium text-neutral-700 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-700 text-xs">
                                                <i class="{{ $course->category->icon ?? 'fas fa-tag' }} text-[10px] text-neutral-400" aria-hidden="true"></i>
                                                <span>{{ $course->category->name }}</span>
                                            </span>
                                        </td>

                                        <!-- Delivery Modes -->
                                        <td class="py-4 px-4 text-xs whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse ($course->deliveryOptions as $opt)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $opt->delivery_mode === 'online' ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($opt->delivery_mode === 'physical' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-purple-50 text-purple-700 border border-purple-200') }}">
                                                        {{ ucfirst($opt->delivery_mode) }}
                                                    </span>
                                                @empty
                                                    <span class="text-neutral-400 text-xs italic">None configured</span>
                                                @endforelse
                                            </div>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if ($course->status === 'published')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fas fa-check-circle text-[10px]" aria-hidden="true"></i>
                                                    <span>Published</span>
                                                </span>
                                            @elseif ($course->status === 'review')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <i class="fas fa-clock text-[10px]" aria-hidden="true"></i>
                                                    <span>In Review</span>
                                                </span>
                                            @elseif ($course->status === 'draft')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-neutral-100 text-neutral-700 border border-neutral-200">
                                                    <i class="fas fa-pencil-alt text-[10px]" aria-hidden="true"></i>
                                                    <span>Draft</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                    <i class="fas fa-archive text-[10px]" aria-hidden="true"></i>
                                                    <span>Archived</span>
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Starting Price -->
                                        <td class="py-4 px-4 text-xs font-bold text-neutral-900 whitespace-nowrap">
                                            @if ($course->lowestPrice() !== null)
                                                @if ($course->lowestPrice() == 0)
                                                    <span class="text-emerald-600 font-extrabold uppercase">Free</span>
                                                @else
                                                    <span>LKR {{ number_format($course->lowestPrice(), 2) }}</span>
                                                @endif
                                            @else
                                                <span class="text-neutral-400 font-normal">TBD</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-4 px-4 text-right pr-6 whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <a
                                                    href="{{ route('management.courses.show', $course) }}"
                                                    class="p-2 rounded-lg text-neutral-500 hover:text-neutral-900 hover:bg-neutral-100 transition"
                                                    title="View & Manage Curriculum"
                                                >
                                                    <i class="fas fa-eye text-sm" aria-hidden="true"></i>
                                                </a>

                                                @can('update', $course)
                                                    <a
                                                        href="{{ route('management.courses.edit', $course) }}"
                                                        class="p-2 rounded-lg text-neutral-500 hover:text-lb-orange hover:bg-orange-50 transition"
                                                        title="Edit Course Details"
                                                    >
                                                        <i class="fas fa-edit text-sm" aria-hidden="true"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $course)
                                                    <form method="POST" action="{{ route('management.courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course draft?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="p-2 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                            title="Delete Draft"
                                                        >
                                                            <i class="fas fa-trash-alt text-sm" aria-hidden="true"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($courses->hasPages())
                        <div class="p-4 border-t border-neutral-100 bg-neutral-50/50">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>

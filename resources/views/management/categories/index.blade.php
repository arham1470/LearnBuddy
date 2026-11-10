<x-layouts.public title="Course Categories - LearnBuddy" description="Manage course categories and taxonomies.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Categories</span>
            </div>

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Course Categories</h1>
                    <p class="text-sm text-neutral-600 mt-1">
                        Taxonomy for grouping and filtering courses across LearnBuddy.
                    </p>
                </div>

                @can('create', \App\Models\Category::class)
                    <a
                        href="{{ route('management.categories.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white text-xs font-semibold hover:bg-[#E65500] transition shadow-sm"
                    >
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        <span>New Category</span>
                    </a>
                @endcan
            </div>

            <!-- Status Flash Alerts -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <div>
                        <ul class="list-disc list-inside text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Categories Table -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-neutral-100 bg-neutral-50/80 text-[11px] font-bold text-neutral-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">Name</th>
                                <th class="py-3.5 px-4">Slug</th>
                                <th class="py-3.5 px-4 text-center">Courses</th>
                                <th class="py-3.5 px-4 text-center">Order</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-neutral-50/50 transition">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-orange-50 text-lb-orange flex items-center justify-center shrink-0">
                                                <i class="{{ $cat->icon ?? 'fas fa-folder' }} text-sm" aria-hidden="true"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-neutral-900 text-xs">{{ $cat->name }}</p>
                                                @if ($cat->description)
                                                    <p class="text-[11px] text-neutral-500 line-clamp-1">{{ $cat->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-xs font-mono text-neutral-500">
                                        {{ $cat->slug }}
                                    </td>
                                    <td class="py-4 px-4 text-xs font-bold text-center text-neutral-700">
                                        {{ $cat->courses_count }}
                                    </td>
                                    <td class="py-4 px-4 text-xs text-center text-neutral-500">
                                        {{ $cat->sort_order }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if ($cat->is_active)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-500">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            @can('update', $cat)
                                                <a href="{{ route('management.categories.edit', $cat) }}" class="p-2 text-neutral-400 hover:text-lb-orange hover:bg-orange-50 rounded-lg transition" title="Edit Category">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                            @endcan

                                            @can('delete', $cat)
                                                <form method="POST" action="{{ route('management.categories.destroy', $cat) }}" onsubmit="return confirm('Delete category {{ $cat->name }}?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-neutral-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Delete Category">
                                                        <i class="fas fa-trash-alt text-xs"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-xs text-neutral-400">
                                        No categories found. Click "New Category" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="p-4 border-t border-neutral-100 bg-neutral-50/50">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>

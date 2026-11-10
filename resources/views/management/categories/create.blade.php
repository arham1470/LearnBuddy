<x-layouts.public title="Create Category - LearnBuddy" description="Add a new course category to LearnBuddy.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-xl mx-auto">
            <!-- Breadcrumbs -->
            <div class="mb-4 flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.categories.index') }}" class="hover:text-lb-orange">Categories</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">New Category</span>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-lb-black tracking-tight">Create Category</h1>
                <p class="text-sm text-neutral-600 mt-1">Add a new taxonomy for organizing courses.</p>
            </div>

            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('management.categories.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold text-neutral-700 mb-1">Category Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Software Engineering" class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 text-sm focus:ring-2 focus:ring-lb-orange">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-xs font-bold text-neutral-700 mb-1">URL Slug (Optional)</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="software-engineering" class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 text-sm focus:ring-2 focus:ring-lb-orange">
                        @error('slug') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-xs font-bold text-neutral-700 mb-1">Font Awesome Icon Class</label>
                        <input type="text" id="icon" name="icon" value="{{ old('icon', 'fas fa-code') }}" placeholder="fas fa-code" class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 text-sm focus:ring-2 focus:ring-lb-orange">
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-neutral-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief overview of courses in this topic..." class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 text-sm focus:ring-2 focus:ring-lb-orange">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="sort_order" class="block text-xs font-bold text-neutral-700 mb-1">Display Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', '0') }}" min="0" class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 text-sm focus:ring-2 focus:ring-lb-orange">
                        </div>
                        <div class="flex items-center pt-5">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded text-lb-orange focus:ring-lb-orange">
                                <span>Active Category</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between">
                        <a href="{{ route('management.categories.index') }}" class="px-5 py-2.5 rounded-full border border-neutral-200 text-xs font-semibold text-neutral-600 hover:bg-neutral-50">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-lb-orange text-white text-xs font-bold hover:bg-[#E65500] cursor-pointer">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>

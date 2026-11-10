<x-modal name="search-modal" maxWidth="lg">
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
            <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Search Courses</span>
            <span class="text-xs text-neutral-400">Press <kbd class="px-1.5 py-0.5 bg-neutral-100 rounded border text-[10px]">ESC</kbd> to close</span>
        </div>

        <form action="#" method="GET" @submit.prevent="alert('Search will be integrated in Phase 7 (Course Catalogue & Discovery).')">
            <div class="flex flex-col sm:flex-row gap-2 bg-neutral-50 p-2 rounded-2xl border border-neutral-200">
                <div class="relative flex-1 flex items-center">
                    <i class="fas fa-search text-neutral-400 pl-3 pr-2" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="q"
                        placeholder="Search web dev, UI/UX, business..."
                        class="w-full bg-transparent border-0 text-sm focus:outline-none focus:ring-0 placeholder-neutral-400 py-2"
                        autofocus
                    >
                </div>

                <div class="flex items-center gap-2">
                    <select
                        name="category"
                        class="bg-white text-xs border border-neutral-200 rounded-xl px-3 py-2 text-neutral-600 focus:outline-none focus:ring-1 focus:ring-lb-orange cursor-pointer"
                    >
                        <option value="">All Categories</option>
                        <option value="development">Development</option>
                        <option value="design">UI/UX Design</option>
                        <option value="business">Business & IT</option>
                    </select>

                    <button
                        type="submit"
                        class="bg-lb-orange hover:bg-lb-orange-hover text-white rounded-xl px-4 py-2 text-xs font-semibold transition shrink-0 cursor-pointer"
                    >
                        Search
                    </button>
                </div>
            </div>
        </form>

        <div class="pt-2">
            <span class="text-xs font-semibold text-neutral-500">Popular Searches:</span>
            <div class="flex flex-wrap gap-1.5 mt-2">
                <span class="px-2.5 py-1 bg-neutral-100 hover:bg-orange-50 hover:text-lb-orange rounded-full text-xs text-neutral-600 transition cursor-pointer">Figma to HTML</span>
                <span class="px-2.5 py-1 bg-neutral-100 hover:bg-orange-50 hover:text-lb-orange rounded-full text-xs text-neutral-600 transition cursor-pointer">Full-Stack Laravel</span>
                <span class="px-2.5 py-1 bg-neutral-100 hover:bg-orange-50 hover:text-lb-orange rounded-full text-xs text-neutral-600 transition cursor-pointer">Colombo Weekend Batch</span>
            </div>
        </div>
    </div>
</x-modal>

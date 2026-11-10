<x-layouts.public :title="'Curriculum In Preparation · ' . $course->title">
    <div class="min-h-[70vh] bg-lb-cream/40 flex items-center justify-center px-4 py-16">
        <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-neutral-100 shadow-sm text-center">
            <div class="w-16 h-16 bg-orange-50 text-lb-orange rounded-2xl flex items-center justify-center mx-auto mb-5 text-2xl">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            
            <span class="inline-block px-3 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full uppercase tracking-wider mb-3">
                Curriculum In Preparation
            </span>

            <h1 class="text-2xl font-bold text-neutral-900 mb-2">
                {{ $course->title }}
            </h1>

            <p class="text-neutral-600 text-sm mb-6 leading-relaxed">
                The lessons for this course are currently being finalized and scheduled. You will be able to start learning as soon as the first module is released.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a
                    href="{{ route('student.dashboard') }}"
                    class="px-5 py-2.5 bg-lb-orange hover:bg-lb-orange-dark text-white text-sm font-semibold rounded-full shadow-sm transition"
                >
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Return to Dashboard
                </a>
                <a
                    href="{{ route('courses.show', $course) }}"
                    class="px-5 py-2.5 bg-neutral-100 hover:bg-neutral-200 text-neutral-800 text-sm font-semibold rounded-full transition"
                >
                    Course Overview
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>

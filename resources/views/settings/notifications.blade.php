<x-layouts.public title="Notification Preferences · LearnBuddy">
    <!-- HEADER -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-10 sm:pb-14 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[850px] mx-auto px-4 sm:px-6">
            <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('notifications.index') }}" class="hover:text-lb-orange transition">Notifications</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">Preferences</span>
            </nav>

            <div class="space-y-1">
                <x-badge variant="orange" icon="fas fa-sliders-h">
                    Preferences & Privacy
                </x-badge>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-lb-black">
                    Notification <span class="text-lb-orange">Preferences</span>
                </h1>
                <p class="text-xs sm:text-sm text-neutral-600">
                    Control which updates you receive and choose your preferred communication channels.
                </p>
            </div>
        </div>
    </section>

    <!-- FORM -->
    <section class="py-8 sm:py-14 bg-lb-off-white min-h-[600px]">
        <div class="max-w-[850px] mx-auto px-4 sm:px-6">

            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('settings.notifications.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 1. Delivery Channels -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                    <div class="border-b border-neutral-100 pb-3">
                        <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-satellite-dish text-lb-orange"></i>
                            <span>Communication Channels</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            Select the delivery mediums you want LearnBuddy to reach you on.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <!-- In-App Notifications -->
                        <label class="flex items-center justify-between p-4 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 text-lb-orange flex items-center justify-center">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-xs sm:text-sm text-neutral-900">In-App Notifications</div>
                                    <p class="text-[11px] text-neutral-500">Notifications banner & bell badge inside your student dashboard.</p>
                                </div>
                            </div>
                            <input
                                type="checkbox"
                                name="in_app_notifications"
                                value="1"
                                {{ $preferences->in_app_notifications ? 'checked' : '' }}
                                class="w-5 h-5 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>

                        <!-- Push Notifications (FCM) -->
                        <label class="flex items-center justify-between p-4 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-xs sm:text-sm text-neutral-900">Web & Mobile Push Notifications</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ config('services.firebase.credentials') ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-100 text-neutral-600' }}">
                                            Firebase FCM {{ config('services.firebase.credentials') ? 'Connected' : '(Requires Provider Setup)' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-neutral-500">Instant push popups on supported desktop and mobile web browsers.</p>
                                </div>
                            </div>
                            <input
                                type="checkbox"
                                name="push_notifications"
                                value="1"
                                {{ $preferences->push_notifications ? 'checked' : '' }}
                                class="w-5 h-5 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>
                    </div>
                </div>

                <!-- 2. Email Categories -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-6 sm:p-8 space-y-4">
                    <div class="border-b border-neutral-100 pb-3">
                        <h2 class="text-sm sm:text-base font-bold text-neutral-900 flex items-center gap-2">
                            <i class="fas fa-envelope text-lb-orange"></i>
                            <span>Transactional Email Alerts</span>
                        </h2>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            Delivered to <strong>{{ $user->email }}</strong>. Managed via Resend / SMTP.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <!-- Enrolment -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div>
                                <div class="font-bold text-xs text-neutral-900">Course Enrolment Confirmations</div>
                                <p class="text-[11px] text-neutral-500">When your seat reservation or access is activated.</p>
                            </div>
                            <input
                                type="checkbox"
                                name="email_enrollment"
                                value="1"
                                {{ $preferences->email_enrollment ? 'checked' : '' }}
                                class="w-4 h-4 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>

                        <!-- Payments -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div>
                                <div class="font-bold text-xs text-neutral-900">Payment & Tax Receipts (LKR)</div>
                                <p class="text-[11px] text-neutral-500">Official transaction receipts and payment confirmations.</p>
                            </div>
                            <input
                                type="checkbox"
                                name="email_payment"
                                value="1"
                                {{ $preferences->email_payment ? 'checked' : '' }}
                                class="w-4 h-4 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>

                        <!-- Live Classes -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div>
                                <div class="font-bold text-xs text-neutral-900">Live Virtual Classes & Reminders</div>
                                <p class="text-[11px] text-neutral-500">Scheduled reminders and rescheduling / cancellation notices (Asia/Colombo).</p>
                            </div>
                            <input
                                type="checkbox"
                                name="email_live_class"
                                value="1"
                                {{ $preferences->email_live_class ? 'checked' : '' }}
                                class="w-4 h-4 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>

                        <!-- Assignments -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div>
                                <div class="font-bold text-xs text-neutral-900">Assignments & Instructor Feedback</div>
                                <p class="text-[11px] text-neutral-500">Submission evaluations, grades, and upcoming assignment deadlines.</p>
                            </div>
                            <input
                                type="checkbox"
                                name="email_assignments"
                                value="1"
                                {{ $preferences->email_assignments ? 'checked' : '' }}
                                class="w-4 h-4 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>

                        <!-- Quizzes -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div>
                                <div class="font-bold text-xs text-neutral-900">Quiz Results & Scoring</div>
                                <p class="text-[11px] text-neutral-500">Scored results and pass/fail evaluations.</p>
                            </div>
                            <input
                                type="checkbox"
                                name="email_quizzes"
                                value="1"
                                {{ $preferences->email_quizzes ? 'checked' : '' }}
                                class="w-4 h-4 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>

                        <!-- Announcements -->
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-neutral-200 hover:border-neutral-300 transition cursor-pointer">
                            <div>
                                <div class="font-bold text-xs text-neutral-900">Course Announcements</div>
                                <p class="text-[11px] text-neutral-500">Broadcasts posted by your course instructors and mentors.</p>
                            </div>
                            <input
                                type="checkbox"
                                name="email_announcements"
                                value="1"
                                {{ $preferences->email_announcements ? 'checked' : '' }}
                                class="w-4 h-4 text-lb-orange rounded focus:ring-lb-orange"
                            >
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a
                        href="{{ route('notifications.index') }}"
                        class="px-5 py-2.5 rounded-xl bg-white border border-neutral-200 text-neutral-700 text-xs font-bold transition hover:bg-neutral-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="px-6 py-2.5 rounded-xl bg-lb-orange hover:bg-lb-orange-hover text-white text-xs font-bold transition shadow-sm cursor-pointer"
                    >
                        Save Preferences
                    </button>
                </div>
            </form>

        </div>
    </section>
</x-layouts.public>

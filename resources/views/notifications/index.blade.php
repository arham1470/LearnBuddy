<x-layouts.public title="My Notifications · LearnBuddy">
    <!-- HEADER -->
    <section class="bg-lb-peach pt-8 sm:pt-12 pb-10 sm:pb-14 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6">
            <nav class="flex items-center gap-2 text-xs text-neutral-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-lb-orange transition">Home</a>
                <span class="text-neutral-300">/</span>
                <a href="{{ route('student.dashboard') }}" class="hover:text-lb-orange transition">Dashboard</a>
                <span class="text-neutral-300">/</span>
                <span class="font-semibold text-neutral-800">Notifications</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <x-badge variant="orange" icon="fas fa-bell">
                            Notifications Centre
                        </x-badge>
                        @if ($unreadCount > 0)
                            <span class="text-xs font-bold text-lb-orange bg-orange-100 px-2.5 py-0.5 rounded-full">
                                {{ $unreadCount }} Unread
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-lb-black">
                        Stay Updated with <span class="text-lb-orange">LearnBuddy</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-600">
                        Live-class reminders, course announcements, assignment feedback, and payment receipts.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto flex-wrap">
                    @if ($unreadCount > 0)
                        <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl bg-white border border-neutral-200 text-neutral-700 hover:text-lb-orange text-xs font-bold transition shadow-sm cursor-pointer"
                            >
                                <i class="fas fa-check-double mr-1 text-emerald-600"></i> Mark All as Read
                            </button>
                        </form>
                    @endif

                    <a
                        href="{{ route('settings.notifications.edit') }}"
                        class="px-4 py-2 rounded-xl bg-neutral-900 hover:bg-lb-black text-white text-xs font-bold transition shadow-sm"
                    >
                        <i class="fas fa-cog mr-1"></i> Preferences
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="py-8 sm:py-14 bg-lb-off-white min-h-[600px]">
        <div class="max-w-[1000px] mx-auto px-4 sm:px-6 space-y-6">

            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 border-b border-neutral-200 pb-3 text-xs font-bold">
                <a
                    href="{{ route('notifications.index', ['filter' => 'all']) }}"
                    class="px-4 py-2 rounded-xl transition {{ $filter === 'all' ? 'bg-lb-orange text-white shadow-sm' : 'bg-white text-neutral-600 hover:text-lb-orange border border-neutral-200' }}"
                >
                    All Notifications
                </a>
                <a
                    href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                    class="px-4 py-2 rounded-xl transition {{ $filter === 'unread' ? 'bg-lb-orange text-white shadow-sm' : 'bg-white text-neutral-600 hover:text-lb-orange border border-neutral-200' }}"
                >
                    Unread Only
                    @if ($unreadCount > 0)
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $filter === 'unread' ? 'bg-white text-lb-orange' : 'bg-orange-100 text-lb-orange' }}">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Notifications List -->
            @if ($notifications->count() === 0)
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-12 text-center max-w-md mx-auto space-y-3">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 text-lb-orange flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-neutral-900">No notifications</h3>
                    <p class="text-xs text-neutral-500">
                        {{ $filter === 'unread' ? 'You have caught up with all your notifications!' : 'You do not have any notifications yet.' }}
                    </p>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm overflow-hidden divide-y divide-neutral-100">
                    @foreach ($notifications as $notification)
                        @php
                            $isUnread = is_null($notification->read_at);
                            $data = $notification->data;
                            $icon = $data['icon'] ?? 'fas fa-info-circle';
                            $title = $data['title'] ?? 'Notification';
                            $body = $data['body'] ?? '';
                            $actionUrl = $data['action_url'] ?? null;
                            $timeColombo = $notification->created_at->setTimezone('Asia/Colombo')->format('d M Y, h:i A');
                        @endphp
                        <div class="p-4 sm:p-5 flex items-start gap-3 sm:gap-4 transition {{ $isUnread ? 'bg-orange-50/30' : 'hover:bg-neutral-50/60' }}">
                            <!-- Icon / Read Dot -->
                            <div class="relative shrink-0 mt-0.5">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm {{ $isUnread ? 'bg-orange-100 text-lb-orange' : 'bg-neutral-100 text-neutral-500' }}">
                                    <i class="{{ $icon }}"></i>
                                </div>
                                @if ($isUnread)
                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-lb-orange border-2 border-white rounded-full"></span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-baseline justify-between gap-2 flex-wrap">
                                    <h4 class="text-xs sm:text-sm font-bold text-neutral-900 {{ $isUnread ? 'font-black' : '' }}">
                                        {{ $title }}
                                    </h4>
                                    <span class="text-[11px] text-neutral-400 whitespace-nowrap">
                                        {{ $notification->created_at->diffForHumans() }} &bull; {{ $timeColombo }}
                                    </span>
                                </div>

                                <p class="text-xs text-neutral-600 leading-relaxed">
                                    {{ $body }}
                                </p>

                                <div class="flex items-center gap-3 pt-2">
                                    @if ($actionUrl)
                                        <a
                                            href="{{ $actionUrl }}"
                                            class="text-xs font-bold text-lb-orange hover:text-lb-orange-hover flex items-center gap-1"
                                        >
                                            <span>View Details</span>
                                            <i class="fas fa-arrow-right text-[10px]"></i>
                                        </a>
                                    @endif

                                    @if ($isUnread)
                                        <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="text-[11px] text-neutral-500 hover:text-neutral-900 underline cursor-pointer"
                                            >
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($notifications->hasPages())
                    <div class="p-4 bg-white rounded-2xl border border-neutral-200">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif

        </div>
    </section>
</x-layouts.public>

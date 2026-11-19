@extends('layouts.admin')

@section('title', 'Support & Inquiries')

@section('content')
<div class="space-y-6" x-data="{ expandedMessageId: null }">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-[#6B6B6B] mb-1">
                <span>Operations</span>
                <svg class="w-3 h-3 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#0A0A0A] font-semibold">Support Desk</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] tracking-tight">Support &amp; User Inquiries</h1>
            <p class="text-[0.875rem] text-[#6B6B6B] mt-1">Review student &amp; visitor messages, compose replies, and send via one-click pre-filled Gmail.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('support.create') }}" target="_blank"
               class="px-4 py-2.5 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-sm font-semibold text-[#0A0A0A] transition-colors flex items-center gap-2 shadow-xs">
                <span>View Public Support Form</span>
                <svg class="w-3.5 h-3.5 text-[#6B6B6B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
            <i class="fas fa-check-circle text-emerald-500 text-lg shrink-0 mt-0.5"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Total Tickets -->
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-[#6B6B6B] uppercase tracking-wider">All Inquiries</p>
                <p class="text-2xl font-black text-[#0A0A0A] mt-1">{{ number_format($totalCount) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Total messages received</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-neutral-100 text-[#0A0A0A] flex items-center justify-center shrink-0">
                <i class="fas fa-inbox text-lg"></i>
            </div>
        </div>

        <!-- Awaiting Reply (Pending) -->
        <div class="lb-card p-5 flex items-center justify-between border-l-4 border-l-[#FF5F00]">
            <div>
                <p class="text-xs font-bold text-[#FF5F00] uppercase tracking-wider">Awaiting Reply</p>
                <p class="text-2xl font-black text-[#FF5F00] mt-1">{{ number_format($pendingCount) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Needs administrator response</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#FF5F00] flex items-center justify-center shrink-0">
                <i class="fas fa-hourglass-half text-lg"></i>
            </div>
        </div>

        <!-- Replied -->
        <div class="lb-card p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Answered</p>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($repliedCount) }}</p>
                <p class="text-[0.72rem] text-[#6B6B6B] mt-0.5">Responded via Gmail</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fas fa-check-double text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="lb-card p-4">
        <form method="GET" action="{{ route('admin.support.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-[#F7F7F5] rounded-xl border border-[#E5E5E5] self-start md:self-auto overflow-x-auto max-w-full">
                <a href="{{ route('admin.support.index', array_merge(request()->query(), ['tab' => 'all'])) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'all' ? 'bg-white text-[#0A0A0A] shadow-xs' : 'text-[#6B6B6B] hover:text-[#0A0A0A]' }}">
                    All ({{ $totalCount }})
                </a>
                <a href="{{ route('admin.support.index', array_merge(request()->query(), ['tab' => 'pending'])) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $tab === 'pending' ? 'bg-[#FF5F00] text-white shadow-xs' : 'text-[#6B6B6B] hover:text-[#FF5F00]' }}">
                    <span>Pending</span>
                    @if($pendingCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'pending' ? 'bg-white text-[#FF5F00]' : 'bg-[#FFF0E6] text-[#FF5F00]' }} font-black">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.support.index', array_merge(request()->query(), ['tab' => 'replied'])) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'replied' ? 'bg-white text-[#0A0A0A] shadow-xs' : 'text-[#6B6B6B] hover:text-[#0A0A0A]' }}">
                    Replied ({{ $repliedCount }})
                </a>
            </div>

            <!-- Category & Search Bar -->
            <div class="flex items-center gap-2.5 flex-1 max-w-md">
                <input type="hidden" name="tab" value="{{ $tab }}">

                <select name="category" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-xs text-[#0A0A0A] focus:outline-none focus:border-[#FF5F00]">
                    <option value="all">All Categories</option>
                    @foreach (\App\Models\SupportMessage::CATEGORIES as $k => $label)
                        <option value="{{ $k }}" {{ $category === $k ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#6B6B6B]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search sender, email, subject..."
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-xs text-[#0A0A0A] focus:outline-none focus:border-[#FF5F00]">
                    @if($search)
                        <a href="{{ route('admin.support.index', ['tab' => $tab, 'category' => $category]) }}" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-[#6B6B6B] hover:text-[#0A0A0A]">
                            <i class="fas fa-times text-xs"></i>
                        </a>
                    @endif
                </div>

                <button type="submit" class="px-3.5 py-2 rounded-xl bg-[#0A0A0A] hover:bg-neutral-800 text-white text-xs font-bold transition shadow-xs">
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Messages List -->
    @if ($messages->isEmpty())
        <div class="lb-card p-12 text-center space-y-3">
            <div class="w-14 h-14 rounded-2xl bg-neutral-100 text-neutral-400 flex items-center justify-center mx-auto text-xl">
                <i class="fas fa-inbox"></i>
            </div>
            <h3 class="text-base font-bold text-[#0A0A0A]">No support messages found</h3>
            <p class="text-xs text-[#6B6B6B] max-w-sm mx-auto">
                {{ $tab === 'pending' ? 'All support requests have been addressed! No pending tickets waiting.' : 'No messages matching the current search criteria.' }}
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($messages as $msg)
                @php
                    $isPending = $msg->isPending();
                    $categoryColors = [
                        'account' => 'bg-amber-100 text-amber-900 border-amber-200',
                        'billing' => 'bg-blue-100 text-blue-900 border-blue-200',
                        'courses' => 'bg-purple-100 text-purple-900 border-purple-200',
                        'technical' => 'bg-rose-100 text-rose-900 border-rose-200',
                        'general' => 'bg-neutral-100 text-neutral-800 border-neutral-200',
                    ];
                    $badgeStyle = $categoryColors[$msg->category] ?? 'bg-neutral-100 text-neutral-800 border-neutral-200';
                @endphp

                <div class="lb-card overflow-hidden transition-all duration-200 {{ $isPending ? 'border-l-4 border-l-[#FF5F00]' : 'border-l-4 border-l-emerald-500' }}">
                    <!-- Card Summary Row -->
                    <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white hover:bg-[#F7F7F5]/50 transition cursor-pointer"
                         @click="expandedMessageId = (expandedMessageId === {{ $msg->id }} ? null : {{ $msg->id }})">

                        <!-- Sender & Subject -->
                        <div class="flex items-start gap-3.5 flex-1 min-w-0">
                            <!-- Initials Avatar -->
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xs font-extrabold uppercase shrink-0 shadow-xs {{ $isPending ? 'bg-[#FFF0E6] text-[#FF5F00] border border-orange-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ strtoupper(substr($msg->name, 0, 2)) }}
                            </div>

                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-extrabold text-[#0A0A0A] truncate">
                                        {{ $msg->subject }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeStyle }}">
                                        {{ $msg->category_label }}
                                    </span>
                                    @if ($isPending)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pending Reply
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-bold uppercase tracking-wider">
                                            <i class="fas fa-check text-[9px]"></i>
                                            Replied
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 text-xs text-[#6B6B6B] flex-wrap">
                                    <span class="font-bold text-[#0A0A0A]">{{ $msg->name }}</span>
                                    <span>&bull;</span>
                                    <span class="font-mono text-[11px] text-[#6B6B6B]">{{ $msg->email }}</span>
                                    @if ($msg->phone)
                                        <span>&bull;</span>
                                        <span class="font-mono text-[11px] text-[#6B6B6B]">
                                            <i class="fas fa-phone text-[10px] mr-0.5"></i> {{ $msg->phone }}
                                        </span>
                                    @endif
                                    @if ($msg->user_id)
                                        <span class="px-1.5 py-0.2 rounded-md bg-neutral-100 text-neutral-600 text-[10px] font-semibold">Registered User</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Date & Action Button -->
                        <div class="flex items-center gap-3 shrink-0 self-end lg:self-center" @click.stop>
                            <div class="text-right text-[11px] text-[#6B6B6B] hidden sm:block">
                                <p class="font-semibold text-[#0A0A0A]">{{ $msg->created_at->setTimezone('Asia/Colombo')->format('d M Y, h:i A') }}</p>
                                <p class="text-[10px]">{{ $msg->created_at->diffForHumans() }}</p>
                            </div>

                            <button
                                type="button"
                                @click="expandedMessageId = (expandedMessageId === {{ $msg->id }} ? null : {{ $msg->id }})"
                                class="px-3.5 py-2 rounded-xl border border-[#E5E5E5] bg-white hover:bg-[#F7F7F5] text-xs font-bold text-[#0A0A0A] transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
                            >
                                <span x-text="expandedMessageId === {{ $msg->id }} ? 'Hide Details' : 'View & Reply'"></span>
                                <i class="fas text-[10px] text-[#6B6B6B]" :class="expandedMessageId === {{ $msg->id }} ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Drawer: Message Details & Gmail Reply Form -->
                    <div
                        x-show="expandedMessageId === {{ $msg->id }}"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="p-5 sm:p-6 border-t border-[#E5E5E5] bg-[#F7F7F5]/40 space-y-6"
                    >
                        <!-- Original Inquiry Message -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs text-[#6B6B6B]">
                                <span class="font-bold uppercase tracking-wider text-[#0A0A0A]">
                                    <i class="fas fa-comment-alt text-[#FF5F00] mr-1"></i> Original Message
                                </span>
                                <span class="font-mono text-[11px]">Ticket #{{ $msg->id }} &bull; IP: {{ $msg->ip_address ?? 'N/A' }}</span>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-[#E5E5E5] text-xs sm:text-sm text-[#0A0A0A] leading-relaxed whitespace-pre-wrap shadow-2xs font-sans">
{{ $msg->message }}
                            </div>
                        </div>

                        <!-- Previous Admin Reply (If already replied) -->
                        @if ($msg->admin_reply)
                            <div class="space-y-2 p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                                <div class="flex items-center justify-between text-xs text-emerald-900">
                                    <span class="font-bold uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-reply text-emerald-600"></i>
                                        <span>Sent Reply by {{ $msg->repliedBy?->name ?? 'Administrator' }}</span>
                                    </span>
                                    <span class="font-mono text-[11px]">
                                        {{ $msg->replied_at ? $msg->replied_at->setTimezone('Asia/Colombo')->format('d M Y, h:i A (T)') : '' }}
                                    </span>
                                </div>
                                <div class="text-xs sm:text-sm text-emerald-950 leading-relaxed whitespace-pre-wrap">
{{ $msg->admin_reply }}
                                </div>
                            </div>
                        @endif

                        <!-- Reply via Gmail Box -->
                        <div class="p-5 rounded-2xl bg-white border border-[#E5E5E5] shadow-xs space-y-4" x-data="{ replyText: '{{ addslashes($msg->admin_reply ?? '') }}' }">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#E5E5E5] pb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-orange-100 text-[#FF5F00] flex items-center justify-center text-xs">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>
                                    <span class="text-xs font-extrabold text-[#0A0A0A]">
                                        Reply to {{ $msg->name }} (<span class="font-mono font-normal">{{ $msg->email }}</span>)
                                    </span>
                                </div>
                                <span class="text-[11px] text-[#6B6B6B]">
                                    One-click redirect opens pre-filled Gmail compose
                                </span>
                            </div>

                            <form method="POST" action="{{ route('admin.support.reply', $msg->id) }}" target="_blank" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-[#6B6B6B] uppercase tracking-wider mb-1">
                                        Type Your Email Reply Body:
                                    </label>
                                    <textarea
                                        name="reply"
                                        x-model="replyText"
                                        rows="4"
                                        required
                                        placeholder="Hello {{ $msg->name }},&#10;&#10;Thank you for contacting LearnBuddy Support. Regarding your request..."
                                        class="w-full p-3.5 rounded-xl border border-[#E5E5E5] bg-[#F7F7F5] focus:bg-white text-xs sm:text-sm text-[#0A0A0A] focus:outline-none focus:border-[#FF5F00] transition leading-relaxed font-sans"
                                    ></textarea>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                                    <p class="text-[11px] text-[#6B6B6B]">
                                        <i class="fab fa-google text-red-500 mr-1"></i>
                                        Clicking below saves this reply in LearnBuddy and redirects to Gmail with recipient, subject, and message pre-filled.
                                    </p>

                                    <div class="flex items-center gap-2 self-end sm:self-auto">
                                        <!-- One-click Gmail Redirect Button -->
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#FF5F00] hover:bg-[#E65500] text-white text-xs font-bold transition shadow-sm cursor-pointer"
                                        >
                                            <i class="fab fa-google text-xs"></i>
                                            <span>Reply via Gmail</span>
                                            <i class="fas fa-external-link-alt text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Secondary Actions Bar -->
                            <div class="pt-3 border-t border-[#E5E5E5] flex items-center justify-between text-xs">
                                <!-- Manual Status Toggle -->
                                <form method="POST" action="{{ route('admin.support.toggle-status', $msg->id) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="text-[11px] text-[#6B6B6B] hover:text-[#0A0A0A] font-semibold underline cursor-pointer"
                                    >
                                        {{ $isPending ? 'Mark as Replied without Gmail' : 'Reopen as Pending' }}
                                    </button>
                                </form>

                                <!-- Delete ticket -->
                                <form method="POST" action="{{ route('admin.support.destroy', $msg->id) }}" onsubmit="return confirm('Are you sure you want to delete this support message?');">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold transition cursor-pointer"
                                    >
                                        <i class="fas fa-trash-alt text-[10px] mr-1"></i> Delete Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($messages->hasPages())
            <div class="p-4 bg-white rounded-2xl border border-[#E5E5E5] shadow-xs">
                {{ $messages->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

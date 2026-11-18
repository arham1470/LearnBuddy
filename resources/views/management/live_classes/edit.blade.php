<x-layouts.public title="Edit Live Class - {{ $liveClass->title }} - LearnBuddy" description="Edit or reschedule a live interactive video class session.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.live-classes.index', $course) }}" class="hover:text-lb-orange">Live Classes</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.live-classes.show', [$course, $liveClass]) }}" class="hover:text-lb-orange">{{ Str::limit($liveClass->title, 20) }}</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Edit / Reschedule</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold uppercase tracking-wide mb-2">
                    <i class="fas fa-edit text-xs" aria-hidden="true"></i> Reschedule Session
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                    Edit Live Class Session
                </h1>
                <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                    Update timing, host instructor, target batch, or status for <strong>{{ $liveClass->title }}</strong>.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8">
                <form method="POST" action="{{ route('management.courses.live-classes.update', [$course, $liveClass]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Session Title / Topic <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title', $liveClass->title) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                            required
                        >
                        @error('title')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description / Agenda -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                            Session Agenda &amp; Learning Objectives
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs text-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange"
                        >{{ old('description', $liveClass->description) }}</textarea>
                        @error('description')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Host Instructor & Target Batch -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- 1. Host Instructor (Custom Modern Dropdown) -->
                        <div>
                            <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Host Instructor <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $selectedInstructorId = (int) old('instructor_id', $liveClass->instructor_id);
                                $currentInst = $instructors->firstWhere('id', $selectedInstructorId) ?? $instructors->first();
                            @endphp
                            <div
                                x-data="{
                                    open: false,
                                    selectedId: {{ $currentInst ? $currentInst->id : 'null' }},
                                    selectedName: @js($currentInst ? $currentInst->name : 'Select Instructor'),
                                    selectedEmail: @js($currentInst ? $currentInst->email : ''),
                                    selectInstructor(id, name, email) {
                                        this.selectedId = id;
                                        this.selectedName = name;
                                        this.selectedEmail = email;
                                        this.open = false;
                                    }
                                }"
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                                class="relative"
                            >
                                <input type="hidden" name="instructor_id" :value="selectedId" required>

                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-neutral-200/90 bg-neutral-50/50 hover:bg-neutral-100/60 text-xs text-neutral-800 flex items-center justify-between gap-3 transition-all focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer shadow-xs min-h-[46px]"
                                    :class="{ 'border-lb-orange ring-2 ring-lb-orange/20 bg-white': open }"
                                    aria-haspopup="listbox"
                                    :aria-expanded="open.toString()"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                                        <span class="w-6 h-6 rounded-full bg-orange-100 text-lb-orange font-bold text-[10px] flex items-center justify-center shrink-0 border border-orange-200">
                                            <i class="fas fa-user-tie text-[10px]"></i>
                                        </span>
                                        <div class="text-left truncate">
                                            <span class="font-bold text-neutral-900 block truncate" x-text="selectedName"></span>
                                            <span class="text-[10px] text-neutral-400 block truncate" x-show="selectedEmail" x-text="selectedEmail"></span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-lb-orange': open }"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="w-full absolute left-0 top-full mt-1.5 rounded-2xl bg-white shadow-xl border border-neutral-100 p-1.5 z-50 text-xs max-h-60 overflow-y-auto"
                                    role="listbox"
                                >
                                    @foreach ($instructors as $inst)
                                        <button
                                            type="button"
                                            @click="selectInstructor({{ $inst->id }}, @js($inst->name), @js($inst->email))"
                                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between gap-2.5 text-left transition-colors cursor-pointer"
                                            :class="selectedId === {{ $inst->id }} ? 'bg-orange-50 text-lb-orange font-bold' : 'text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900'"
                                            role="option"
                                        >
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0" :class="selectedId === {{ $inst->id }} ? 'bg-lb-orange text-white' : 'bg-neutral-100 text-neutral-600'">
                                                    {{ strtoupper(substr($inst->name, 0, 1)) }}
                                                </span>
                                                <div class="truncate">
                                                    <span class="block text-xs font-semibold truncate">{{ $inst->name }}</span>
                                                    <span class="block text-[10px] text-neutral-400 truncate">{{ $inst->email }}</span>
                                                </div>
                                            </div>
                                            <i x-show="selectedId === {{ $inst->id }}" class="fas fa-check text-xs text-lb-orange shrink-0"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('instructor_id')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 2. Target Batch (Custom Modern Dropdown) -->
                        <div>
                            <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Target Batch (Optional)
                            </label>
                            @php
                                $selectedBatchId = old('batch_id', $liveClass->batch_id);
                                $currentBatch = $batches->firstWhere('id', $selectedBatchId);
                            @endphp
                            <div
                                x-data="{
                                    open: false,
                                    selectedId: @js($selectedBatchId),
                                    selectedLabel: @js($currentBatch ? ($currentBatch->batch_code . ' • ' . $currentBatch->batch_name) : 'All Active Course Enrolments'),
                                    selectBatch(id, label) {
                                        this.selectedId = id;
                                        this.selectedLabel = label;
                                        this.open = false;
                                    }
                                }"
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                                class="relative"
                            >
                                <input type="hidden" name="batch_id" :value="selectedId">

                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-neutral-200/90 bg-neutral-50/50 hover:bg-neutral-100/60 text-xs text-neutral-800 flex items-center justify-between gap-3 transition-all focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer shadow-xs min-h-[46px]"
                                    :class="{ 'border-lb-orange ring-2 ring-lb-orange/20 bg-white': open }"
                                    aria-haspopup="listbox"
                                    :aria-expanded="open.toString()"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center shrink-0 border border-blue-200">
                                            <i class="fas fa-layer-group text-[10px]"></i>
                                        </span>
                                        <span class="font-bold text-neutral-900 truncate" x-text="selectedLabel"></span>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-lb-orange': open }"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="w-full absolute left-0 top-full mt-1.5 rounded-2xl bg-white shadow-xl border border-neutral-100 p-1.5 z-50 text-xs max-h-60 overflow-y-auto"
                                    role="listbox"
                                >
                                    <!-- All Enrolments Option -->
                                    <button
                                        type="button"
                                        @click="selectBatch('', 'All Active Course Enrolments')"
                                        class="w-full px-3 py-2 rounded-xl flex items-center justify-between gap-2.5 text-left transition-colors cursor-pointer"
                                        :class="selectedId === '' || selectedId === null ? 'bg-orange-50 text-lb-orange font-bold' : 'text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900'"
                                        role="option"
                                    >
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-5 h-5 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500 text-[10px] shrink-0">
                                                <i class="fas fa-globe-asia text-[10px]"></i>
                                            </span>
                                            <span class="truncate font-semibold">All Active Course Enrolments</span>
                                        </div>
                                        <i x-show="selectedId === '' || selectedId === null" class="fas fa-check text-xs text-lb-orange shrink-0"></i>
                                    </button>

                                    @foreach ($batches as $b)
                                        <button
                                            type="button"
                                            @click="selectBatch({{ $b->id }}, @js($b->batch_code . ' • ' . $b->batch_name))"
                                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between gap-2.5 text-left transition-colors cursor-pointer"
                                            :class="selectedId == {{ $b->id }} ? 'bg-orange-50 text-lb-orange font-bold' : 'text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900'"
                                            role="option"
                                        >
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-neutral-100 text-neutral-700 shrink-0">
                                                    {{ $b->batch_code }}
                                                </span>
                                                <span class="truncate font-medium text-neutral-900">{{ $b->batch_name }}</span>
                                            </div>
                                            <i x-show="selectedId == {{ $b->id }}" class="fas fa-check text-xs text-lb-orange shrink-0"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('batch_id')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 3. Scheduled Timings (Custom Modern Date & Time Pickers) -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-neutral-50/80 border border-neutral-200/90 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-neutral-200/70">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-full bg-orange-100 text-lb-orange flex items-center justify-center text-xs shrink-0">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-neutral-800 uppercase tracking-wider block">
                                        Class Timing &amp; Duration <span class="text-rose-500">*</span>
                                    </span>
                                    <span class="text-[11px] text-neutral-500">Pick live classroom date and session schedule</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-neutral-600 uppercase bg-neutral-200/80 px-2.5 py-1 rounded-full border border-neutral-300/60 self-start sm:self-auto flex items-center gap-1.5 shrink-0">
                                <i class="fas fa-globe text-lb-orange text-[10px]"></i>
                                Asia/Colombo (GMT+5:30)
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Start Date & Time (Custom Modern Picker) -->
                            <div
                                class="relative"
                                x-data="liveClassDatePicker(@js(old('scheduled_start_time', $liveClass->scheduled_start_time->format('Y-m-d\TH:i'))), '06', '00', 'PM')"
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                            >
                                <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1.5">
                                    Start Date &amp; Time <span class="text-rose-500">*</span>
                                </label>

                                <input type="hidden" name="scheduled_start_time" :value="isoValue" required>

                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="w-full px-3.5 py-2.5 rounded-2xl border border-neutral-200/90 bg-white hover:bg-neutral-50/70 text-xs text-neutral-800 flex items-center justify-between gap-2.5 transition-all focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer shadow-xs min-h-[46px]"
                                    :class="{ 'border-lb-orange ring-2 ring-lb-orange/20': open }"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                                        <span class="w-6 h-6 rounded-xl bg-orange-50 text-lb-orange flex items-center justify-center text-xs shrink-0">
                                            <i class="fas fa-calendar-day"></i>
                                        </span>
                                        <span class="font-bold text-neutral-900 truncate" x-text="displayValue"></span>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-lb-orange': open }"></i>
                                </button>

                                <!-- Calendar & Time Popover (Left Aligned) -->
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="absolute z-50 left-0 top-full mt-2 w-80 sm:w-88 bg-white rounded-3xl border border-neutral-200 shadow-2xl p-4 space-y-3.5"
                                >
                                    <!-- Month Header -->
                                    <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
                                        <button
                                            type="button"
                                            @click.stop="prevMonth()"
                                            class="w-7 h-7 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Previous Month"
                                        >
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </button>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-neutral-900" x-text="months[currentMonth] + ' ' + currentYear"></span>
                                            <button
                                                type="button"
                                                @click.stop="goToToday()"
                                                class="text-[10px] font-bold text-lb-orange hover:underline px-1.5 py-0.5 rounded-md hover:bg-orange-50 cursor-pointer"
                                            >
                                                Today
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            @click.stop="nextMonth()"
                                            class="w-7 h-7 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Next Month"
                                        >
                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                        </button>
                                    </div>

                                    <!-- Quick Date Presets -->
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px]">
                                        <button type="button" @click.stop="setQuickDate(0)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">Today</button>
                                        <button type="button" @click.stop="setQuickDate(1)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">Tomorrow</button>
                                        <button type="button" @click.stop="setQuickDate(3)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">+3 Days</button>
                                        <button type="button" @click.stop="setQuickDate(7)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">+1 Week</button>
                                    </div>

                                    <!-- Calendar Days Grid -->
                                    <div class="space-y-1">
                                        <div class="grid grid-cols-7 text-center text-[10px] font-bold text-neutral-400 uppercase py-1">
                                            <template x-for="day in daysOfWeek" :key="day">
                                                <span x-text="day"></span>
                                            </template>
                                        </div>
                                        <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                            <template x-for="(d, i) in calendarDays" :key="i">
                                                <button
                                                    type="button"
                                                    @click.stop="selectDate(d)"
                                                    class="h-8 w-8 rounded-xl mx-auto flex items-center justify-center font-semibold transition-all cursor-pointer"
                                                    :class="{
                                                        'bg-[#FF5F00] text-white font-bold shadow-md shadow-orange-500/30 scale-105': d.isSelected,
                                                        'text-neutral-900 hover:bg-orange-50 hover:text-lb-orange': d.currentMonth && !d.isSelected,
                                                        'text-neutral-300': !d.currentMonth,
                                                        'ring-1 ring-lb-orange/40 font-bold': d.isToday && !d.isSelected
                                                    }"
                                                    x-text="d.day"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Time Selection -->
                                    <div class="pt-3 border-t border-neutral-100 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="fas fa-clock text-lb-orange text-[10px]"></i>
                                                Start Time (SL Time)
                                            </span>
                                            <div class="flex items-center gap-1">
                                                <button type="button" @click.stop="setTimePreset('10', '00', 'AM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">10 AM</button>
                                                <button type="button" @click.stop="setTimePreset('02', '00', 'PM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">2 PM</button>
                                                <button type="button" @click.stop="setTimePreset('06', '00', 'PM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">6 PM</button>
                                                <button type="button" @click.stop="setTimePreset('07', '30', 'PM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">7:30 PM</button>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-center gap-2">
                                            <select x-model="hour" class="text-xs p-1.5 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-800 font-bold focus:ring-1 focus:ring-lb-orange focus:outline-none">
                                                <template x-for="h in ['01','02','03','04','05','06','07','08','09','10','11','12']" :key="h">
                                                    <option :value="h" x-text="h"></option>
                                                </template>
                                            </select>
                                            <span class="font-bold text-neutral-400">:</span>
                                            <select x-model="minute" class="text-xs p-1.5 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-800 font-bold focus:ring-1 focus:ring-lb-orange focus:outline-none">
                                                <template x-for="m in ['00','15','30','45']" :key="m">
                                                    <option :value="m" x-text="m"></option>
                                                </template>
                                            </select>
                                            <div class="flex rounded-xl bg-neutral-100 p-0.5 border border-neutral-200">
                                                <button type="button" @click.stop="ampm = 'AM'" class="px-2 py-1 text-[11px] font-bold rounded-lg transition-colors cursor-pointer" :class="ampm === 'AM' ? 'bg-white text-lb-orange shadow-xs' : 'text-neutral-500 hover:text-neutral-800'">AM</button>
                                                <button type="button" @click.stop="ampm = 'PM'" class="px-2 py-1 text-[11px] font-bold rounded-lg transition-colors cursor-pointer" :class="ampm === 'PM' ? 'bg-white text-lb-orange shadow-xs' : 'text-neutral-500 hover:text-neutral-800'">PM</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="pt-2 border-t border-neutral-100 flex items-center justify-end">
                                        <button
                                            type="button"
                                            @click.stop="open = false"
                                            class="px-4 py-1.5 rounded-xl bg-lb-orange hover:bg-orange-600 text-white text-xs font-bold transition-all shadow-xs cursor-pointer"
                                        >
                                            Confirm
                                        </button>
                                    </div>
                                </div>

                                @error('scheduled_start_time')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date & Time (Custom Modern Picker) -->
                            <div
                                class="relative"
                                x-data="liveClassDatePicker(@js(old('scheduled_end_time', $liveClass->scheduled_end_time->format('Y-m-d\TH:i'))), '08', '00', 'PM')"
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                            >
                                <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1.5">
                                    End Date &amp; Time <span class="text-rose-500">*</span>
                                </label>

                                <input type="hidden" name="scheduled_end_time" :value="isoValue" required>

                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="w-full px-3.5 py-2.5 rounded-2xl border border-neutral-200/90 bg-white hover:bg-neutral-50/70 text-xs text-neutral-800 flex items-center justify-between gap-2.5 transition-all focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer shadow-xs min-h-[46px]"
                                    :class="{ 'border-lb-orange ring-2 ring-lb-orange/20': open }"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                                        <span class="w-6 h-6 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs shrink-0">
                                            <i class="fas fa-hourglass-end"></i>
                                        </span>
                                        <span class="font-bold text-neutral-900 truncate" x-text="displayValue"></span>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-lb-orange': open }"></i>
                                </button>

                                <!-- Calendar & Time Popover (Right Aligned on desktop) -->
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="absolute z-50 left-0 sm:left-auto sm:right-0 top-full mt-2 w-80 sm:w-88 bg-white rounded-3xl border border-neutral-200 shadow-2xl p-4 space-y-3.5"
                                >
                                    <!-- Month Header -->
                                    <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
                                        <button
                                            type="button"
                                            @click.stop="prevMonth()"
                                            class="w-7 h-7 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Previous Month"
                                        >
                                            <i class="fas fa-chevron-left text-[10px]"></i>
                                        </button>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-neutral-900" x-text="months[currentMonth] + ' ' + currentYear"></span>
                                            <button
                                                type="button"
                                                @click.stop="goToToday()"
                                                class="text-[10px] font-bold text-lb-orange hover:underline px-1.5 py-0.5 rounded-md hover:bg-orange-50 cursor-pointer"
                                            >
                                                Today
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            @click.stop="nextMonth()"
                                            class="w-7 h-7 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-600 flex items-center justify-center transition-colors cursor-pointer"
                                            title="Next Month"
                                        >
                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                        </button>
                                    </div>

                                    <!-- Quick Date Presets -->
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px]">
                                        <button type="button" @click.stop="setQuickDate(0)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">Today</button>
                                        <button type="button" @click.stop="setQuickDate(1)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">Tomorrow</button>
                                        <button type="button" @click.stop="setQuickDate(3)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">+3 Days</button>
                                        <button type="button" @click.stop="setQuickDate(7)" class="px-2.5 py-1 rounded-lg bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-semibold transition-colors shrink-0 cursor-pointer">+1 Week</button>
                                    </div>

                                    <!-- Calendar Days Grid -->
                                    <div class="space-y-1">
                                        <div class="grid grid-cols-7 text-center text-[10px] font-bold text-neutral-400 uppercase py-1">
                                            <template x-for="day in daysOfWeek" :key="day">
                                                <span x-text="day"></span>
                                            </template>
                                        </div>
                                        <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                            <template x-for="(d, i) in calendarDays" :key="i">
                                                <button
                                                    type="button"
                                                    @click.stop="selectDate(d)"
                                                    class="h-8 w-8 rounded-xl mx-auto flex items-center justify-center font-semibold transition-all cursor-pointer"
                                                    :class="{
                                                        'bg-[#FF5F00] text-white font-bold shadow-md shadow-orange-500/30 scale-105': d.isSelected,
                                                        'text-neutral-900 hover:bg-orange-50 hover:text-lb-orange': d.currentMonth && !d.isSelected,
                                                        'text-neutral-300': !d.currentMonth,
                                                        'ring-1 ring-lb-orange/40 font-bold': d.isToday && !d.isSelected
                                                    }"
                                                    x-text="d.day"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Time Selection -->
                                    <div class="pt-3 border-t border-neutral-100 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="fas fa-clock text-blue-600 text-[10px]"></i>
                                                End Time (SL Time)
                                            </span>
                                            <div class="flex items-center gap-1">
                                                <button type="button" @click.stop="setTimePreset('07', '00', 'PM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">7 PM</button>
                                                <button type="button" @click.stop="setTimePreset('08', '00', 'PM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">8 PM</button>
                                                <button type="button" @click.stop="setTimePreset('09', '00', 'PM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">9 PM</button>
                                                <button type="button" @click.stop="setTimePreset('10', '00', 'PM')" class="text-[9px] px-1.5 py-0.5 rounded bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-bold cursor-pointer">10 PM</button>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-center gap-2">
                                            <select x-model="hour" class="text-xs p-1.5 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-800 font-bold focus:ring-1 focus:ring-lb-orange focus:outline-none">
                                                <template x-for="h in ['01','02','03','04','05','06','07','08','09','10','11','12']" :key="h">
                                                    <option :value="h" x-text="h"></option>
                                                </template>
                                            </select>
                                            <span class="font-bold text-neutral-400">:</span>
                                            <select x-model="minute" class="text-xs p-1.5 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-800 font-bold focus:ring-1 focus:ring-lb-orange focus:outline-none">
                                                <template x-for="m in ['00','15','30','45']" :key="m">
                                                    <option :value="m" x-text="m"></option>
                                                </template>
                                            </select>
                                            <div class="flex rounded-xl bg-neutral-100 p-0.5 border border-neutral-200">
                                                <button type="button" @click.stop="ampm = 'AM'" class="px-2 py-1 text-[11px] font-bold rounded-lg transition-colors cursor-pointer" :class="ampm === 'AM' ? 'bg-white text-lb-orange shadow-xs' : 'text-neutral-500 hover:text-neutral-800'">AM</button>
                                                <button type="button" @click.stop="ampm = 'PM'" class="px-2 py-1 text-[11px] font-bold rounded-lg transition-colors cursor-pointer" :class="ampm === 'PM' ? 'bg-white text-lb-orange shadow-xs' : 'text-neutral-500 hover:text-neutral-800'">PM</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="pt-2 border-t border-neutral-100 flex items-center justify-end">
                                        <button
                                            type="button"
                                            @click.stop="open = false"
                                            class="px-4 py-1.5 rounded-xl bg-lb-orange hover:bg-orange-600 text-white text-xs font-bold transition-all shadow-xs cursor-pointer"
                                        >
                                            Confirm
                                        </button>
                                    </div>
                                </div>

                                @error('scheduled_end_time')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 4. Status & Access Permissions (Custom Modern Dropdowns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Session Status (Custom Modern Dropdown) -->
                        <div>
                            <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Session Status <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $selectedStatus = old('status', $liveClass->status);
                                $statusOptions = [
                                    'scheduled' => ['label' => 'Scheduled', 'desc' => 'On calendar, waiting for start time', 'icon' => 'fas fa-clock'],
                                    'live' => ['label' => 'Live Now', 'desc' => 'Session is currently active & accessible', 'icon' => 'fas fa-video'],
                                    'completed' => ['label' => 'Completed', 'desc' => 'Session has concluded', 'icon' => 'fas fa-check-circle'],
                                    'cancelled' => ['label' => 'Cancelled', 'desc' => 'Session is cancelled', 'icon' => 'fas fa-times-circle'],
                                ];
                                $currentStatusOpt = $statusOptions[$selectedStatus] ?? $statusOptions['scheduled'];
                            @endphp
                            <div
                                x-data="{
                                    open: false,
                                    selectedVal: @js($selectedStatus),
                                    selectedLabel: @js($currentStatusOpt['label']),
                                    selectedIcon: @js($currentStatusOpt['icon']),
                                    selectStatus(val, label, icon) {
                                        this.selectedVal = val;
                                        this.selectedLabel = label;
                                        this.selectedIcon = icon;
                                        this.open = false;
                                    }
                                }"
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                                class="relative"
                            >
                                <input type="hidden" name="status" :value="selectedVal" required>

                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-neutral-200/90 bg-neutral-50/50 hover:bg-neutral-100/60 text-xs text-neutral-800 flex items-center justify-between gap-3 transition-all focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer shadow-xs min-h-[46px]"
                                    :class="{ 'border-lb-orange ring-2 ring-lb-orange/20 bg-white': open }"
                                    aria-haspopup="listbox"
                                    :aria-expanded="open.toString()"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] shrink-0 font-bold bg-neutral-100 text-neutral-700 border border-neutral-200">
                                            <i :class="selectedIcon" class="text-[10px]"></i>
                                        </span>
                                        <span class="font-bold text-neutral-900 truncate" x-text="selectedLabel"></span>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-lb-orange': open }"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="w-full absolute left-0 top-full mt-1.5 rounded-2xl bg-white shadow-xl border border-neutral-100 p-2 z-50 text-xs space-y-1"
                                    role="listbox"
                                >
                                    @foreach ($statusOptions as $key => $opt)
                                        <button
                                            type="button"
                                            @click="selectStatus(@js($key), @js($opt['label']), @js($opt['icon']))"
                                            class="w-full p-2.5 rounded-xl flex items-center justify-between gap-2.5 text-left transition-colors cursor-pointer"
                                            :class="selectedVal === @js($key) ? 'bg-orange-50 text-lb-orange font-bold border border-orange-200/80' : 'text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 border border-transparent'"
                                            role="option"
                                        >
                                            <div class="flex items-start gap-2.5 min-w-0">
                                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5" :class="selectedVal === @js($key) ? 'bg-lb-orange text-white' : 'bg-neutral-100 text-neutral-600'">
                                                    <i class="{{ $opt['icon'] }}"></i>
                                                </span>
                                                <div class="truncate">
                                                    <span class="block text-xs font-semibold truncate">{{ $opt['label'] }}</span>
                                                    <span class="block text-[10px] text-neutral-400 truncate">{{ $opt['desc'] }}</span>
                                                </div>
                                            </div>
                                            <i x-show="selectedVal === @js($key)" class="fas fa-check text-xs text-lb-orange shrink-0"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('status')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Access Permissions (Custom Modern Dropdown) -->
                        <div>
                            <label class="block text-xs font-bold text-neutral-800 uppercase tracking-wider mb-1.5">
                                Access Permissions <span class="text-rose-500">*</span>
                            </label>
                            @php
                                $selectedAccess = old('access_permission', $liveClass->access_permission);
                                $accessOptions = [
                                    'enrolled_students' => [
                                        'label' => 'All Active Enrolled Students',
                                        'desc' => 'Open to all students with active enrollment',
                                        'icon' => 'fas fa-graduation-cap',
                                    ],
                                    'batch_students_only' => [
                                        'label' => 'Target Batch Enrollees Only',
                                        'desc' => 'Strictly for students in the selected batch',
                                        'icon' => 'fas fa-users',
                                    ],
                                    'instructors_only' => [
                                        'label' => 'Instructors & Faculty Only (Dry Run)',
                                        'desc' => 'Private session for staff & instructors only',
                                        'icon' => 'fas fa-shield-alt',
                                    ],
                                ];
                                $currentAccess = $accessOptions[$selectedAccess] ?? $accessOptions['enrolled_students'];
                            @endphp
                            <div
                                x-data="{
                                    open: false,
                                    selectedVal: @js($selectedAccess),
                                    selectedLabel: @js($currentAccess['label']),
                                    selectedIcon: @js($currentAccess['icon']),
                                    selectAccess(val, label, icon) {
                                        this.selectedVal = val;
                                        this.selectedLabel = label;
                                        this.selectedIcon = icon;
                                        this.open = false;
                                    }
                                }"
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                                class="relative"
                            >
                                <input type="hidden" name="access_permission" :value="selectedVal" required>

                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="w-full px-4 py-2.5 rounded-2xl border border-neutral-200/90 bg-neutral-50/50 hover:bg-neutral-100/60 text-xs text-neutral-800 flex items-center justify-between gap-3 transition-all focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer shadow-xs min-h-[46px]"
                                    :class="{ 'border-lb-orange ring-2 ring-lb-orange/20 bg-white': open }"
                                    aria-haspopup="listbox"
                                    :aria-expanded="open.toString()"
                                >
                                    <div class="flex items-center gap-2.5 min-w-0 truncate">
                                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] flex items-center justify-center shrink-0 border border-emerald-200">
                                            <i :class="selectedIcon" class="text-[10px]"></i>
                                        </span>
                                        <span class="font-bold text-neutral-900 truncate" x-text="selectedLabel"></span>
                                    </div>
                                    <i class="fas fa-chevron-down text-[10px] text-neutral-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-lb-orange': open }"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="w-full absolute left-0 top-full mt-1.5 rounded-2xl bg-white shadow-xl border border-neutral-100 p-2 z-50 text-xs space-y-1"
                                    role="listbox"
                                >
                                    @foreach ($accessOptions as $key => $opt)
                                        <button
                                            type="button"
                                            @click="selectAccess(@js($key), @js($opt['label']), @js($opt['icon']))"
                                            class="w-full p-2.5 rounded-xl flex items-center justify-between gap-2.5 text-left transition-colors cursor-pointer"
                                            :class="selectedVal === @js($key) ? 'bg-orange-50 text-lb-orange font-bold border border-orange-200/80' : 'text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 border border-transparent'"
                                            role="option"
                                        >
                                            <div class="flex items-start gap-2.5 min-w-0">
                                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5" :class="selectedVal === @js($key) ? 'bg-lb-orange text-white' : 'bg-neutral-100 text-neutral-600'">
                                                    <i class="{{ $opt['icon'] }}"></i>
                                                </span>
                                                <div class="truncate">
                                                    <span class="block text-xs font-semibold truncate">{{ $opt['label'] }}</span>
                                                    <span class="block text-[10px] text-neutral-400 truncate">{{ $opt['desc'] }}</span>
                                                </div>
                                            </div>
                                            <i x-show="selectedVal === @js($key)" class="fas fa-check text-xs text-lb-orange shrink-0"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @error('access_permission')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="pt-4 flex items-center justify-between border-t border-neutral-100">
                        <a
                            href="{{ route('management.courses.live-classes.show', [$course, $liveClass]) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-600 hover:bg-neutral-50 transition"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition cursor-pointer"
                        >
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        function liveClassDatePicker(initialValue, defaultHour = '06', defaultMinute = '00', defaultAmPm = 'PM') {
            return {
                open: false,
                rawInitial: initialValue || '',
                currentYear: new Date().getFullYear(),
                currentMonth: new Date().getMonth(),
                selectedDay: null,
                selectedMonth: null,
                selectedYear: null,
                hour: defaultHour,
                minute: defaultMinute,
                ampm: defaultAmPm,
                months: ['January','February','March','April','May','June','July','August','September','October','November','December'],
                daysOfWeek: ['Su','Mo','Tu','We','Th','Fr','Sa'],

                init() {
                    if (this.rawInitial) {
                        this.parseInitial(this.rawInitial);
                    } else {
                        this.setQuickDate(1);
                    }
                },

                parseInitial(str) {
                    if (!str) return;
                    try {
                        const parts = str.split(/[T\s]/);
                        if (parts[0]) {
                            const [y, m, d] = parts[0].split('-').map(Number);
                            if (y && m && d) {
                                this.selectedYear = y;
                                this.selectedMonth = m - 1;
                                this.selectedDay = d;
                                this.currentYear = y;
                                this.currentMonth = m - 1;
                            }
                        }
                        if (parts[1]) {
                            const [h, min] = parts[1].split(':').map(Number);
                            if (h !== undefined) {
                                let h12 = h % 12 || 12;
                                this.hour = String(h12).padStart(2, '0');
                                this.minute = String(min || 0).padStart(2, '0');
                                this.ampm = h >= 12 ? 'PM' : 'AM';
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing date string:', e);
                    }
                },

                get isoValue() {
                    if (!this.selectedDay || this.selectedMonth === null || !this.selectedYear) return '';
                    const m = String(this.selectedMonth + 1).padStart(2, '0');
                    const d = String(this.selectedDay).padStart(2, '0');
                    let h = parseInt(this.hour, 10);
                    if (this.ampm === 'PM' && h < 12) h += 12;
                    if (this.ampm === 'AM' && h === 12) h = 0;
                    const hStr = String(h).padStart(2, '0');
                    const minStr = String(this.minute).padStart(2, '0');
                    return `${this.selectedYear}-${m}-${d}T${hStr}:${minStr}`;
                },

                get displayValue() {
                    if (!this.selectedDay || this.selectedMonth === null || !this.selectedYear) {
                        return 'Choose Date & Time...';
                    }
                    const dateObj = new Date(this.selectedYear, this.selectedMonth, this.selectedDay);
                    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const dayName = dayNames[dateObj.getDay()];
                    const monthName = monthNames[this.selectedMonth];
                    return `${dayName}, ${monthName} ${this.selectedDay}, ${this.selectedYear} • ${this.hour}:${this.minute} ${this.ampm}`;
                },

                get calendarDays() {
                    const firstDayIndex = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    const totalDays = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    const prevTotalDays = new Date(this.currentYear, this.currentMonth, 0).getDate();
                    const days = [];
                    for (let i = firstDayIndex - 1; i >= 0; i--) {
                        days.push({ day: prevTotalDays - i, currentMonth: false, isPrev: true });
                    }
                    for (let i = 1; i <= totalDays; i++) {
                        const isSelected = this.selectedDay === i && this.selectedMonth === this.currentMonth && this.selectedYear === this.currentYear;
                        const today = new Date();
                        const isToday = today.getDate() === i && today.getMonth() === this.currentMonth && today.getFullYear() === this.currentYear;
                        days.push({ day: i, currentMonth: true, isSelected, isToday });
                    }
                    const remaining = (7 - (days.length % 7)) % 7;
                    for (let i = 1; i <= remaining; i++) {
                        days.push({ day: i, currentMonth: false, isNext: true });
                    }
                    return days;
                },

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                },

                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                },

                selectDate(d) {
                    if (!d.currentMonth) {
                        if (d.isPrev) this.prevMonth();
                        if (d.isNext) this.nextMonth();
                    }
                    this.selectedDay = d.day;
                    this.selectedMonth = this.currentMonth;
                    this.selectedYear = this.currentYear;
                },

                setQuickDate(daysAhead) {
                    const d = new Date();
                    d.setDate(d.getDate() + daysAhead);
                    this.currentYear = d.getFullYear();
                    this.currentMonth = d.getMonth();
                    this.selectedYear = d.getFullYear();
                    this.selectedMonth = d.getMonth();
                    this.selectedDay = d.getDate();
                },

                goToToday() {
                    const today = new Date();
                    this.currentYear = today.getFullYear();
                    this.currentMonth = today.getMonth();
                    this.selectedYear = today.getFullYear();
                    this.selectedMonth = today.getMonth();
                    this.selectedDay = today.getDate();
                },

                setTimePreset(h, m, ampm) {
                    this.hour = h;
                    this.minute = m;
                    this.ampm = ampm;
                }
            };
        }
    </script>
</x-layouts.public>

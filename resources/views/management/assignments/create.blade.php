<x-layouts.public title="Create Assignment - LearnBuddy" description="Create a new course assignment.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-3xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Courses</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <a href="{{ route('management.courses.assignments.index', $course) }}" class="hover:text-lb-orange">Assignments</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">New Assignment</span>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <div>
                        <p class="font-semibold">Please correct the following errors:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-lb-black tracking-tight">Create Assignment</h1>
                    <p class="text-xs text-neutral-500 mt-1">Course: <strong class="text-neutral-700">{{ $course->title }}</strong></p>
                </div>

                <form action="{{ route('management.courses.assignments.store', $course) }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="title" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Assignment Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            required
                            placeholder="e.g. Mid-term Cloud Architecture Project"
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Short Summary
                        </label>
                        <input
                            type="text"
                            name="description"
                            id="description"
                            value="{{ old('description') }}"
                            placeholder="Brief 1-line overview of the assignment scope..."
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >
                    </div>

                    <div>
                        <label for="instructions" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                            Detailed Instructions &amp; Requirements
                        </label>
                        <textarea
                            name="instructions"
                            id="instructions"
                            rows="6"
                            placeholder="Detail what students must complete, formatting criteria, and deliverables..."
                            class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                        >{{ old('instructions') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="max_points" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Maximum Marks <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="max_points"
                                id="max_points"
                                value="{{ old('max_points', 100) }}"
                                min="1"
                                max="1000"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="passing_points" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Passing Marks
                            </label>
                            <input
                                type="number"
                                name="passing_points"
                                id="passing_points"
                                value="{{ old('passing_points', 50) }}"
                                min="1"
                                max="1000"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div
                            class="relative"
                            x-data="{
                                open: false,
                                rawInitial: @js(old('due_date', '')),
                                currentYear: new Date().getFullYear(),
                                currentMonth: new Date().getMonth(),
                                selectedDay: null,
                                selectedMonth: null,
                                selectedYear: null,
                                hour: '11',
                                minute: '59',
                                ampm: 'PM',
                                months: ['January','February','March','April','May','June','July','August','September','October','November','December'],
                                daysOfWeek: ['Su','Mo','Tu','We','Th','Fr','Sa'],

                                init() {
                                    if (this.rawInitial) {
                                        this.parseInitial(this.rawInitial);
                                    }
                                },

                                parseInitial(str) {
                                    if (!str) return;
                                    try {
                                        const d = new Date(str);
                                        if (!isNaN(d.getTime())) {
                                            this.selectedYear = d.getFullYear();
                                            this.selectedMonth = d.getMonth();
                                            this.selectedDay = d.getDate();
                                            this.currentYear = d.getFullYear();
                                            this.currentMonth = d.getMonth();
                                            let h = d.getHours();
                                            let h12 = h % 12 || 12;
                                            this.hour = String(h12).padStart(2, '0');
                                            this.minute = String(d.getMinutes()).padStart(2, '0');
                                            this.ampm = h >= 12 ? 'PM' : 'AM';
                                            return;
                                        }
                                        const parts = str.split(/[T\s]/);
                                        if (parts[0]) {
                                            const [y, m, day] = parts[0].split('-').map(Number);
                                            if (y && m && day) {
                                                this.selectedYear = y;
                                                this.selectedMonth = m - 1;
                                                this.selectedDay = day;
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
                                    } catch (e) {}
                                },

                                get formattedDisplay() {
                                    if (!this.selectedDay || this.selectedMonth === null || !this.selectedYear) {
                                        return '';
                                    }
                                    const mo = this.months[this.selectedMonth].substring(0, 3);
                                    return `${mo} ${this.selectedDay}, ${this.selectedYear} ${this.hour}:${this.minute} ${this.ampm}`;
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
                                },

                                clear() {
                                    this.selectedDay = null;
                                    this.selectedMonth = null;
                                    this.selectedYear = null;
                                    this.open = false;
                                }
                            }"
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                        >
                            <label for="due_date" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Due Date (SL Time)
                            </label>

                            <div class="relative">
                                <input
                                    type="text"
                                    name="due_date"
                                    id="due_date"
                                    :value="formattedDisplay"
                                    readonly
                                    @click="open = !open"
                                    @focus="open = true"
                                    placeholder="Select due date &amp; time..."
                                    class="w-full text-xs p-3 pl-10 pr-10 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange cursor-pointer bg-white text-neutral-800 font-semibold hover:border-neutral-400 transition-all shadow-xs"
                                    :class="{ 'ring-2 ring-lb-orange border-lb-orange': open }"
                                >
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-lb-orange hover:text-orange-600 transition-colors"
                                    aria-label="Open calendar"
                                >
                                    <i class="fas fa-calendar-days text-sm"></i>
                                </button>
                                <button
                                    type="button"
                                    x-show="formattedDisplay"
                                    x-cloak
                                    @click.stop="clear()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-rose-500 transition-colors"
                                    title="Clear due date"
                                    aria-label="Clear date"
                                >
                                    <i class="fas fa-times-circle text-xs"></i>
                                </button>
                            </div>

                            <!-- Dropdown Calendar Popover -->
                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                                x-cloak
                                class="absolute z-50 left-0 mt-2 w-80 sm:w-88 bg-white rounded-3xl border border-neutral-200 shadow-2xl p-4 space-y-3.5"
                            >
                                <!-- Month Navigation -->
                                <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
                                    <button
                                        type="button"
                                        @click.stop="prevMonth()"
                                        class="w-7 h-7 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-600 hover:text-neutral-900 flex items-center justify-center transition-colors"
                                        title="Previous month"
                                    >
                                        <i class="fas fa-chevron-left text-[11px]"></i>
                                    </button>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-neutral-900" x-text="months[currentMonth] + ' ' + currentYear"></span>
                                        <button
                                            type="button"
                                            @click.stop="goToToday()"
                                            class="text-[10px] font-semibold text-lb-orange hover:underline px-1.5 py-0.5 rounded-md hover:bg-orange-50"
                                        >
                                            Today
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        @click.stop="nextMonth()"
                                        class="w-7 h-7 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-600 hover:text-neutral-900 flex items-center justify-center transition-colors"
                                        title="Next month"
                                    >
                                        <i class="fas fa-chevron-right text-[11px]"></i>
                                    </button>
                                </div>

                                <!-- Quick Presets -->
                                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px]">
                                    <button
                                        type="button"
                                        @click.stop="setQuickDate(1)"
                                        class="px-2 py-0.5 rounded-lg border border-neutral-200 bg-neutral-50 hover:bg-orange-50 hover:border-orange-200 hover:text-lb-orange text-neutral-600 font-medium whitespace-nowrap transition-colors"
                                    >
                                        Tomorrow
                                    </button>
                                    <button
                                        type="button"
                                        @click.stop="setQuickDate(3)"
                                        class="px-2 py-0.5 rounded-lg border border-neutral-200 bg-neutral-50 hover:bg-orange-50 hover:border-orange-200 hover:text-lb-orange text-neutral-600 font-medium whitespace-nowrap transition-colors"
                                    >
                                        +3 Days
                                    </button>
                                    <button
                                        type="button"
                                        @click.stop="setQuickDate(7)"
                                        class="px-2 py-0.5 rounded-lg border border-neutral-200 bg-neutral-50 hover:bg-orange-50 hover:border-orange-200 hover:text-lb-orange text-neutral-600 font-medium whitespace-nowrap transition-colors"
                                    >
                                        +1 Week
                                    </button>
                                    <button
                                        type="button"
                                        @click.stop="setQuickDate(14)"
                                        class="px-2 py-0.5 rounded-lg border border-neutral-200 bg-neutral-50 hover:bg-orange-50 hover:border-orange-200 hover:text-lb-orange text-neutral-600 font-medium whitespace-nowrap transition-colors"
                                    >
                                        +2 Weeks
                                    </button>
                                </div>

                                <!-- Days Grid -->
                                <div>
                                    <div class="grid grid-cols-7 gap-1 text-center mb-1">
                                        <template x-for="dw in daysOfWeek" :key="dw">
                                            <span class="text-[10px] font-bold text-neutral-400 uppercase" x-text="dw"></span>
                                        </template>
                                    </div>

                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="(d, idx) in calendarDays" :key="idx">
                                            <button
                                                type="button"
                                                @click.stop="selectDate(d)"
                                                class="h-7 w-full rounded-xl text-xs flex items-center justify-center transition-all"
                                                :class="{
                                                    'bg-lb-orange text-white font-bold shadow-xs scale-105': d.isSelected,
                                                    'text-neutral-800 hover:bg-orange-50 hover:text-lb-orange font-semibold': d.currentMonth && !d.isSelected,
                                                    'text-neutral-300 hover:text-neutral-400': !d.currentMonth,
                                                    'ring-1 ring-lb-orange text-lb-orange font-bold': d.isToday && !d.isSelected
                                                }"
                                                x-text="d.day"
                                            ></button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Time Selector -->
                                <div class="pt-3 border-t border-neutral-100 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-neutral-600 uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="fas fa-clock text-lb-orange text-xs"></i>
                                            <span>Due Time (SL Time)</span>
                                        </span>
                                        <div class="flex items-center gap-1">
                                            <button
                                                type="button"
                                                @click.stop="setTimePreset('11', '59', 'PM')"
                                                class="text-[10px] px-2 py-0.5 rounded-md bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-medium transition-colors"
                                            >
                                                11:59 PM
                                            </button>
                                            <button
                                                type="button"
                                                @click.stop="setTimePreset('05', '00', 'PM')"
                                                class="text-[10px] px-2 py-0.5 rounded-md bg-neutral-100 hover:bg-orange-100 hover:text-lb-orange text-neutral-600 font-medium transition-colors"
                                            >
                                                5:00 PM
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-center gap-2">
                                        <select
                                            x-model="hour"
                                            class="text-xs p-1.5 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-800 font-bold focus:ring-1 focus:ring-lb-orange focus:outline-none"
                                        >
                                            <template x-for="h in ['01','02','03','04','05','06','07','08','09','10','11','12']" :key="h">
                                                <option :value="h" x-text="h"></option>
                                            </template>
                                        </select>
                                        <span class="font-bold text-neutral-400">:</span>
                                        <select
                                            x-model="minute"
                                            class="text-xs p-1.5 rounded-xl border border-neutral-200 bg-neutral-50 text-neutral-800 font-bold focus:ring-1 focus:ring-lb-orange focus:outline-none"
                                        >
                                            <template x-for="m in ['00','15','30','45','59']" :key="m">
                                                <option :value="m" x-text="m"></option>
                                            </template>
                                        </select>
                                        <div class="flex rounded-xl bg-neutral-100 p-0.5 border border-neutral-200">
                                            <button
                                                type="button"
                                                @click.stop="ampm = 'AM'"
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg transition-colors"
                                                :class="ampm === 'AM' ? 'bg-white text-lb-orange shadow-xs' : 'text-neutral-500 hover:text-neutral-800'"
                                            >
                                                AM
                                            </button>
                                            <button
                                                type="button"
                                                @click.stop="ampm = 'PM'"
                                                class="px-2.5 py-1 text-xs font-bold rounded-lg transition-colors"
                                                :class="ampm === 'PM' ? 'bg-white text-lb-orange shadow-xs' : 'text-neutral-500 hover:text-neutral-800'"
                                            >
                                                PM
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="pt-2.5 border-t border-neutral-100 flex items-center justify-between">
                                    <span class="text-[10px] text-neutral-400 flex items-center gap-1 font-medium">
                                        <i class="fas fa-globe text-lb-orange text-[10px]"></i>
                                        SL Time (GMT+5:30)
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click.stop="clear()"
                                            class="text-xs font-semibold text-neutral-500 hover:text-rose-600 transition-colors"
                                        >
                                            Clear
                                        </button>
                                        <button
                                            type="button"
                                            @click.stop="open = false"
                                            class="px-3.5 py-1.5 rounded-xl bg-lb-orange hover:bg-orange-600 text-white text-xs font-bold transition-all shadow-xs"
                                        >
                                            Done
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="allowed_file_types" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Allowed Formats
                            </label>
                            <input
                                type="text"
                                name="allowed_file_types"
                                id="allowed_file_types"
                                value="{{ old('allowed_file_types', 'pdf,zip,docx,txt') }}"
                                placeholder="pdf,zip,doc,docx"
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                        <div>
                            <label for="max_file_size_mb" class="block text-xs font-bold text-neutral-700 uppercase tracking-wider mb-1">
                                Max Size (MB) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="max_file_size_mb"
                                id="max_file_size_mb"
                                value="{{ old('max_file_size_mb', 10) }}"
                                min="1"
                                max="100"
                                required
                                class="w-full text-xs p-3 rounded-2xl border border-neutral-300 focus:outline-none focus:ring-2 focus:ring-lb-orange"
                            >
                        </div>
                    </div>

                    <div class="pt-4 border-t border-neutral-100 flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-neutral-300 text-lb-orange focus:ring-lb-orange">
                            <span>Publish Assignment immediately</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700">
                            <input type="checkbox" name="allow_late_submissions" value="1" {{ old('allow_late_submissions') ? 'checked' : '' }} class="rounded border-neutral-300 text-lb-orange focus:ring-lb-orange">
                            <span>Allow late submissions after due date</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-neutral-100 flex items-center justify-end gap-3">
                        <a
                            href="{{ route('management.courses.assignments.index', $course) }}"
                            class="px-5 py-2.5 rounded-full border border-neutral-300 text-xs font-semibold text-neutral-700 hover:bg-neutral-50"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            Save Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layouts.public>

<div
    x-data="{
        open: false,
        submitting: false,
        submitted: false,
        ticketId: null,
        errorMessage: '',
        formData: {
            name: '{{ auth()->user()?->name ?? '' }}',
            email: '{{ auth()->user()?->email ?? '' }}',
            phone: '{{ auth()->user()?->phone ?? '' }}',
            category: 'general',
            subject: '',
            message: ''
        },
        openModal(detail = {}) {
            this.open = true;
            this.submitted = false;
            this.errorMessage = '';
            if (detail.subject) this.formData.subject = detail.subject;
            if (detail.category) this.formData.category = detail.category;
        },
        closeModal() {
            this.open = false;
        },
        async submitForm() {
            this.submitting = true;
            this.errorMessage = '';
            try {
                const response = await fetch('{{ route('support.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(this.formData)
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    this.submitted = true;
                    this.ticketId = data.ticket_id;
                    this.formData.message = '';
                } else {
                    this.errorMessage = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Unable to send message. Please try again.');
                }
            } catch (err) {
                this.errorMessage = 'Network connection issue. Please try again.';
            } finally {
                this.submitting = false;
            }
        }
    }"
    @open-support-modal.window="openModal($event.detail || {})"
    x-init="
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-open-support]');
            if (trigger) {
                e.preventDefault();
                openModal({
                    subject: trigger.dataset.supportSubject || '',
                    category: trigger.dataset.supportCategory || 'general'
                });
            }
        });
    "
    x-show="open"
    x-cloak
    @keydown.escape.window="closeModal()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-900/60 backdrop-blur-md transition-opacity duration-300 overflow-y-auto"
    role="dialog"
    aria-modal="true"
    aria-labelledby="support-modal-title"
>
    <div
        @click.outside="closeModal()"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white rounded-3xl border border-neutral-200/80 shadow-2xl p-6 sm:p-8 max-w-lg w-full relative my-8 text-left"
    >
        <!-- Close button -->
        <button
            type="button"
            @click="closeModal()"
            class="absolute top-5 right-5 w-8 h-8 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 hover:text-neutral-800 flex items-center justify-center transition cursor-pointer"
            aria-label="Close modal"
        >
            <i class="fas fa-times text-xs"></i>
        </button>

        <!-- Header -->
        <div class="flex items-center gap-3 mb-5 pr-8">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-orange-200/80 text-lb-orange flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-headset" aria-hidden="true"></i>
            </div>
            <div>
                <h3 id="support-modal-title" class="text-lg sm:text-xl font-extrabold text-neutral-900 tracking-tight">
                    Contact Support
                </h3>
                <p class="text-xs text-neutral-500">
                    Send a direct message to the LearnBuddy team.
                </p>
            </div>
        </div>

        <!-- Success State -->
        <div x-show="submitted" class="space-y-4 py-4 text-center">
            <div class="w-14 h-14 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-500 flex items-center justify-center mx-auto text-2xl">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="space-y-1">
                <h4 class="text-base font-bold text-neutral-900">Support Ticket Created!</h4>
                <p class="text-xs sm:text-sm text-neutral-600 max-w-xs mx-auto leading-relaxed">
                    Thank you! Your ticket <span class="font-bold text-neutral-900" x-text="ticketId ? '#' + ticketId : ''"></span> has been received. Our team will review your message and reply directly to your email address.
                </p>
            </div>
            <div class="pt-3">
                <button
                    type="button"
                    @click="closeModal()"
                    class="w-full py-2.5 px-5 rounded-full bg-lb-orange hover:bg-[#E65500] text-white text-xs font-bold transition shadow-xs cursor-pointer"
                >
                    Close
                </button>
            </div>
        </div>

        <!-- Form State -->
        <form x-show="!submitted" @submit.prevent="submitForm()" class="space-y-4">
            <!-- Error message if any -->
            <div x-show="errorMessage" x-cloak class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-rose-500 shrink-0"></i>
                <span x-text="errorMessage"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        x-model="formData.name"
                        required
                        placeholder="Your name"
                        class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus:outline-none focus:ring-2 focus:ring-lb-orange"
                    >
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="email"
                        x-model="formData.email"
                        required
                        placeholder="name@example.com"
                        class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus:outline-none focus:ring-2 focus:ring-lb-orange"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                        Phone (Optional)
                    </label>
                    <input
                        type="text"
                        x-model="formData.phone"
                        placeholder="+94 77 123 4567"
                        class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus:outline-none focus:ring-2 focus:ring-lb-orange font-mono"
                    >
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                        Category <span class="text-rose-500">*</span>
                    </label>
                    <select
                        x-model="formData.category"
                        required
                        class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus:outline-none focus:ring-2 focus:ring-lb-orange"
                    >
                        @foreach (\App\Models\SupportMessage::CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                    Subject <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    x-model="formData.subject"
                    required
                    placeholder="e.g. Account reactivation / Payment question"
                    class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus:outline-none focus:ring-2 focus:ring-lb-orange"
                >
            </div>

            <div>
                <label class="block text-[11px] font-bold text-neutral-700 uppercase tracking-wider mb-1">
                    Message <span class="text-rose-500">*</span>
                </label>
                <textarea
                    x-model="formData.message"
                    required
                    rows="4"
                    placeholder="Describe your issue or question in detail..."
                    class="w-full p-3 rounded-xl border border-neutral-200 bg-neutral-50/50 text-xs focus:outline-none focus:ring-2 focus:ring-lb-orange"
                ></textarea>
            </div>

            <div class="pt-2 flex items-center justify-between gap-3">
                <span class="text-[11px] text-neutral-400">
                    <i class="fas fa-lock text-[10px] mr-1"></i> Received by Admin
                </span>

                <button
                    type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center gap-2 py-2.5 px-6 rounded-full font-bold text-white bg-lb-orange hover:bg-[#E65500] active:scale-[0.99] transition shadow-xs text-xs cursor-pointer disabled:opacity-50"
                >
                    <span x-show="!submitting">Send Message</span>
                    <span x-show="submitting" x-cloak class="flex items-center gap-1.5">
                        <i class="fas fa-circle-notch fa-spin"></i> Sending...
                    </span>
                    <i x-show="!submitting" class="fas fa-paper-plane text-[10px]" aria-hidden="true"></i>
                </button>
            </div>
        </form>
    </div>
</div>

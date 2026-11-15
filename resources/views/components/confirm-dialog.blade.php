<div
    x-data="{
        isOpen: false,
        title: 'Are you sure?',
        itemName: '',
        message: 'This action cannot be undone. Are you sure you want to proceed?',
        action: '',
        method: 'POST',
        targetForm: null,
        fields: [],
        confirmText: 'Delete',
        confirmVariant: 'danger',
        icon: 'fas fa-trash-alt',
        submitting: false,

        openModal(detail) {
            detail = detail || {};
            this.title = detail.title || 'Are you sure?';
            this.itemName = detail.itemName || '';
            this.message = detail.message || 'This action cannot be undone.';
            this.action = detail.action || '';
            this.method = (detail.method || 'POST').toUpperCase();
            this.targetForm = detail.targetForm || null;
            this.confirmText = detail.confirmText || (detail.confirmVariant === 'danger' || !detail.confirmVariant ? 'Delete' : 'Confirm');
            this.confirmVariant = detail.confirmVariant || 'danger';
            this.icon = detail.icon || (
                this.confirmVariant === 'danger' ? 'fas fa-trash-alt' :
                (this.confirmVariant === 'success' ? 'fas fa-check-circle' :
                (this.confirmVariant === 'warning' ? 'fas fa-exclamation-triangle' : 'fas fa-arrow-right'))
            );

            const rawFields = detail.fields || {};
            if (Array.isArray(rawFields)) {
                this.fields = rawFields;
            } else {
                this.fields = Object.keys(rawFields).map(key => ({ name: key, value: rawFields[key] }));
            }

            this.submitting = false;
            this.isOpen = true;
        },

        close() {
            if (!this.submitting) {
                this.isOpen = false;
            }
        },

        submit() {
            if (this.submitting) return;

            if (this.targetForm) {
                const formEl = typeof this.targetForm === 'string' ? document.getElementById(this.targetForm) : this.targetForm;
                if (formEl) {
                    this.submitting = true;
                    formEl.submit();
                    return;
                }
            }

            if (!this.action) return;

            if (this.method === 'GET') {
                this.submitting = true;
                window.location.href = this.action;
                return;
            }

            this.submitting = true;
            this.$refs.confirmForm.submit();
        }
    }"
    x-on:confirm-dialog.window="openModal($event.detail)"
    x-on:keydown.escape.window="close()"
    x-show="isOpen"
    x-cloak
    class="fixed inset-0 z-[100] overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirm-dialog-title"
>
    <!-- Backdrop Overlay -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-250"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm transition-opacity"
        @click="close()"
    ></div>

    <!-- Dialog Box -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative bg-white rounded-3xl shadow-2xl transform transition-all w-full max-w-md p-6 sm:p-7 z-10 border border-neutral-100 overflow-hidden"
        @click.stop
    >
        <!-- Close 'X' Button in Top Corner -->
        <button
            type="button"
            @click="close()"
            class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-700 p-2 rounded-xl transition cursor-pointer"
            aria-label="Close dialog"
        >
            <i class="fas fa-times text-xs" aria-hidden="true"></i>
        </button>

        <div class="flex items-start gap-4">
            <!-- Dynamic Icon Container -->
            <div
                class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0"
                :class="{
                    'bg-rose-50 text-rose-600': confirmVariant === 'danger',
                    'bg-amber-50 text-amber-600': confirmVariant === 'warning',
                    'bg-emerald-50 text-emerald-600': confirmVariant === 'success',
                    'bg-orange-50 text-lb-orange': confirmVariant === 'primary'
                }"
            >
                <i :class="icon" aria-hidden="true"></i>
            </div>

            <!-- Content Area -->
            <div class="flex-1 pr-4 min-w-0">
                <h3 class="text-base sm:text-lg font-bold text-neutral-900 tracking-tight" id="confirm-dialog-title" x-text="title">
                    Confirm Action
                </h3>

                <!-- Optional Highlighted Item Name -->
                <div x-show="itemName" class="mt-1.5">
                    <span class="inline-block px-2.5 py-1 rounded-xl bg-neutral-100 text-neutral-800 font-semibold text-xs truncate max-w-full" x-text="itemName"></span>
                </div>

                <p class="mt-2 text-xs sm:text-sm text-neutral-600 leading-relaxed" x-text="message">
                    Are you sure you want to proceed?
                </p>
            </div>
        </div>

        <!-- Hidden Form Submitted Upon Confirmation -->
        <form x-ref="confirmForm" :action="action" method="POST" class="hidden">
            @csrf
            <template x-if="method !== 'POST' && method !== 'GET'">
                <input type="hidden" name="_method" :value="method">
            </template>
            <template x-for="(field, index) in fields" :key="index">
                <input type="hidden" :name="field.name" :value="field.value">
            </template>
        </form>

        <!-- Action Buttons -->
        <div class="mt-6 pt-5 border-t border-neutral-100 flex items-center justify-end gap-3">
            <button
                type="button"
                @click="close()"
                :disabled="submitting"
                class="px-5 py-2.5 rounded-full text-xs font-semibold text-neutral-700 bg-neutral-100 hover:bg-neutral-200 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400 cursor-pointer disabled:opacity-50"
            >
                Cancel
            </button>

            <button
                type="button"
                @click="submit()"
                :disabled="submitting"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-xs font-bold text-white transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 cursor-pointer disabled:opacity-50"
                :class="{
                    'bg-rose-600 hover:bg-rose-700 focus-visible:ring-rose-500': confirmVariant === 'danger',
                    'bg-amber-600 hover:bg-amber-700 focus-visible:ring-amber-500': confirmVariant === 'warning',
                    'bg-emerald-600 hover:bg-emerald-700 focus-visible:ring-emerald-500': confirmVariant === 'success',
                    'bg-lb-orange hover:bg-[#E65500] focus-visible:ring-lb-orange': confirmVariant === 'primary'
                }"
            >
                <i x-show="submitting" class="fas fa-spinner fa-spin text-xs" aria-hidden="true"></i>
                <span x-text="submitting ? 'Processing...' : confirmText">Confirm</span>
            </button>
        </div>
    </div>
</div>

<script>
    if (typeof window.confirmDialog !== 'function') {
        window.confirmDialog = function(detail) {
            window.dispatchEvent(new CustomEvent('confirm-dialog', { detail: detail || {} }));
        };
    }
</script>

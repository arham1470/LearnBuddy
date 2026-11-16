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
        confirmText: 'Confirm',
        confirmVariant: 'danger',
        icon: 'fas fa-trash-alt',
        submitting: false,
        onConfirm: null,
        onCancel: null,

        openModal(detail) {
            detail = detail || {};
            this.title = detail.title || 'Are you sure?';
            this.itemName = detail.itemName || '';
            this.message = detail.message || 'This action cannot be undone. Are you sure you want to proceed?';
            this.action = detail.action || '';
            this.method = (detail.method || 'POST').toUpperCase();
            this.targetForm = detail.targetForm || null;
            this.confirmVariant = detail.confirmVariant || 'danger';
            this.confirmText = detail.confirmText || (this.confirmVariant === 'danger' ? 'Delete' : 'Confirm');
            this.onConfirm = typeof detail.onConfirm === 'function' ? detail.onConfirm : null;
            this.onCancel = typeof detail.onCancel === 'function' ? detail.onCancel : null;

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
                if (this.onCancel && typeof this.onCancel === 'function') {
                    try { this.onCancel(); } catch (err) { console.error(err); }
                    this.onCancel = null;
                }
            }
        },

        submit() {
            if (this.submitting) return;

            if (this.onConfirm && typeof this.onConfirm === 'function') {
                this.submitting = true;
                try { this.onConfirm(); } catch (err) { console.error(err); }
                this.submitting = false;
                this.isOpen = false;
                return;
            }

            if (this.targetForm) {
                const formEl = typeof this.targetForm === 'string' ? document.getElementById(this.targetForm) : this.targetForm;
                if (formEl) {
                    this.submitting = true;
                    formEl.dataset.confirmed = 'true';
                    if (typeof HTMLFormElement.prototype.submit === 'function') {
                        HTMLFormElement.prototype.submit.call(formEl);
                    } else {
                        formEl.submit();
                    }
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
        class="relative bg-white rounded-3xl shadow-2xl transform transition-all w-full max-w-md p-6 sm:p-7 z-10 border border-[#E5E5E5] overflow-hidden"
        @click.stop
    >
        <!-- Close 'X' Button in Top Corner -->
        <button
            type="button"
            @click="close()"
            class="absolute top-4 right-4 text-[#6B6B6B] hover:text-[#0A0A0A] p-2 rounded-xl hover:bg-[#F7F7F5] transition-colors cursor-pointer"
            aria-label="Close dialog"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="flex items-start gap-4">
            <!-- Dynamic Icon Container -->
            <div
                class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0"
                :class="{
                    'bg-rose-50 text-rose-600 border border-rose-200/60': confirmVariant === 'danger',
                    'bg-amber-50 text-amber-600 border border-amber-200/60': confirmVariant === 'warning',
                    'bg-emerald-50 text-emerald-600 border border-emerald-200/60': confirmVariant === 'success',
                    'bg-[#FFF0E6] text-[#FF5F00] border border-orange-200/60': confirmVariant === 'primary'
                }"
            >
                <template x-if="confirmVariant === 'danger'">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </template>
                <template x-if="confirmVariant === 'warning'">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </template>
                <template x-if="confirmVariant === 'success'">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="confirmVariant === 'primary'">
                    <svg class="w-6 h-6 text-[#FF5F00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </div>

            <!-- Content Area -->
            <div class="flex-1 pr-4 min-w-0">
                <h3 class="text-base sm:text-lg font-extrabold text-[#0A0A0A] tracking-tight" id="confirm-dialog-title" x-text="title">
                    Confirm Action
                </h3>

                <!-- Optional Highlighted Item Name -->
                <div x-show="itemName" class="mt-1.5">
                    <span class="inline-block px-2.5 py-1 rounded-xl bg-[#F7F7F5] border border-[#E5E5E5] text-[#0A0A0A] font-semibold text-xs truncate max-w-full" x-text="itemName"></span>
                </div>

                <p class="mt-2 text-xs sm:text-sm text-[#6B6B6B] leading-relaxed" x-text="message">
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
        <div class="mt-6 pt-5 border-t border-[#E5E5E5] flex items-center justify-end gap-3">
            <button
                type="button"
                @click="close()"
                :disabled="submitting"
                class="px-5 py-2.5 rounded-xl text-xs font-semibold text-[#0A0A0A] bg-[#F7F7F5] hover:bg-[#E5E5E5] transition-colors border border-[#E5E5E5] cursor-pointer disabled:opacity-50"
            >
                Cancel
            </button>

            <button
                type="button"
                @click="submit()"
                :disabled="submitting"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white transition-all shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 cursor-pointer disabled:opacity-50"
                :class="{
                    'bg-rose-600 hover:bg-rose-700 focus-visible:ring-rose-500 shadow-rose-600/20': confirmVariant === 'danger',
                    'bg-amber-600 hover:bg-amber-700 focus-visible:ring-amber-500 shadow-amber-600/20': confirmVariant === 'warning',
                    'bg-emerald-600 hover:bg-emerald-700 focus-visible:ring-emerald-500 shadow-emerald-600/20': confirmVariant === 'success',
                    'bg-[#FF5F00] hover:bg-[#e65500] focus-visible:ring-orange-500 shadow-orange-500/25': confirmVariant === 'primary'
                }"
            >
                <svg x-show="submitting" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
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

    if (typeof window.confirmAction !== 'function') {
        window.confirmAction = function(options) {
            return new Promise(function(resolve) {
                window.confirmDialog(Object.assign({}, options, {
                    onConfirm: function() { resolve(true); },
                    onCancel: function() { resolve(false); }
                }));
            });
        };
    }

    // Global declarative interceptor for data-confirm and legacy onsubmit confirm
    if (!window.__learnBuddyConfirmInterceptorInstalled) {
        window.__learnBuddyConfirmInterceptorInstalled = true;

        // 1. Intercept clicks on links or non-submit elements with data-confirm
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-confirm]');
            if (!target) return;

            // If target is inside a form and is a submit button, let the submit handler catch it
            if (target.matches('button[type="submit"], input[type="submit"]') && target.closest('form')) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const isLink = target.tagName === 'A' && target.href;
            window.confirmDialog({
                title: target.dataset.confirmTitle || (target.dataset.confirmVariant === 'danger' ? 'Confirm Deletion' : 'Confirm Action'),
                itemName: target.dataset.confirmItem || '',
                message: target.dataset.confirm || 'Are you sure you want to proceed?',
                confirmText: target.dataset.confirmBtn || (target.dataset.confirmVariant === 'danger' ? 'Delete' : 'Confirm'),
                confirmVariant: target.dataset.confirmVariant || 'danger',
                icon: target.dataset.confirmIcon || '',
                action: isLink ? target.href : '',
                method: isLink ? 'GET' : 'POST'
            });
        }, true);

        // 2. Intercept form submissions with data-confirm or legacy onsubmit="return confirm(...)"
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.dataset.confirmed === 'true') return;

            const submitBtn = e.submitter || form.querySelector('button[type="submit"]:focus') || form.querySelector('button[type="submit"]');
            const dataConfirm = form.dataset.confirm || submitBtn?.dataset?.confirm;
            const onsubmitAttr = form.getAttribute('onsubmit') || '';

            if (dataConfirm || onsubmitAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopPropagation();

                let message = dataConfirm;
                if (!message && onsubmitAttr.includes('confirm(')) {
                    const match = onsubmitAttr.match(/confirm\s*\(\s*['"]([^'"]+)['"]\s*\)/);
                    message = match ? match[1] : 'Are you sure you want to proceed?';
                }

                const variant = form.dataset.confirmVariant || submitBtn?.dataset?.confirmVariant || 'danger';
                const defaultTitle = variant === 'danger' ? 'Confirm Deletion' : (variant === 'success' ? 'Confirm Approval' : 'Confirm Action');
                const title = form.dataset.confirmTitle || submitBtn?.dataset?.confirmTitle || defaultTitle;
                const itemName = form.dataset.confirmItem || submitBtn?.dataset?.confirmItem || '';
                const defaultBtn = variant === 'danger' ? 'Delete' : (variant === 'success' ? 'Approve' : 'Confirm');
                const confirmText = form.dataset.confirmBtn || submitBtn?.dataset?.confirmBtn || defaultBtn;
                const icon = form.dataset.confirmIcon || submitBtn?.dataset?.confirmIcon || '';

                window.confirmDialog({
                    title: title,
                    itemName: itemName,
                    message: message,
                    confirmText: confirmText,
                    confirmVariant: variant,
                    icon: icon,
                    targetForm: form
                });
            }
        }, true);
    }
</script>

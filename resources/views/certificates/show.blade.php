<x-layouts.public :title="'Official Certificate: ' . $certificate->course->title . ' · LearnBuddy'">
    <!-- PRINT STYLES -->
    <style>
        @media print {
            header, footer, nav, .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .certificate-container {
                border: 4px double #18181b !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                width: 100% !important;
                max-width: 100% !important;
                page-break-inside: avoid;
                padding: 2.5rem !important;
            }
        }
    </style>

    <!-- TOP TOOLBAR (NO-PRINT) -->
    <section class="no-print bg-neutral-900 border-b border-neutral-800 py-4 text-white">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('learning.index', $certificate->course) }}"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-neutral-300 hover:text-white transition py-1.5 px-3 rounded-lg bg-neutral-800 hover:bg-neutral-700"
                >
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    <span>Back to Course</span>
                </a>
                <span class="text-xs text-neutral-400 hidden sm:inline">&bull;</span>
                <span class="text-xs font-mono text-neutral-300 hidden sm:inline">
                    Credential ID: <strong class="text-amber-400">{{ $certificate->certificate_code }}</strong>
                </span>
            </div>

            <div class="flex items-center gap-2" x-data="{ copied: false }">
                <!-- Copy Verification Link -->
                <button
                    type="button"
                    @click="navigator.clipboard.writeText('{{ $certificate->verificationUrl() }}'); copied = true; setTimeout(() => copied = false, 2500)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neutral-800 hover:bg-neutral-700 text-xs font-semibold text-neutral-200 transition"
                    title="Copy Public Verification Link"
                >
                    <i :class="copied ? 'fas fa-check text-emerald-400' : 'fas fa-link text-neutral-400'"></i>
                    <span x-text="copied ? 'Link Copied!' : 'Copy Verify Link'"></span>
                </button>

                <!-- Public Verify Page Link -->
                <a
                    href="{{ $certificate->verificationUrl() }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neutral-800 hover:bg-neutral-700 text-xs font-semibold text-neutral-200 transition"
                    title="Open public verification page"
                >
                    <i class="fas fa-external-link-alt text-neutral-400 text-[10px]"></i>
                    <span class="hidden sm:inline">Public Verification</span>
                </a>

                <!-- Print / Save as PDF Button -->
                <button
                    type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-lg bg-lb-orange hover:bg-orange-600 text-white text-xs font-bold transition shadow-sm"
                >
                    <i class="fas fa-print"></i>
                    <span>Print / Save PDF</span>
                </button>
            </div>
        </div>
    </section>

    <!-- REVOCATION NOTICE BANNER (IF REVOKED) -->
    @if ($certificate->isRevoked())
        <div class="no-print bg-red-600 text-white py-4">
            <div class="max-w-[1100px] mx-auto px-4 sm:px-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                    <div>
                        <h3 class="font-bold text-sm">Certificate Officially Revoked</h3>
                        <p class="text-xs text-red-100">
                            Reason: {{ $certificate->revoked_reason }} &bull; Revoked on {{ $certificate->revoked_at?->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
                <span class="text-xs uppercase tracking-wider font-extrabold px-3 py-1 bg-red-800 rounded-md">
                    Invalid Credential
                </span>
            </div>
        </div>
    @endif

    <!-- MAIN CERTIFICATE CANVAS SECTION -->
    <section class="py-8 sm:py-16 bg-neutral-100 min-h-[800px] flex items-center justify-center">
        <div class="max-w-[1050px] w-full mx-auto px-4 sm:px-6">

            <!-- CERTIFICATE CARD -->
            <div class="certificate-container relative bg-white rounded-3xl sm:rounded-[36px] shadow-2xl border-8 border-neutral-900 p-6 sm:p-14 overflow-hidden text-neutral-800">

                <!-- DECORATIVE CORNER ACCENTS -->
                <div class="absolute top-3 left-3 w-8 h-8 border-t-2 border-l-2 border-amber-500 pointer-events-none"></div>
                <div class="absolute top-3 right-3 w-8 h-8 border-t-2 border-r-2 border-amber-500 pointer-events-none"></div>
                <div class="absolute bottom-3 left-3 w-8 h-8 border-b-2 border-l-2 border-amber-500 pointer-events-none"></div>
                <div class="absolute bottom-3 right-3 w-8 h-8 border-b-2 border-r-2 border-amber-500 pointer-events-none"></div>

                <!-- SUBTLE INNER BORDER -->
                <div class="absolute inset-4 sm:inset-6 border border-neutral-300 pointer-events-none rounded-2xl"></div>

                <!-- WATERMARK BACKGROUND -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03] select-none">
                    <i class="fas fa-graduation-cap text-[320px] text-neutral-900"></i>
                </div>

                <!-- REVOKED WATERMARK OVERLAY -->
                @if ($certificate->isRevoked())
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
                        <span class="text-red-500/25 font-black text-6xl sm:text-9xl uppercase tracking-widest -rotate-45 select-none border-8 border-red-500/25 px-8 py-4 rounded-3xl">
                            REVOKED
                        </span>
                    </div>
                @endif

                <div class="relative z-10 space-y-8 sm:space-y-10 text-center">

                    <!-- 1. INSTITUTION LOGO & HEADER -->
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-lb-orange text-white flex items-center justify-center text-2xl shadow-md">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="text-left">
                                <h4 class="font-extrabold text-xl tracking-tight text-neutral-900 leading-none">
                                    Learn<span class="text-lb-orange">Buddy</span>
                                </h4>
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">
                                    Sri Lanka Academic Directorate
                                </span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <span class="text-[11px] sm:text-xs font-bold uppercase tracking-[0.25em] text-neutral-500 block">
                                Verified Academic Credential
                            </span>
                            <h2 class="text-2xl sm:text-4xl font-serif font-black tracking-wider text-neutral-900 uppercase mt-1">
                                Certificate of Completion
                            </h2>
                        </div>
                    </div>

                    <!-- 2. RECIPIENT SECTION -->
                    <div class="space-y-3 max-w-2xl mx-auto">
                        <p class="text-xs sm:text-sm text-neutral-500 italic font-serif">
                            This is officially awarded and accredited to
                        </p>

                        <div class="border-b-2 border-neutral-300 pb-2">
                            <h1 class="text-3xl sm:text-5xl font-serif font-extrabold text-neutral-900 tracking-wide uppercase">
                                {{ $certificate->user->name }}
                            </h1>
                        </div>

                        <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed pt-2">
                            for successfully fulfilling all curriculum requirements, continuous coursework, hands-on assignments, and summative assessments in the approved professional programme:
                        </p>
                    </div>

                    <!-- 3. COURSE TITLE -->
                    <div class="py-2 max-w-2xl mx-auto">
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-lb-orange tracking-tight">
                            {{ $certificate->course->title }}
                        </h3>
                        @if ($certificate->course->category)
                            <span class="inline-block mt-2 px-3 py-1 bg-neutral-100 rounded-full text-xs font-semibold text-neutral-600">
                                {{ $certificate->course->category->name }}
                            </span>
                        @endif
                    </div>

                    <!-- 4. ACADEMIC PERFORMANCE (IF APPLICABLE) -->
                    <div class="flex items-center justify-center gap-6 text-xs text-neutral-600">
                        @if ($certificate->final_grade)
                            <div>
                                <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Overall Grade</span>
                                <span class="font-mono font-extrabold text-neutral-900 text-sm">
                                    {{ $certificate->formattedGrade() }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Date of Issue</span>
                            <span class="font-semibold text-neutral-900 text-sm">
                                {{ $certificate->issue_date->format('F d, Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-neutral-400 block text-[10px] uppercase font-bold tracking-wider">Delivery Mode</span>
                            <span class="font-semibold text-neutral-900 text-sm capitalize">
                                {{ $certificate->enrollment?->deliveryOption?->delivery_mode ?? 'Online / Hybrid' }}
                            </span>
                        </div>
                    </div>

                    <!-- 5. SIGNATURES & VERIFICATION SEAL -->
                    <div class="pt-8 sm:pt-12 grid grid-cols-1 sm:grid-cols-3 items-end gap-6 border-t border-neutral-200">
                        <!-- Instructor Signature -->
                        <div class="text-center sm:text-left space-y-1">
                            <div class="font-serif italic text-lg sm:text-xl text-neutral-800 font-bold tracking-wider">
                                {{ $certificate->course->instructors->first()?->name ?? 'Lead Instructor' }}
                            </div>
                            <div class="w-40 border-t border-neutral-400 mx-auto sm:mx-0"></div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-neutral-500 block">
                                Lead Course Instructor
                            </span>
                        </div>

                        <!-- Central Verified Seal -->
                        <div class="flex justify-center">
                            <div class="relative w-24 h-24 rounded-full border-4 border-amber-500 bg-amber-50/60 flex flex-col items-center justify-center text-amber-700 shadow-inner">
                                <i class="fas fa-certificate text-3xl"></i>
                                <span class="text-[7px] font-black uppercase tracking-widest mt-1 text-center leading-tight">
                                    LearnBuddy<br>Verified
                                </span>
                            </div>
                        </div>

                        <!-- Academic Directorate Signature -->
                        <div class="text-center sm:text-right space-y-1">
                            <div class="font-serif italic text-lg sm:text-xl text-neutral-800 font-bold tracking-wider">
                                Academic Council
                            </div>
                            <div class="w-40 border-t border-neutral-400 mx-auto sm:ml-auto sm:mr-0"></div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-neutral-500 block">
                                Directorate of Academics
                            </span>
                        </div>
                    </div>

                    <!-- 6. FOOTER SECURITY & VERIFICATION CREDENTIALS -->
                    <div class="pt-6 border-t border-neutral-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-neutral-500">
                        <div class="text-left font-mono space-y-0.5">
                            <div>Credential ID: <strong class="text-neutral-800">{{ $certificate->certificate_code }}</strong></div>
                            <div class="text-[10px] text-neutral-400">Security Hash: {{ substr($certificate->verification_hash, 0, 20) }}...</div>
                        </div>

                        <div class="text-right">
                            <span class="block">Verify authenticity publicly at:</span>
                            <a
                                href="{{ $certificate->verificationUrl() }}"
                                class="text-lb-orange hover:underline font-mono text-[10px]"
                            >
                                {{ $certificate->verificationUrl() }}
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</x-layouts.public>

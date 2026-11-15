<x-layouts.public :title="'Verify Certificate Credential · LearnBuddy'">
    <!-- HEADER -->
    <section class="bg-lb-peach pt-8 sm:pt-14 pb-10 sm:pb-16 rounded-b-[32px] sm:rounded-b-[48px] border-b border-orange-100">
        <div class="max-w-[850px] mx-auto px-4 sm:px-6 text-center space-y-4">
            <x-badge variant="orange" icon="fas fa-shield-alt">
                Official Credential Verification Register
            </x-badge>

            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-lb-black">
                Verify <span class="text-lb-orange">Certificate Authenticity</span>
            </h1>

            <p class="text-xs sm:text-sm text-neutral-600 max-w-xl mx-auto">
                Employers, academic partners, and organizations can instantly verify authentic LearnBuddy academic credentials using the unique certificate code or cryptographic security hash.
            </p>

            <!-- SEARCH FORM -->
            <form action="{{ route('certificates.search') }}" method="GET" class="pt-4 max-w-lg mx-auto">
                <div class="relative flex items-center bg-white p-1.5 sm:p-2 rounded-2xl shadow-lb-sm border border-neutral-200/90 focus-within:border-lb-orange focus-within:ring-2 focus-within:ring-orange-200/60 transition-all duration-200">
                    <div class="relative flex-1 min-w-0">
                        <div class="absolute inset-y-0 left-0 pl-3.5 sm:pl-4 flex items-center pointer-events-none text-neutral-400">
                            <i class="fas fa-search text-xs sm:text-sm" aria-hidden="true"></i>
                        </div>
                        <input
                            type="text"
                            name="code"
                            value="{{ $lookupQuery ?? '' }}"
                            placeholder="e.g. LB-CERT-2026-XXXXXXXX"
                            required
                            class="w-full bg-transparent border-0 text-xs sm:text-sm font-mono text-neutral-800 placeholder-neutral-400 py-2 sm:py-2.5 pl-10 sm:pl-11 pr-8 focus:outline-none focus:ring-0 focus-visible:outline-none !border-0 !outline-none !ring-0"
                            style="border: none !important; outline: none !important; box-shadow: none !important;"
                        >
                        @if (!empty($lookupQuery))
                            <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
                                <a href="{{ route('certificates.search') }}" class="text-neutral-400 hover:text-neutral-700 text-xs p-1 rounded-full transition" title="Clear search">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <button
                        type="submit"
                        class="px-5 sm:px-6 py-2.5 bg-lb-orange hover:bg-lb-orange-hover active:bg-orange-700 text-white text-xs sm:text-sm font-bold rounded-xl transition duration-150 shrink-0 cursor-pointer shadow-xs flex items-center gap-2"
                    >
                        <i class="fas fa-shield-alt text-xs hidden sm:inline" aria-hidden="true"></i>
                        <span>Verify Now</span>
                    </button>
                </div>
                <p class="text-[11px] text-neutral-500 mt-2">
                    Enter the Certificate Code printed on the lower left of any official LearnBuddy certificate.
                </p>
            </form>
        </div>
    </section>

    <!-- MAIN VERIFICATION RESULT SECTION -->
    <section class="py-10 sm:py-16 bg-lb-off-white min-h-[500px]">
        <div class="max-w-[850px] mx-auto px-4 sm:px-6">

            @if ($certificate)
                <!-- RESULT FOUND -->
                @if ($certificate->isValid())
                    <!-- VALID STATUS CARD -->
                    <div class="bg-white rounded-3xl border-2 border-emerald-500/30 shadow-lg p-6 sm:p-10 space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-neutral-100 pb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl shrink-0 shadow-md">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                        Valid &amp; Authentic
                                    </span>
                                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-900 mt-1">
                                        Official Certificate Verified
                                    </h2>
                                    <p class="text-xs text-neutral-500">
                                        LearnBuddy Academy Sri Lanka confirms this credential is genuine and in good standing.
                                    </p>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs self-start sm:self-auto">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active Record
                            </span>
                        </div>

                        <!-- VERIFIED METADATA TABLE (PUBLIC ONLY, STRICT PRIVACY) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Recipient Name</span>
                                <p class="text-sm font-bold text-neutral-900">{{ $certificate->user->name }}</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Credential ID</span>
                                <p class="text-sm font-mono font-bold text-amber-600">{{ $certificate->certificate_code }}</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 space-y-1 sm:col-span-2">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Certified Course</span>
                                <p class="text-sm font-bold text-neutral-900">{{ $certificate->course->title }}</p>
                                @if ($certificate->course->category)
                                    <span class="text-[11px] text-neutral-500 block">{{ $certificate->course->category->name }}</span>
                                @endif
                            </div>

                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Date of Issuance</span>
                                <p class="text-sm font-semibold text-neutral-800">{{ $certificate->issue_date->format('F d, Y') }}</p>
                                <span class="text-[10px] text-neutral-400">Asia/Colombo Timezone</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 space-y-1">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Academic Result</span>
                                <p class="text-sm font-semibold text-neutral-800">
                                    {{ $certificate->final_grade ? $certificate->formattedGrade() : 'Successfully Completed' }}
                                </p>
                                <span class="text-[10px] text-neutral-400">100% Coursework &amp; Assessments Met</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-neutral-50 border border-neutral-100 space-y-1 sm:col-span-2">
                                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider">Issuing Authority</span>
                                <p class="text-xs font-semibold text-neutral-800">LearnBuddy Academy (Pvt) Ltd, Colombo, Sri Lanka</p>
                                <p class="text-[10px] font-mono text-neutral-400 truncate">Verification Hash: {{ $certificate->verification_hash }}</p>
                            </div>
                        </div>

                        <!-- PRIVACY NOTICE -->
                        <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100 flex items-start gap-3 text-xs text-blue-900">
                            <i class="fas fa-lock text-blue-500 mt-0.5 shrink-0"></i>
                            <div>
                                <strong class="font-bold">Student Privacy Protection:</strong>
                                In compliance with data privacy policies, private student contact details (email addresses, phone numbers, home addresses, and financial records) are strictly confidential and are never disclosed on public verification registries.
                            </div>
                        </div>
                    </div>
                @else
                    <!-- REVOKED STATUS CARD -->
                    <div class="bg-white rounded-3xl border-2 border-red-500/40 shadow-lg p-6 sm:p-10 space-y-6">
                        <div class="flex items-center gap-4 border-b border-red-100 pb-6">
                            <div class="w-14 h-14 rounded-2xl bg-red-600 text-white flex items-center justify-center text-2xl shrink-0 shadow-md">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-red-800 bg-red-100 px-2.5 py-0.5 rounded-full">
                                    Revoked Credential
                                </span>
                                <h2 class="text-xl sm:text-2xl font-bold text-red-900 mt-1">
                                    Certificate Officially Revoked
                                </h2>
                                <p class="text-xs text-neutral-500">
                                    This certificate is invalid and is no longer recognized by LearnBuddy Academy.
                                </p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs text-red-900 space-y-2">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-red-700 block">Revocation Date:</span>
                                <span class="font-semibold">{{ $certificate->revoked_at?->format('d M Y, h:i A') ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-red-700 block">Official Reason:</span>
                                <p class="font-medium text-red-800">{{ $certificate->revoked_reason ?? 'Academic record revision or administrative cancellation.' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-red-700 block">Credential ID:</span>
                                <span class="font-mono font-bold">{{ $certificate->certificate_code }}</span>
                            </div>
                        </div>
                    </div>
                @endif

            @elseif (! empty($lookupQuery))
                <!-- NO RECORD FOUND -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-8 sm:p-12 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-neutral-100 text-neutral-400 flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-lg sm:text-xl font-bold text-neutral-900">No Credential Found</h2>
                        <p class="text-xs text-neutral-500 max-w-md mx-auto">
                            No certificate was found matching the identifier <strong class="font-mono text-neutral-800">"{{ $lookupQuery }}"</strong>.
                        </p>
                    </div>
                    <p class="text-xs text-neutral-400 max-w-sm mx-auto">
                        Please double check the spelling of the code (e.g. <span class="font-mono text-neutral-600">LB-CERT-2026-XXXXXXXX</span>) or contact LearnBuddy support if you believe this is an error.
                    </p>
                </div>

            @else
                <!-- DEFAULT SEARCH INSTRUCTIONS -->
                <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-lb-sm p-8 sm:p-12 text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-orange-50 text-lb-orange flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Enter a Credential Identifier</h2>
                        <p class="text-xs text-neutral-500 max-w-md mx-auto">
                            Please provide a certificate code above to verify recipient details and credential validity in real-time.
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </section>
</x-layouts.public>

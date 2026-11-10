<x-layouts.public title="LearnBuddy · Design System & Component Library">
    <div class="bg-lb-off-white py-12 sm:py-16 border-b border-neutral-200/60">
        <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
            <x-badge variant="orange" icon="fas fa-layer-group" class="mb-4">
                Component Library
            </x-badge>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-lb-black">
                Design System &amp; <span class="text-lb-orange">Tokens</span>
            </h1>
            <p class="mt-3 text-sm sm:text-base text-neutral-600 max-w-2xl leading-relaxed">
                The authoritative UI design tokens, accessible components, and responsive building blocks for LearnBuddy LMS, adhering to the approved reference design and palette.
            </p>
        </div>
    </div>

    <div class="max-w-[1140px] mx-auto px-4 sm:px-6 py-12 sm:py-16 space-y-16 sm:space-y-20">
        <!-- 1. Brand Palette Tokens -->
        <section aria-labelledby="tokens-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3 flex items-center justify-between">
                <div>
                    <h2 id="tokens-title" class="text-xl sm:text-2xl font-bold text-lb-black">1. Brand Palette Tokens</h2>
                    <p class="text-xs sm:text-sm text-neutral-500">Official CSS variables defined in <code>resources/css/app.css</code></p>
                </div>
                <x-badge variant="white">WCAG AAA / AA</x-badge>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <!-- Orange Primary -->
                <div class="p-3.5 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-2">
                    <div class="h-16 rounded-xl bg-[#FF5F00] shadow-inner"></div>
                    <div>
                        <span class="text-xs font-bold block text-neutral-900">Primary Orange</span>
                        <code class="text-[11px] text-neutral-500 block">#FF5F00</code>
                        <span class="text-[10px] text-neutral-400">--orange</span>
                    </div>
                </div>

                <!-- Orange Light -->
                <div class="p-3.5 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-2">
                    <div class="h-16 rounded-xl bg-[#FF8A3D] shadow-inner"></div>
                    <div>
                        <span class="text-xs font-bold block text-neutral-900">Orange Light</span>
                        <code class="text-[11px] text-neutral-500 block">#FF8A3D</code>
                        <span class="text-[10px] text-neutral-400">--orange-light</span>
                    </div>
                </div>

                <!-- Orange Dark -->
                <div class="p-3.5 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-2">
                    <div class="h-16 rounded-xl bg-[#D94E00] shadow-inner"></div>
                    <div>
                        <span class="text-xs font-bold block text-neutral-900">Orange Dark</span>
                        <code class="text-[11px] text-neutral-500 block">#D94E00</code>
                        <span class="text-[10px] text-neutral-400">--orange-dark</span>
                    </div>
                </div>

                <!-- Peach Background -->
                <div class="p-3.5 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-2">
                    <div class="h-16 rounded-xl bg-[#FFF0E6] border border-orange-200/60 shadow-inner"></div>
                    <div>
                        <span class="text-xs font-bold block text-neutral-900">Peach Hero/Footer</span>
                        <code class="text-[11px] text-neutral-500 block">#FFF0E6</code>
                        <span class="text-[10px] text-neutral-400">--peach-bg</span>
                    </div>
                </div>

                <!-- Black Neutral -->
                <div class="p-3.5 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-2">
                    <div class="h-16 rounded-xl bg-[#0A0A0A] shadow-inner"></div>
                    <div>
                        <span class="text-xs font-bold block text-neutral-900">Deep Black</span>
                        <code class="text-[11px] text-neutral-500 block">#0A0A0A</code>
                        <span class="text-[10px] text-neutral-400">--black</span>
                    </div>
                </div>

                <!-- Teal Accent -->
                <div class="p-3.5 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-2">
                    <div class="h-16 rounded-xl bg-[#14B8A6] shadow-inner"></div>
                    <div>
                        <span class="text-xs font-bold block text-neutral-900">Teal Accent</span>
                        <code class="text-[11px] text-neutral-500 block">#14B8A6</code>
                        <span class="text-[10px] text-neutral-400">--teal-card</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Reusable Buttons -->
        <section aria-labelledby="buttons-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3">
                <h2 id="buttons-title" class="text-xl sm:text-2xl font-bold text-lb-black">2. Reusable Button Components</h2>
                <p class="text-xs sm:text-sm text-neutral-500">Pill buttons with hover micro-animations and accessible focus indicators</p>
            </div>

            <div class="space-y-4">
                <div class="p-6 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Button Variants</span>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-button variant="primary" icon="fas fa-arrow-right">Primary Brand</x-button>
                        <x-button variant="dark" icon="fas fa-sign-in-alt">Dark Action</x-button>
                        <x-button variant="outline" icon="fas fa-book">Outline Button</x-button>
                        <x-button variant="teal" icon="fas fa-graduation-cap">Teal Special</x-button>
                        <x-button variant="ghost" icon="fas fa-info-circle">Ghost Text</x-button>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-neutral-400">Sizes &amp; States</span>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-button variant="primary" size="sm">Small (sm)</x-button>
                        <x-button variant="primary" size="md">Default (md)</x-button>
                        <x-button variant="primary" size="lg">Large (lg)</x-button>
                        <x-button variant="primary" disabled>Disabled State</x-button>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Badges & Status Tags -->
        <section aria-labelledby="badges-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3">
                <h2 id="badges-title" class="text-xl sm:text-2xl font-bold text-lb-black">3. Badges &amp; Status Tags</h2>
                <p class="text-xs sm:text-sm text-neutral-500">Pill tags used for delivery modes, difficulty levels, and section labels</p>
            </div>

            <div class="p-6 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm flex flex-wrap gap-2.5 items-center">
                <x-badge variant="orange" icon="fas fa-star">Featured Course</x-badge>
                <x-badge variant="white" icon="fas fa-laptop-code">Online Self-Paced</x-badge>
                <x-badge variant="teal" icon="fas fa-building">Physical Classroom</x-badge>
                <x-badge variant="dark" icon="fas fa-users">Hybrid Intake</x-badge>
                <x-badge variant="gray" icon="fas fa-clock">12 Weeks Duration</x-badge>
                <x-badge variant="white" icon="fas fa-map-marker-alt">Colombo Campus</x-badge>
            </div>
        </section>

        <!-- 4. Form Controls & Touch Targets -->
        <section aria-labelledby="forms-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3">
                <h2 id="forms-title" class="text-xl sm:text-2xl font-bold text-lb-black">4. Form Inputs &amp; Controls</h2>
                <p class="text-xs sm:text-sm text-neutral-500">Accessible form inputs with labels, icons, error boundaries, and touch ergonomics</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 sm:p-8 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm">
                <x-input
                    label="Full Name"
                    name="name_demo"
                    placeholder="e.g. Kasun Perera"
                    icon="fas fa-user"
                    required
                />

                <x-input
                    label="Email Address"
                    name="email_demo"
                    type="email"
                    placeholder="kasun@example.lk"
                    icon="fas fa-envelope"
                    required
                    hint="We will send enrollment confirmations here."
                />

                <x-input
                    label="Sri Lankan Mobile Number"
                    name="phone_demo"
                    placeholder="+94 77 123 4567"
                    icon="fas fa-phone"
                    hint="Supported: +94 7X or 07X formats"
                />

                <x-input
                    label="Validated Input (Error Demonstration)"
                    name="error_demo"
                    value="invalid-entry"
                    error="Please provide a valid Sri Lankan postal code."
                />
            </div>
        </section>

        <!-- 5. Cards & Elevation -->
        <section aria-labelledby="cards-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3">
                <h2 id="cards-title" class="text-xl sm:text-2xl font-bold text-lb-black">5. Cards &amp; Surfaces</h2>
                <p class="text-xs sm:text-sm text-neutral-500">Modular surfaces with radius-16px, soft shadows, and hover lift effects</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-card variant="white" class="space-y-4">
                    <x-badge variant="orange" icon="fas fa-layer-group">Online</x-badge>
                    <h3 class="text-lg font-bold">Standard White Surface</h3>
                    <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">
                        Default card surface utilized for course listings, instructor biographies, and curriculum overview.
                    </p>
                    <div class="pt-2 border-t border-neutral-100 flex items-center justify-between text-xs font-semibold">
                        <span>LKR 14,500</span>
                        <span class="text-lb-orange">Explore <i class="fas fa-chevron-right text-[10px]"></i></span>
                    </div>
                </x-card>

                <x-card variant="peach" class="space-y-4">
                    <x-badge variant="dark" icon="fas fa-map-marker-alt">Physical</x-badge>
                    <h3 class="text-lg font-bold">Peach Highlight Surface</h3>
                    <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed">
                        Tinted surface used for prominent highlights, Colombo classroom schedules, and batch announcements.
                    </p>
                    <div class="pt-2 border-t border-orange-200/50 flex items-center justify-between text-xs font-semibold">
                        <span>Weekend Intake</span>
                        <span class="text-lb-black">Details <i class="fas fa-chevron-right text-[10px]"></i></span>
                    </div>
                </x-card>

                <x-card variant="teal" class="space-y-4">
                    <x-badge variant="white" icon="fas fa-graduation-cap">Join Academy</x-badge>
                    <h3 class="text-lg font-bold text-white">Teal Action Card</h3>
                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed">
                        High-contrast action box modeled directly from the reference UI "Do You Want Learn Here" card.
                    </p>
                    <div class="pt-2 flex items-center justify-between text-xs font-semibold text-white">
                        <x-button variant="dark" size="sm">Get Started</x-button>
                    </div>
                </x-card>
            </div>
        </section>

        <!-- 6. Alerts & Notifications -->
        <section aria-labelledby="alerts-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3">
                <h2 id="alerts-title" class="text-xl sm:text-2xl font-bold text-lb-black">6. Feedback &amp; Alerts</h2>
                <p class="text-xs sm:text-sm text-neutral-500">Accessible notices for operation results, batch notifications, and warnings</p>
            </div>

            <div class="space-y-3.5 max-w-2xl">
                <x-alert type="info" title="System Notice" dismissible>
                    LearnBuddy operates on Sri Lanka Standard Time (Asia/Colombo). All class schedules reflect local Sri Lankan time.
                </x-alert>

                <x-alert type="success" title="Payment Verified" dismissible>
                    Your enrollment for the Colombo Weekend Batch has been successfully confirmed. A receipt was issued.
                </x-alert>

                <x-alert type="warning" title="Seat Capacity Alert" dismissible>
                    Only 3 classroom seats remaining for the upcoming physical intake at Colombo Central Campus.
                </x-alert>

                <x-alert type="error" title="Submission Error" dismissible>
                    The uploaded file exceeds the 20MB limit. Permitted extensions: PDF, ZIP, DOCX.
                </x-alert>
            </div>
        </section>

        <!-- 7. Interactive Modals -->
        <section aria-labelledby="modals-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3">
                <h2 id="modals-title" class="text-xl sm:text-2xl font-bold text-lb-black">7. Alpine.js Interactive Modals</h2>
                <p class="text-xs sm:text-sm text-neutral-500">Accessible dialogs with focus trapping, backdrop blur, and keyboard escape support</p>
            </div>

            <div class="p-6 bg-white rounded-2xl border border-neutral-200/70 shadow-lb-sm space-y-4 max-w-xl">
                <p class="text-xs sm:text-sm text-neutral-600">
                    Test the accessible modal component. Click below or press the header search icon to test modal opening, focus trap, and Escape key handling.
                </p>

                <div class="flex flex-wrap gap-3">
                    <x-button
                        variant="primary"
                        icon="fas fa-window-restore"
                        @click="$dispatch('open-modal', 'demo-modal')"
                    >
                        Open Demonstration Modal
                    </x-button>

                    <x-button
                        variant="outline"
                        icon="fas fa-search"
                        @click="$dispatch('open-modal', 'search-modal')"
                    >
                        Open Search Modal
                    </x-button>
                </div>
            </div>

            <!-- Demo Modal Instance -->
            <x-modal name="demo-modal" title="Enrollment Information Modal" maxWidth="md">
                <div class="space-y-3 text-sm text-neutral-600 leading-relaxed">
                    <p>
                        This is an accessible modal component powered by Alpine.js. It features:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-neutral-700">
                        <li>Backdrop blur and smooth zoom animations</li>
                        <li>Accessible <code>aria-modal="true"</code> attributes</li>
                        <li>Esc-key trigger to dismiss cleanly</li>
                        <li>Responsive width scaling for mobile screens</li>
                    </ul>
                </div>

                <x-slot:footer>
                    <x-button variant="outline" size="sm" @click="$dispatch('close-modal', 'demo-modal')">
                        Cancel
                    </x-button>
                    <x-button variant="primary" size="sm" @click="$dispatch('close-modal', 'demo-modal')">
                        Understood
                    </x-button>
                </x-slot:footer>
            </x-modal>
        </section>

        <!-- 8. Responsive Viewport Matrix Checklist -->
        <section aria-labelledby="responsive-title" class="space-y-6">
            <div class="border-b border-neutral-200 pb-3">
                <h2 id="responsive-title" class="text-xl sm:text-2xl font-bold text-lb-black">8. Responsive Viewport Matrix Verification</h2>
                <p class="text-xs sm:text-sm text-neutral-500">Mandatory verification viewports from PROMPT 0 &amp; PROMPT 4</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div class="p-4 bg-white rounded-xl border border-neutral-200">
                    <span class="font-bold text-neutral-900 block mb-1">Mobile Small</span>
                    <code class="text-lb-orange font-semibold">360 × 800</code>
                    <p class="text-neutral-500 mt-1">Single-col collapse, hamburger drawer, zero horizontal overflow.</p>
                </div>
                <div class="p-4 bg-white rounded-xl border border-neutral-200">
                    <span class="font-bold text-neutral-900 block mb-1">Mobile Standard</span>
                    <code class="text-lb-orange font-semibold">390 × 844 &amp; 430 × 932</code>
                    <p class="text-neutral-500 mt-1">iPhone 13/14 &amp; Pro Max touch-friendly 44px tap targets.</p>
                </div>
                <div class="p-4 bg-white rounded-xl border border-neutral-200">
                    <span class="font-bold text-neutral-900 block mb-1">Tablet Portrait &amp; Landscape</span>
                    <code class="text-lb-orange font-semibold">768 × 1024 &amp; 1024 × 768</code>
                    <p class="text-neutral-500 mt-1">2-col grid adaptation, responsive search bar.</p>
                </div>
                <div class="p-4 bg-white rounded-xl border border-neutral-200">
                    <span class="font-bold text-neutral-900 block mb-1">Laptop &amp; Desktop</span>
                    <code class="text-lb-orange font-semibold">1366 × 768 &amp; 1920 × 1080</code>
                    <p class="text-neutral-500 mt-1">Centered 1140px container, floating pill header navigation.</p>
                </div>
            </div>
        </section>
    </div>
</x-layouts.public>

<footer class="bg-lb-peach pt-16 sm:pt-20 pb-8 border-t border-orange-100/60 text-lb-black">
    <div class="max-w-[1140px] mx-auto px-4 sm:px-6">
        <!-- 4-Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-12 sm:pb-16 border-b border-orange-200/50">
            <!-- Col 1: Brand & Social (lg: 4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <a href="{{ url('/') }}" class="inline-block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lb-orange rounded-lg">
                    @if (file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="LearnBuddy" class="h-8 w-auto">
                    @else
                        <span class="text-2xl font-extrabold tracking-tight text-lb-black">Learn<span class="text-lb-orange">Buddy</span>.</span>
                    @endif
                </a>
                <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed max-w-sm">
                    Sri Lanka's premier hybrid learning platform. Empowering students, professionals, and educators nationwide with verified online and physical classroom experiences.
                </p>

                <!-- Social Icons -->
                <div class="flex items-center gap-2.5 pt-2">
                    <a
                        href="https://facebook.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-neutral-800 hover:bg-lb-orange hover:text-white transition shadow-lb-sm"
                        aria-label="LearnBuddy on Facebook"
                    >
                        <i class="fab fa-facebook-f text-xs" aria-hidden="true"></i>
                    </a>
                    <a
                        href="https://twitter.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-neutral-800 hover:bg-lb-orange hover:text-white transition shadow-lb-sm"
                        aria-label="LearnBuddy on X / Twitter"
                    >
                        <i class="fab fa-twitter text-xs" aria-hidden="true"></i>
                    </a>
                    <a
                        href="https://instagram.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-neutral-800 hover:bg-lb-orange hover:text-white transition shadow-lb-sm"
                        aria-label="LearnBuddy on Instagram"
                    >
                        <i class="fab fa-instagram text-xs" aria-hidden="true"></i>
                    </a>
                    <a
                        href="https://linkedin.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-neutral-800 hover:bg-lb-orange hover:text-white transition shadow-lb-sm"
                        aria-label="LearnBuddy on LinkedIn"
                    >
                        <i class="fab fa-linkedin-in text-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Col 2: Navigation Links (lg: 2 cols) -->
            <div class="lg:col-span-2 space-y-3.5">
                <h4 class="text-sm font-bold tracking-tight text-neutral-900 uppercase">Explore</h4>
                <ul class="space-y-2 text-xs sm:text-sm text-neutral-600">
                    <li><a href="{{ url('/') }}" class="hover:text-lb-orange transition-colors">Home</a></li>
                    <li><a href="{{ url('/#courses') }}" class="hover:text-lb-orange transition-colors">All Courses</a></li>
                    <li><a href="{{ url('/#about') }}" class="hover:text-lb-orange transition-colors">About Us</a></li>
                    <li><a href="{{ url('/#faq') }}" class="hover:text-lb-orange transition-colors">FAQ</a></li>
                    <li><a href="{{ url('/design-system') }}" class="hover:text-lb-orange transition-colors">Design System</a></li>
                </ul>
            </div>

            <!-- Col 3: Categories (lg: 3 cols) -->
            <div class="lg:col-span-3 space-y-3.5">
                <h4 class="text-sm font-bold tracking-tight text-neutral-900 uppercase">Top Categories</h4>
                <ul class="space-y-2 text-xs sm:text-sm text-neutral-600">
                    <li><a href="{{ url('/#courses') }}" class="hover:text-lb-orange transition-colors">Full-Stack Development</a></li>
                    <li><a href="{{ url('/#courses') }}" class="hover:text-lb-orange transition-colors">UI/UX Design Systems</a></li>
                    <li><a href="{{ url('/#courses') }}" class="hover:text-lb-orange transition-colors">Mobile App Engineering</a></li>
                    <li><a href="{{ url('/#courses') }}" class="hover:text-lb-orange transition-colors">Cloud & DevOps Essentials</a></li>
                    <li><a href="{{ url('/#courses') }}" class="hover:text-lb-orange transition-colors">Leadership & Management</a></li>
                </ul>
            </div>

            <!-- Col 4: Sri Lanka Contact & App (lg: 3 cols) -->
            <div class="lg:col-span-3 space-y-3.5">
                <h4 class="text-sm font-bold tracking-tight text-neutral-900 uppercase">Sri Lanka Office</h4>
                <div class="text-xs sm:text-sm text-neutral-600 space-y-1.5">
                    <p class="flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-lb-orange shrink-0" aria-hidden="true"></i>
                        <span>Colombo Central Campus, Sri Lanka</span>
                    </p>
                    <p class="flex items-center gap-2 font-medium">
                        <i class="fas fa-phone text-lb-orange shrink-0" aria-hidden="true"></i>
                        <span>+94 11 234 5678</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i class="fas fa-envelope text-lb-orange shrink-0" aria-hidden="true"></i>
                        <span>hello@learnbuddy.lk</span>
                    </p>
                </div>

                <!-- App badges placeholder -->
                <div class="pt-2">
                    <p class="text-xs text-neutral-500 mb-2">Mobile Learning Apps:</p>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neutral-900 text-white text-[11px] font-medium opacity-90 cursor-default">
                            <i class="fab fa-google-play text-sm" aria-hidden="true"></i>
                            <span>Google Play</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neutral-900 text-white text-[11px] font-medium opacity-90 cursor-default">
                            <i class="fab fa-apple text-sm" aria-hidden="true"></i>
                            <span>App Store</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-600">
            <!-- Payment Badges (Sri Lanka Focus) -->
            <div class="flex items-center gap-3 text-neutral-700">
                <span class="font-semibold text-[11px] uppercase tracking-wider text-neutral-500">Supported Payments:</span>
                <div class="flex items-center gap-2 text-base text-neutral-800">
                    <i class="fab fa-cc-visa" title="Visa" aria-hidden="true"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard" aria-hidden="true"></i>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-white border border-neutral-300">LKR</span>
                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-white border border-neutral-300">Bank Transfer</span>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center sm:text-right">
                <p>&copy; {{ date('Y') }} LearnBuddy. Sri Lanka. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

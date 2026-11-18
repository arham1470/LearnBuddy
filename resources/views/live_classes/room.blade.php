<x-layouts.public title="Live Room - {{ $liveClass->title }} - LearnBuddy" description="Interactive virtual classroom live meeting room.">
    <script>
        // Immediately flag body for full-viewport live room mode
        document.body.classList.add('live-room-active');
    </script>

    <style>
        /* Suppress site header, footer and skip links for dedicated full-viewport video conferencing */
        body.live-room-active > header,
        body.live-room-active header,
        body.live-room-active > footer,
        body.live-room-active footer,
        body.live-room-active > a[href="#main-content"] {
            display: none !important;
        }

        body.live-room-active {
            overflow: hidden !important;
            height: 100vh !important;
            height: 100dvh !important;
            background-color: #0A0A0A !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body.live-room-active main#main-content {
            height: 100vh !important;
            height: 100dvh !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }

        /* Classroom Viewport Container */
        #live-classroom-viewport {
            height: 100% !important;
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            background-color: #0A0A0A !important;
        }

        /* Video Stage & Jitsi iframe Styling */
        #classroom-stage {
            position: relative !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 0 !important;
            flex: 1 1 0% !important;
            display: flex !important;
            flex-direction: column !important;
            background-color: #000000 !important;
            overflow: hidden !important;
        }

        #jitsi-meet-container {
            position: relative !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 0 !important;
            flex: 1 1 0% !important;
            display: flex !important;
        }

        #jitsi-meet-container iframe {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 100% !important;
            border: 0 !important;
            border-radius: 0.875rem !important;
        }

        /* Dedicated Fullscreen Overrides */
        #classroom-stage:fullscreen,
        #classroom-stage:-webkit-full-screen,
        #classroom-stage:-moz-full-screen,
        #classroom-stage:-ms-fullscreen {
            width: 100vw !important;
            height: 100vh !important;
            max-width: 100vw !important;
            max-height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            background-color: #000000 !important;
        }

        #classroom-stage:fullscreen #jitsi-meet-container iframe,
        #classroom-stage:-webkit-full-screen #jitsi-meet-container iframe,
        #classroom-stage:-moz-full-screen #jitsi-meet-container iframe,
        #classroom-stage:-ms-fullscreen #jitsi-meet-container iframe {
            border-radius: 0 !important;
        }

        #classroom-stage:fullscreen #stage-exit-fullscreen-btn,
        #classroom-stage:-webkit-full-screen #stage-exit-fullscreen-btn {
            display: inline-flex !important;
        }
    </style>

    <section id="live-classroom-viewport" class="w-full h-full flex flex-col flex-1 min-h-0 bg-neutral-950 text-white">
        <!-- Top Navigation & Controls Bar -->
        <header id="classroom-top-bar" class="shrink-0 z-30 px-3 sm:px-6 py-2.5 bg-neutral-900/95 backdrop-blur border-b border-neutral-800/80 flex items-center justify-between gap-3 shadow-md">
            <!-- Left: Brand / Title / Meta -->
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('courses.live-classes.show', [$course->slug, $liveClass]) }}" class="shrink-0 flex items-center gap-2 group" title="Return to Session Overview">
                    <img src="{{ asset('images/only-logo.png') }}" alt="LearnBuddy" class="h-7 sm:h-8 w-auto transition-transform group-hover:scale-105">
                </a>

                <div class="h-5 w-px bg-neutral-800 hidden sm:block"></div>

                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-600 text-white animate-pulse shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                    LIVE
                </span>

                <div class="min-w-0">
                    <h1 class="text-xs sm:text-sm font-extrabold text-white truncate leading-tight">
                        {{ $liveClass->title }}
                    </h1>
                    <p class="text-[11px] text-neutral-400 truncate hidden sm:block">
                        {{ $course->title }} &bull; Host: {{ $liveClass->instructor->name }}
                    </p>
                </div>
            </div>

            <!-- Right: Actions & Controls -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <span class="text-[11px] px-2.5 py-1 rounded-full {{ $isModerator ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'bg-neutral-800 text-neutral-300 font-medium border border-neutral-700/60' }}">
                    {{ $isModerator ? 'Host / Moderator' : 'Student Participant' }}
                </span>

                @if ($meetingConfig['is_configured'])
                    <!-- Dedicated Fullscreen Toggle Button -->
                    <button
                        id="toggle-fullscreen-btn"
                        type="button"
                        onclick="toggleClassroomFullscreen()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-neutral-200 hover:text-white text-xs font-semibold border border-neutral-700/80 transition shadow-sm cursor-pointer"
                        title="Toggle Fullscreen Video (Keyboard shortcut: F)"
                    >
                        <i id="fullscreen-icon" class="fas fa-expand text-xs"></i>
                        <span id="fullscreen-text" class="hidden sm:inline">Full Screen</span>
                    </button>
                @endif

                @if ($isModerator)
                    <!-- Host End Live Session for All -->
                    <form id="end-live-form" method="POST" action="{{ route('courses.live-classes.end', [$course->slug, $liveClass]) }}" class="inline">
                        @csrf
                        <button
                            type="button"
                            data-item="{{ $liveClass->title }}"
                            onclick="confirmDialog({
                                title: 'End Live Session for Everyone?',
                                itemName: this.dataset.item,
                                message: 'Are you sure you want to end this live class? This will complete the session, disconnect all attendees, and close the room.',
                                targetForm: document.getElementById('end-live-form'),
                                confirmText: 'End Session for All',
                                confirmVariant: 'danger',
                                icon: 'fas fa-phone-slash'
                            })"
                            class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm cursor-pointer"
                            title="End live session for all participants"
                        >
                            <i class="fas fa-phone-slash text-xs"></i>
                            <span class="hidden sm:inline">End Session</span>
                            <span class="sm:hidden">End</span>
                        </button>
                    </form>

                    <!-- Host Leave Room Button (temporarily step out without ending) -->
                    <button
                        type="button"
                        data-item="{{ $liveClass->title }}"
                        data-action="{{ route('courses.live-classes.show', [$course->slug, $liveClass]) }}"
                        onclick="confirmDialog({
                            title: 'Leave Live Classroom?',
                            itemName: this.dataset.item,
                            message: 'Step out of the classroom? The live session will remain open for learners until you click End Session.',
                            action: this.dataset.action,
                            method: 'GET',
                            confirmText: 'Leave Room',
                            confirmVariant: 'warning',
                            icon: 'fas fa-sign-out-alt'
                        })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-neutral-300 hover:text-white border border-neutral-700/80 text-xs font-semibold transition shadow-sm cursor-pointer"
                        title="Step out without ending the meeting"
                    >
                        <i class="fas fa-sign-out-alt text-xs"></i>
                        <span class="hidden sm:inline">Leave</span>
                    </button>
                @else
                    <!-- Student Leave Room Button -->
                    <button
                        type="button"
                        data-item="{{ $liveClass->title }}"
                        data-action="{{ route('courses.live-classes.show', [$course->slug, $liveClass]) }}"
                        onclick="confirmDialog({
                            title: 'Leave Live Classroom?',
                            itemName: this.dataset.item,
                            message: 'Are you sure you want to exit this active live classroom session? You can re-enter at any time while the session is live.',
                            action: this.dataset.action,
                            method: 'GET',
                            confirmText: 'Leave Room',
                            confirmVariant: 'danger',
                            icon: 'fas fa-sign-out-alt'
                        })"
                        class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm cursor-pointer"
                    >
                        <i class="fas fa-sign-out-alt text-xs"></i>
                        <span class="hidden sm:inline">Leave Room</span>
                        <span class="sm:hidden">Leave</span>
                    </button>
                @endif
            </div>
        </header>

        <!-- Main Video Stage / Unconfigured Container -->
        @if (! $meetingConfig['is_configured'])
            <!-- Honest Unconfigured Provider State -->
            <div class="flex-1 w-full h-full min-h-0 overflow-y-auto flex items-center justify-center p-4">
                <div class="bg-neutral-900 rounded-3xl border border-neutral-800 p-6 sm:p-10 text-center space-y-6 shadow-2xl max-w-2xl w-full my-auto">
                    <div class="w-16 h-16 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center mx-auto text-2xl border border-amber-500/30">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white">
                            Live Meeting Provider Not Configured
                        </h2>
                        <p class="text-xs sm:text-sm text-neutral-300 leading-relaxed">
                            The video meeting provider (<strong>Jitsi Meet</strong>) has not been configured with production credentials in this environment.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-neutral-950/80 border border-neutral-800 text-left text-xs space-y-3 text-neutral-300">
                        <div class="font-bold text-amber-400 uppercase tracking-wider text-[10px]">
                            Deployment &amp; Hosting Evaluation Notes
                        </div>
                        <ul class="space-y-2 list-disc list-inside text-[11px] leading-relaxed">
                            <li>
                                <strong>8x8 JaaS (Jitsi as a Service):</strong> Official cloud hosted embedding. Free Developer tier allows up to 25 Monthly Active Users (MAUs). Commercial tiers start at $99/mo (300 MAUs) and $499/mo (1,500 MAUs). Requires <code class="text-neutral-200 bg-neutral-800 px-1 py-0.5 rounded">JITSI_DOMAIN=8x8.vc</code>, <code class="text-neutral-200 bg-neutral-800 px-1 py-0.5 rounded">JITSI_APP_ID</code>, <code class="text-neutral-200 bg-neutral-800 px-1 py-0.5 rounded">JITSI_API_KEY_ID</code>, and <code class="text-neutral-200 bg-neutral-800 px-1 py-0.5 rounded">JITSI_PRIVATE_KEY</code>.
                            </li>
                            <li>
                                <strong>Self-Hosted Jitsi Meet:</strong> Open-source deployment on dedicated cloud infrastructure (approx. $20&ndash;$60/mo server costs) configured with token authentication (<code class="text-neutral-200 bg-neutral-800 px-1 py-0.5 rounded">JITSI_DOMAIN</code> and <code class="text-neutral-200 bg-neutral-800 px-1 py-0.5 rounded">JITSI_SECRET</code>).
                            </li>
                            <li>
                                <strong>Public meet.jit.si:</strong> Disabled anonymous room creation in August 2023 and is not supported for commercial embedded applications.
                            </li>
                        </ul>
                    </div>

                    <div class="pt-2 flex items-center justify-center gap-3">
                        <a
                            href="{{ route('courses.live-classes.show', [$course->slug, $liveClass]) }}"
                            class="px-6 py-2.5 rounded-full bg-neutral-800 hover:bg-neutral-700 text-white text-xs font-semibold transition border border-neutral-700"
                        >
                            Return to Session Details
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Active Jitsi Meet WebRTC Video Player Container -->
            <div id="classroom-stage-wrapper" class="flex-1 w-full h-full min-h-0 relative p-2 sm:p-3 flex flex-col overflow-hidden bg-neutral-950">
                <div id="classroom-stage" class="relative w-full h-full flex-1 min-h-0 rounded-2xl overflow-hidden bg-black border border-neutral-800/80 shadow-2xl flex items-center justify-center">
                    <!-- Target Jitsi Container (maintaining ID selector) -->
                    <div id="jitsi-meet-container" class="w-full h-full min-h-0 relative"></div>

                    <!-- Floating Exit Fullscreen Button (Shown only in fullscreen mode) -->
                    <button
                        id="stage-exit-fullscreen-btn"
                        type="button"
                        onclick="toggleClassroomFullscreen()"
                        class="absolute top-4 right-4 z-50 hidden items-center gap-1.5 px-3 py-1.5 rounded-full bg-neutral-900/80 hover:bg-neutral-800 text-white backdrop-blur border border-white/20 transition shadow-xl cursor-pointer text-xs font-semibold"
                        title="Exit Full Screen (Esc / F)"
                    >
                        <i class="fas fa-compress text-xs"></i>
                        <span>Exit Full Screen</span>
                    </button>
                </div>
            </div>

            <!-- Load Jitsi Meet External API SDK -->
            <script src="https://{{ $meetingConfig['domain'] }}/external_api.js" onerror="handleJitsiScriptError()"></script>
            <script>
                function handleJitsiScriptError() {
                    const container = document.getElementById('jitsi-meet-container');
                    if (container) {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center p-8 text-center text-neutral-400 space-y-4 max-w-md mx-auto my-auto">
                                <div class="w-14 h-14 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center text-2xl border border-rose-500/30">
                                    <i class="fas fa-video-slash"></i>
                                </div>
                                <h3 class="text-white font-bold text-base">Unable to connect to video server</h3>
                                <p class="text-xs text-neutral-400 leading-relaxed">
                                    Could not reach the meeting service at <code class="text-neutral-300 bg-neutral-800 px-1 py-0.5 rounded">{{ $meetingConfig['domain'] }}</code>. Please check your network connection and server settings.
                                </p>
                                <button onclick="window.location.reload()" class="px-4 py-2 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-white text-xs font-semibold transition border border-neutral-700">
                                    <i class="fas fa-redo-alt mr-1.5"></i> Retry Connection
                                </button>
                            </div>
                        `;
                    }
                }

                function toggleClassroomFullscreen() {
                    const stage = document.getElementById('classroom-stage') || document.getElementById('live-classroom-viewport') || document.documentElement;
                    const isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);

                    if (!isFullscreen) {
                        if (stage.requestFullscreen) {
                            stage.requestFullscreen().catch(function(err) {
                                console.warn('Direct stage fullscreen failed, falling back to documentElement:', err);
                                if (document.documentElement.requestFullscreen) {
                                    document.documentElement.requestFullscreen().catch(function(e) {
                                        console.warn('Fullscreen failed:', e);
                                    });
                                }
                            });
                        } else if (stage.webkitRequestFullscreen) {
                            stage.webkitRequestFullscreen();
                        } else if (stage.mozRequestFullScreen) {
                            stage.mozRequestFullScreen();
                        } else if (stage.msRequestFullscreen) {
                            stage.msRequestFullscreen();
                        }
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen().catch(function() {});
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen();
                        } else if (document.mozCancelFullScreen) {
                            document.mozCancelFullScreen();
                        } else if (document.msExitFullscreen) {
                            document.msExitFullscreen();
                        }
                    }
                }

                function updateFullscreenUI() {
                    const isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
                    const icon = document.getElementById('fullscreen-icon');
                    const text = document.getElementById('fullscreen-text');

                    if (icon) {
                        icon.className = isFullscreen ? 'fas fa-compress text-xs' : 'fas fa-expand text-xs';
                    }
                    if (text) {
                        text.textContent = isFullscreen ? 'Exit Full Screen' : 'Full Screen';
                    }
                }

                document.addEventListener('fullscreenchange', updateFullscreenUI);
                document.addEventListener('webkitfullscreenchange', updateFullscreenUI);
                document.addEventListener('mozfullscreenchange', updateFullscreenUI);
                document.addEventListener('MSFullscreenChange', updateFullscreenUI);

                // 'F' key shortcut to toggle fullscreen when not typing in form controls
                document.addEventListener('keydown', function(e) {
                    if (['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
                    if (e.key === 'f' || e.key === 'F') {
                        if (!e.ctrlKey && !e.metaKey && !e.altKey) {
                            e.preventDefault();
                            toggleClassroomFullscreen();
                        }
                    }
                });

                document.addEventListener('DOMContentLoaded', function () {
                    const domain = "{{ $meetingConfig['domain'] }}";
                    const options = {
                        roomName: "{{ $meetingConfig['room_name'] }}",
                        parentNode: document.querySelector('#jitsi-meet-container'),
                        width: '100%',
                        height: '100%',
                        @if (!empty($meetingConfig['jwt']))
                            jwt: "{{ $meetingConfig['jwt'] }}",
                        @endif
                        userInfo: {
                            displayName: "{{ addslashes($meetingConfig['user_name']) }}",
                            email: "{{ addslashes($meetingConfig['user_email']) }}"
                        },
                        configOverwrite: {
                            startWithAudioMuted: {{ $meetingConfig['settings']['startWithAudioMuted'] ? 'true' : 'false' }},
                            startWithVideoMuted: {{ $meetingConfig['settings']['startWithVideoMuted'] ? 'true' : 'false' }},
                            disableRemoteMute: {{ $meetingConfig['settings']['disableRemoteMute'] ? 'true' : 'false' }},
                            hideConferenceSubject: false,
                            subject: "{{ addslashes($meetingConfig['settings']['subject']) }}",
                            enableWelcomePage: false,
                            prejoinPageEnabled: false,
                            disableDeepLinking: true,
                            disableInviteFunctions: true,
                            enableClosePage: false
                        },
                        interfaceConfigOverwrite: {
                            TOOLBAR_BUTTONS: @json($meetingConfig['toolbar_buttons']),
                            MOBILE_APP_PROMO: false,
                            HIDE_INVITE_MORE_HEADER: true,
                            SHOW_CHROME_EXTENSION_BANNER: false
                        }
                    };

                    try {
                        const api = new JitsiMeetExternalAPI(domain, options);
                        window.jitsiApi = api;

                        // Ensure the generated Jitsi iframe has full permissions including fullscreen & display-capture
                        const patchIframe = function () {
                            const iframe = document.querySelector('#jitsi-meet-container iframe');
                            if (iframe) {
                                iframe.setAttribute('allow', 'camera *; microphone *; display-capture *; fullscreen *; autoplay *; clipboard-write *; speaker *');
                                iframe.setAttribute('allowfullscreen', 'true');
                                iframe.setAttribute('webkitallowfullscreen', 'true');
                                iframe.setAttribute('mozallowfullscreen', 'true');
                                iframe.style.width = '100%';
                                iframe.style.height = '100%';
                                iframe.style.border = '0';
                            }
                        };
                        patchIframe();

                        // Observer to re-apply permissions if Jitsi re-attaches or refreshes the iframe element
                        const container = document.querySelector('#jitsi-meet-container');
                        if (container && window.MutationObserver) {
                            const observer = new MutationObserver(function () {
                                patchIframe();
                            });
                            observer.observe(container, { childList: true, subtree: true });
                        }

                        api.addEventListener('videoConferenceLeft', function () {
                            window.location.href = "{{ route('courses.live-classes.show', [$course->slug, $liveClass]) }}";
                        });
                    } catch (err) {
                        console.error('Failed to initialize Jitsi Meet External API:', err);
                    }
                });
            </script>
        @endif
    </section>
</x-layouts.public>

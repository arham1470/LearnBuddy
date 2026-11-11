<x-layouts.public title="Live Room - {{ $liveClass->title }} - LearnBuddy" description="Interactive virtual classroom live meeting room.">
    <section class="min-h-[calc(100vh-140px)] py-4 sm:py-6 px-3 sm:px-6 lg:px-8 bg-neutral-900 text-white flex flex-col justify-between">
        <div class="max-w-7xl mx-auto w-full space-y-4">
            <!-- Top Navigation & Controls Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-neutral-800/90 backdrop-blur px-5 py-3 rounded-2xl border border-neutral-700/60 shadow-md">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-600 text-white animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        LIVE
                    </span>

                    <div class="min-w-0">
                        <h1 class="text-xs sm:text-sm font-extrabold text-white truncate">
                            {{ $liveClass->title }}
                        </h1>
                        <p class="text-[11px] text-neutral-400 truncate">
                            {{ $course->title }} &bull; Host: {{ $liveClass->instructor->name }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-[11px] px-2.5 py-1 rounded-full {{ $isModerator ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold' : 'bg-neutral-700 text-neutral-300 font-medium' }}">
                        {{ $isModerator ? 'Host / Moderator' : 'Student Participant' }}
                    </span>

                    <a
                        href="{{ route('courses.live-classes.show', [$course->slug, $liveClass]) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm"
                        onclick="return confirm('Leave this live classroom session?');"
                    >
                        <i class="fas fa-sign-out-alt text-xs"></i>
                        <span>Leave Room</span>
                    </a>
                </div>
            </div>

            <!-- Main Meeting Stage / Container -->
            @if (! $meetingConfig['is_configured'])
                <!-- Honest Unconfigured Provider State -->
                <div class="bg-neutral-800 rounded-3xl border border-neutral-700 p-8 sm:p-12 text-center space-y-6 shadow-2xl max-w-2xl mx-auto my-8">
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

                    <div class="p-4 rounded-2xl bg-neutral-900/80 border border-neutral-700/70 text-left text-xs space-y-3 text-neutral-300">
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
                            class="px-6 py-2.5 rounded-full bg-neutral-700 hover:bg-neutral-600 text-white text-xs font-semibold transition"
                        >
                            Return to Session Details
                        </a>
                    </div>
                </div>
            @else
                <!-- Active Jitsi Meet WebRTC Video Player Container -->
                <div class="relative w-full rounded-3xl overflow-hidden border border-neutral-800 bg-black shadow-2xl min-h-[560px] sm:min-h-[640px] flex items-center justify-center">
                    <div id="jitsi-meet-container" class="w-full h-full min-h-[560px] sm:min-h-[640px]"></div>
                </div>

                <!-- Load Jitsi Meet External API SDK -->
                <script src="https://{{ $meetingConfig['domain'] }}/external_api.js"></script>
                <script>
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
                                prejoinPageEnabled: false
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
                            api.addEventListener('videoConferenceLeft', function () {
                                window.location.href = "{{ route('courses.live-classes.show', [$course->slug, $liveClass]) }}";
                            });
                        } catch (err) {
                            console.error('Failed to initialize Jitsi Meet External API:', err);
                        }
                    });
                </script>
            @endif
        </div>
    </section>
</x-layouts.public>

{{-- Third-party tracking scripts driven by active extensions (Setting > Extension).
     Perf: their heavy JS is loaded only after the first user interaction (or a
     short idle fallback), so it never blocks first paint / interactivity. --}}

@php
    $gaMeasurementId = $googleAnalytics['measurement_id'] ?? null;
    $metaPixelId = $metaPixel['pixel_id'] ?? null;
@endphp

@if ($gaMeasurementId || $metaPixelId)
    <script>
        (function () {
            var loaded = false;
            var GA_ID = @json($gaMeasurementId);
            var PIXEL_ID = @json($metaPixelId);

            function loadScript(src) {
                var s = document.createElement('script');
                s.async = true;
                s.src = src;
                document.head.appendChild(s);
                return s;
            }

            function boot() {
                if (loaded) return;
                loaded = true;
                cleanup();

                if (GA_ID) {
                    window.dataLayer = window.dataLayer || [];
                    window.gtag = function () { dataLayer.push(arguments); };
                    gtag('js', new Date());
                    gtag('config', GA_ID);
                    loadScript('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(GA_ID));
                }

                if (PIXEL_ID) {
                    !function (f, b, e, v, n, t, s) {
                        if (f.fbq) return; n = f.fbq = function () {
                            n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                        };
                        if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
                        n.queue = []; t = b.createElement(e); t.async = !0;
                        t.src = v; s = b.getElementsByTagName(e)[0];
                        s.parentNode.insertBefore(t, s)
                    }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', PIXEL_ID);
                    fbq('track', 'PageView');
                }
            }

            var events = ['scroll', 'pointerdown', 'keydown', 'touchstart', 'mousemove'];
            function cleanup() {
                events.forEach(function (ev) { window.removeEventListener(ev, boot, { passive: true }); });
            }
            events.forEach(function (ev) { window.addEventListener(ev, boot, { passive: true, once: true }); });
            // Fallback: fire after the page settles even if the user never interacts.
            window.addEventListener('load', function () { setTimeout(boot, 3500); }, { once: true });
        })();
    </script>

    @if ($metaPixelId)
        <noscript><img height="1" width="1" style="display:none" alt=""
            src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1" /></noscript>
    @endif
@endif

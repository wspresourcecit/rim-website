{{-- Google reCAPTCHA v2, driven by the active "recaptcha" extension (Setting > Extension).
     Perf: the API script is only needed for the modal lead forms, so it is loaded
     lazily on the first user interaction instead of blocking initial load. --}}

@php($recaptchaSiteKey = $recaptcha['site_key'] ?? null)

@if ($recaptchaSiteKey)
    <meta name="recaptcha-site-key" content="{{ $recaptchaSiteKey }}">
    <script>
        (function () {
            var loaded = false;
            function loadRecaptcha() {
                if (loaded) return;
                loaded = true;
                var s = document.createElement('script');
                s.src = 'https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit';
                s.async = true;
                s.defer = true;
                document.head.appendChild(s);
            }
            var events = ['pointerdown', 'keydown', 'touchstart', 'scroll'];
            events.forEach(function (ev) {
                window.addEventListener(ev, loadRecaptcha, { passive: true, once: true });
            });
            window.addEventListener('load', function () { setTimeout(loadRecaptcha, 4000); }, { once: true });
        })();
    </script>
@endif

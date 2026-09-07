<!doctype html>
<html lang="bn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') — Creative IT Institute</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    {{--
      Self-contained on purpose. The site layout (layouts.app) pulls header/
      footer data through view composers that hit the database, so extending it
      here would make the error page fail whenever the DB / a service is the
      cause of a 500. The CSS + inline SVG below mirror resources/css/app.css
      design tokens (brand gradient #ff861c -> #d20000, red #e42625, Kalpurush
      font, .btn-primary / .btn-outline, .container) so it still matches — and
      modernizes — the frontend theme.
    --}}
    <style>
        @font-face {
            font-family: "Kalpurush";
            src: url("/fonts/kalpurush.ttf") format("truetype");
            font-weight: 100 900;
            font-display: swap;
        }

        :root {
            --primary-50: #e42625;
            --primary-100: #fcf2e9;
            --primary-200: #fdf8f8;
            --primary-300: #f9c7c7;
            --orange-100: #fee0b6;
            --red-700: #b91c1c;
            --grad-from: #ff861c;
            --grad-to: #d20000;
            --black-100: #363840;
            --gray-50: #54565c;
            --gray-400: #e3e3e3;
            --white-50: #ffffff;
            --white-100: #fbfbfb;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--white-100);
            color: var(--black-100);
            font-family: "Kalpurush", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            width: 100%;
            max-width: 90rem;
            margin-inline: auto;
            padding-inline: 1rem;
            position: relative;
            z-index: 1;
        }

        /* ---- Header ---- */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--grad-from), var(--grad-to));
        }

        .site-header {
            background: var(--white-50);
            box-shadow: 0 4px 24px 0 rgba(188, 188, 188, 0.25);
        }

        .site-header .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 72px;
        }

        /* Maintenance page: no CTA, so centre the logo. */
        .site-header .container.is-centered {
            justify-content: center;
        }

        .site-header img {
            height: 40px;
            width: auto;
            display: block;
        }

        /* ---- Buttons (mirror .btn-primary / .btn-outline) ---- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .btn svg {
            width: 1.1em;
            height: 1.1em;
        }

        .btn-primary {
            background: var(--primary-50);
            color: var(--white-50);
            box-shadow: 0 10px 24px -8px rgba(228, 38, 37, 0.45);
        }

        .btn-primary:hover {
            background: var(--red-700);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: var(--white-50);
            border: 1px solid var(--primary-50);
            color: var(--primary-50);
        }

        .btn-outline:hover {
            background: var(--primary-200);
            transform: translateY(-1px);
        }

        @media (min-width: 1024px) {
            .btn {
                padding: 0.75rem 1.125rem;
                font-size: 1.125rem;
            }
        }

        /* ---- Main + vector backdrop ---- */
        main {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-block: 3.5rem;
            overflow: hidden;
        }

        .bg-decor {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        /* ---- Error card ---- */
        .error-card {
            position: relative;
            max-width: 40rem;
            margin-inline: auto;
            padding: clamp(2rem, 5vw, 3.5rem);
            text-align: center;
            background: rgba(255, 255, 255, 0.82);
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(227, 227, 227, 0.8);
            border-radius: 2rem;
            box-shadow: 0 30px 60px -24px rgba(120, 120, 120, 0.35);
        }

        .error-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 9999px;
            margin-bottom: 1.25rem;
            color: var(--primary-50);
            background: linear-gradient(170deg, var(--orange-100), var(--primary-100));
            box-shadow: 0 14px 30px -10px rgba(228, 38, 37, 0.4);
        }

        .error-badge svg {
            width: 32px;
            height: 32px;
        }

        .error-code {
            font-size: clamp(5rem, 22vw, 9rem);
            font-weight: 800;
            line-height: 0.95;
            letter-spacing: -0.04em;
            color: var(--primary-50);
            background: linear-gradient(170deg, var(--grad-from), var(--grad-to));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 12px 22px rgba(210, 0, 0, 0.18));
        }

        .error-card h1 {
            margin: 0.75rem 0 0;
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--black-100);
        }

        .error-card p {
            margin: 0.75rem auto 0;
            max-width: 32rem;
            font-size: 1rem;
            line-height: 1.75;
            color: var(--gray-50);
        }

        .error-actions {
            margin-top: 2.25rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        @media (min-width: 1024px) {
            .error-card h1 {
                font-size: 2.375rem;
            }

            .error-card p {
                font-size: 1.125rem;
            }
        }

        /* ---- Footer ---- */
        .site-footer {
            border-top: 1px solid var(--gray-400);
            background: var(--white-50);
            padding-block: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--gray-50);
        }

        @media (prefers-reduced-motion: no-preference) {
            .error-card {
                animation: rise 0.5s ease both;
            }

            @keyframes rise {
                from {
                    opacity: 0;
                    transform: translateY(12px);
                }
            }
        }
    </style>
</head>

<body>
    <div class="accent-bar"></div>

    @php($isMaintenance = (bool) ($settings?->maintenance_mode ?? false))

    <header class="site-header">
        <div @class(['container', 'is-centered' => $isMaintenance])>
            <a href="{{ url('/') }}" aria-label="Reem">
                <img src="{{ asset($settings?->main_logo ?: 'images/main-logo.png') }}" alt="Reem">
            </a>

        </div>
    </header>

    <main>
        {{-- Decorative vector backdrop: soft brand-gradient glow blobs + a
             fading dot grid. Purely cosmetic, hidden from assistive tech. --}}
        <svg class="bg-decor" viewBox="0 0 1200 800" preserveAspectRatio="xMidYMid slice"
            xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="errWarm" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#ff861c" />
                    <stop offset="1" stop-color="#d20000" />
                </linearGradient>
                <filter id="errBlur" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur stdDeviation="70" />
                </filter>
                <pattern id="errDots" width="26" height="26" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1.5" fill="#363840" fill-opacity="0.10" />
                </pattern>
                <radialGradient id="errFade" cx="50%" cy="42%" r="60%">
                    <stop offset="0" stop-color="#fff" stop-opacity="1" />
                    <stop offset="1" stop-color="#fff" stop-opacity="0" />
                </radialGradient>
                <mask id="errDotMask">
                    <rect width="1200" height="800" fill="url(#errFade)" />
                </mask>
            </defs>
            <rect width="1200" height="800" fill="url(#errDots)" mask="url(#errDotMask)" />
            <circle cx="150" cy="120" r="190" fill="url(#errWarm)" filter="url(#errBlur)" opacity="0.20" />
            <circle cx="1060" cy="710" r="210" fill="#e42625" filter="url(#errBlur)" opacity="0.12" />
        </svg>

        <div class="container">
            <div class="error-card">
                <span class="error-badge">
                    @yield('icon')
                </span>
                <div class="error-code">@yield('code')</div>
                <h1>@yield('title')</h1>
                <span>@yield('message')</span>
                @if (!($settings?->maintenance_mode ?? false))
                    <div class="error-actions">
                        <a class="btn btn-primary" href="{{ url('/') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 11.5 12 4l9 7.5" />
                                <path d="M5 10v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-9" />
                            </svg>
                            হোমে ফিরে যান
                        </a>
                        <a class="btn btn-outline" href="{{ url('/contact-us') }}">যোগাযোগ করুন</a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            &copy; {{ date('Y') }} Reem. সর্বস্বত্ব সংরক্ষিত।
        </div>
    </footer>
</body>

</html>

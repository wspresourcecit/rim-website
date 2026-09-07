<!doctype html>
<html lang="bn">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', 'Creative IT Institute দক্ষিণ এশিয়ার অন্যতম শীর্ষ আইটি স্কিল ডেভেলপমেন্ট ইনস্টিটিউট। গ্রাফিক্স ডিজাইন, ওয়েব ডেভেলপমেন্ট, ডিজিটাল মার্কেটিং, নেটওয়ার্কিং সহ ইন্ডাস্ট্রি-রেডি কোর্সে ভর্তি হন, এক্সপার্ট মেন্টরের গাইডলাইনে দক্ষতা অর্জন করুন এবং জব প্লেসমেন্ট সাপোর্ট পান।')">
    <meta name="keywords" content="@yield('meta_keywords', 'Creative IT Institute, আইটি ট্রেনিং, কোর্স, গ্রাফিক্স ডিজাইন কোর্স, ওয়েব ডেভেলপমেন্ট কোর্স, ডিজিটাল মার্কেটিং কোর্স, নেটওয়ার্কিং কোর্স, ডিপ্লোমা প্রোগ্রাম, জব প্লেসমেন্ট, ফ্রিল্যান্সিং')">

    <title>@yield('title', 'Creative IT Institute')</title>

    {{-- Favicon --}}

    <link rel="icon" type="image/png" href="{{ asset($settings->favicon ?? 'images/favicon.png') }}">

    <link rel="apple-touch-icon" href="{{ asset($settings->favicon ?? 'images/favicon.png') }}">

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Perf: warm up third-party origins + preload the brand font (blocks visible text). --}}
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
    <link rel="preconnect" href="https://connect.facebook.net" crossorigin>
    <link rel="preconnect" href="https://www.google.com">
    <link rel="dns-prefetch" href="https://www.gstatic.com">
    @stack('head')

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Reem')">
    <meta property="og:description" content="@yield('meta_description', '')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/site-thumnail.png'))">

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Reem')">
    <meta name="twitter:description" content="@yield('meta_description', '')">
    <meta name="twitter:image" content="@yield('og_image', '')">
    @include('layouts.partials.tracking')
    @include('layouts.partials.recaptcha')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Icon webfonts: load without blocking first paint. --}}
    @php($iconsCss = \Illuminate\Support\Facades\Vite::asset('resources/css/icons.css'))
    <link rel="preload" as="style" href="{{ $iconsCss }}" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ $iconsCss }}">
    </noscript>

    <style>
        @font-face {
            font-family: "Noto Sans Bengali";
            src: url("{{ asset('fonts/noto-sans-bangla/NotoSansBengali-Regular.ttf') }}") format("truetype");
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Noto Sans Bengali";
            src: url("{{ asset('fonts/noto-sans-bangla/NotoSansBengali-Medium.ttf') }}") format("truetype");
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Noto Sans Bengali";
            src: url("{{ asset('fonts/noto-sans-bangla/NotoSansBengali-SemiBold.ttf') }}") format("truetype");
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Noto Sans Bengali";
            src: url("{{ asset('fonts/noto-sans-bangla/NotoSansBengali-Bold.ttf') }}") format("truetype");
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
    </style>
</head>

<body class="bg-white-50  lg:pb-0 font-bangla-sans">
    @include('layouts.partials.header')
    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

</body>

</html>

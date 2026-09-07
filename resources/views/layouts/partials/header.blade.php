<header class="sticky top-0 z-100 bg-white-50 shadow-[0px_4px_24px_0px_rgba(188,188,188,0.25)] lg:py-0 py-2">
    <div class="container relative flex items-center justify-between gap-4 ">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex shrink-0 items-center">
            <img src="{{ asset($settings->main_logo ?? 'images/main-logo.png') }}" alt="" width="240"
                height="40" fetchpriority="high" decoding="async" class="h-10 w-auto" />
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden items-center gap-6 xl:flex">
            <a href="{{ url('/') }}"
                class="py-6 text-lg font-bangla-sans font-normal {{ navActive('/') }} transition-colors hover:text-primary-50">হোম</a>

        </nav>

        <!-- CTA + Mobile Toggle -->
        <div class="flex items-center gap-3">


            <button type="button" id="mobile-menu-toggle" data-mobile-menu-toggle
                class="inline-flex items-center justify-center rounded-full p-2 text-primary-50 xl:hidden"
                aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
                <i class="ti ti-menu-2 text-3xl"></i>
            </button>
        </div>
    </div>

    <!-- Offcanvas Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 z-40 hidden bg-black-50/50 xl:hidden"></div>

    <!-- Offcanvas Mobile Menu -->
    <aside id="mobile-menu"
        class="fixed inset-y-0 right-0 z-50 flex w-72 max-w-[80%] translate-x-full flex-col bg-white-50 shadow-xl transition-transform duration-300 ease-in-out xl:hidden"
        aria-hidden="true" inert>
        <div class="flex items-center justify-between border-b border-gray-400 px-4 py-3">
            <img loading="lazy" width="96" height="32" decoding="async"
                src="{{ asset($settings->main_logo ?? 'images/main-logo.png') }}" alt="" class="h-8 w-auto" />
            <button type="button" id="mobile-menu-close"
                class="inline-flex items-center justify-center rounded-full p-2 text-black-50 " aria-label="Close menu">
                <i class="ti ti-x text-2xl"></i>
            </button>
        </div>

        <nav class="flex flex-1 flex-col gap-1 overflow-y-auto px-4 py-3">
            <a href="{{ url('/') }}"
                class="rounded-lg px-3 py-2 text-lg font-bangla-sans font-normal {{ navActive('/') }} hover:text-primary-50">হোম</a>

        </nav>

        <div class="border-t border-gray-400 px-4 py-3">

        </div>
    </aside>
</header>

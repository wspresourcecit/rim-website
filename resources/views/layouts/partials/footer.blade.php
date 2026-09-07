@php
    $quickLinks = [['label' => 'হোম', 'url' => url('/')]];
    $otherLinks = [];
@endphp

<footer>
    <div
        class="bg-white  overflow-hidden section-padding rounded-t-3xl lg:rounded-t-4xl xl:rounded-t-[80px] shadow-[10px_0_40px_0_rgba(180,180,180,0.2)] font-bangla-sans">
        <div class="mx-auto container">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-12 gap-6">
                <div class="col-span-12 sm:col-span-12 md:col-span-6 lg:col-span-3">
                    <img loading="lazy" src="{{ asset($settings->main_logo ?? 'images/main-logo.png') }}" alt=""
                        class="h-10 w-auto" />
                    <p class="mt-3 text-lg leading-relaxed text-black-100 max-w-85.5">
                        {{ $settings->footer_text ?? '' }}
                    </p>
                    <div class="mt-4 flex gap-2">
                        <div>
                            <img loading="lazy" src="{{ asset('images/cit-mobile-app-footer-img.webp') }}"
                                alt=" Mobile App" class="max-w-24.5" />
                        </div>
                        <div class="flex flex-col justify-between">
                            <a href="#" target="_blank"
                                class="hover:opacity-70 transition-opacity duration-150 ease-in-out">
                                <img loading="lazy" src="{{ asset('images/app-store-download-icon.webp') }}"
                                    alt=" Mobile App" class="" />
                            </a>
                            <a href="#" target="_blank" rel="noopener"
                                class="hover:opacity-70 transition-opacity duration-150 ease-in-out">
                                <img loading="lazy" src="{{ asset('images/play-store-download-icon.webp') }}"
                                    alt="" class="" />
                            </a>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-3">
                        @foreach (json_decode($settings->social_handles) as $social)
                            <a href="{{ $social->link }}" target="_blank" rel="noopener"
                                aria-label="{{ ucfirst(str_replace('brand-', '', $social->platform_icon)) }}"
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-primary-50 transition-colors hover:bg-primary-50 hover:text-white-50">
                                <i class="ti ti-{{ $social->platform_icon }}" aria-hidden="true"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="col-span-12 sm:col-span-6 md:col-span-3 lg:col-span-2">
                    <h3 class="text-xl font-bold text-black-50">কুইক লিংক</h3>
                    <ul class="mt-3 space-y-2 text-lg text-gray-100">
                        @foreach ($quickLinks as $link)
                            <li><a href="{{ $link['url'] }}" class="hover:text-primary-50">{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-span-12 sm:col-span-6 md:col-span-3 lg:col-span-3">
                    <h3 class="text-xl font-bold text-black-50">একাডেমিক ক্যারিয়ার সেক্টরস</h3>
                    <ul class="mt-3 space-y-2 text-lg text-gray-100">

                    </ul>
                </div>

                <div class="col-span-12 sm:col-span-6 md:col-span-3 lg:col-span-2">
                    <h3 class="text-xl font-bold text-black-50">অন্যান্য</h3>
                    <ul class="mt-3 space-y-2 text-lg text-gray-100">
                        @foreach ($otherLinks as $link)
                            <li><a href="{{ $link['url'] }}" class="hover:text-primary-50">{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-span-12 sm:col-span-6 md:col-span-3 lg:col-span-2">
                    <h3 class="text-xl font-bold text-black-50">পেমেন্ট পার্টনার্স</h3>

                </div>
            </div>

            <div
                class="mx-auto mt-6 pt-6 border-t border-gray-600 flex container flex-col items-center justify-center md:justify-between gap-2 py-4 text-base text-gray-100 sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ $settings->copyright_text ?? '' }}</p>

            </div>
        </div>
    </div>
</footer>

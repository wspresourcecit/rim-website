/* =============================================================
 * Sticky Learning-Mode Tab Bar
 * Solid bg-orange-50 at the top of the page; once the user scrolls
 * (and the bar is pinned under the header via `sticky`), it turns
 * transparent.
 * ============================================================= */
const stickyFadeBar = document.querySelector("[data-sticky-fade]");

if (stickyFadeBar) {
    const updateStickyFadeBar = () => {
        const atTop = window.scrollY <= 10;
        stickyFadeBar.classList.toggle("bg-orange-50", atTop);
        stickyFadeBar.classList.toggle("bg-transparent", !atTop);
    };

    window.addEventListener("scroll", updateStickyFadeBar, { passive: true });
    updateStickyFadeBar();
}

/* =============================================================
 * Mobile Slide-in Menu (drawer with overlay)
 * Triggered by [data-mobile-menu-toggle], closed via close button,
 * overlay click, or Escape key.
 * ============================================================= */
const menuToggles = document.querySelectorAll("[data-mobile-menu-toggle]");
const menuClose = document.getElementById("mobile-menu-close");
const menuOverlay = document.getElementById("mobile-menu-overlay");
const mobileMenu = document.getElementById("mobile-menu");

function openMobileMenu() {
    mobileMenu.classList.remove("translate-x-full");
    menuOverlay.classList.remove("hidden");
    mobileMenu.setAttribute("aria-hidden", "false");
    mobileMenu.removeAttribute("inert");
    menuToggles.forEach((toggle) =>
        toggle.setAttribute("aria-expanded", "true"),
    );
    document.body.classList.add("overflow-hidden");
}

function closeMobileMenu() {
    mobileMenu.classList.add("translate-x-full");
    menuOverlay.classList.add("hidden");
    mobileMenu.setAttribute("aria-hidden", "true");
    mobileMenu.setAttribute("inert", "");
    menuToggles.forEach((toggle) =>
        toggle.setAttribute("aria-expanded", "false"),
    );
    document.body.classList.remove("overflow-hidden");
}

if (menuToggles.length && menuClose && menuOverlay && mobileMenu) {
    menuToggles.forEach((toggle) =>
        toggle.addEventListener("click", openMobileMenu),
    );
    menuClose.addEventListener("click", closeMobileMenu);
    menuOverlay.addEventListener("click", closeMobileMenu);

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            closeMobileMenu();
        }
    });
}

/* =============================================================
 * Simple Mobile Menu Toggle (icon swap between menu / close)
 * Independent of the drawer above — targets [menu-toggle] /
 * [mobile-menu] / [menu-icon] used elsewhere in the header.
 * ============================================================= */
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("menu-toggle");
    const menu = document.getElementById("mobile-menu");
    const icon = document.getElementById("menu-icon");

    if (!toggle || !menu) return;

    toggle.addEventListener("click", () => {
        const willOpen = menu.classList.contains("hidden");

        menu.classList.toggle("hidden", !willOpen);
        toggle.setAttribute("aria-expanded", String(willOpen));

        if (icon) {
            icon.classList.toggle("ti-menu-2", !willOpen);
            icon.classList.toggle("ti-x", willOpen);
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    /* ---------------------------------------------------------
     * Carousel
     * Scoped to each [data-carousel] instance, so the same markup
     * can repeat any number of times on one page without id
     * collisions. Handles prev/next scroll buttons and dot
     * navigation, and keeps the active dot in sync via
     * IntersectionObserver as the user scrolls manually.
     * --------------------------------------------------------- */
    document.querySelectorAll("[data-carousel]").forEach((carousel) => {
        const track = carousel.querySelector("[data-carousel-track]");
        if (!track) return;

        /* Grab-to-drag scrolling for mouse users (touch already
         * swipes natively via overflow-x-auto). */
        track.classList.add("cursor-grab");
        let isDragging = false;
        let dragged = false;
        let dragStartX = 0;
        let dragStartScroll = 0;

        // Prevent the browser's native image/link drag-and-drop from
        // hijacking the gesture so our mousemove-based scroll always wins.
        track.addEventListener("dragstart", (e) => e.preventDefault());

        track.addEventListener("mousedown", (e) => {
            isDragging = true;
            dragged = false;
            dragStartX = e.pageX;
            dragStartScroll = track.scrollLeft;
            track.classList.replace("cursor-grab", "cursor-grabbing");
            // CSS scroll-smooth would animate every scrollLeft write below,
            // making the drag lag behind the cursor instead of following it.
            // track.style.scrollBehavior = "auto";
        });

        const stopDrag = () => {
            if (!isDragging) return;
            isDragging = false;
            track.classList.replace("cursor-grabbing", "cursor-grab");
            // track.style.scrollBehavior = "";
        };
        window.addEventListener("mouseup", stopDrag);
        track.addEventListener("mouseleave", stopDrag);

        track.addEventListener("mousemove", (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const delta = e.pageX - dragStartX;
            if (Math.abs(delta) > 3) dragged = true;
            track.scrollLeft = dragStartScroll - delta;
        });

        // Swallow the click that follows a drag so links/buttons inside
        // the track don't fire when the user was just sliding it.
        track.addEventListener(
            "click",
            (e) => {
                if (dragged) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            },
            true,
        );

        const scroll = (dir) => {
            track.scrollBy({
                left: dir * track.clientWidth * 0.8,
                behavior: "smooth",
            });
        };

        carousel
            .querySelector("[data-carousel-prev]")
            ?.addEventListener("click", () => scroll(-1));
        carousel
            .querySelector("[data-carousel-next]")
            ?.addEventListener("click", () => scroll(1));

        const dotsContainer = carousel.querySelector("[data-carousel-dots]");
        const dots = dotsContainer
            ? Array.from(dotsContainer.querySelectorAll("[data-carousel-dot]"))
            : [];
        const slides = Array.from(track.children);
        if (!dots.length || !slides.length) return;

        const setActiveDot = (index) => {
            dots.forEach((dot, i) =>
                dot.classList.toggle("is-active", i === index),
            );
        };

        dots.forEach((dot, index) => {
            dot.addEventListener("click", () => {
                const slide = slides[index];
                if (!slide) return;
                track.scrollTo({
                    left: slide.offsetLeft,
                    behavior: "smooth",
                });
            });
        });

        const slideObserver = new IntersectionObserver(
            (entries) => {
                const mostVisible = entries
                    .filter((entry) => entry.isIntersecting)
                    .sort(
                        (a, b) => b.intersectionRatio - a.intersectionRatio,
                    )[0];
                if (mostVisible)
                    setActiveDot(slides.indexOf(mostVisible.target));
            },
            { root: track, threshold: [0.5, 0.6, 0.7, 0.8, 0.9] },
        );
        slides.forEach((slide) => slideObserver.observe(slide));
    });

    /* ---------------------------------------------------------
     * Accordion
     * Only one [data-accordion-item] stays open per [data-accordion]
     * group — opening one collapses the rest via their shared panel/
     * icon/title classes.
     * --------------------------------------------------------- */
    document.querySelectorAll("[data-accordion]").forEach((accordion) => {
        const items = accordion.querySelectorAll("[data-accordion-item]");
        items.forEach((item) => {
            const trigger = item.querySelector("[data-accordion-trigger]");
            const panel = item.querySelector("[data-accordion-panel]");
            const icon = item.querySelector("[data-accordion-icon]");
            const iconWrap = item.querySelector("[data-accordion-icon-wrap]");
            const title = item.querySelector("[data-accordion-title]");
            if (!trigger || !panel) return;

            trigger.addEventListener("click", () => {
                const isOpen = panel.classList.contains("grid-rows-[1fr]");

                items.forEach((otherItem) => {
                    otherItem
                        .querySelector("[data-accordion-panel]")
                        ?.classList.remove("grid-rows-[1fr]", "opacity-100");
                    otherItem
                        .querySelector("[data-accordion-panel]")
                        ?.classList.add("grid-rows-[0fr]", "opacity-0");
                    otherItem
                        .querySelector("[data-accordion-icon]")
                        ?.classList.replace("ti-minus", "ti-plus");
                    otherItem
                        .querySelector("[data-accordion-icon-wrap]")
                        ?.classList.remove("bg-primary-50", "text-white-50");
                    otherItem
                        .querySelector("[data-accordion-icon-wrap]")
                        ?.classList.add("text-primary-50");
                    otherItem
                        .querySelector("[data-accordion-title]")
                        ?.classList.replace("text-black-100", "text-gray-50");
                });

                if (!isOpen) {
                    panel.classList.remove("grid-rows-[0fr]", "opacity-0");
                    panel.classList.add("grid-rows-[1fr]", "opacity-100");
                    icon?.classList.replace("ti-plus", "ti-minus");
                    iconWrap?.classList.add("bg-primary-50", "text-white-50");
                    iconWrap?.classList.remove("text-primary-50");
                    title?.classList.replace("text-gray-50", "text-black-100");
                }
            });
        });
    });

    /* ---------------------------------------------------------
     * Tab Groups
     * Handles tab selection/highlighting, optional per-tab active/
     * inactive color classes, optional card filtering by category,
     * optional step-counter text, and optional autoplay cycling.
     * --------------------------------------------------------- */
    document.querySelectorAll("[data-tab-group]").forEach((group) => {
        const tabs = Array.from(group.querySelectorAll("[data-tab]"));
        const filterTarget = group.dataset.filterTarget
            ? document.querySelector(group.dataset.filterTarget)
            : null;

        // Optional per-tab color swap: tabs carrying data-active-class /
        // data-inactive-class get those tokens toggled alongside is-active,
        // so each tab can have its own highlight color instead of one shared style.
        const applyTabState = (tab, isActive) => {
            tab.classList.toggle("is-active", isActive);
            const tokens =
                (isActive
                    ? tab.dataset.activeClass
                    : tab.dataset.inactiveClass
                )?.split(" ") ?? [];
            const otherTokens =
                (isActive
                    ? tab.dataset.inactiveClass
                    : tab.dataset.activeClass
                )?.split(" ") ?? [];
            if (otherTokens.length) tab.classList.remove(...otherTokens);
            if (tokens.length) tab.classList.add(...tokens);
        };

        const counterTarget = group.dataset.counterTarget
            ? document.querySelector(group.dataset.counterTarget)
            : null;

        const segments = Array.from(
            group.querySelectorAll("[data-journey-segment]"),
        );

        const updateSegments = (activeTab) => {
            if (!segments.length) return;
            const activeIndex = tabs.indexOf(activeTab);
            const containerRect = group.getBoundingClientRect();

            segments.forEach((seg) => {
                const segIndex = parseInt(seg.dataset.journeySegment, 10);
                const leftTab = tabs[segIndex - 1];
                const rightTab = tabs[segIndex];
                if (!leftTab || !rightTab) return;

                const leftRect = leftTab.getBoundingClientRect();
                const rightRect = rightTab.getBoundingClientRect();

                const leftPct =
                    ((leftRect.left + leftRect.width / 2 - containerRect.left) /
                        containerRect.width) *
                    100;
                const rightPct =
                    ((rightRect.left +
                        rightRect.width / 2 -
                        containerRect.left) /
                        containerRect.width) *
                    100;

                seg.style.left = leftPct.toFixed(2) + "%";
                seg.style.width = (rightPct - leftPct).toFixed(2) + "%";
                seg.style.opacity = segIndex <= activeIndex ? "1" : "0.15";
            });
        };

        const selectTab = (tab) => {
            tabs.forEach((t) => applyTabState(t, t === tab));

            if (counterTarget && tab.dataset.stepLabel) {
                counterTarget.textContent = tab.dataset.stepLabel;
            }

            updateSegments(tab);

            if (!filterTarget) return;

            const categoryId = tab.dataset.categoryId;
            filterTarget
                .querySelectorAll("[data-category-id]")
                .forEach((card) => {
                    const matches =
                        categoryId === "all" ||
                        card.dataset.categoryId === categoryId;
                    card.classList.toggle("hidden", !matches);
                });
        };

        tabs.forEach((tab) =>
            tab.addEventListener("click", () => selectTab(tab)),
        );

        // Set initial segment positions after layout is ready.
        requestAnimationFrame(() => {
            const initialTab =
                tabs.find((t) => t.classList.contains("is-active")) || tabs[0];
            if (initialTab) updateSegments(initialTab);
        });

        // Auto-advance: cycles to the next tab on a timer, reusing the same
        // click handling above so highlight color and panel filtering stay in sync.
        if (group.dataset.autoplay && tabs.length > 1) {
            const delay = Number(group.dataset.autoplay) || 5000;
            setInterval(() => {
                const activeIndex = tabs.findIndex((t) =>
                    t.classList.contains("is-active"),
                );
                selectTab(tabs[(activeIndex + 1) % tabs.length]);
            }, delay);
        }
    });

    // External prev/next controls for any [data-tab-group]: point
    // data-tab-group-target at the group's selector and this steps through
    // its tabs via the same selectTab logic (no DOM-nesting requirement,
    // so the buttons can live anywhere on the page, e.g. inside the panel).
    document
        .querySelectorAll("[data-tab-prev], [data-tab-next]")
        .forEach((btn) => {
            const group = document.querySelector(btn.dataset.tabGroupTarget);
            const tabs = group
                ? Array.from(group.querySelectorAll("[data-tab]"))
                : [];
            if (!tabs.length) return;

            btn.addEventListener("click", () => {
                const activeIndex = tabs.findIndex((t) =>
                    t.classList.contains("is-active"),
                );
                const direction = btn.hasAttribute("data-tab-next") ? 1 : -1;
                tabs[
                    (activeIndex + direction + tabs.length) % tabs.length
                ].click();
            });
        });

    /* ---------------------------------------------------------
     * Generic Modal
     * Any [data-modal-trigger="x"] opens the [data-modal="x"] with
     * that id. Supports any number of modals on a page. Closes via
     * the close button or Escape key only — clicking the backdrop
     * does not dismiss it.
     * --------------------------------------------------------- */
    document.querySelectorAll("[data-modal]").forEach((modal) => {
        const closeModal = () => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");

            // Reset any lead form and clear its status message on close.
            modal.querySelectorAll("form").forEach((form) => form.reset());
            modal.querySelectorAll("[data-form-status]").forEach((statusEl) => {
                statusEl.textContent = "";
                statusEl.classList.add("hidden");
            });
        };

        modal
            .querySelector("[data-modal-close]")
            ?.addEventListener("click", closeModal);

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && !modal.classList.contains("hidden"))
                closeModal();
        });
    });

    document.querySelectorAll("[data-modal-trigger]").forEach((trigger) => {
        trigger.addEventListener("click", (event) => {
            if (trigger.tagName === "A") event.preventDefault();
            const modal = document.querySelector(
                `[data-modal="${trigger.dataset.modalTrigger}"]`,
            );
            if (!modal) return;
            modal.classList.remove("hidden");
            modal.classList.add("flex");

            // Preselect a course in the modal form when the trigger carries one.
            const freeCourseId = trigger.dataset.freeCourseId;
            const courseId = trigger.dataset.courseId;

            if (freeCourseId) {
                const courseSelect = modal.querySelector(
                    'select[name="free_course_id"]',
                );
                if (courseSelect) courseSelect.value = freeCourseId;
            }
            if (courseId) {
                const courseSelect = modal.querySelector(
                    'select[name="course_id"]',
                );
                if (courseSelect) courseSelect.value = courseId;
            }

            // Carry the clicked seminar's id into the join form's hidden input.
            const seminarId = trigger.dataset.seminarId;
            if (seminarId) {
                const seminarInput = modal.querySelector(
                    "[data-seminar-id-input]",
                );
                if (seminarInput) seminarInput.value = seminarId;
            }
        });
    });

    /* ---------------------------------------------------------
     * Video Modal
     * Loads the triggering [data-video-trigger] URL into the
     * iframe with autoplay, and clears the src on close so the
     * video stops playing in the background.
     * --------------------------------------------------------- */
    const videoModal = document.querySelector("[data-video-modal]");
    const videoFrame = videoModal?.querySelector("[data-video-frame]");

    const openVideoModal = (videoUrl) => {
        if (!videoModal || !videoFrame || !videoUrl) return;
        videoFrame.src = videoUrl + "?autoplay=1";
        videoModal.classList.remove("hidden");
        videoModal.classList.add("flex");
    };

    const closeVideoModal = () => {
        if (!videoModal || !videoFrame) return;
        videoFrame.src = "";
        videoModal.classList.add("hidden");
        videoModal.classList.remove("flex");
    };

    document.querySelectorAll("[data-video-trigger]").forEach((trigger) => {
        trigger.addEventListener("click", () =>
            openVideoModal(trigger.dataset.videoTrigger),
        );
    });

    videoModal
        ?.querySelector("[data-video-close]")
        ?.addEventListener("click", closeVideoModal);

    videoModal?.addEventListener("click", (event) => {
        if (event.target === videoModal) closeVideoModal();
    });

    document.addEventListener("keydown", (event) => {
        if (
            event.key === "Escape" &&
            !videoModal?.classList.contains("hidden")
        ) {
            closeVideoModal();
        }
    });
});

/* =============================================================
 * Floating Action Button
 * Toggles the [floating-action-panel] open/closed, swaps the
 * open/close icons, and closes on outside click or Escape.
 * ============================================================= */
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("floating-action-toggle");
    const panel = document.getElementById("floating-action-panel");
    const iconOpen = document.getElementById("floating-action-icon-open");
    const iconClose = document.getElementById("floating-action-icon-close");

    if (!toggle || !panel) return;

    const setOpen = (willOpen) => {
        panel.classList.toggle("hidden", !willOpen);
        panel.classList.toggle("flex", willOpen);
        toggle.setAttribute("aria-expanded", String(willOpen));
        iconOpen?.classList.toggle("hidden", willOpen);
        iconClose?.classList.toggle("hidden", !willOpen);
    };

    toggle.addEventListener("click", () =>
        setOpen(panel.classList.contains("hidden")),
    );

    document.addEventListener("click", (event) => {
        if (
            !panel.classList.contains("hidden") &&
            !panel.contains(event.target) &&
            !toggle.contains(event.target)
        ) {
            setOpen(false);
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !panel.classList.contains("hidden")) {
            setOpen(false);
        }
    });
});

/* =============================================================
 * OTP Verify
 * Auto-advances between [data-otp-input] boxes, supports paste,
 * runs the [data-otp-timer] countdown, and enables [data-otp-resend]
 * once it expires.
 * ============================================================= */
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("[data-otp-form]");
    if (!form) return;

    const inputs = Array.from(form.querySelectorAll("[data-otp-input]"));
    const timerEl = form.querySelector("[data-otp-timer]");
    const resendBtn = form.querySelector("[data-otp-resend]");
    const errorEl = form.querySelector("[data-otp-error]");

    inputs[0]?.focus();

    inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            input.value = input.value.replace(/[^0-9]/g, "").slice(0, 1);
            errorEl?.classList.add("hidden");
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (event) => {
            if (event.key === "Backspace" && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener("paste", (event) => {
            const digits = (event.clipboardData?.getData("text") || "")
                .replace(/[^0-9]/g, "")
                .split("");
            if (!digits.length) return;
            event.preventDefault();
            inputs.forEach((otherInput, otherIndex) => {
                otherInput.value = digits[otherIndex] || "";
            });
            inputs[Math.min(digits.length, inputs.length) - 1]?.focus();
        });
    });

    const successModal = document.querySelector("[data-otp-success-modal]");
    const failureModal = document.querySelector("[data-otp-failure-modal]");
    const showModal = (modal) => {
        modal?.classList.remove("hidden");
        modal?.classList.add("flex");
    };
    const hideModal = (modal) => {
        modal?.classList.add("hidden");
        modal?.classList.remove("flex");
    };

    let secondsLeft = 60;
    let timerInterval = null;

    const renderTimer = () => {
        if (!timerEl) return;
        const minutes = String(Math.floor(secondsLeft / 60)).padStart(2, "0");
        const seconds = String(secondsLeft % 60).padStart(2, "0");
        timerEl.textContent = `${minutes}:${seconds}`;
    };

    const startTimer = () => {
        clearInterval(timerInterval);
        secondsLeft = 60;
        renderTimer();
        if (resendBtn) resendBtn.disabled = true;
        timerInterval = setInterval(() => {
            secondsLeft -= 1;
            renderTimer();
            if (secondsLeft <= 0) {
                clearInterval(timerInterval);
                if (resendBtn) resendBtn.disabled = false;
            }
        }, 1000);
    };

    const resetOtp = () => {
        inputs.forEach((input) => (input.value = ""));
        errorEl?.classList.add("hidden");
        inputs[0]?.focus();
    };

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        const code = inputs.map((input) => input.value).join("");
        if (code.length !== inputs.length) {
            errorEl?.classList.remove("hidden");
            inputs.find((input) => !input.value)?.focus();
            return;
        }

        showModal(secondsLeft <= 0 ? failureModal : successModal);
    });

    successModal
        ?.querySelector("[data-otp-success-close]")
        ?.addEventListener("click", () => {
            hideModal(successModal);
            resetOtp();
        });

    failureModal
        ?.querySelector("[data-otp-failure-close]")
        ?.addEventListener("click", () => {
            hideModal(failureModal);
            resetOtp();
        });

    failureModal
        ?.querySelector("[data-otp-failure-resend]")
        ?.addEventListener("click", () => {
            hideModal(failureModal);
            resetOtp();
            startTimer();
        });

    if (timerEl && resendBtn) {
        startTimer();

        resendBtn.addEventListener("click", () => {
            if (resendBtn.disabled) return;
            resetOtp();
            startTimer();
        });
    }
});

// Career page: server-side paginated job listing with filters + "load more".
// Page 1 is rendered by resources/views/career.blade.php; changing a filter
// re-fetches page 1 and further pages are appended from route('career.load'),
// which returns { html, has_more, total }.
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("job_filter_form");
    const jobList = document.getElementById("job-list");
    if (!form || !jobList || !form.dataset.loadUrl) return;

    const loadUrl = form.dataset.loadUrl;
    const titleInput = document.getElementById("job_title");
    const employeeSelect = document.getElementById("employee_type");
    const jobTypeSelect = document.getElementById("job_type");
    const departmentSelect = document.getElementById("department");
    const locationInput = document.getElementById("location");
    const clearBtn = document.getElementById("clear_job_filter");
    const loadMoreBtn = document.getElementById("load_more_jobs");
    const loadMoreWrapper = document.getElementById("load-more-wrapper");
    const noResults = document.getElementById("no-jobs-found");
    const resultCount = document.getElementById("job-result-count");
    const resultCountText = document.getElementById("job-result-count-text");

    let page = 1;
    let loading = false;

    const hasFilter = () =>
        titleInput.value.trim() ||
        locationInput.value.trim() ||
        employeeSelect.value ||
        jobTypeSelect.value ||
        departmentSelect.value;

    const buildParams = (targetPage) => {
        const params = new URLSearchParams();
        if (titleInput.value.trim())
            params.set("search_data", titleInput.value.trim());
        if (locationInput.value.trim())
            params.set("location", locationInput.value.trim());
        if (employeeSelect.value)
            params.set("employee_type", employeeSelect.value);
        if (jobTypeSelect.value) params.set("job_type", jobTypeSelect.value);
        if (departmentSelect.value)
            params.set("job_department_id", departmentSelect.value);
        params.set("page", targetPage);
        return params;
    };

    const load = async (reset) => {
        if (loading) return;
        loading = true;
        loadMoreBtn.disabled = true;
        loadMoreBtn.classList.add("opacity-60");

        const targetPage = reset ? 1 : page + 1;

        try {
            const res = await fetch(
                `${loadUrl}?${buildParams(targetPage).toString()}`,
                {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                },
            );
            if (!res.ok) throw new Error("request failed");
            const data = await res.json();

            page = targetPage;
            if (reset) jobList.innerHTML = data.html;
            else jobList.insertAdjacentHTML("beforeend", data.html);

            noResults.classList.toggle("hidden", data.total !== 0);
            loadMoreWrapper.classList.toggle("hidden", !data.has_more);

            resultCount.classList.toggle("hidden", !hasFilter());
            resultCountText.textContent = `${data.total} টি ফলাফল পাওয়া গেছে`;
        } catch (e) {
            // leave the button in place so the user can retry
        } finally {
            loading = false;
            loadMoreBtn.disabled = false;
            loadMoreBtn.classList.remove("opacity-60");
        }
    };

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        load(true);
    });

    [employeeSelect, jobTypeSelect, departmentSelect].forEach((el) =>
        el.addEventListener("change", () => load(true)),
    );

    clearBtn.addEventListener("click", () => {
        titleInput.value = "";
        locationInput.value = "";
        employeeSelect.value = "";
        jobTypeSelect.value = "";
        departmentSelect.value = "";
        load(true);
    });

    loadMoreBtn.addEventListener("click", () => load(false));
});

document.addEventListener("DOMContentLoaded", () => {
    const copyBtn = document.getElementById("career-copy-link");
    if (!copyBtn) return;

    const label = copyBtn.querySelector("[data-copy-label]");
    const defaultLabel = label ? label.textContent : "";

    copyBtn.addEventListener("click", async () => {
        const url = copyBtn.dataset.copyUrl || window.location.href;

        try {
            await navigator.clipboard.writeText(url);
        } catch (err) {
            console.error("Failed to copy URL: ", err);
            return;
        }

        if (!label) return;
        label.textContent = "কপি হয়েছে!";
        setTimeout(() => {
            label.textContent = defaultLabel;
        }, 2000);
    });
});

/* ---------------------------------------------------------
 * Gallery Lightbox
 * Any [data-lightbox-trigger] opens the [data-lightbox] overlay
 * showing that image. Prev/next cycle through only the triggers
 * currently visible (so it respects the active tab-group filter).
 * --------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    const lightbox = document.querySelector("[data-lightbox]");
    if (!lightbox) return;

    const imageEl = lightbox.querySelector("[data-lightbox-image]");
    let items = [];
    let currentIndex = 0;

    const render = () => {
        if (!items.length) return;
        imageEl.src = items[currentIndex].dataset.lightboxSrc;
        imageEl.alt = items[currentIndex].dataset.lightboxAlt || "";
    };

    const open = (triggers, index) => {
        items = triggers;
        currentIndex = index;
        render();
        lightbox.classList.remove("hidden");
        lightbox.classList.add("flex");
    };

    const close = () => {
        lightbox.classList.add("hidden");
        lightbox.classList.remove("flex");
    };

    const step = (delta) => {
        if (!items.length) return;
        currentIndex = (currentIndex + delta + items.length) % items.length;
        render();
    };

    document.querySelectorAll("[data-lightbox-trigger]").forEach((trigger) => {
        trigger.addEventListener("click", () => {
            const visible = Array.from(
                document.querySelectorAll("[data-lightbox-trigger]"),
            ).filter((el) => !el.closest(".hidden"));
            const index = visible.indexOf(trigger);
            open(visible, index === -1 ? 0 : index);
        });
    });

    lightbox
        .querySelector("[data-lightbox-close]")
        ?.addEventListener("click", close);
    lightbox
        .querySelector("[data-lightbox-prev]")
        ?.addEventListener("click", () => step(-1));
    lightbox
        .querySelector("[data-lightbox-next]")
        ?.addEventListener("click", () => step(1));

    lightbox.addEventListener("click", (event) => {
        if (event.target === lightbox) close();
    });

    document.addEventListener("keydown", (event) => {
        if (lightbox.classList.contains("hidden")) return;
        if (event.key === "Escape") close();
        if (event.key === "ArrowLeft") step(-1);
        if (event.key === "ArrowRight") step(1);
    });
});

// Gallery page: "load more" reveal over the active tab's images, on top
// of the generic [data-tab-group] category filtering above.
document.addEventListener("DOMContentLoaded", () => {
    const grid = document.getElementById("gallery-grid");
    const loadMoreBtn = document.getElementById("gallery-load-more");
    if (!grid || !loadMoreBtn) return;

    const tabs = Array.from(
        document.querySelectorAll(
            '[data-tab-group][data-filter-target="#gallery-grid"] [data-tab]',
        ),
    );
    const pageSize = 2;
    let visibleCount = pageSize;

    const render = () => {
        const categoryId =
            tabs.find((tab) => tab.classList.contains("is-active"))?.dataset
                .categoryId ?? "all";

        const matches = Array.from(grid.children).filter(
            (card) =>
                categoryId === "all" || card.dataset.categoryId === categoryId,
        );

        matches.forEach((card, index) =>
            card.classList.toggle("hidden", index >= visibleCount),
        );

        loadMoreBtn.classList.toggle("hidden", visibleCount >= matches.length);
    };

    tabs.forEach((tab) =>
        tab.addEventListener("click", () => {
            visibleCount = pageSize;
            render();
        }),
    );

    loadMoreBtn.addEventListener("click", () => {
        visibleCount += pageSize;
        render();
    });

    render();
});

/* ---------------------------------------------------------
 * Google reCAPTCHA v2: explicitly render the widget inside each
 * lead form once the API loads. The site key / widget markup come
 * from layouts/partials/recaptcha.blade.php + <x-recaptcha-field />
 * and are only present when the "recaptcha" extension is active.
 * --------------------------------------------------------- */
const LEAD_FORM_SELECTOR =
    "[data-free-course-form], [data-free-class-form], [data-admission-form], [data-seminar-join-form]";
const recaptchaWidgets = new WeakMap();

window.onRecaptchaLoad = () => {
    const render = () => {
        document.querySelectorAll(LEAD_FORM_SELECTOR).forEach((form) => {
            const el = form.querySelector(".g-recaptcha");
            if (!el || recaptchaWidgets.has(form)) return;
            recaptchaWidgets.set(
                form,
                grecaptcha.render(el, { sitekey: el.dataset.sitekey }),
            );
        });
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", render);
    } else {
        render();
    }
};

/* ---------------------------------------------------------
 * Free Class / Free Course / Admission Modals: submit the lead
 * form to the API without leaving the page. Shows an inline
 * success / error message. All share the same behaviour.
 * --------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll(LEAD_FORM_SELECTOR);
    if (!forms.length) return;

    forms.forEach((form) => {
        const statusEl = form.querySelector("[data-form-status]");
        const submitBtn = form.querySelector('button[type="submit"]');

        const showStatus = (message, ok) => {
            if (!statusEl) return;
            statusEl.textContent = message;
            statusEl.classList.remove("hidden");
            statusEl.classList.toggle("text-primary-50", !ok);
            statusEl.classList.toggle("text-green-600", ok);
        };

        const recaptchaEl = form.querySelector(".g-recaptcha");

        form.addEventListener("submit", async (event) => {
            event.preventDefault();
            statusEl?.classList.add("hidden");

            const payload = Object.fromEntries(new FormData(form).entries());

            // reCAPTCHA v2: require a solved challenge before submitting.
            if (recaptchaEl) {
                const widgetId = recaptchaWidgets.get(form);
                const token =
                    typeof grecaptcha !== "undefined" && widgetId !== undefined
                        ? grecaptcha.getResponse(widgetId)
                        : "";

                if (!token) {
                    showStatus(
                        "অনুগ্রহ করে ‘I'm not a robot’ যাচাই সম্পন্ন করুন।",
                        false,
                    );
                    return;
                }

                payload.recaptcha_token = token;
            }
            delete payload["g-recaptcha-response"];

            if (submitBtn) submitBtn.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });
                const body = await response.json().catch(() => ({}));

                if (response.ok) {
                    form.reset();
                    showStatus(body.message || "সফলভাবে জমা হয়েছে।", true);
                } else {
                    const firstError = body.errors
                        ? Object.values(body.errors)[0]?.[0]
                        : null;
                    showStatus(
                        firstError ||
                            body.message ||
                            "কিছু একটা সমস্যা হয়েছে।",
                        false,
                    );
                }
            } catch (error) {
                showStatus("নেটওয়ার্ক সমস্যা, আবার চেষ্টা করুন।", false);
            } finally {
                if (submitBtn) submitBtn.disabled = false;

                // A reCAPTCHA v2 token is single-use — reset for the next try.
                const widgetId = recaptchaWidgets.get(form);
                if (
                    typeof grecaptcha !== "undefined" &&
                    widgetId !== undefined
                ) {
                    try {
                        grecaptcha.reset(widgetId);
                    } catch (e) {
                        /* widget not ready */
                    }
                }
            }
        });
    });
});

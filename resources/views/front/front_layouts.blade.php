<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ isset($seoDetail) ? $seoDetail->seo_title : '' }} | {{ ucwords($frontSetting->app_name) }}</title>
    <meta name="description" content="{{ isset($seoDetail) ? $seoDetail->seo_description : '' }}">
    <meta name="keywords" content="{{ isset($seoDetail) ? $seoDetail->seo_keywords : '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <meta property="og:title"
        content="{{ isset($seoDetail) ? $seoDetail->seo_title : '' }} | {{ ucwords($frontSetting->app_name) }}">
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('front.index') }}">
    <meta property="og:site_name" content="{{ ucwords($frontSetting->app_name) }}" />
    <meta property="og:description" content="{{ isset($seoDetail) ? $seoDetail->seo_description : '' }}">

    @include('front.sections.styles')

    @yield('styles')
</head>

<body class="antialiased bg-body text-body font-body">
    <div class="">
        @include('front.sections.header')

        @yield('content')


        {{-- @include('front.sections.footer') --}}
        @include('front.sections.scripts')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        @yield('scripts')

        <script>
document.addEventListener("DOMContentLoaded", () => {
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                // Optional: Unobserve if you only want it to animate once
                // animationObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1, // Trigger when 10% of the section is visible
        rootMargin: "0px 0px -50px 0px" // Trigger slightly before it hits the viewport
    });

    document.querySelectorAll('[data-animate]').forEach(el => {
        animationObserver.observe(el);
    });
});
</script>
        <script>

            $(document).ready(function() {
                $(".client-logo-carousel").owlCarousel({
                    loop: true,
                    margin: 30,
                    nav: false,
                    dots: false,
                    autoplay: true,
                    autoplayTimeout: 3000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: {
                            items: 2,
                            margin: 20
                        },
                        600: {
                            items: 3
                        },
                        1000: {
                            items: 5
                        }
                    }
                });
            });


            document.addEventListener('DOMContentLoaded', () => {
                const counters = document.querySelectorAll('.counter');
                const speed = 200; // The higher the slower

                const startCounter = (counter) => {
                    const updateCount = () => {
                        const target = +counter.getAttribute('data-target');
                        const count = +counter.innerText;

                        // Lower inc means smoother and slower animation
                        const inc = target / speed;

                        if (count < target) {
                            // Add increment and round up
                            counter.innerText = Math.ceil(count + inc);
                            // Call function every ms
                            setTimeout(updateCount, 1);
                        } else {
                            counter.innerText = target.toLocaleString();
                        }
                    };
                    updateCount();
                };

                // Professional Intersection Observer to trigger when visible
                const observerOptions = {
                    threshold: 0.5 // Trigger when 50% of the section is visible
                };

                const observer = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            startCounter(entry.target);
                            observer.unobserve(entry.target); // Stop observing once animated
                        }
                    });
                }, observerOptions);

                counters.forEach(counter => observer.observe(counter));
            });
        </script>
        <script>
            $(document).ready(function() {
                $(".testimonial-carousel").owlCarousel({
                    loop: true,
                    margin: 20,
                    nav: false,
                    dots: true,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    responsive: {
                        0: {
                            items: 1
                        },
                        768: {
                            items: 2
                        },
                        1024: {
                            items: 3
                        }
                    }
                });
            });
        </script>

        <script>
            "use strict";

            function changeLang(langKey) {
                art.sendXhr({
                    url: "{{ route('front.change-language') }}",
                    type: "POST",
                    data: {
                        key: langKey
                    },
                    container: "#ajax-register-form",
                    success: function(response) {
                        if (response.status == 'success') {
                            window.location.reload();
                        }
                    }
                });
            }

            function toggleFAQ(index) {
                const answer = document.getElementById(`answer-${index}`);
                const icon = document.getElementById(`icon-${index}`);

                // Toggle the current one
                const isOpen = answer.classList.contains('open');

                // Close all others (Optional: comment out if you want multiple open)
                document.querySelectorAll('.faq-answer').forEach(el => el.classList.remove('open'));
                document.querySelectorAll('.faq-icon').forEach(el => el.classList.remove('active'));

                if (!isOpen) {
                    answer.classList.add('open');
                    icon.classList.add('active');
                }
            }
        </script>
    </div>
</body>

</html>

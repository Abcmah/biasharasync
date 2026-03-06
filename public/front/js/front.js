"use strict";

/**
 * BiasharaSync Landing Page Scripts
 * Consolidated from inline scripts for better caching and performance.
 */

document.addEventListener("DOMContentLoaded", function () {

    // --- Mobile Menu Toggle ---
    var menuToggle = document.getElementById('menuToggle');
    var navLinks = document.getElementById('navLinks');

    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var isActive = navLinks.classList.toggle('active');
            menuToggle.innerHTML = isActive ? '&#10005;' : '&#9776;';
            menuToggle.setAttribute('aria-expanded', isActive);
        });

        document.addEventListener('click', function (event) {
            if (!navLinks.contains(event.target) && !menuToggle.contains(event.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                menuToggle.innerHTML = '&#9776;';
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navLinks.classList.remove('active');
                menuToggle.innerHTML = '&#9776;';
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // --- Scroll Animations (IntersectionObserver) ---
    if ('IntersectionObserver' in window) {
        var animationObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        });

        document.querySelectorAll('[data-animate]').forEach(function (el) {
            animationObserver.observe(el);
        });
    } else {
        // Fallback: show all elements immediately
        document.querySelectorAll('[data-animate]').forEach(function (el) {
            el.classList.add('is-visible');
        });
    }

    // --- Counter Animation ---
    var counters = document.querySelectorAll('.counter');
    if (counters.length > 0 && 'IntersectionObserver' in window) {
        var speed = 200;

        var startCounter = function (counter) {
            var updateCount = function () {
                var target = +counter.getAttribute('data-target');
                var count = +counter.innerText;
                var inc = target / speed;

                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target.toLocaleString();
                }
            };
            updateCount();
        };

        var counterObserver = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    startCounter(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function (counter) {
            counterObserver.observe(counter);
        });
    }

    // --- FAQ Accordion ---
    window.toggleFAQ = function (index) {
        var answer = document.getElementById('answer-' + index);
        var icon = document.getElementById('icon-' + index);
        var button = answer ? answer.previousElementSibling : null;

        if (!answer || !icon) return;

        var isOpen = answer.classList.contains('open');

        // Close all others
        document.querySelectorAll('.faq-answer').forEach(function (el) { el.classList.remove('open'); });
        document.querySelectorAll('.faq-icon').forEach(function (el) { el.classList.remove('active'); });
        document.querySelectorAll('.faq-question').forEach(function (el) { el.setAttribute('aria-expanded', 'false'); });

        if (!isOpen) {
            answer.classList.add('open');
            icon.classList.add('active');
            if (button) button.setAttribute('aria-expanded', 'true');
        }
    };
});

/**
 * Initialize Owl Carousel instances.
 * Called after jQuery and Owl Carousel are loaded.
 */
function initCarousels() {
    if (typeof jQuery === 'undefined' || typeof jQuery.fn.owlCarousel === 'undefined') return;

    var $ = jQuery;

    if ($(".client-logo-carousel").length) {
        $(".client-logo-carousel").owlCarousel({
            loop: true,
            margin: 30,
            nav: false,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            responsive: {
                0: { items: 2, margin: 20 },
                600: { items: 3 },
                1000: { items: 5 }
            }
        });
    }

    if ($(".testimonial-carousel").length) {
        $(".testimonial-carousel").owlCarousel({
            loop: true,
            margin: 20,
            nav: false,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            responsive: {
                0: { items: 1 },
                768: { items: 2 },
                1024: { items: 3 }
            }
        });
    }
}

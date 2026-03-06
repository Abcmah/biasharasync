<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $company->short_name }}</title>
    <link rel="icon" type="image/png" href="{{ $company->small_light_logo_url }}">
    <meta name="msapplication-TileImage" href="{{ $company->small_light_logo_url }}">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:wght@400;500;600;700;800;900&display=swap">

      <style>
        :root {
            --primary-color: #5254cf;
            --primary-light: #7678e8;
            --bg-color: #f0f2f5;
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        /* === PREMIUM LOADING CONTAINER === */
        .loading-app-container {
            height: 100vh;
            width: 100vw;
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            align-items: center;
            justify-content: center;
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            z-index: 99999;
            overflow: hidden;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }

        .loading-app-container.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        /* Animated Background Shapes */
        .loading-app-container::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: rgba(158, 157, 218, 0.144);
            border-radius: 50%;
            top: -300px;
            right: -200px;
            animation: floatBackground 20s infinite ease-in-out;
        }

        .loading-app-container::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            bottom: -200px;
            left: -150px;
            animation: floatBackground 15s infinite ease-in-out reverse;
        }

        /* Premium Loader Card */
        .loader-card {
            padding: 3.5rem 3rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow:
                0 30px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.5);
            text-align: center;
            max-width: 480px;
            width: 90%;
            position: relative;
            z-index: 1;
            animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }


        .loader-image-wrapper {
            margin-bottom: 1.8rem;
        }

        .floating-logo {
            width: 100px;
            height: auto;
            filter: drop-shadow(0 8px 20px rgba(82, 84, 207, 0.25));
            animation: float 3s ease-in-out infinite;
        }

        /* Main Loading Text with Gradient */
        .loader-text {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            font-weight: 700;
            font-size: 1.35rem;
            margin-bottom: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.03em;
        }

        /* === LINEAR PROGRESS BAR === */
        .progress-wrapper {
            margin-top: 2rem;
        }

        .progress-container-app {
            width: 100%;
            height: 8px;
            /* background: rgba(102, 126, 234, 0.15); */
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .progress-bar-app {
            height: 100%;
            background: linear-gradient(90deg,
                #667eea 0%,
                #764ba2 50%,
                #667eea 100%);
            background-size: 200% 100%;
            border-radius: 10px;
            width: 0%;
            position: relative;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:
                0 0 20px rgba(102, 126, 234, 0.4),
                0 2px 8px rgba(118, 75, 162, 0.3);
            animation: gradientShift 2s ease infinite;
        }

        /* Shimmer effect on progress bar */
        .progress-bar-app::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent
            );
            animation: shimmer 1.8s infinite;
        }

        .progress-percentage {
            color: #667eea;
            font-size: 0.875rem;
            font-weight: 700;
            margin-top: 12px;
            letter-spacing: 0.5px;
            font-family: 'Courier New', monospace;
        }

        .loading-message {
            color: #64748b;
            font-size: 0.813rem;
            margin-top: 16px;
            min-height: 20px;
            font-weight: 500;
            opacity: 0.8;
            animation: fadeSlideUp 0.5s ease;
        }

        /* Animated dots */
        .loader-dots {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .loader-dots span {
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out;
        }

        .loader-dots span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .loader-dots span:nth-child(2) {
            animation-delay: -0.16s;
        }

        /* === ANIMATIONS === */
        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatBackground {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-40px) rotate(180deg);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0.7);
                opacity: 0.4;
            }
            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 0.8;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .loader-card {
                padding: 2.5rem 1.5rem;
                border-radius: 24px;
            }

            .floating-logo {
                width: 80px;
            }

            .loader-text {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Preloader: outside #app so Vue mounting doesn't destroy it -->
    <div class="loading-app-container" id="appLoader">
        <div class="loader-card">
            <div class="loader-image-wrapper">
                <img src="{{ $loadingImage }}" alt="App Logo" class="floating-logo">
            </div>

            @if (isset($loadingLangMessageLang))
                <div class="loader-text" id="loaderMainText">
                    {{ $loadingLangMessageLang }}
                </div>
            @endif

            <!-- Premium Linear Progress Bar -->
            <div class="progress-wrapper">
                <div class="progress-container-app">
                    <div class="progress-bar-app" id="appProgressBar"></div>
                </div>
                <div class="progress-percentage" id="appProgressPercentage">0%</div>
                <div class="loading-message" id="appLoadingMessage"></div>
            </div>

            <!-- Animated Dots -->
            <div class="loader-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <div id="app"></div>
    <script>
        window.config = {
            'path': '{{ url('/') }}',
            'download_lang_csv_url': "{{ route('api.extra.langs.download') }}",
            'invoice_url': "{{ url('api/v1/pdf') }}",
            'pos_invoice_css': "{{ asset('css/pos_invoice_css.css') }}",
            'verify_purchase_background': "{{ asset('images/verify_purchase_background.svg') }}",
            'login_background': "{{ asset('images/login_background.svg') }}",
            'product_sample_file': "{{ asset('images/sample_products.csv') }}",
            'category_sample_file': "{{ asset('images/sample_categories.csv') }}",
            'brand_sample_file': "{{ asset('images/sample_brands.csv') }}",
            'customer_sample_file': "{{ asset('images/sample_customers.csv') }}",
            'supplier_sample_file': "{{ asset('images/sample_suppliers.csv') }}",
            'staff_member_sample_file': "{{ asset('images/sample_staff_members.csv') }}",
            'translatioins_sample_file': "{{ asset('images/sample_translations.csv') }}",
            'perPage': 10,
            'product_name': "{{ $appName }}",
            'product_version': "{{ $appVersion }}",
            'modules': @json($enabledModules),
            'installed_modules': @json($installedModules),
            'theme_mode': "{{ $themeMode }}",
            'appChecking': true,
            'app_version': "{{ $appVersion }}",
            'app_env': "{{ $appEnv }}",
            'app_type': "{{ $appType }}",
            'frontStoreWarehouse': @json($frontStoreWarehouse),
            'frontStoreCompany': @json($frontStoreCompany),
            'frontStoreSettings': @json($frontStoreSettings),
            'warehouseCurrency': @json($warehouseCurrency),
            'defaultLangKey': "{{ $defaultLangKey }}",
        };
    </script>

    @vite('resources/js/app.js')
    <script>
        // === VUE-AWARE PRELOADER WITH PERCEIVED PERFORMANCE ===
        // Progress races to ~85% fast (Gmail-style illusion), then waits
        // for Vue to call window.__appReady() before completing to 100%.
        (function () {
            var loadingMessages = [
                'Initializing application...',
                'Loading resources...',
                'Preparing workspace...',
                'Setting up interface...',
                'Loading modules...',
                'Optimizing performance...',
                'Almost ready...',
                'Finalizing...',
            ];

            var appLoader = document.getElementById('appLoader');
            var progressBar = document.getElementById('appProgressBar');
            var progressPercentage = document.getElementById('appProgressPercentage');
            var loadingMessage = document.getElementById('appLoadingMessage');
            var loaderMainText = document.getElementById('loaderMainText');

            var progress = 0;
            var messageIndex = 0;
            var vueReady = false;
            var interval = 40; // ms per tick

            // Phase 1: Fast start – race to 85% with variable speed
            function tick() {
                if (vueReady) return; // completion handled by finishLoading()

                if (progress < 45) {
                    progress += 1.2 + Math.random() * 2.5;   // fast burst
                } else if (progress < 70) {
                    progress += 0.3 + Math.random() * 0.8;   // natural slowdown
                } else if (progress < 85) {
                    progress += 0.15 + Math.random() * 0.35;  // crawl toward cap
                }
                // Stall at 85 – never cross it until Vue signals ready
                progress = Math.min(progress, 85);

                applyProgress(progress);
                setTimeout(tick, interval);
            }

            function applyProgress(value) {
                if (progressBar)       progressBar.style.width = value + '%';
                if (progressPercentage) progressPercentage.textContent = Math.floor(value) + '%';

                var idx = Math.min(
                    Math.floor((value / 100) * loadingMessages.length),
                    loadingMessages.length - 1
                );
                if (idx > messageIndex && loadingMessage) {
                    messageIndex = idx;
                    loadingMessage.style.animation = 'none';
                    setTimeout(function () {
                        loadingMessage.textContent = loadingMessages[messageIndex];
                        loadingMessage.style.animation = 'fadeSlideUp 0.5s ease';
                    }, 50);
                }
            }

            // Phase 2: Called by Vue after app.mount() – animate 85→100% then fade out
            function finishLoading() {
                if (vueReady) return;
                vueReady = true;

                var remaining = 100 - progress;
                var steps = 8;
                var step = 0;

                function completeStep() {
                    step++;
                    progress = Math.min(progress + remaining / steps, 100);
                    applyProgress(progress);

                    if (step < steps) {
                        setTimeout(completeStep, 30);
                    } else {
                        // Show "Ready" briefly, then fade out
                        if (loaderMainText) loaderMainText.textContent = 'Ready!';
                        if (loadingMessage) loadingMessage.textContent = 'Loading complete';

                        setTimeout(function () {
                            if (appLoader) appLoader.classList.add('fade-out');

                            // Remove from DOM after CSS transition finishes
                            setTimeout(function () {
                                if (appLoader && appLoader.parentNode) {
                                    appLoader.parentNode.removeChild(appLoader);
                                }
                            }, 600);
                        }, 300);
                    }
                }

                completeStep();
            }

            // Expose the ready signal for Vue
            window.__appReady = finishLoading;

            // Safety: if Vue never calls __appReady, force-complete after 15 s
            setTimeout(function () {
                if (!vueReady) finishLoading();
            }, 15000);

            // Start the illusion immediately
            tick();
        })();
    </script>
</body>

</html>

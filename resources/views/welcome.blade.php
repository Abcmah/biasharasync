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
            --bg-color: #f0f2f5;
            --glass-bg: rgba(255, 255, 255, 0.8);
        }

        .loading-app-container {
            height: 100vh;
            width: 100vw;
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, #ffffff 0%, #e6e9ff 100%);
            z-index: 9999;
        }

        .loader-card {
            padding: 3rem;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .loader-image-wrapper {
            margin-bottom: 1.5rem;
        }

        .floating-logo {
            width: 120px;
            filter: drop-shadow(0 5px 15px rgba(82, 84, 207, 0.2));
            animation: float 3s ease-in-out infinite;
        }

        .loader-text {
            color: var(--primary-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-weight: 600;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
            margin-bottom: 2rem;
            opacity: 0.8;
        }

        /* Modern Smooth Spinner */
        .smooth-spinner {
            width: 40px;
            height: 40px;
            margin: 0 auto;
            border: 3px solid rgba(82, 84, 207, 0.1);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 0.8s cubic-bezier(0.6, 0.2, 0.4, 0.8) infinite;
        }

        .loader-text {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 2rem;

            /* Shimmer Effect Setup */
            background: linear-gradient(to right,
                    #5254cf 20%,
                    #a3a4ff 40%,
                    #a3a4ff 60%,
                    #5254cf 80%);
            background-size: 200% auto;
            color: transparent;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: cardPulse 4s ease-in-out infinite;
            animation: textShimmer 2s linear infinite;
        }

        @keyframes cardPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        @keyframes textShimmer {
            to {
                background-position: 200% center;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="loading-app-container">
            <div class="loader-card">
                <div class="loader-image-wrapper">
                    <img src="{{ $loadingImage }}" alt="App Logo" class="floating-logo">
                </div>

                @if (isset($loadingLangMessageLang))
                    <div class="loader-text">
                        {{ $loadingLangMessageLang }}
                    </div>
                @endif

                <div class="loader-visual">
                    <div class="smooth-spinner"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.config = {
            'path': '{{ url('/') }}',
            'download_lang_csv_url': "{{ route('api.extra.langs.download') }}",
            'invoice_url': "{{ route('api.extra.pdf.v1', '') }}",
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
</body>

</html>

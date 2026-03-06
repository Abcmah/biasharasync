<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ isset($seoDetail) ? $seoDetail->seo_title : '' }} | {{ ucwords($frontSetting->app_name) }}</title>
    <meta name="description" content="{{ isset($seoDetail) ? $seoDetail->seo_description : '' }}">
    <meta name="keywords" content="{{ isset($seoDetail) ? $seoDetail->seo_keywords : '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:title" content="{{ isset($seoDetail) ? $seoDetail->seo_title : '' }} | {{ ucwords($frontSetting->app_name) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('front.index') }}">
    <meta property="og:site_name" content="{{ ucwords($frontSetting->app_name) }}">
    <meta property="og:description" content="{{ isset($seoDetail) ? $seoDetail->seo_description : '' }}">

    {{-- Fonts: Only load weights actually used (400, 600, 700, 800) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('front/css/inos.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/output.css') }}">

    @yield('styles')
</head>

<body class="antialiased">
    @include('front.sections.header')

    @yield('content')

    @include('front.sections.footer')

    {{-- Core Scripts --}}
    @include('front.sections.scripts')
    <script src="{{ asset('front/js/front.js') }}" defer></script>

    {{-- jQuery + Owl Carousel (only loaded when carousels are present) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" defer onload="initCarousels()"></script>

    @yield('scripts')
</body>

</html>

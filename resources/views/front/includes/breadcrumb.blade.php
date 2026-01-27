<section class="breadcrumb-dome-wrapper animate-up" data-animate>
    <div class="breadcrumb-overlay"></div>

    <div class="breadcrumb-container">
        <div class="breadcrumb-content">
            <h1 class="breadcrumb-main-title">{{ $breadcrumbTitle }}</h1>
            <div class="breadcrumb-nav" aria-label="breadcrumb">
                <ol class="breadcrumb-list">
                    <li class="breadcrumb-item">
                        <a href="{{ route('front.index') }}">{{ $frontSetting->home_text }}</a>
                    </li>
                    <li class="breadcrumb-separator">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $breadcrumbTitle }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

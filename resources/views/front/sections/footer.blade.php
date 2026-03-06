<footer class="main-footer animate-up" data-animate>
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-col brand-info">
                <div class="footer-logo-wrapper">
                    <img src="{{ $frontSetting->footer_logo_url }}" alt="{{ $frontSetting->app_name }}" class="footer-dynamic-logo">
                </div>
                <p class="footer-about">
                    {{ $frontSetting->footer_description }}
                </p>
                <div class="footer-socials">
                    @if($frontSetting->facebook_url)
                        <a href="{{ $frontSetting->facebook_url }}" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if($frontSetting->twitter_url)
                        <a href="{{ $frontSetting->twitter_url }}" class="social-link" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                    @endif
                    @if($frontSetting->linkedin_url)
                        <a href="{{ $frontSetting->linkedin_url }}" class="social-link" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    @endif
                    @if($frontSetting->instagram_url)
                        <a href="{{ $frontSetting->instagram_url }}" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    @endif
                </div>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">{{ $frontSetting->footer_links_text }}</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('front.index') }}">{{ $frontSetting->home_text }}</a></li>
                    <li><a href="{{ route('front.features') }}">{{ $frontSetting->features_text }}</a></li>
                    <li><a href="{{ route('front.pricing') }}">{{ $frontSetting->pricing_text }}</a></li>
                    <li><a href="{{ route('front.contact') }}">{{ $frontSetting->contact_text }}</a></li>
                    <li><a href="{{ route('front.blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('front.terms') }}">Terms & Conditions</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">{{ $frontSetting->footer_pages_text }}</h4>
                <ul class="footer-links">
                    @foreach($footerPages as $footerPage)
                        <li><a href="{{ route('front.page', $footerPage->slug) }}">{{ $footerPage->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-title">{{ $frontSetting->footer_contact_us_text }}</h4>
                <ul class="footer-contact-list">
                    <li>
                        <span class="contact-label">Email:</span>
                        <a href="mailto:{{ $frontSetting->contact_email }}">{{ $frontSetting->contact_email }}</a>
                    </li>
                    <li>
                        <span class="contact-label">Phone:</span>
                        <a href="tel:{{ $frontSetting->contact_phone }}">{{ $frontSetting->contact_phone }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>{{ $frontSetting->footer_copyright_text }}</p>
            <div class="footer-legal">
                <a href="{{ route('front.terms') }}">Terms & Conditions</a>
                @foreach($footerPages as $footerPage)
                    <a href="{{ route('front.page', $footerPage->slug) }}">{{ $footerPage->title }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

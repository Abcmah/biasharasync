@extends('front.front_layouts')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
@endsection

@section('content')

{{-- Stats Section --}}
<section class="stats-section">
    <div class="stats-container">
        <div class="stats-grid animate-up" data-animate>
            <div class="stat-item animate-zoom" data-animate>
                <span class="stat-icon"><i class="bi bi-cloud-check"></i></span>
                <span class="stat-number">99.9%</span>
                <span class="stat-label">Uptime</span>
                <p class="stat-desc">Reliable infrastructure ensuring your data is always ready.</p>
            </div>

            <div class="stat-item animate-zoom" data-animate>
                <span class="stat-icon"><i class="bi bi-shield-check"></i></span>
                <span class="stat-number">100%</span>
                <span class="stat-label">Secure</span>
                <p class="stat-desc">Securely handling transactions every single month.</p>
            </div>

            <div class="stat-item animate-zoom" data-animate>
                <span class="stat-icon"><i class="bi bi-headset"></i></span>
                <span class="stat-number">24/7</span>
                <span class="stat-label">Support</span>
                <p class="stat-desc">Our dedicated team is here whenever you need help.</p>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="cta-wrapper">
    <div class="cta-container">
        <div class="cta-content">
            <span class="cta-tagline">Boost Your Productivity</span>
            <h2 class="cta-title">Ready to streamline your business operations?</h2>
            <p class="cta-text">Join thousands of businesses that have simplified their billing and inventory management with {{ ucwords($frontSetting->app_name) }}.</p>
        </div>

        <div class="cta-actions">
            <a href="{{ route('front.register') }}" class="cta-btn cta-btn-primary">Get Started Now</a>
            <span class="cta-note">No credit card required &bull; 14-day free trial</span>
        </div>
    </div>
</section>

{{-- Features Section --}}
<div class="features-wrapper">
    @foreach ($allHomePageFeatures as $allHomePageFeature)
    <section class="feature-row animate-up {{ $loop->even ? 'row-reverse' : '' }}" data-animate>
        <div class="feature-container">
            <div class="feature-col feature-text-content">
                <div class="content-inner">
                    <h2 class="feature-heading">{{ $allHomePageFeature->title }}</h2>
                    <p class="feature-description">{{ $allHomePageFeature->description }}</p>

                    <ul class="feature-points">
                        @foreach ($allHomePageFeature->features as $allFeaturePoint)
                            <li class="feature-point">
                                <div class="check-icon">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <span>{{ $allFeaturePoint }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="feature-col feature-visual-content">
                <div class="image-frame">
                    <img src="{{ $allHomePageFeature->image_url }}"
                         alt="{{ $allHomePageFeature->title }}"
                         loading="lazy">
                </div>
            </div>
        </div>
    </section>
    @endforeach
</div>

{{-- Testimonials Section --}}
<section class="testimonial-section animate-up" data-animate>
    <div class="testimonial-container">
        <div class="testimonial-header">
            <span class="testimonial-tagline">{{ $frontSetting->testimonial_title }}</span>
            <h2 class="testimonial-main-title">{{ $frontSetting->testimonial_description }}</h2>
            <div class="testimonial-divider"></div>
        </div>

        <div class="owl-carousel testimonial-carousel">
            @foreach ($frontTestimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="quote-icon">&ldquo;</div>
                    <p class="testimonial-text">{{ $testimonial->comment }}</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="{{ $testimonial->image_url }}"
                                 alt="{{ $testimonial->name }}"
                                 loading="lazy">
                        </div>
                        <div class="author-info">
                            <h4 class="author-name">{{ $testimonial->name }}</h4>
                            @if(!empty($testimonial->designation))
                                <p class="author-position">{{ $testimonial->designation }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@php
$frontSetting->faq_items = [
    [
        'question' => 'How does the 14-day free trial work?',
        'answer' => 'You get full access to all professional features, including automated invoicing and multi-store sync, for 14 days. No credit card is required to start, and you can cancel anytime.'
    ],
    [
        'question' => 'Can I import my existing inventory data?',
        'answer' => 'Absolutely. You can upload your stock lists via CSV or Excel files. Our system will automatically map your columns to our database for a seamless transition.'
    ],
    [
        'question' => 'Is my financial data secure?',
        'answer' => 'We use bank-grade 256-bit SSL encryption to protect your data. We also perform daily backups and are fully compliant with global data privacy standards.'
    ],
    [
        'question' => 'Does {{ ucwords($frontSetting->app_name) }} support multiple currencies?',
        'answer' => 'Yes! You can issue invoices and track expenses in over 130 currencies. The system also provides real-time exchange rate updates for accurate reporting.'
    ],
    [
        'question' => 'What kind of support do you offer?',
        'answer' => 'We provide 24/7 email support for all plans. Users on our Professional and Enterprise plans also have access to live chat and a dedicated account manager.'
    ]
];
@endphp

@include('front.sections.call_to_action')

{{-- FAQ Section --}}
<section class="faq-section animate-up" data-animate>
    <div class="faq-container">
        <div class="faq-header">
            <span class="faq-tagline">Support</span>
            <h2 class="faq-title">Frequently Asked Questions</h2>
            <p class="faq-desc">Everything you need to know about the product and billing.</p>
        </div>

        <div class="faq-grid">
            @foreach($frontSetting->faq_items as $index => $faq)
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFAQ({{ $index }})" aria-expanded="false" aria-controls="answer-{{ $index }}">
                    <span>{{ $faq['question'] }}</span>
                    <div class="faq-icon" id="icon-{{ $index }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </div>
                </button>
                <div class="faq-answer" id="answer-{{ $index }}" role="region">
                    <div class="answer-content">
                        <p>{{ $faq['answer'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="faq-footer">
            <p>Still have questions? <a href="{{ route('front.contact') }}">Contact our support team</a></p>
        </div>
    </div>
</section>

@endsection

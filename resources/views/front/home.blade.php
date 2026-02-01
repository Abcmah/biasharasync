@extends('front.front_layouts')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

@endsection

@section('content')

{{-- Clients - Start --}}

{{-- <section class="logo-section">
    <div class="logo-container">
        <div class="client-heading-wrapper">
            <h4 class="client-heading">
                {{ $frontSetting->client_title ?? 'Trusted by Industry Leaders' }}
            </h4>
            <div class="heading-line"></div>
        </div>

        <div class="owl-carousel client-logo-carousel">
            @foreach ($frontClients as $frontClient)
                <div class="item">

                    <img src="{{ $frontClient->logo_url }}"
                         alt="{{ $frontClient->name }}">
                </div>
            @endforeach
        </div>
    </div>
</section> --}}
{{-- Clients - End --}}

{{-- Stats --}}
<style>
    .stats-section {
        padding: 100px 0;
        background-color: #f5f7fa;
    }

    .stats-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        text-align: center;
    }

    .stat-item {
        background: #ffffff;
        padding: 40px 20px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease;
        border: 1px solid #eef2f6;
    }

    .stat-item:hover {
        transform: translateY(-10px);
        border-color: #2563eb;
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 15px;
        display: block;
    }

    .stat-number {
        font-size: 42px;
        font-weight: 800;
        color: #303840; /* Theme Dark */
        margin-bottom: 5px;
        display: block;
    }

    .stat-label {
        font-size: 14px;
        font-weight: 600;
        color: #2563eb; /* Theme Blue */
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-desc {
        font-size: 13px;
        color: #64748b;
        margin-top: 10px;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>

<section class="stats-section">
    <div class="stats-container">
        <div style="" class="stats-grid animate-up" data-animate>
            {{-- <div class="stat-item">
                <span class="stat-icon"><i class="bi bi-person-up"></i></span>
                <span class="stat-number counter" data-target="00">0</span> --}}
                {{-- <span class="stat-number-suffix">k+</span> --}}
                {{-- <span class=" stat-label">Active Users</span>
                <p class="stat-desc">Trusted by businesses globally to manage operations.</p>
            </div> --}}

            <div class="stat-item" data-animate class="animate-zoom">
                <span class="stat-icon"><i class="bi bi-cloud-check"></i></span>
                <span class="stat-number">99.9%</span>
                <span class="stat-label">Uptime</span>
                <p class="stat-desc">Reliable infrastructure ensuring your data is always ready.</p>
            </div>

            <div class="stat-item" data-animate class="animate-zoom">
                <span class="stat-icon"><i class="bi bi-shield-check"></i></span>
                {{-- <span class="stat-number">$10M+</span> --}}
                <span class="stat-number">100%</span>
                <span class="stat-label">Secure</span>
                {{-- <span class="stat-label">Processed</span> --}}
                <p class="stat-desc">Securely handling transactions every single month.</p>
            </div>

            <div class="stat-item" data-animate class="animate-zoom">
                <span class="stat-icon"><i class="bi bi-headset"></i></span>
                <span class="stat-number">24/7</span>
                <span class="stat-label">Support</span>
                <p class="stat-desc">Our dedicated team is here whenever you need help.</p>
            </div>
        </div>
    </div>
</section>
{{-- end stats --}}
<style>
    .cta-wrapper {
        padding: 100px 0;
        background-color: #303840; /* Theme Dark */
        position: relative;
        overflow: hidden;
    }

    /* Decorative background element */
    .cta-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, rgba(48, 56, 64, 0) 70%);
        border-radius: 50%;
    }

    .cta-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        position: relative;
        z-index: 2;
    }

    .cta-content {
        flex: 1;
        max-width: 650px;
    }

    .cta-tagline {
        color: #2563eb; /* Theme Blue */
        font-weight: 700;
        text-transform: uppercase;
        font-size: 14px;
        letter-spacing: 2px;
        margin-bottom: 15px;
        display: block;
    }

    .cta-title {
        color: #ffffff;
        font-size: clamp(32px, 4vw, 48px);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 20px;
    }

    .cta-text {
        color: #94a3b8;
        font-size: 18px;
        margin-bottom: 0;
    }

    .cta-actions {
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }

    /* Buttons */
    .cta-btn {
        padding: 16px 40px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        min-width: 220px;
    }

    .cta-btn-primary {
        background-color: #2563eb; /* Theme Blue */
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
    }

    .cta-btn-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
    }

    .cta-btn-outline {
        background-color: transparent;
        color: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.1);
    }

    .cta-btn-outline:hover {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .cta-note {
        margin-top: 15px;
        color: #64748b;
        font-size: 13px;
    }

    /* Responsive Logic */
    @media (max-width: 968px) {
        .cta-container {
            flex-direction: column;
            text-align: center;
        }
        .cta-content { max-width: 100%; }
    }
</style>

<section class="cta-wrapper">
    <div class="cta-container">
        <div class="cta-content">
            <span class="cta-tagline">Boost Your Productivity</span>
            <h2 class="cta-title">Ready to streamline your business operations?</h2>
            <p class="cta-text">Join thousands of businesses that have simplified their billing and inventory management with BillTrack.</p>
        </div>

        <div class="cta-actions">
            <a href="#" class="cta-btn cta-btn-primary">Get Started Now</a>
            {{-- <a href="#" class="cta-btn cta-btn-outline">Book a 1:1 Demo</a> --}}
            <span class="cta-note">No credit card required • 14-day free trial</span>
        </div>
    </div>
</section>
{{-- Features - Start --}}
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
                                    {{-- <img src="{{ asset('front/images/check.svg') }}" alt="Check"> --}}
                                </div>
                                <span>{{ $allFeaturePoint }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="feature-col feature-visual-content">
                <div class="image-frame">
                    <img src="{{ $allHomePageFeature->image_url }}" alt="{{ $allHomePageFeature->title }}">
                </div>
            </div>
        </div>
    </section>
    @endforeach
</div>
{{-- Features - End --}}

{{-- Testimonials Section - Start --}}
<section class="testimonial-section animate-up" data-animate>
    <div class="testimonial-container">
        <div class="testimonial-header">
            <span class="testimonial-tagline">{{ $frontSetting->testimonial_title }}</span>
            <h2 class="testimonial-main-title">{{ $frontSetting->testimonial_description }}</h2>
            <div class="testimonial-divider"></div>
        </div>

        <div class="owl-carousel testimonial-carousel">
            {{-- Note: Replace $frontTestimonials with your actual variable --}}
            @foreach ($frontTestimonials as $testimonial)
                <div class="testimonial-card">
                    <div class="quote-icon">“</div>
                    <p class="testimonial-text">
                        {{ $testimonial->comment }}
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}">
                        </div>
                        <div class="author-info">
                            <h4 class="author-name">{{ $testimonial->name }}</h4>
                            <p class="author-position">sdd</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
{{-- Testimonials Section - End --}}
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
        'question' => 'Does BillTrack support multiple currencies?',
        'answer' => 'Yes! You can issue invoices and track expenses in over 130 currencies. The system also provides real-time exchange rate updates for accurate reporting.'
    ],
    [
        'question' => 'What kind of support do you offer?',
        'answer' => 'We provide 24/7 email support for all plans. Users on our Professional and Enterprise plans also have access to live chat and a dedicated account manager.'
    ]
];
@endphp


@include('front.sections.call_to_action')
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
                <button class="faq-question" onclick="toggleFAQ({{ $index }})">
                    <span>{{ $faq['question'] }}</span>
                    <div class="faq-icon" id="icon-{{ $index }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </div>
                </button>
                <div class="faq-answer" id="answer-{{ $index }}">
                    <div class="answer-content">
                        <p>{{ $faq['answer'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="faq-footer">
            <p>Still have questions? <a href="#">Contact our support team</a></p>
        </div>
    </div>
</section>


@endsection

@extends('front.front_layouts')


@section('content')

<section class="pricing-section animate-up" data-animate>
    <div class="pricing-container">
        <div class="pricing-header">
            <h2 class="pricing-main-title">{{ $frontSetting->price_title }}</h2>
            <p class="pricing-sub-text">{{ $frontSetting->price_description }}</p>

            <div class="pricing-toggle-wrapper">
                <span class="toggle-label {{ !isset($isYearly) ? 'active' : '' }}">{{ $frontSetting->pricing_monthly_text ?? 'Monthly' }}</span>
                <label class="switch">
                    <input type="checkbox" id="pricing-switch" onchange="togglePricing()">
                    <span class="slider round"></span>
                </label>
                <span class="toggle-label">{{ $frontSetting->pricing_yearly_text ?? 'Yearly' }}</span>
            </div>
        </div>

        <div class="plans-grid">
            @foreach ($subscriptionPlans as $subscriptionPlan)
                <div class="plan-card {{ $subscriptionPlan->is_popular ? 'plan-popular' : '' }}">
                    @if($subscriptionPlan->is_popular)
                        <div class="popular-badge">Most Popular</div>
                    @endif

                    <div class="plan-header">
                        <span class="plan-name">{{ $subscriptionPlan->name }}</span>
                        <div class="plan-price">
                            @if($subscriptionPlan->default == 'yes')
                                <h3 class="price-amount">{{ $frontSetting->pricing_free_text }}</h3>
                            @else
                                <h3 class="price-amount monthly_price">
                                    {{ \App\SuperAdmin\Classes\SuperAdminCommon::formatAmountCurrency($subscriptionPlan->monthly_price) }}<small>/{{ $frontSetting->pricing_month_text }}</small>
                                </h3>
                                <h3 class="price-amount yearly_price" style="display: none;">
                                    {{ \App\SuperAdmin\Classes\SuperAdminCommon::formatAmountCurrency($subscriptionPlan->annual_price) }}<small>/{{ $frontSetting->pricing_year_text ?? 'year' }}</small>
                                </h3>
                            @endif
                        </div>
                        <p class="plan-billing-info">
                            @if($subscriptionPlan->default == 'yes')
                                {{ $frontSetting->pricing_no_card_text }}
                            @else
                                <span class="monthly_price">{{ $frontSetting->pricing_billed_monthly_text ?? 'Billed Monthly' }}</span>
                                <span class="yearly_price" style="display: none;">{{ $frontSetting->pricing_billed_yearly_text ?? 'Billed Yearly' }}</span>
                            @endif
                        </p>
                    </div>

                    <p class="plan-desc">{{ $subscriptionPlan->description }}</p>

                    <ul class="plan-features">
                        @foreach ($subscriptionPlan->features as $subscriptionPlanFeature)
                            <li>
                                <img src="{{ asset('front/images/check.svg') }}" alt="check">
                                <span>{{ $subscriptionPlanFeature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="plan-footer">
                        <a href="{{ route('front.register') }}" class="plan-btn">
                            {{ $frontSetting->pricing_get_started_button_text }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- <div class="pricing-trust-section">
            <p class="trust-title">{{ $frontSetting->price_card_title }}</p>
            <div class="trust-logos">
                @foreach ($pricingCards as $pricingCard)
                    <div class="trust-logo-item">
                        <img src="{{ $pricingCard->logo_url }}" alt="{{ $pricingCard->name }}">
                    </div>
                @endforeach
            </div>
        </div> --}}
    </div>
</section>


@include('front.sections.call_to_action')

@endsection

@section('scripts')
<script>
function togglePricing() {
    const isYearly = document.getElementById('pricing-switch').checked;
    const monthlyDivs = document.querySelectorAll('.monthly_price');
    const yearlyDivs = document.querySelectorAll('.yearly_price');

    if (isYearly) {
        monthlyDivs.forEach(div => div.style.display = 'none');
        yearlyDivs.forEach(div => div.style.display = 'block');
    } else {
        monthlyDivs.forEach(div => div.style.display = 'block');
        yearlyDivs.forEach(div => div.style.display = 'none');
    }
}
</script>
@endsection

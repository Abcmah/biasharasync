@extends('front.front_layouts')

@section('content')

{{-- Features Page - Start --}}
<div class="features-page-wrapper">
    @foreach ($allFeatures as $allFeature)
    <section class="feature-category-section animate-up {{ $loop->even ? 'bg-light' : '' }}"  data-animate>
        <div class="features-container">
            <div class="category-header">
                <h2 class="category-title">{{ $allFeature->title }}</h2>
                <p class="category-description">{{ $allFeature->description }}</p>
                <div class="category-accent"></div>
            </div>

            <div class="inner-features-grid">
                @foreach ($allFeature->features as $innerFeature)
                <div class="feature-service-card">
                    <div class="feature-card-image">
                        <img src="{{ $innerFeature['image_url'] }}" alt="{{ $innerFeature['title'] }}">

                    </div>
                    <div class="feature-card-content">
                        <h3 class="feature-card-title">{{ $innerFeature['title'] }}</h3>
                        <p class="feature-card-desc">{{ $innerFeature['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endforeach
</div>



@include('front.sections.call_to_action')

@endsection

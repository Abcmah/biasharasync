@extends('front.front_layouts')

@section('content')
@include('front.includes.breadcrumb')

<section class="legal-page-section animate-up" data-animate>
    <div class="legal-container">
        <div class="legal-card">
            @if(isset($frontPageDetails) && $frontPageDetails)
                <div class="legal-content">
                    {!! $frontPageDetails->content !!}
                </div>
            @else
                <div class="legal-content">
                    <p>Page not found.</p>
                </div>
            @endif
        </div>
    </div>
</section>

@include('front.sections.call_to_action')
@endsection

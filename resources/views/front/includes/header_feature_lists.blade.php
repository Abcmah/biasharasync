{{-- @foreach ($headerFeatures as $headerFeature)
    @if($loop->index == 0)
        <div class="features-row">
            @include('front.includes.header_feature')
        </div>
    @else
        @if(($loop->index % 2) != 0)
        <div class="features-row @if(!$loop->last) features-row-constrained @endif @if(($loop->iteration % 4) == 0) features-row-offset-xl @endif">
        @endif
            @include('front.includes.header_feature', ['divideMultiple' => true])
        @if(($loop->index % 2) == 0)
        </div>
        @endif
    @endif
@endforeach --}}
<div style="background: red" class="hero-visual">
    <div class="hero-visual-content">
        @foreach ($headerFeatures as $index => $headerFeature)
            @php
                // Determine card styling
                $isAccent = ($index % 3 == 1); // Every 3rd card gets accent
                $isLightAccent = ($index % 4 == 2); // Every 4th card gets light accent
                $cardClass = $isAccent ? 'card-accent' : ($isLightAccent ? 'card-light-accent' : '');
            @endphp

            <div class="dashboard-card card-position-{{ $index }} {{ $cardClass }}">
                @if($headerFeature->image_url ?? false)
                    <div class="card-icon">
                        <img src="{{ $headerFeature->image_url }}" alt="{{ $headerFeature->name }}">
                    </div>
                @endif

                <div class="card-header">{{ $headerFeature->name }}</div>

                <div class="card-amount">
                    {{ $headerFeature->value ?? $headerFeature->short_description ?? 'N/A' }}
                </div>

                @if($headerFeature->description ?? false)
                    <div class="card-description">{{ $headerFeature->description }}</div>
                @endif
            </div>
        @endforeach
    </div>
</div>

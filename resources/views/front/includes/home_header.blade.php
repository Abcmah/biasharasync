<section class="hero">
    <div class="container">
        <div class="hero-content animate-up" data-animate>
            <div class="hero-text">
                {{ $frontSetting->header_sub_title }}
                {{-- <span style="color: var(--primary)"></span> --}}
                <h1> {{ $frontSetting->header_title }}</h1>
                <p>{{ $frontSetting->header_description }}</p>
                @if ($frontSetting->header_button1_show == 1)
                    <div class="hero-buttons">
                        <a href="{{ $frontSetting->header_button1_url }}"
                            class="btn btn-primary">{{ $frontSetting->header_button2_text }}</a>
                    </div>
                @endif
            </div>
            <div class="hero-visual">
                @php

                    $tier1 = $headerFeatures->slice(0, 1);
                    $tier2 = $headerFeatures->slice(1, 2);
                    $tier3 = $headerFeatures->slice(3, 3);
                    $getIcon = function ($feature) {
                        return match (true) {
                            str_contains(strtolower($feature->name), 'bill') => '📑',
                            str_contains(strtolower($feature->name), 'stock') => '📦',
                            str_contains(strtolower($feature->name), 'money') => '💰',
                            str_contains(strtolower($feature->name), 'secure') => '🔒',
                            str_contains(strtolower($feature->name), 'chart') => '📈',
                            default => '✨',
                        };
                    };
                @endphp

                @foreach ($tier1 as $feature)
                    <div class="tier-1 animate-zoom" data-animate>
                        <div class="p-card tier-1">
                            @if ($feature->image_url ?? false)
                                <div class="card-icon">
                                    <img src="{{ $feature->image_url }}" alt="{{ $feature->name }}">
                                </div>
                            @else
                                <i>{{ $getIcon($feature) }}</i>
                            @endif
                            <h4>{{ $feature->name }}</h4>
                            <p>{{ Str::limit($feature->description, 40) }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="tier-2 animate-zoom" data-animate>
                    @foreach ($tier2 as $feature)
                        <div class="p-card">
                            @if ($feature->image_url ?? false)
                                <div class="card-icon">
                                    <img src="{{ $feature->image_url }}" alt="{{ $feature->name }}">
                                </div>
                            @else
                                <i>{{ $getIcon($feature) }}</i>
                            @endif
                            <h4>{{ $feature->name }}</h4>
                            <p>{{ Str::limit($feature->description, 40) }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="tier-3 animate-zoom" data-animate>
                    @foreach ($tier3 as $feature)
                        <div class="p-card">
                            @if ($feature->image_url ?? false)
                                <div class="card-icon">
                                    <img src="{{ $feature->image_url }}" alt="{{ $feature->name }}">
                                </div>
                            @else
                                <i>{{ $getIcon($feature) }}</i>
                            @endif
                            <h4>{{ $feature->name }}</h4>
                            <p>{{ Str::limit($feature->description, 40) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

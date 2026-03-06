<section>
    <nav>
        <div class="container">
            <div class="nav-content">
                <div class="logo-con">
                    <a href="{{ route('front.index') }}">
                        <img src="{{ $frontSetting->header_logo_url }}" alt="{{ $frontSetting->app_name }}">
                    </a>
                </div>

                <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation" aria-expanded="false">&#9776;</button>
                <ul class="nav-links" id="navLinks">
                    <li>
                        <a class="nav-link {{ request()->routeIs('front.index') ? 'active' : '' }}" href="{{ route('front.index') }}">{{ $frontSetting->home_text }}</a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('front.features') ? 'active' : '' }}" href="{{ route('front.features') }}">{{ $frontSetting->features_text }}</a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('front.pricing') ? 'active' : '' }}" href="{{ route('front.pricing') }}">{{ $frontSetting->pricing_text }}</a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('front.contact') ? 'active' : '' }}" href="{{ route('front.contact') }}">{{ $frontSetting->contact_text }}</a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('front.blog.*') ? 'active' : '' }}" href="{{ route('front.blog.index') }}">Blog</a>
                    </li>
                    @if ($frontSetting->login_button_show == 1)
                        <li>
                            <a href="{{ route('main', ['path' => 'admin/login']) }}" class="btn-sm btn-primary" style="color: white;">{{ $frontSetting->login_button_text }}</a>
                        </li>
                    @endif
                    @if ($frontSetting->register_button_show == 1)
                        <li>
                            <a href="{{ route('front.register') }}" class="btn-sm btn-primary-outline">{{ $frontSetting->register_button_text }}</a>
                        </li>
                    @endif
                    <li class="lang-item">
                        <div class="dropdown">
                            <button class="dropdown-button">
                                <img src="{{ $selectedLang->image_url }}" alt="{{ $langKey }}">
                                <span>{{ $langKey }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path>
                                </svg>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach ($allLangs as $allLang)
                                    <li class="dropdown-item">
                                        <a href="javascript:void(0)" onclick="changeLang('{{ $allLang->key }}')" class="dropdown-link">
                                            <img src="{{ $allLang->image_url }}" alt="{{ $allLang->key }}">
                                            <span>{{ $allLang->key }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @if ($showFullHeader)
        <div class="container mx-auto px-8">
            @include('front.includes.home_header')
        </div>
    @else
        @include('front.includes.breadcrumb')
    @endif
</section>

<section>
    <nav>
        <div class="container">
            <div class="nav-content">
                <div class="logo-con">
                    <a href="{{ route('front.index') }}">
                        <img src="{{ $frontSetting->header_logo_url }}" alt="">
                    </a>
                </div>

                <button class="menu-toggle" id="menuToggle">☰</button>
                <ul class="nav-links" id="navLinks">
                    <li> <a class="nav-link" href="{{ route('front.index') }}">
                            {{ $frontSetting->home_text }}
                        </a></li>
                    <li> <a class="nav-link" href="{{ route('front.features') }}">
                            {{ $frontSetting->features_text }}
                        </a></li>
                    <li><a class="nav-link" href="{{ route('front.pricing') }}">
                            {{ $frontSetting->pricing_text }}
                        </a>
                    </li>
                    @if ($frontSetting->login_button_show == 1)
                        <li>
                            <a href="{{ route('main', ['path' => 'admin/login']) }}" class="btn-sm btn-primary"
                                style="color: white;">{{ $frontSetting->login_button_text }}</a>
                        </li>
                    @endif
                    @if ($frontSetting->register_button_show == 1)
                        <li>
                            <a href="{{ route('front.register') }}" class="btn-sm btn-primary-outline"
                                style="color: white;">{{ $frontSetting->register_button_text }}</a>
                        </li>
                    @endif
                    <div class="dropdown">
                        <button class="dropdown-button">
                            <img src="{{ $selectedLang->image_url }}" alt="">
                            <span>{{ $langKey }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z">
                                </path>
                            </svg>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach ($allLangs as $allLang)
                                <li class="dropdown-item">
                                    <a href="javascript:void(0);changeLang('{{ $allLang->key }}')"
                                        class="dropdown-link">
                                        <img src="{{ $allLang->image_url }}" alt="">
                                        <span>{{ $allLang->key }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
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
 <script>
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        const mainNav = document.getElementById('mainNav');

        // Toggle menu on click
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navLinks.classList.toggle('active');
            menuToggle.textContent = navLinks.classList.contains('active') ? '✕' : '☰';
        });

        // Close if clicked outside
        document.addEventListener('click', (event) => {
            const isClickInsideMenu = navLinks.contains(event.target);
            const isClickOnToggle = menuToggle.contains(event.target);

            if (!isClickInsideMenu && !isClickOnToggle && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                menuToggle.textContent = '☰';
            }
        });

        // Close menu when a link is clicked
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                menuToggle.textContent = '☰';
            });
        });
    </script>
</section>

<section class="final-cta-section animate-up" data-animate>
    <div class="final-cta-container">
        <div class="final-cta-header">
            <h2 class="cta-main-title">{{ $frontSetting->call_to_action_title }}</h2>
            <p class="cta-sub-text">{{ $frontSetting->call_to_action_description }}</p>
        </div>

        <div class="cta-widget-grid">
            @foreach ($frontSetting->call_to_action_widgets as $callToActionWidget)
                <div class="cta-widget-item">
                    <div class="widget-icon">
                        <svg width="20" height="20" viewbox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="2"></circle>
                            <path d="M7 13L10 16L17 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div class="widget-content">
                        <h3 class="widget-value">{{ $callToActionWidget['value'] }}</h3>
                        <p class="widget-title">{{ $callToActionWidget['title'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="cta-form-wrapper">
            {{ html()->form('POST', '')->class(['ajax-form'])->id('callToAction')->open() }}
                <div class="modern-input-group">
                    <input type="email"
                           class="cta-input"
                           placeholder="{{ $frontSetting->call_to_action_email_text }}"
                           id="action_email"
                           name="action_email"
                           required>
                    <button class="cta-submit-btn" type="submit" onclick="callToAction();return false">
                        {{ $frontSetting->call_to_action_submit_button_text }}
                    </button>
                </div>
            {{ html()->form()->close() }}

            <div class="cta-security-note">
                <svg width="16" height="16" viewbox="0 0 18 18" fill="none">
                    <path d="M9 1.5C6.9 1.5 5.25 3.1 5.25 5.25V7.5H4.5C3.25 7.5 2.25 8.5 2.25 9.75V14.25C2.25 15.5 3.25 16.5 4.5 16.5H13.5C14.75 16.5 15.75 15.5 15.75 14.25V9.75C15.75 8.5 14.75 7.5 13.5 7.5H12.75V5.25C12.75 3.1 11.1 1.5 9 1.5ZM6.75 5.25C6.75 4 7.75 3 9 3C10.25 3 11.25 4 11.25 5.25V7.5H6.75V5.25Z" fill="currentColor"></path>
                </svg>
                <span>{{ $frontSetting->call_to_action_no_email_sell_text }}</span>
            </div>
        </div>
    </div>
</section>

@extends('front.front_layouts')

@section('content')
<section class="contact-combined-section">
    {{-- Decorative background split --}}
    <div class="section-bg-split"></div>

    <div class="contact-container">
        <div class="contact-split-grid">

            {{-- Left Side: Info --}}
            <div class="contact-sidebar">
                <div class="sidebar-header">
                    <h2 class="contact-main-title">{{ $frontSetting->contact_title }}</h2>
                    <p class="contact-sub-text">{{ $frontSetting->contact_description }}</p>
                </div>

                <div class="sidebar-info-list">
                    <div class="info-item-horizontal">
                        <div class="mini-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></div>
                        <div>
                            <h4 class="info-label">{{ $frontSetting->contact_email_text }}</h4>
                            <p class="info-value">{{ $frontSetting->contact_email }}</p>
                        </div>
                    </div>

                    <div class="info-item-horizontal">
                        <div class="mini-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></div>
                        <div>
                            <h4 class="info-label">{{ $frontSetting->contact_phone_text }}</h4>
                            <p class="info-value">{{ $frontSetting->contact_phone }}</p>
                        </div>
                    </div>

                    <div class="info-item-horizontal">
                        <div class="mini-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></div>
                        <div>
                            <h4 class="info-label">{{ $frontSetting->contact_address_text }}</h4>
                            <p class="info-value">{{ $frontSetting->contact_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Form --}}
            <div class="contact-form-main">
                <div class="form-card">
                    <h3 class="form-heading-text">{{ $frontSetting->contact_form_title }}</h3>
                    <p class="form-sub-text">{{ $frontSetting->contact_form_description }}</p>

                    <div id="alert" class="modern-alert-container"></div>

                    {{ html()->form('POST', '')->id('ajax-contact-form')->open() }}
                        <div class="form-grid-compact">
                            <div class="input-group">
                                <input class="modern-input" name="name" id="name" type="text" placeholder="{{ $frontSetting->contact_form_name_text }}" required>
                            </div>
                            <div class="input-group">
                                <input class="modern-input" name="email" id="email" type="email" placeholder="{{ $frontSetting->contact_form_email_text }}" required>
                            </div>
                            <div class="input-group full-width">
                                <textarea class="modern-textarea" name="message" id="message" rows="4" placeholder="{{ $frontSetting->contact_form_message_text }}" required></textarea>
                            </div>
                            <button class="contact-submit-btn" type="submit" onclick="updateContactForm();return false">
                                {{ $frontSetting->contact_form_send_message_text }}
                            </button>
                        </div>
                    {{ html()->form()->close() }}
                </div>
            </div>

        </div>
    </div>
</section>
@include('front.sections.call_to_action')
@endsection


@section('scripts')

    <script>
    "use strict";

    function updateContactForm() {
        art.sendXhr({
            url: '{{route('front.submit-contact-form')}}',
            type: "POST",
            file: true,
            container: "#ajax-contact-form",
            disableButton: true,
            messageLocation: 'inline', // This targets the #alert div
            success: function(response) {
                if(response.status == 'success'){
                    // Optional: Fade out the form or clear it
                    $('#ajax-contact-form')[0].reset();
                    // Custom success message styling via JS if needed
                    $('#alert').html('<div class="alert alert-success">✅ Your message has been sent successfully!</div>');
                }
            },
            error: function(response) {
                // Error handling
                $('#alert').html('<div class="alert alert-danger">❌ Something went wrong. Please try again.</div>');
            }
        });
    }
</script>
@endsection

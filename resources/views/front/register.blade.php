@extends('front.front_layouts')

@section('content')
<section class="auth-centered-section animate-up" data-animate>
    <div class="auth-card">
        <div class="auth-header">
            <h2 class="auth-title">{{ $frontSetting->register_title }}</h2>
            <p class="auth-subtitle">{{ $frontSetting->register_description }}</p>
        </div>

        {{ html()->form('POST', route('front.register.save'))->id('ajax-register-form')->open() }}
            {{-- Standard alert for general messages --}}
            <div id="alert-container"></div>

            <div class="form-grid">
                <div class="form-group full-width" id="group-company_name">
                    <label class="form-label">{{ $frontSetting->register_company_name_text }}</label>
                    <input class="auth-input" type="text" name="company_name" id="company_name" required>
                    <div class="error-message"></div>
                </div>

                <div class="form-group" id="group-company_email">
                    <label class="form-label">{{ $frontSetting->register_email_text }}</label>
                    <input class="auth-input" type="email" name="company_email" id="company_email" value="{{ $actionEmail }}" required>
                    <div class="error-message"></div>
                </div>

                <div class="form-group" id="group-company_phone">
                    <label class="form-label">{{ $frontSetting->register_phone_text ?? 'Phone' }}</label>
                    <input class="auth-input" type="text" name="company_phone" id="company_phone" required>
                    <div class="error-message"></div>
                </div>

                <div class="form-group" id="group-password">
                    <label class="form-label">{{ $frontSetting->register_password_text }}</label>
                    <input class="auth-input" type="password" name="password" id="password" required>
                    <div class="error-message"></div>
                </div>

                <div class="form-group" id="group-confirm_password">
                    <label class="form-label">{{ $frontSetting->register_confirm_password_text }}</label>
                    <input class="auth-input" type="password" name="confirm_password" id="confirm_password" required>
                    <div class="error-message"></div>
                </div>

                <div class="form-group full-width" id="group-condition">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="condition" name="condition" class="custom-checkbox">
                        <label for="condition" class="checkbox-label">
                            I agree to the <a href="{{ $frontSetting->register_agree_url }}" target="_blank">{{ $frontSetting->register_agree_text }}</a>
                        </label>
                    </div>
                    <div class="error-message"></div>
                </div>

                <div class="form-group full-width">
                    <button class="auth-submit-btn" type="submit" id="submit-btn">
                        <span>{{ $frontSetting->register_submit_button_text }}</span>
                    </button>
                </div>
            </div>
        {{ html()->form()->close() }}
    </div>
</section>
@endsection

@section('scripts')
<script>
document.getElementById('ajax-register-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const btn = document.getElementById('submit-btn');
    const alertContainer = document.getElementById('alert-container');
    const formData = new FormData(form);

    // Reset States
    btn.disabled = true;
    btn.innerText = 'Processing...';
    document.querySelectorAll('.form-group').forEach(el => el.classList.remove('has-error'));
    document.querySelectorAll('.error-message').forEach(el => el.innerText = '');
    alertContainer.innerHTML = '';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(async response => {
        const data = await response.json();

        if (response.status === 422) { // Validation Errors
            btn.disabled = false;
            btn.innerText = '{{ $frontSetting->register_submit_button_text }}';

            // Loop through errors and apply to groups
            Object.keys(data.errors).forEach(key => {
                const group = document.getElementById(`group-${key}`);
                if (group) {
                    group.classList.add('has-error');
                    group.querySelector('.error-message').innerText = data.errors[key][0];
                }
            });
        }
        else if (data.status === 'success') {
            alertContainer.innerHTML = `<div class="alert alert-success">Registration successful! Redirecting...</div>`;
            setTimeout(() => {
                window.location.href = data.redirect_url || '/dashboard';
            }, 2000);
        }
        else {
            throw new Error(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerText = '{{ $frontSetting->register_submit_button_text }}';
        alertContainer.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
    });
});
</script>
@endsection

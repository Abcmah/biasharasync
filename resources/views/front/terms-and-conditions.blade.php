@extends('front.front_layouts')

@section('content')

<section class="legal-page-section animate-up" data-animate>
    <div class="legal-container">
        <div class="legal-card">
            <div class="legal-meta">
                <span class="legal-badge">Legal</span>
                <span class="legal-date">Last updated: {{ date('F d, Y') }}</span>
            </div>

            <div class="legal-content">
                <h2>1. Introduction</h2>
                <p>Welcome to <strong>{{ ucwords($frontSetting->app_name) }}</strong>. These Terms and Conditions govern your use of our platform, services, and any related applications (collectively referred to as the "Service"). By accessing or using the Service, you agree to be bound by these Terms. If you do not agree, please do not use the Service.</p>

                <h2>2. Definitions</h2>
                <ul>
                    <li><strong>"Company"</strong> refers to {{ ucwords($frontSetting->app_name) }}, the provider of the Service.</li>
                    <li><strong>"User"</strong> or <strong>"You"</strong> refers to any individual or entity accessing or using the Service.</li>
                    <li><strong>"Account"</strong> refers to the unique account created for you to access our Service.</li>
                    <li><strong>"Subscription"</strong> refers to the paid plan that grants access to premium features of the Service.</li>
                </ul>

                <h2>3. Account Registration</h2>
                <p>To use certain features of the Service, you must register for an account. You agree to:</p>
                <ul>
                    <li>Provide accurate, current, and complete information during registration.</li>
                    <li>Maintain the security of your password and account credentials.</li>
                    <li>Accept responsibility for all activities that occur under your account.</li>
                    <li>Notify us immediately of any unauthorized use of your account.</li>
                </ul>

                <h2>4. Use of Service</h2>
                <p>You agree to use the Service only for lawful purposes and in accordance with these Terms. You shall not:</p>
                <ul>
                    <li>Use the Service in any way that violates any applicable law or regulation.</li>
                    <li>Attempt to gain unauthorized access to any part of the Service.</li>
                    <li>Use the Service to transmit any malware, viruses, or harmful code.</li>
                    <li>Reproduce, duplicate, copy, sell, or exploit any portion of the Service without express written permission.</li>
                    <li>Use automated systems or bots to access the Service without prior consent.</li>
                </ul>

                <h2>5. Subscription & Payments</h2>
                <p>Some features of the Service require a paid subscription. By subscribing, you agree to:</p>
                <ul>
                    <li>Pay all fees associated with your selected subscription plan.</li>
                    <li>Provide valid and up-to-date payment information.</li>
                    <li>Accept that subscription fees are billed in advance on a recurring basis (monthly or annually, depending on your plan).</li>
                </ul>
                <p>We reserve the right to modify subscription pricing with prior notice. Continued use of the Service after a price change constitutes acceptance of the new pricing.</p>

                <h2>6. Free Trial</h2>
                <p>We may offer a free trial period for new users. At the end of the trial period, your account will be subject to the applicable subscription fees unless you cancel before the trial expires.</p>

                <h2>7. Cancellation & Refunds</h2>
                <p>You may cancel your subscription at any time through your account settings. Upon cancellation:</p>
                <ul>
                    <li>You will retain access to premium features until the end of your current billing period.</li>
                    <li>No refunds will be provided for partial billing periods unless required by law.</li>
                    <li>Your data will be retained for a reasonable period to allow for reactivation.</li>
                </ul>

                <h2>8. Data & Privacy</h2>
                <p>Your use of the Service is also governed by our Privacy Policy. By using the Service, you consent to the collection, use, and processing of your data as described in our Privacy Policy. We are committed to protecting your data and maintaining its confidentiality.</p>

                <h2>9. Intellectual Property</h2>
                <p>All content, features, and functionality of the Service — including but not limited to text, graphics, logos, icons, software, and design — are the exclusive property of {{ ucwords($frontSetting->app_name) }} and are protected by intellectual property laws. You may not use, reproduce, or distribute any content from the Service without prior written permission.</p>

                <h2>10. User Content</h2>
                <p>You retain ownership of any data or content you upload to the Service. By uploading content, you grant us a limited, non-exclusive license to store, process, and display your content solely for the purpose of providing the Service. We will not share your content with third parties except as necessary to operate the Service or as required by law.</p>

                <h2>11. Service Availability</h2>
                <p>We strive to maintain high availability of the Service but do not guarantee uninterrupted access. We reserve the right to:</p>
                <ul>
                    <li>Perform scheduled and emergency maintenance.</li>
                    <li>Modify, suspend, or discontinue any part of the Service with reasonable notice.</li>
                    <li>Limit access during periods of high demand or security threats.</li>
                </ul>

                <h2>12. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, {{ ucwords($frontSetting->app_name) }} shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or business opportunities, arising from your use of or inability to use the Service.</p>

                <h2>13. Indemnification</h2>
                <p>You agree to indemnify and hold harmless {{ ucwords($frontSetting->app_name) }}, its officers, directors, employees, and agents from any claims, damages, losses, or expenses (including legal fees) arising from your use of the Service or violation of these Terms.</p>

                <h2>14. Termination</h2>
                <p>We may terminate or suspend your account and access to the Service at our sole discretion, without prior notice, for conduct that we determine violates these Terms, is harmful to other users, or is otherwise objectionable.</p>

                <h2>15. Changes to Terms</h2>
                <p>We reserve the right to modify these Terms at any time. We will notify users of significant changes via email or through the Service. Your continued use of the Service after changes are posted constitutes acceptance of the revised Terms.</p>

                <h2>16. Governing Law</h2>
                <p>These Terms shall be governed by and construed in accordance with the laws of the jurisdiction in which {{ ucwords($frontSetting->app_name) }} operates, without regard to its conflict of law provisions.</p>

                <h2>17. Contact Us</h2>
                <p>If you have any questions about these Terms and Conditions, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> {{ $frontSetting->contact_email }}</li>
                    <li><strong>Phone:</strong> {{ $frontSetting->contact_phone }}</li>
                    <li><strong>Address:</strong> {{ $frontSetting->contact_address }}</li>
                </ul>
            </div>
        </div>
    </div>
</section>

@include('front.sections.call_to_action')
@endsection

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="text-center mb-4"><i class="bi bi-shield-check"></i> <?= e(__('legal.privacy_title')) ?></h3>
                <p class="text-muted small text-center">Last updated: March 23, 2026</p>

                <h5>1. Information We Collect</h5>
                <p>We collect the following categories of information:</p>
                <ul>
                    <li><strong>Account Information:</strong> Name, email address, phone number (optional)</li>
                    <li><strong>Health Data:</strong> Weight, calories, macronutrients, heart rate, blood sugar, exercise, medications, appointments</li>
                    <li><strong>CGM Data:</strong> Continuous glucose monitor readings (if connected)</li>
                    <li><strong>Usage Data:</strong> Login timestamps, IP addresses, and audit trail for HIPAA compliance</li>
                </ul>

                <h5>2. How We Protect Your Data</h5>
                <p>We implement industry-leading security measures:</p>
                <ul>
                    <li><strong>Encryption at Rest:</strong> All personal and health data is encrypted using AES-256-CBC</li>
                    <li><strong>Encryption in Transit:</strong> All connections are secured with TLS/SSL (HTTPS)</li>
                    <li><strong>Password Security:</strong> Passwords are hashed using bcrypt with cost factor 12</li>
                    <li><strong>Two-Factor Authentication:</strong> Optional 2FA via email or SMS</li>
                    <li><strong>Session Security:</strong> HTTP-only cookies, strict same-site policy, 30-minute timeout</li>
                    <li><strong>Rate Limiting:</strong> Protection against brute-force attacks</li>
                    <li><strong>Audit Logging:</strong> All data access and modifications are logged per HIPAA requirements</li>
                </ul>

                <h5>3. How We Use Your Information</h5>
                <p>Your data is used exclusively to:</p>
                <ul>
                    <li>Provide the health tracking features you use</li>
                    <li>Send verification emails, 2FA codes, and appointment reminders</li>
                    <li>Maintain HIPAA-required audit trails</li>
                    <li>Improve Service reliability and security</li>
                </ul>
                <p><strong>We do not sell, share, or monetize your personal health information.</strong></p>

                <h5>4. Data Sharing</h5>
                <p>We only share your data in these circumstances:</p>
                <ul>
                    <li><strong>With Your Providers:</strong> Only when you explicitly invite a healthcare provider through the Provider Portal</li>
                    <li><strong>Legal Requirements:</strong> When required by law, court order, or to protect public safety</li>
                </ul>

                <h5>5. Your Rights</h5>
                <p>You have the right to:</p>
                <ul>
                    <li>Access your health data at any time</li>
                    <li>Export your data in CSV format</li>
                    <li>Request correction of inaccurate data</li>
                    <li>Delete your account and all associated data</li>
                    <li>Revoke provider access at any time</li>
                </ul>

                <h5>6. Data Retention</h5>
                <p>Your data is retained for as long as your account is active. Audit logs are retained for a minimum of 6 years per HIPAA requirements. Upon account deletion, all personal and health data is permanently removed.</p>

                <h5>7. Children's Privacy</h5>
                <p>VQ Healthy is not intended for use by individuals under 18. We do not knowingly collect information from children.</p>

                <h5>8. Contact</h5>
                <p>For privacy inquiries: <a href="mailto:email4johnson@gmail.com">email4johnson@gmail.com</a></p>

                <div class="text-center mt-4">
                    <span class="badge bg-success hipaa-badge">
                        <i class="bi bi-shield-lock"></i> <?= e(__('hipaa_notice')) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

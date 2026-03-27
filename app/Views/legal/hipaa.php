<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="text-center mb-4"><i class="bi bi-hospital"></i> <?= e(__('legal.hipaa_title')) ?></h3>
                <p class="text-muted small text-center">Last updated: March 23, 2026</p>

                <div class="alert alert-success">
                    <i class="bi bi-shield-lock"></i>
                    <strong>VQ Healthy is designed to be HIPAA compliant.</strong> We protect your Protected Health Information (PHI) with the same standards used by healthcare organizations.
                </div>

                <h5>1. What is HIPAA?</h5>
                <p>The Health Insurance Portability and Accountability Act (HIPAA) is a federal law that requires safeguards to protect the privacy and security of personal health information.</p>

                <h5>2. Protected Health Information (PHI)</h5>
                <p>PHI includes any individually identifiable health information. In VQ Healthy, this includes:</p>
                <ul>
                    <li>Your name and contact information</li>
                    <li>Health metrics (weight, blood sugar, heart rate, etc.)</li>
                    <li>Medication records and prescriber information</li>
                    <li>Appointment details</li>
                    <li>CGM (glucose monitor) data</li>
                </ul>

                <h5>3. How We Protect Your PHI</h5>
                <table class="table table-sm">
                    <thead><tr><th>Safeguard</th><th>Implementation</th></tr></thead>
                    <tbody>
                        <tr><td>Encryption at Rest</td><td>AES-256-CBC for all PHI fields</td></tr>
                        <tr><td>Encryption in Transit</td><td>TLS 1.2+ (HTTPS required)</td></tr>
                        <tr><td>Access Controls</td><td>Session-based authentication with 2FA option</td></tr>
                        <tr><td>Audit Controls</td><td>All access and modifications logged with timestamp, user, IP</td></tr>
                        <tr><td>Integrity Controls</td><td>CSRF protection, input sanitization, prepared statements</td></tr>
                        <tr><td>Transmission Security</td><td>SSL/TLS for all data transmission</td></tr>
                        <tr><td>Password Security</td><td>bcrypt hashing with cost factor 12</td></tr>
                    </tbody>
                </table>

                <h5>4. Your Rights Under HIPAA</h5>
                <ul>
                    <li><strong>Right to Access:</strong> You can view and export your health data at any time</li>
                    <li><strong>Right to Amendment:</strong> You can request correction of your health records</li>
                    <li><strong>Right to an Accounting of Disclosures:</strong> You can request a record of who has accessed your data</li>
                    <li><strong>Right to Restrict Disclosures:</strong> You control which providers can access your information</li>
                    <li><strong>Right to Confidential Communications:</strong> You choose how we contact you</li>
                </ul>

                <h5>5. Breach Notification</h5>
                <p>In the unlikely event of a data breach affecting your PHI, we will:</p>
                <ul>
                    <li>Notify you within 60 days of discovering the breach</li>
                    <li>Provide details about what information was affected</li>
                    <li>Describe steps we are taking to address the breach</li>
                    <li>Report to the HHS Secretary as required by law</li>
                </ul>

                <h5>6. Minimum Necessary Standard</h5>
                <p>We access, use, and disclose only the minimum amount of PHI necessary for the intended purpose. Provider connections only expose your medication list — not your full health data.</p>

                <h5>7. Contact Our Privacy Officer</h5>
                <p>For HIPAA-related questions or to exercise your rights:</p>
                <p><strong>VisionQuest Services LLC</strong><br>
                Email: <a href="mailto:email4johnson@gmail.com">email4johnson@gmail.com</a></p>

                <div class="text-center mt-4">
                    <span class="badge bg-success hipaa-badge">
                        <i class="bi bi-shield-lock"></i> <?= e(__('hipaa_notice')) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

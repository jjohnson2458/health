<div class="row justify-content-center">
    <div class="col-md-10">
        <h4 class="mb-4"><i class="bi bi-book"></i> <?= e(__('nav.guide')) ?></h4>

        <div class="accordion" id="guideAccordion">
            <!-- Getting Started -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#guide1">
                        <i class="bi bi-rocket me-2"></i> Getting Started
                    </button>
                </h2>
                <div id="guide1" class="accordion-collapse collapse show" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <ol>
                            <li><strong>Register</strong> with your email address, first name, and last name.</li>
                            <li><strong>Verify</strong> your email by clicking the link sent to your inbox.</li>
                            <li><strong>Log in</strong> with your email and password. A 6-digit verification code will be sent to your email for two-factor authentication.</li>
                            <li>Start tracking your health data from the <strong>Dashboard</strong>!</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Daily Entry -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide2">
                        <i class="bi bi-journal-plus me-2"></i> Daily Health Entry
                    </button>
                </h2>
                <div id="guide2" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p>Track the following metrics each day:</p>
                        <ul>
                            <li><strong>Weight</strong> (lbs) - weigh yourself at the same time daily for consistency</li>
                            <li><strong>Calories</strong> - total daily caloric intake</li>
                            <li><strong>Macros</strong> - protein, carbs, and fat in grams</li>
                            <li><strong>Heart Rate</strong> (bpm) - resting heart rate</li>
                            <li><strong>Blood Sugar</strong> (mg/dL) - fasting blood glucose</li>
                            <li><strong>Exercise</strong> - type and duration in minutes</li>
                            <li><strong>Notes</strong> - any additional observations</li>
                        </ul>
                        <p class="text-muted small">You can have one entry per day. Editing is available for the same day.</p>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide3">
                        <i class="bi bi-graph-up me-2"></i> Analytics
                    </button>
                </h2>
                <div id="guide3" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p>View your health trends over time with interactive charts:</p>
                        <ul>
                            <li><strong>Weight Over Time</strong> - track your weight loss/gain progress</li>
                            <li><strong>Calories Over Time</strong> - monitor your caloric intake patterns</li>
                            <li><strong>Heart Rate & Blood Sugar</strong> - keep an eye on your vitals</li>
                            <li><strong>Day of Week</strong> - see which days you perform best</li>
                        </ul>
                        <p>Use the date range buttons (Week, Month, 3 Months, 6 Months, Year) to adjust the view.</p>
                    </div>
                </div>
            </div>

            <!-- Calculators -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide4">
                        <i class="bi bi-calculator me-2"></i> Calculators
                    </button>
                </h2>
                <div id="guide4" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <h6>Calorie Deficit Calculator</h6>
                        <p>Uses the Mifflin-St Jeor equation to calculate your BMR and TDEE based on age, gender, height, weight, and activity level. It then recommends a daily calorie target based on your goal.</p>
                        <h6>Macro Calculator</h6>
                        <p>Takes your recommended daily calories and goal, then calculates the ideal split of protein, carbs, and fat in both grams and calories.</p>
                    </div>
                </div>
            </div>

            <!-- Weight Loss Planner -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide5">
                        <i class="bi bi-calendar-check me-2"></i> Weight Loss Planner
                    </button>
                </h2>
                <div id="guide5" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p>Set a goal weight and weekly target, and the planner will:</p>
                        <ul>
                            <li>Calculate how many weeks to reach your goal</li>
                            <li>Generate weekly projected milestones</li>
                            <li>Show a chart comparing projected vs actual progress</li>
                            <li>Provide personalized recommendations (lifestyle, supplements, medication considerations)</li>
                        </ul>
                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle"></i>
                            Always consult your healthcare provider before starting any weight loss medication or supplement regimen.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy & Security -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide6">
                        <i class="bi bi-shield-lock me-2"></i> Privacy & Security (HIPAA)
                    </button>
                </h2>
                <div id="guide6" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p>Your health data is protected with industry-standard security measures:</p>
                        <ul>
                            <li><strong>AES-256 Encryption</strong> for personal information (name, email, weight, notes)</li>
                            <li><strong>Bcrypt Hashing</strong> for passwords (never stored in plain text)</li>
                            <li><strong>Two-Factor Authentication</strong> on every login</li>
                            <li><strong>CSRF Protection</strong> on all forms</li>
                            <li><strong>Rate Limiting</strong> to prevent brute-force attacks</li>
                            <li><strong>Audit Logging</strong> of all data access for accountability</li>
                            <li><strong>Session Timeouts</strong> for automatic logout after inactivity</li>
                        </ul>
                        <div class="alert alert-success small">
                            <i class="bi bi-shield-check"></i>
                            <?= e(__('hipaa_notice')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#guide7">
                        <i class="bi bi-translate me-2"></i> Language Settings
                    </button>
                </h2>
                <div id="guide7" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p>Claude Health is available in English and Spanish. Switch languages using the <i class="bi bi-translate"></i> button in the navigation bar. Your language preference is saved to your account.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

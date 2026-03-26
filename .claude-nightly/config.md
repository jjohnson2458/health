# Nightly Config — claude_health

## Enabled Tasks
- unit_tests: true
- playwright: true
- cleanup: true
- subscription_maintenance: true
- security_audit: true
- docs: true
- user_guide: true
- feature_guide: true
- business_plan: false
- backup: false
- auto_push: false
- cost_estimate: true

## Task Definitions

### unit_tests
Run the full PHPUnit test suite and report pass/fail results.
Command: `php vendor/bin/phpunit --testdox`
On failure: email

### playwright
Run Playwright end-to-end browser tests.
Command: `npx playwright test`
Config: `playwright.config.js`
Requires: Node.js, npm, Chromium.
On failure: email

### cleanup
- Purge expired password reset tokens (password_reset_expires < NOW())
- Purge expired 2FA codes (twofa_expires < NOW())
- Purge expired email verification tokens (email_token_expires < NOW())
- Purge expired provider invite tokens (connection_status = 'pending' AND created_at < 7 days ago)
- Purge old error_log entries (older than 90 days)
- Purge old audit_log entries (older than 1 year)
- Purge old login_attempts entries (older than 30 days)
- Clear storage/logs/*.log files older than 30 days
- Clear storage/cache/*
- Purge affiliate_clicks older than 1 year

### subscription_maintenance
- Cancel expired subscriptions where cancel_at_period_end = 1 AND current_period_end < NOW()
- Sync user subscription_tier column with active subscription status
- Log count of active subscriptions by tier (free/premium/premium_plus)
- Log affiliate click stats for the past 24 hours
- Flag any subscriptions in 'past_due' status for more than 7 days

### security_audit
- Run `composer audit` for known vulnerabilities
- Verify .env and secrets.txt are NOT in git tracking
- Verify ENCRYPTION_KEY is set and 64 chars
- Check that SESSION_SECURE=true in production
- Verify database schema matches schema.sql
- Check for any unencrypted PII in database (spot check)

### docs
- Rebuild PHPDoc API documentation if phpdoc.dist.xml exists
- Command: `php vendor/bin/phpdoc run`

### user_guide
- Verify in-app user guide at /guide is accessible and renders
- Check all translation keys used in guide exist in both en.php and es.php

### feature_guide
- Verify docs/FEATURES.html exists and is not empty
- Cross-check that all routes in config/routes.php are documented in FEATURES.html
- If new routes are found that aren't documented, flag them in the nightly report
- Regenerate the "Last updated" note if changes were made

### cost_estimate
- Run cost estimator if available
- Command: `php C:\xampp\htdocs\claude_estimator\php-cost-estimator\estimate.php`
- Compare to previous estimate and flag significant growth (>10%)

## Reporting
- Generate a summary of all task results
- Email the report via claude_messenger with project flag `claude_health`
- Always send report, even on partial failure

## Notes
- All tasks run sequentially; failures in one task do not block subsequent tasks
- Reports are always sent, even on partial failure
- CGM sync health check: verify cgm_connections with status 'active' have synced within last 24 hours (once Phase 10 is built)
- Appointment reminder check: verify scripts/appointment-reminders.php cron is running (once Phase 8 is built)

import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

/**
 * Accessibility Tests
 * Uses axe-core to audit every public page for WCAG 2.1 violations.
 * Catches: missing labels, poor contrast, missing alt text,
 * invalid ARIA attributes, form accessibility issues.
 */

const publicPages = [
  { url: '/login', name: 'Login' },
  { url: '/register', name: 'Register' },
  { url: '/forgot-password', name: 'Forgot Password' },
  { url: '/terms', name: 'Terms of Service' },
  { url: '/privacy', name: 'Privacy Policy' },
  { url: '/hipaa', name: 'HIPAA Notice' },
];

for (const pg of publicPages) {
  test(`accessibility: ${pg.name} has no critical violations`, async ({ page }) => {
    await page.goto(pg.url);

    const results = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa'])
      .analyze();

    // Filter to critical and serious only
    const critical = results.violations.filter(
      v => v.impact === 'critical' || v.impact === 'serious'
    );

    if (critical.length > 0) {
      const summary = critical.map(v =>
        `[${v.impact}] ${v.id}: ${v.description} (${v.nodes.length} instances)`
      ).join('\n');
      console.log(`Accessibility issues on ${pg.url}:\n${summary}`);
    }

    // Fail only on critical issues
    const criticalOnly = results.violations.filter(v => v.impact === 'critical');
    expect(
      criticalOnly,
      `Critical accessibility violations found on ${pg.url}`
    ).toHaveLength(0);
  });
}

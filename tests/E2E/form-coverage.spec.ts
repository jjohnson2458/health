import { test, expect } from '@playwright/test';

/**
 * Form Coverage Test
 * Ensures every page with forms has reachable, visible, enabled
 * input fields and submit buttons. This catches:
 * - Hidden/overlapped form fields
 * - Disabled inputs that shouldn't be
 * - Missing submit buttons
 * - Unlabeled inputs
 */

const publicPages = [
  { url: '/login', name: 'Login' },
  { url: '/register', name: 'Register' },
  { url: '/forgot-password', name: 'Forgot Password' },
];

for (const page of publicPages) {
  test.describe(`Form Coverage: ${page.name}`, () => {
    test(`all inputs are visible and enabled`, async ({ page: p }) => {
      await p.goto(page.url);

      const inputs = p.locator('input:not([type="hidden"])');
      const count = await inputs.count();
      expect(count).toBeGreaterThan(0);

      for (let i = 0; i < count; i++) {
        const input = inputs.nth(i);
        await expect(input).toBeVisible();
        await expect(input).toBeEnabled();
      }
    });

    test(`has at least one submit button`, async ({ page: p }) => {
      await p.goto(page.url);

      const submitButtons = p.locator('button[type="submit"], input[type="submit"]');
      const count = await submitButtons.count();
      expect(count).toBeGreaterThanOrEqual(1);

      for (let i = 0; i < count; i++) {
        await expect(submitButtons.nth(i)).toBeVisible();
        await expect(submitButtons.nth(i)).toBeEnabled();
      }
    });

    test(`all inputs have associated labels or aria-label`, async ({ page: p }) => {
      await p.goto(page.url);

      const inputs = p.locator('input:not([type="hidden"]):not([type="submit"])');
      const count = await inputs.count();

      for (let i = 0; i < count; i++) {
        const input = inputs.nth(i);
        const id = await input.getAttribute('id');
        const ariaLabel = await input.getAttribute('aria-label');
        const ariaLabelledBy = await input.getAttribute('aria-labelledby');
        const placeholder = await input.getAttribute('placeholder');

        // Input should have one of: label[for], aria-label, aria-labelledby, or placeholder
        if (id) {
          const label = p.locator(`label[for="${id}"]`);
          const hasLabel = await label.count() > 0;
          const hasAria = !!ariaLabel || !!ariaLabelledBy;
          const hasPlaceholder = !!placeholder;

          expect(
            hasLabel || hasAria || hasPlaceholder,
            `Input #${id} on ${page.url} has no label, aria-label, or placeholder`
          ).toBeTruthy();
        }
      }
    });

    test(`all forms have CSRF token`, async ({ page: p }) => {
      await p.goto(page.url);

      const forms = p.locator('form');
      const formCount = await forms.count();

      for (let i = 0; i < formCount; i++) {
        const csrfInput = forms.nth(i).locator('input[name="_csrf_token"]');
        await expect(csrfInput).toBeAttached();
      }
    });

    test(`all inputs can receive focus (keyboard navigable)`, async ({ page: p }) => {
      await p.goto(page.url);

      const inputs = p.locator('input:not([type="hidden"])');
      const count = await inputs.count();

      for (let i = 0; i < count; i++) {
        const input = inputs.nth(i);
        await input.focus();
        await expect(input).toBeFocused();
      }
    });
  });
}

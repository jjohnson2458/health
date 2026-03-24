import { test, expect } from '@playwright/test';

test.describe('Forgot Password', () => {
  test('page loads with email field', async ({ page }) => {
    await page.goto('/forgot-password');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  test('has back to login link', async ({ page }) => {
    await page.goto('/forgot-password');
    const backLink = page.locator('a[href="/login"]');
    await expect(backLink).toBeVisible();
  });

  test('submitting email shows success message', async ({ page }) => {
    await page.goto('/forgot-password');
    await page.fill('input[name="email"]', 'test@example.com');
    await page.click('button[type="submit"]');
    // Should always show success (anti-enumeration)
    await expect(page.locator('.alert-success, .alert')).toBeVisible();
  });

  test('navigable from login page', async ({ page }) => {
    await page.goto('/login');
    await page.click('a[href="/forgot-password"]');
    await expect(page).toHaveURL(/forgot-password/);
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });
});

const { test, expect } = require('@playwright/test');

test.describe('Admin Panel', () => {
  test('unauthenticated user visiting /admin is redirected to login', async ({ page }) => {
    await page.goto('/admin');
    await expect(page).toHaveURL(/login/);
  });

  test('non-admin user cannot access /admin', async ({ page }) => {
    // Login as a regular user
    await page.goto('/login');
    await page.fill('input[name="email"]', 'user@test.com');
    await page.fill('input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Attempt to visit admin - should redirect or show forbidden
    await page.goto('/admin');
    const url = page.url();
    const isBlocked = /login|dashboard|403/.test(url) ||
      (await page.locator('.alert-danger, .alert-warning').count()) > 0;
    expect(isBlocked).toBeTruthy();
  });
});

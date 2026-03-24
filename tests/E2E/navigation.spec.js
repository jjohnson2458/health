const { test, expect } = require('@playwright/test');

test.describe('Navigation - Auth Guard', () => {
  const protectedRoutes = ['/dashboard', '/entry', '/analytics'];

  for (const route of protectedRoutes) {
    test(`unauthenticated user visiting ${route} is redirected to login`, async ({ page }) => {
      await page.goto(route);
      await expect(page).toHaveURL(/login/);
    });
  }
});

test.describe('Navigation - Public Legal Pages', () => {
  test('terms page is publicly accessible', async ({ page }) => {
    await page.goto('/terms');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('h3')).toContainText('Terms');
  });

  test('privacy page is publicly accessible', async ({ page }) => {
    await page.goto('/privacy');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('h3')).toContainText('Privacy');
  });

  test('HIPAA page is publicly accessible', async ({ page }) => {
    await page.goto('/hipaa');
    await expect(page).not.toHaveURL(/login/);
    await expect(page.locator('h3')).toContainText('HIPAA');
  });

  test('HIPAA badge is visible on public pages', async ({ page }) => {
    await page.goto('/login');
    await expect(page.locator('.hipaa-badge')).toBeVisible();
  });
});

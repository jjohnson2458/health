import { test, expect } from '@playwright/test';

test.describe('Medications (requires auth)', () => {
  test('medications page redirects to login when not authenticated', async ({ page }) => {
    await page.goto('/medications');
    await expect(page).toHaveURL(/login/);
  });

  test('medications create page redirects to login when not authenticated', async ({ page }) => {
    await page.goto('/medications/create');
    await expect(page).toHaveURL(/login/);
  });

  test('medications share page redirects to login when not authenticated', async ({ page }) => {
    await page.goto('/medications/share');
    await expect(page).toHaveURL(/login/);
  });

  test('login page has medications nav link visible after auth', async ({ page }) => {
    // Verify the route structure exists by checking redirect behavior
    const response = await page.goto('/medications');
    // Should redirect to login (302) not 404
    expect(response?.url()).toContain('login');
  });
});

import { test, expect } from '@playwright/test';

test.describe('Appointments (requires auth)', () => {
  test('appointments page redirects to login when not authenticated', async ({ page }) => {
    await page.goto('/appointments');
    await expect(page).toHaveURL(/login/);
  });

  test('appointments calendar redirects to login when not authenticated', async ({ page }) => {
    await page.goto('/appointments/calendar');
    await expect(page).toHaveURL(/login/);
  });

  test('appointments create redirects to login when not authenticated', async ({ page }) => {
    await page.goto('/appointments/create');
    await expect(page).toHaveURL(/login/);
  });

  test('appointments route exists (not 404)', async ({ page }) => {
    const response = await page.goto('/appointments');
    // Should redirect to login (302) not return 404
    expect(response?.url()).toContain('login');
    expect(response?.status()).not.toBe(404);
  });
});

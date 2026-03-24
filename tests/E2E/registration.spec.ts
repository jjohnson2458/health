import { test, expect } from '@playwright/test';

test.describe('Registration Page', () => {
  test('loads with all required fields', async ({ page }) => {
    await page.goto('/register');
    await expect(page.locator('input[name="first_name"]')).toBeVisible();
    await expect(page.locator('input[name="last_name"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('input[name="password_confirm"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  test('has link back to login', async ({ page }) => {
    await page.goto('/register');
    const loginLink = page.locator('a[href="/login"]');
    await expect(loginLink).toBeVisible();
  });

  test('shows HIPAA badge', async ({ page }) => {
    await page.goto('/register');
    await expect(page.locator('.hipaa-badge')).toBeVisible();
  });
});

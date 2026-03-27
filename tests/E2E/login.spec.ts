import { test, expect } from '@playwright/test';

test.describe('Login Page', () => {
  test('loads with correct elements', async ({ page }) => {
    await page.goto('/login');
    await expect(page.locator('h3')).toContainText('VQ Healthy');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();
  });

  test('shows forgot password link', async ({ page }) => {
    await page.goto('/login');
    const forgotLink = page.locator('a[href="/forgot-password"]');
    await expect(forgotLink).toBeVisible();
  });

  test('shows register link', async ({ page }) => {
    await page.goto('/login');
    const registerLink = page.locator('a[href="/register"]');
    await expect(registerLink).toBeVisible();
  });

  test('shows error with invalid credentials', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'invalid@test.com');
    await page.fill('input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');
    await expect(page.locator('.alert-danger, .alert')).toBeVisible();
  });

  test('shows HIPAA badge', async ({ page }) => {
    await page.goto('/login');
    await expect(page.locator('.hipaa-badge')).toBeVisible();
  });

  test('has footer legal links', async ({ page }) => {
    await page.goto('/login');
    await expect(page.locator('a[href="/terms"]')).toBeVisible();
    await expect(page.locator('a[href="/privacy"]')).toBeVisible();
    await expect(page.locator('a[href="/hipaa"]')).toBeVisible();
  });
});

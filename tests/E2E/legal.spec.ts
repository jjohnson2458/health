import { test, expect } from '@playwright/test';

test.describe('Legal Pages', () => {
  test('terms of service page loads', async ({ page }) => {
    await page.goto('/terms');
    await expect(page.locator('h3')).toContainText('Terms');
    await expect(page.locator('.hipaa-badge')).toBeVisible();
  });

  test('privacy policy page loads', async ({ page }) => {
    await page.goto('/privacy');
    await expect(page.locator('h3')).toContainText('Privacy');
    await expect(page.locator('.hipaa-badge')).toBeVisible();
  });

  test('HIPAA notice page loads', async ({ page }) => {
    await page.goto('/hipaa');
    await expect(page.locator('h3')).toContainText('HIPAA');
    await expect(page.locator('.alert-success')).toBeVisible();
  });

  test('terms page has required sections', async ({ page }) => {
    await page.goto('/terms');
    const content = await page.textContent('body');
    expect(content).toContain('Acceptance of Terms');
    expect(content).toContain('Health Information Disclaimer');
    expect(content).toContain('Data Handling');
    expect(content).toContain('Limitation of Liability');
  });

  test('privacy page mentions AES-256', async ({ page }) => {
    await page.goto('/privacy');
    const content = await page.textContent('body');
    expect(content).toContain('AES-256');
    expect(content).toContain('bcrypt');
  });

  test('HIPAA page has safeguards table', async ({ page }) => {
    await page.goto('/hipaa');
    await expect(page.locator('table')).toBeVisible();
    const content = await page.textContent('body');
    expect(content).toContain('Encryption at Rest');
    expect(content).toContain('Audit Controls');
    expect(content).toContain('Breach Notification');
  });

  test('legal pages are accessible without login', async ({ page }) => {
    // These should NOT redirect to login
    for (const path of ['/terms', '/privacy', '/hipaa']) {
      await page.goto(path);
      await expect(page).not.toHaveURL(/login/);
    }
  });
});

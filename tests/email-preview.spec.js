const { test, expect } = require('@playwright/test');
const { pathToFileURL } = require('node:url');
const path = require('node:path');
const preview = pathToFileURL(path.resolve(__dirname, '../preview/email-preview.html')).href;
const templates = {
  'editorial-decision': 'Revisions requested',
  'reviewer-invitation': 'Invitation to review',
  'submission-confirmation': 'Submission received',
  'publication-notice': 'Your article is published',
  'account-access': 'Welcome to WesSciJo',
  'password-reset': 'Reset your password',
};

for (const width of [320, 390, 1440]) {
  for (const theme of ['light', 'dark']) {
    test(`email letters at ${width}px in ${theme}`, async ({ page }) => {
      const errors = [];
      page.on('pageerror', error => errors.push(error.message));
      await page.setViewportSize({ width, height: 1000 });
      await page.emulateMedia({ colorScheme: theme });
      await page.goto(preview);
      await page.locator('#theme').selectOption(theme);
      for (const [name, heading] of Object.entries(templates)) {
        await page.locator('#template').selectOption(name);
        const email = page.frameLocator('#preview-frame');
        await expect(email.locator('h1')).toHaveText(heading);
        const overflow = await email.locator('html').evaluate(element => element.scrollWidth > window.innerWidth);
        expect(overflow).toBe(false);
        await expect(email.locator('a[href="#"]')).toHaveCount(0);
        await page.locator('#images').uncheck();
        await expect(email.locator('img')).toHaveCount(0);
        await expect(email.locator('h1')).toHaveText(heading);
        await expect(email.getByRole('link', { name: 'The Wesleyan Science Journal', exact: true }).first()).toBeVisible();
        await page.locator('#images').check();
      }
      await page.locator('#template').selectOption('editorial-decision');
      await expect(page.frameLocator('#preview-frame').locator('h1')).toHaveText('Revisions requested');
      const extraHeight = await page.locator('#preview-frame').evaluate(frame =>
        frame.clientHeight - frame.contentDocument.body.getBoundingClientRect().height);
      expect(extraHeight).toBeLessThanOrEqual(26);
      await page.screenshot({ path: `screenshots/email-${width}-${theme}.png`, fullPage: true });
      expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
      expect(errors).toEqual([]);
    });
  }
}

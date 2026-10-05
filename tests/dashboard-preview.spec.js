const { test, expect } = require('@playwright/test');
const { pathToFileURL } = require('node:url');
const path = require('node:path');
const preview = pathToFileURL(path.resolve(__dirname, '../preview/dashboard.html')).href;

for (const width of [390, 1440]) {
  test(`native WordPress dashboard at ${width}px`, async ({ page }) => {
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.setViewportSize({ width, height: 950 });
    await page.goto(preview);
    await expect(page.locator('h1')).toHaveText('Dashboard');
    await expect(page.locator('#wpadminbar')).toBeVisible();
    await expect(page.locator('#welcome-panel')).toBeVisible();
    await expect(page.locator('#dashboard_quick_press')).toBeVisible();
    await expect(page.locator('#dashboard_activity')).toBeVisible();
    await expect(page.locator('#adminmenu a', { hasText: 'Posts' })).toHaveAttribute('href', /edit.php$/);
    await expect(page.locator('[data-queue-count]')).toHaveText('4 manuscripts shown');
    await page.locator('[data-filter="division"]').selectOption('life');
    await expect(page.locator('.wessci-queue-row:visible')).toHaveCount(1);
    await page.locator('[data-filter="stage"]').selectOption('in_review');
    await expect(page.locator('[data-queue-empty]')).toBeVisible();
    await page.locator('[data-filter="division"]').selectOption('all');
    await expect(page.locator('.wessci-queue-row:visible')).toHaveCount(2);
    await page.locator('[data-filter="search"]').fill('quantum');
    await expect(page.locator('.wessci-queue-row:visible')).toHaveCount(1);
    const collapse = page.locator('#wessci_dashboard_division_queues .handlediv');
    await collapse.click();
    await expect(page.locator('#wessci_dashboard_division_queues-inside')).toBeHidden();
    await collapse.click();
    await page.locator('#screen-options-toggle').click();
    await page.locator('[data-widget="dashboard_activity"]').uncheck();
    await expect(page.locator('#dashboard_activity')).toBeHidden();
    await page.locator('[data-widget="dashboard_activity"]').check();
    await page.locator('#screen-options-toggle').click();
    await page.locator('#draft-title').fill('A sample draft');
    await page.locator('#quick-draft button').click();
    await expect(page.locator('#draft-feedback')).toContainText('Preview only');
    await page.locator('[data-filter="stage"]').selectOption('all');
    await page.locator('[data-filter="search"]').fill('');
    if (width < 782) {
      await page.locator('#menu-toggle').click();
      await expect(page.locator('#adminmenuwrap')).toBeVisible();
      await page.locator('#menu-toggle').click();
    }
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await page.locator('h1').click();
    await page.screenshot({ path: `screenshots/dashboard-${width}.png`, fullPage: true });
    expect(errors).toEqual([]);
  });
}

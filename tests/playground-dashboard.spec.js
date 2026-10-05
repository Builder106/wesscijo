const { test, expect } = require('@playwright/test');

const base = process.env.WP_PLAYGROUND_URL;
test.skip(!base, 'Set WP_PLAYGROUND_URL to the seeded Playground site.');

test('seeded dashboard in a running WordPress', async ({ page }) => {
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  await page.setViewportSize({ width: 1440, height: 950 });
  await page.goto(`${base}/wp-login.php`);
  await page.fill('#user_login', 'admin');
  await page.fill('#user_pass', 'password');
  await page.click('#wp-submit');
  await page.goto(`${base}/wp-admin/index.php`);
  await expect(page.locator('#wessci_dashboard_division_queues')).toBeVisible();
  await expect(page.locator('#wessci_dashboard_issue_progress h3')).toHaveText('Volume 14, Issue 1');
  await expect(page.locator('[data-queue-count]')).toHaveText('6 manuscripts shown');
  await page.screenshot({ path: 'screenshots/playground-dashboard.png', fullPage: true });
  expect(errors).toEqual([]);
});

const { test, expect } = require('@playwright/test');

const base = process.env.WP_PLAYGROUND_URL;
// Needs a real WordPress seeded with tests/seed-manuscripts.php. Playground logs every request in as admin, so it cannot test roles.
test.skip(!base, 'Set WP_PLAYGROUND_URL to a seeded WordPress site.');

const roles = [
  { user: 'admin', rows: 6, issueCount: '1 of 4' },
  { user: 'editor1', rows: 6, issueCount: '1 of 4' },
  { user: 'author1', rows: 2, issueCount: '0 of 1' },
];

for (const role of roles) {
  test(`dashboard as ${role.user}`, async ({ page }) => {
    await page.goto(`${base}/wp-login.php`);
    await page.fill('#user_login', role.user);
    await page.fill('#user_pass', 'password');
    await page.click('#wp-submit');
    await page.goto(`${base}/wp-admin/index.php`);
    await expect(page.locator('#wp-admin-bar-my-account .display-name').first()).toHaveText(role.user);

    await expect(page.locator('[data-queue-count]')).toHaveText(`${role.rows} manuscripts shown`);
    await expect(page.locator('#wessci_dashboard_issue_progress')).toContainText(`${role.issueCount} articles ready`);
  });
}

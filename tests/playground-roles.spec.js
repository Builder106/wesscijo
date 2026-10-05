const { test, expect } = require('@playwright/test');

const base = process.env.WP_PLAYGROUND_URL;
// Needs a real WordPress seeded with tests/seed-manuscripts.php. Playground logs every request in as admin, so it cannot test roles.
test.skip(!base, 'Set WP_PLAYGROUND_URL to a seeded WordPress site.');

const roles = [
  { user: 'admin', rows: 6, issueCount: '1 of 4', publishing: true },
  { user: 'editor1', rows: 6, issueCount: '1 of 4', publishing: false },
  { user: 'author1', rows: 2, issueCount: '0 of 1', publishing: false },
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
    const adminBarLink = page.locator('#wp-admin-bar-wessci-vercel-deploy');
    await expect(adminBarLink).toHaveCount(role.publishing ? 1 : 0);

    const publishing = await page.goto(`${base}/wp-admin/index.php?page=wessci-publishing`);
    if (role.publishing) {
      await expect(page.locator('h1')).toHaveText('Publishing');
    } else {
      expect(publishing.status()).toBeGreaterThanOrEqual(400);
      await expect(page.locator('body')).not.toContainText('Request rebuild');
    }

    const rebuild = await page.request.post(`${base}/wp-admin/admin-post.php`, {
      form: { action: 'wessci_trigger_deploy', confirm_rebuild: '1' },
      maxRedirects: 0,
    });
    expect(rebuild.status()).toBeGreaterThanOrEqual(400);
  });
}

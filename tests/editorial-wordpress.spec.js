const { test, expect } = require('@playwright/test');
const { readFileSync } = require('node:fs');
const path = require('node:path');
const articles = JSON.parse(readFileSync(path.resolve(__dirname, '../content/issue-2026/articles.json'), 'utf8'));
const base = process.env.WORDPRESS_PREVIEW_URL || 'http://127.0.0.1:59089';
const url = route => new URL(route, base).href;

test('WordPress contains the nine issue articles with working permalinks and images', async ({ page, request }) => {
  const response = await request.get(url('/wp-json/wp/v2/posts?per_page=100'));
  expect(response.status()).toBe(200);
  const posts = await response.json();
  expect(posts).toHaveLength(9);
  expect(new Set(posts.map(post => post.slug)).size).toBe(9);
  for (const article of articles) {
    const response = await page.goto(url(`/${article.slug}/`));
    expect(response.status(), article.slug).toBe(200);
    await expect(page.locator('main h1')).toHaveText(article.title);
    await page.locator('img').evaluateAll(async images => {
      for (const image of images) { image.loading = 'eager'; await image.decode(); }
    });
    expect((await page.locator('main .prose').first().innerText()).length).toBeGreaterThan(1000);
  }
});

test('WordPress About, submission pages and search use editorial content', async ({ page }) => {
  await page.goto(url('/about/'));
  await expect(page.locator('main')).toContainText('Shriya, Aryia, and Elena');
  await expect(page.locator('main')).toContainText('Giancarlo Fedolfi');
  await expect(page.locator('main')).toContainText('Minaal Khwaja');
  await page.goto(url('/submit/'));
  await expect(page.getByRole('link', { name: 'Open the article submission form' })).toHaveAttribute('href', /1FAIpQLSf3YVPrAoa6FFr3QyJYtGbWm5aDzRgoVsL6EjiwLnagwW63cA/);
  await expect(page.locator('main')).toContainText('Prior to publication');
  await page.goto(url('/'));
  await expect(page.getByRole('link', { name: 'Calendar', exact: true })).toHaveCount(0);
  await page.getByRole('searchbox').fill('quantum');
  await page.getByRole('button', { name: 'Go', exact: true }).click();
  await page.getByRole('link', { name: 'Can Quantum Physics Secure Our Votes?', exact: true }).click();
  await expect(page.locator('main h1')).toHaveText('Can Quantum Physics Secure Our Votes?');
});

for (const width of [390, 1440]) {
  test(`WordPress preview is readable at ${width}px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 950 });
    await page.goto(url('/'));
    await expect(page.locator('.hero__title')).toHaveText('The Wesleyan Science Journal');
    await expect(page.locator('.hero__logo-slot img')).toHaveCount(1);
    if (width === 390) {
      expect(await page.locator('.hero__title').evaluate(element => element.getBoundingClientRect().width)).toBeGreaterThan(200);
    }
    const summary = page.locator('.panel summary').first();
    await summary.focus();
    await summary.press('Enter');
    await expect(page.locator('.panel').first()).toHaveAttribute('open', '');
    await summary.press('Enter');
    await page.locator('img').evaluateAll(async images => {
      for (const image of images) { image.loading = 'eager'; await image.decode(); }
    });
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await page.screenshot({ path: `artifacts/editorial-review/wordpress-home-${width}.png`, fullPage: true });
  });
}

const { test, expect } = require('@playwright/test');
const { readFileSync, mkdirSync } = require('node:fs');
const path = require('node:path');

const articles = JSON.parse(readFileSync(path.resolve(__dirname, '../content/issue-2026/articles.json'), 'utf8'));
const base = process.env.PUBLICATION_BASE_URL || 'http://127.0.0.1:59090';
const url = route => new URL(route, base).href;
const screenshots = path.resolve(__dirname, '../artifacts/editorial-review');
mkdirSync(screenshots, { recursive: true });

test('logo entrance lands and replays on reload', async ({ page }) => {
  const errors = [];
  page.on('pageerror', error => errors.push(error.message));
  await page.goto(url('/'), { waitUntil: 'domcontentloaded' });
  await expect(page.locator('#logo-entrance')).toBeVisible();
  const mark = await page.locator('.logo-entrance__mark').boundingBox();
  const viewport = page.viewportSize();
  expect(Math.abs(mark.x + mark.width / 2 - viewport.width / 2)).toBeLessThan(1);
  expect(Math.abs(mark.y + mark.height / 2 - viewport.height / 2)).toBeLessThan(1);
  await expect(page.locator('.hero')).toHaveCSS('visibility', 'hidden');
  expect(await page.locator('.logo-entrance__flight').evaluate(image => image.naturalWidth)).toBeGreaterThan(0);
  const firstFlight = await page.locator('.logo-entrance__flight').getAttribute('src');
  await page.waitForFunction(() => document.documentElement.classList.contains('logo-entrance-revealing'));
  await expect(page.locator('.hero')).toHaveCSS('visibility', 'visible');
  await expect(page.locator('.hero__logo-slot')).toHaveCSS('visibility', 'hidden');
  await expect(page.locator('#logo-entrance')).toBeHidden({ timeout: 6000 });
  await expect(page.locator('.hero')).toBeVisible();
  await page.evaluate(() => sessionStorage.setItem('wessci-entrance-seen', '1'));
  await page.reload();
  await expect(page.locator('#logo-entrance')).toBeVisible();
  await expect(page.locator('.logo-entrance__flight')).not.toHaveAttribute('src', firstFlight);
  await expect(page.locator('.hero')).toHaveCSS('visibility', 'hidden');
  await expect(page.locator('#logo-entrance')).toBeHidden({ timeout: 6000 });
  await expect(page.locator('.hero')).toBeVisible();
  expect(errors).toEqual([]);
});

test('logo entrance in dark mode never exposes unstyled artwork', async ({ browser }) => {
  const dark = await browser.newPage();
  await dark.addInitScript(() => localStorage.setItem('theme', 'dark'));
  await dark.goto(url('/'), { waitUntil: 'domcontentloaded' });
  await expect(dark.locator('#logo-entrance')).toBeVisible();
  await expect(dark.locator('.logo-entrance__flight')).toHaveAttribute('src', /logo-flight-dark\.svg/);
  await expect(dark.locator('.hero')).toHaveCSS('visibility', 'hidden');
  await dark.close();

  const systemDark = await browser.newPage({ colorScheme: 'dark' });
  await systemDark.goto(url('/'), { waitUntil: 'domcontentloaded' });
  await expect(systemDark.locator('.logo-entrance__flight')).toHaveAttribute('src', /logo-flight-dark\.svg/);
  await systemDark.close();

  const explicitLight = await browser.newPage({ colorScheme: 'dark' });
  await explicitLight.addInitScript(() => localStorage.setItem('theme', 'light'));
  await explicitLight.goto(url('/'), { waitUntil: 'domcontentloaded' });
  await expect(explicitLight.locator('.logo-entrance__flight')).toHaveAttribute('src', /\/logo-flight\.svg/);
  await explicitLight.close();

  const missingStyle = await browser.newPage();
  await missingStyle.route('**/publication.css*', route => route.abort());
  await missingStyle.goto(url('/'), { waitUntil: 'domcontentloaded' });
  await expect(missingStyle.locator('#logo-entrance')).toBeHidden();
  await expect(missingStyle.locator('.hero')).toBeVisible();
  await missingStyle.close();
});

test('logo entrance reduced motion and missing asset keep the homepage visible', async ({ browser }) => {
  const reduced = await browser.newPage({ reducedMotion: 'reduce' });
  await reduced.goto(url('/'), { waitUntil: 'domcontentloaded' });
  await expect(reduced.locator('#logo-entrance')).toBeHidden();
  await expect(reduced.locator('.hero')).toBeVisible();
  await reduced.close();

  const unavailable = await browser.newPage();
  let blockedFlight = false;
  await unavailable.route('**/assets/logo-flight.svg*', route => {
    blockedFlight = true;
    return route.abort();
  });
  await unavailable.goto(url('/'), { waitUntil: 'domcontentloaded' });
  await expect(unavailable.locator('#logo-entrance')).toBeHidden({ timeout: 6000 });
  await expect(unavailable.locator('.hero')).toBeVisible();
  expect(blockedFlight).toBe(true);
  await unavailable.close();
});

test('every issue title opens its own complete article', async ({ page }) => {
  await page.goto(url('/archives/'));
  await expect(page.locator('main .card')).toHaveCount(9);
  for (const article of articles) {
    await page.goto(url('/archives/'));
    await page.getByRole('link', { name: article.title, exact: true }).click();
    await expect(page).toHaveURL(url(`/articles/${article.slug}/`));
    await expect(page.getByRole('heading', { level: 1 })).toHaveText(article.title);
    expect((await page.locator('.prose').innerText()).length).toBeGreaterThan(1000);
    await page.locator('img').evaluateAll(async images => {
      for (const image of images) {
        image.loading = 'eager';
        await image.decode();
      }
    });
    expect(await page.locator('img').evaluateAll(images => images.every(image => image.complete && image.naturalWidth > 0))).toBe(true);
  }
});

test('search finds real titles and safely handles missing queries', async ({ page }) => {
  await page.goto(url('/'));
  await page.getByRole('searchbox').fill('quantum');
  await page.getByRole('button', { name: 'Go', exact: true }).click();
  await expect(page.getByRole('status')).toContainText('1 article found');
  await page.getByRole('link', { name: 'Can Quantum Physics Secure Our Votes?', exact: true }).click();
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Can Quantum Physics Secure Our Votes?');
  await page.goto(url('/search/?q=' + encodeURIComponent('<script>missing</script>')));
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Search: <script>missing</script>');
  await expect(page.getByRole('status')).toContainText('No articles found');
  expect(await page.locator('main script').count()).toBe(0);
});

test('approved pages have the letter, guidelines and exact submission form', async ({ page }) => {
  await page.goto(url('/about/'));
  await expect(page.locator('#editorial-letter')).toContainText('Dear readers,');
  await expect(page.locator('#editorial-letter')).toContainText('Shriya, Aryia, and Elena');
  await expect(page.locator('main')).not.toContainText(/Official Seal|Insignia|describing the logo/i);
  await page.goto(url('/submit/'));
  await expect(page.getByRole('link', { name: 'Open the article submission form' })).toHaveAttribute('href', 'https://docs.google.com/forms/d/e/1FAIpQLSf3YVPrAoa6FFr3QyJYtGbWm5aDzRgoVsL6EjiwLnagwW63cA/viewform');
  await expect(page.locator('main')).toContainText('Article Content Guidelines');
  await expect(page.locator('main')).toContainText('View the complete guidelines');
  const guidelinesLink = page.getByRole('link', { name: /complete guidelines/i });
  await expect(guidelinesLink).toHaveAttribute('target', '_blank');
  await expect(guidelinesLink).not.toHaveAttribute('download');
  await expect(page.getByRole('link', { name: 'Calendar', exact: true })).toHaveCount(0);
});

test('GMO article contains both comic pages and all supplied sources', async ({ page }) => {
  await page.goto(url('/articles/gmos-applications-and-controversy/'));
  await expect(page.locator('.prose img[src*="comic"]')).toHaveCount(2);
  await expect(page.locator('.prose')).toContainText('Sources Cited');
  await expect(page.locator('.prose a[href="https://doi.org/10.3389/fpls.2022.1027828"]')).toHaveCount(1);
  expect(await page.locator('.prose').evaluate(element => {
    const comic = element.querySelector('img[src*="comic-page-2"]');
    const sources = Array.from(element.querySelectorAll('h2')).find(heading => heading.textContent.trim() === 'Sources Cited');
    return Boolean(comic && sources && (comic.compareDocumentPosition(sources) & Node.DOCUMENT_POSITION_FOLLOWING));
  })).toBe(true);
});

for (const width of [390, 768, 1440]) {
  test(`menus, artwork and reading layout at ${width}px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 950 });
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.goto(url('/'));
    await expect(page.locator('#logo-entrance')).toBeHidden({ timeout: 6000 });
    await expect(page.locator('.hero__title')).toHaveText('The Wesleyan Science Journal');
    await expect(page.locator('body')).toHaveCSS('background-color', 'rgb(255, 255, 255)');
    const menu = page.locator('.panel').first();
    await menu.locator('summary').focus();
    await menu.locator('summary').press('Enter');
    await expect(menu).toHaveAttribute('open', '');
    await menu.locator('summary').press('Escape');
    await expect(menu).not.toHaveAttribute('open', '');
    if (width === 390) {
      expect(await page.locator('.hero__title').evaluate(element => element.getBoundingClientRect().width)).toBeGreaterThan(200);
    }
    await page.locator('img').evaluateAll(async images => {
      for (const image of images) { image.loading = 'eager'; await image.decode(); }
    });
    await page.screenshot({ path: path.join(screenshots, `home-${width}.png`), fullPage: true });
    for (const route of ['/', '/about/', '/submit/', '/articles/bile-salt-mixed-micelles/', '/articles/math-behind-llms/']) {
      await page.goto(url(route));
      expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), route).toBe(true);
      await expect(page.locator('main h1')).toHaveCount(1);
    }
    await page.screenshot({ path: path.join(screenshots, `llms-${width}.png`), fullPage: true });
    expect(errors).toEqual([]);
  });
}

test('internal publication links resolve and old view links retain their own story', async ({ page, request }) => {
  const routes = ['/', '/about/', '/submit/', '/archives/', '/search/', ...articles.map(a => `/articles/${a.slug}/`)];
  const targets = new Set();
  for (const route of routes) {
    await page.goto(url(route));
    const links = await page.locator('a[href]').evaluateAll(items => items.map(a => a.getAttribute('href')));
    links.filter(href => href.startsWith('/') && !href.startsWith('//')).forEach(href => targets.add(href.split('#')[0]));
  }
  for (const target of targets) expect((await request.get(url(target))).status(), target).toBe(200);
  await page.goto(url('/?view=feature'));
  await expect(page).toHaveURL(url('/articles/birds-of-wesleyan/'));
  expect((await request.get(url('/nonexistent-article/'))).status()).toBe(404);
});

test('theme toggle cycles between system, light, and dark', async ({ page }) => {
  await page.goto(url('/'));
  const toggle = page.locator('#theme-toggle');

  // Verify system monitor icon was dynamically injected
  await expect(toggle.locator('.theme-toggle__icon--system')).toHaveCount(1);

  // Starts in system mode (no data-theme)
  await expect(toggle).toHaveAttribute('aria-label', 'Switch to light theme');
  expect(await page.evaluate(() => document.documentElement.getAttribute('data-theme'))).toBeNull();
  expect(await page.evaluate(() => localStorage.getItem('theme'))).toBeNull();
  await page.screenshot({ path: path.join(screenshots, 'theme-1-system.png'), clip: { x: 0, y: 0, width: 1440, height: 400 } });

  // 1st click: Switch to light theme
  await toggle.click();
  await page.waitForTimeout(300);
  await expect(toggle).toHaveAttribute('aria-label', 'Switch to dark theme');
  expect(await page.evaluate(() => document.documentElement.getAttribute('data-theme'))).toBe('light');
  expect(await page.evaluate(() => localStorage.getItem('theme'))).toBe('light');
  await page.screenshot({ path: path.join(screenshots, 'theme-2-light.png'), clip: { x: 0, y: 0, width: 1440, height: 400 } });

  // 2nd click: Switch to dark theme
  await toggle.click();
  await page.waitForTimeout(300);
  await expect(toggle).toHaveAttribute('aria-label', 'Switch to system theme');
  expect(await page.evaluate(() => document.documentElement.getAttribute('data-theme'))).toBe('dark');
  expect(await page.evaluate(() => localStorage.getItem('theme'))).toBe('dark');
  await page.screenshot({ path: path.join(screenshots, 'theme-3-dark.png'), clip: { x: 0, y: 0, width: 1440, height: 400 } });

  // 3rd click: Switch back to system theme
  await toggle.click();
  await page.waitForTimeout(300);
  await expect(toggle).toHaveAttribute('aria-label', 'Switch to light theme');
  expect(await page.evaluate(() => document.documentElement.getAttribute('data-theme'))).toBeNull();
  expect(await page.evaluate(() => localStorage.getItem('theme'))).toBeNull();
});


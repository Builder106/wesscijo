const { test, expect } = require('@playwright/test');

const routes = ['/', '/about/', '/calendar/', '/archives/', '/submit/'];
const archiveRoutes = ['/category/research-reviews/', '/category/news-features-perspectives/'];
const fixtureRoutes = new Set(['/', '/calendar/', '/archives/', '/submit/']);

function siteUrl(path) {
  return new URL(path, process.env.BASE_URL || 'https://wessci.yinkavaughan.me/').toString();
}

test.describe('WesSciJo public site smoke tests', () => {
  test('expected routes expose landmarks and metadata', async ({ page }) => {
    for (const route of routes) {
      const response = await page.goto(siteUrl(route));
      expect(response && response.status(), route).toBe(200);
      await expect(page.locator('main')).toHaveCount(1);
      await expect(page.locator('html[lang]')).toHaveCount(1);
      expect(await page.title()).toMatch(/\S+/);
      await expect(page.locator('link[rel="canonical"]')).toHaveCount(1);
      if (fixtureRoutes.has(route)) {
        await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', /noindex/i);
      } else {
        await expect(page.locator('meta[name="description"]')).toHaveCount(1);
        await expect(page.locator('meta[property="og:description"]')).toHaveCount(1);
      }
    }
  });

  test('archive pages use an h1', async ({ page }) => {
    for (const route of archiveRoutes) {
      const response = await page.goto(siteUrl(route));
      expect(response && response.status(), route).toBe(200);
      await expect(page.locator('main h1')).toHaveCount(1);
    }
  });

  test('missing routes return a styled 404', async ({ page }) => {
    const response = await page.goto(siteUrl('/browser-smoke-missing-route/'));
    expect(response && response.status()).toBe(404);
    await expect(page.locator('main')).toHaveCount(1);
    await expect(page.locator('main h1')).toHaveCount(1);
    await expect(page.locator('body')).toContainText(/not found/i);
  });

  test('search has an accessible label and returns results', async ({ page }) => {
    await page.goto(siteUrl('/'));
    const search = page.locator('form[role="search"]');
    await expect(search).toHaveCount(1);
    await expect(search.locator('input[type="search"]')).toHaveAccessibleName(/search/i);
    await search.locator('input[type="search"]').fill('biology');
    await search.locator('button[type="submit"]').click();
    await expect(page).toHaveURL(/\?s=biology/);
    await expect(page.locator('main h1')).toContainText(/search results/i);
    await expect(page.locator('main')).not.toContainText(/undefined|null|error/i);
  });

  test('skip link focuses the main content', async ({ page }) => {
    await page.goto(siteUrl('/about/'));
    const skipLink = page.locator('a.skip-link');
    await expect(skipLink).toHaveCount(1);
    await skipLink.focus();
    await expect(skipLink).toBeFocused();
    await skipLink.press('Enter');
    await expect(page.locator('#main')).toBeFocused();
  });

  test('native details menus open from the keyboard', async ({ page }) => {
    await page.goto(siteUrl('/'));
    const details = page.locator('details').first();
    const summary = details.locator('summary');
    await summary.focus();
    await summary.press('Enter');
    await expect(details).toHaveAttribute('open', '');
    await expect(details.locator('a')).not.toHaveCount(0);
  });

  for (const viewport of [{ width: 390, height: 844 }, { width: 768, height: 900 }, { width: 1280, height: 720 }]) {
    test(`has no horizontal overflow at ${viewport.width}px`, async ({ page }) => {
      await page.setViewportSize(viewport);
      await page.goto(siteUrl('/'));
      const dimensions = await page.evaluate(() => ({
        body: document.body.scrollWidth,
        document: document.documentElement.scrollWidth,
        viewport: window.innerWidth
      }));
      expect(dimensions.body).toBeLessThanOrEqual(dimensions.viewport);
      expect(dimensions.document).toBeLessThanOrEqual(dimensions.viewport);
    });
  }

  test('images follow the loading, priority, alt, and sizes policy', async ({ page }) => {
    await page.goto(siteUrl('/'));
    const images = page.locator('main img');
    await expect(images).not.toHaveCount(0);
    const imagePolicy = await images.evaluateAll((items) => items.map((image) => ({
      alt: image.getAttribute('alt'),
      loading: image.getAttribute('loading'),
      fetchPriority: image.getAttribute('fetchpriority'),
      sizes: image.getAttribute('sizes'),
      decorative: image.closest('[aria-hidden="true"]') !== null,
      heading: image.closest('article')?.querySelector('h2, h3') !== null
    })));
    expect(imagePolicy[0].loading).not.toBe('lazy');
    expect(imagePolicy[0].fetchPriority).toBe('high');
    expect(imagePolicy[0].sizes).toMatch(/50vw|100vw/);
    for (const image of imagePolicy.slice(1)) {
      expect(image.loading).toBe('lazy');
      expect(image.sizes).toMatch(/25vw|50vw|100vw/);
      if (image.decorative) expect(image.alt).toBe('');
      else if (image.heading) expect(image.alt).toBeTypeOf('string');
    }
  });

  test('calendar keeps existing undated entries visible', async ({ page }) => {
    await page.goto(siteUrl('/calendar/'));
    const events = page.locator('main article');
    await expect(events).not.toHaveCount(0);
    await expect(events.locator('h2, h3')).not.toHaveCount(0);
    const times = page.locator('main time');
    if (await times.count()) {
      await expect(times).toHaveAttribute('datetime', /.+/);
    }
  });
});

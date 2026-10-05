const { test, expect } = require('@playwright/test');
const { mkdirSync } = require('node:fs');
const { pathToFileURL } = require('node:url');
const path = require('node:path');
const preview = pathToFileURL(path.resolve(__dirname, '../preview/index.html')).href;
mkdirSync(path.resolve(__dirname, '../screenshots/cardinal'), { recursive: true });

for (const width of [390, 1440]) {
  test(`preview reader journeys at ${width}px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 950 });
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.goto(preview);
    await expect(page.locator('h1')).toHaveText('A home forcurious minds.');
    await expect(page.locator('body')).toHaveCSS('background-color', 'rgb(16, 16, 16)');
    await expect(page.locator('.story')).toHaveCount(3);
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    await expect(page.locator('.opening-art img')).toBeVisible();
    await page.locator('img').evaluateAll(images => Promise.all(images.map(image => {
      image.loading = 'eager';
      return image.decode();
    })));
    expect(await page.locator('img').evaluateAll(images => images.every(image => image.complete && image.naturalWidth > 0))).toBe(true);
    await page.screenshot({ path: `screenshots/cardinal/cardinal-${width}.png`, fullPage: true });
    await page.getByRole('link', { name: 'What a wing can teach us about the world', exact: true }).click();
    await expect(page.locator('h1')).toHaveText('What a wing can teach us about the world');
    await expect(page.getByRole('heading', { name: 'References', exact: true })).toBeVisible();
    await page.screenshot({ path: `screenshots/cardinal/cardinal-article-${width}.png`, fullPage: true });
    await page.getByRole('link', { name: 'More from the journal' }).click();
    await page.getByRole('button', { name: 'Research & Reviews', exact: true }).click();
    await expect(page.locator('.story')).toHaveCount(1);
    await page.getByRole('button', { name: 'All stories', exact: true }).click();
    await expect(page.locator('.story')).toHaveCount(3);
    await page.getByRole('link', { name: 'Who gets to ask the next big question?', exact: true }).click();
    await expect(page.locator('.article__figure')).toHaveCount(0);
    if (width === 390) {
      await page.locator('.journal-menu > summary').click();
      await page.screenshot({ path: 'screenshots/cardinal/cardinal-mobile-menu.png' });
    }
    await page.getByRole('searchbox').fill('flight');
    await page.getByRole('button', { name: 'Search', exact: true }).click();
    await expect(page.locator('.story')).toHaveCount(1);
    if (width === 390) { await page.locator('.journal-menu > summary').click(); }
    await page.getByRole('searchbox').fill('<script>missing</script>');
    await page.getByRole('button', { name: 'Search', exact: true }).click();
    await expect(page.locator('h1')).toHaveText('Search: <script>missing</script>');
    await expect(page.locator('.empty')).toContainText('No stories found');
    await page.goto(`${preview}#calendar`);
    await expect(page.getByRole('heading', { name: 'No confirmed events in this preview' })).toBeVisible();
    await page.goto(`${preview}#submit`);
    await expect(page.getByRole('heading', { name: 'Submission details are coming' })).toBeVisible();
    expect(errors).toEqual([]);
  });
}

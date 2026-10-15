const { chromium } = require('@playwright/test');
const { pathToFileURL } = require('node:url');
const { copyFile, stat } = require('node:fs/promises');
const path = require('node:path');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    reducedMotion: 'no-preference',
    recordVideo: {
      dir: path.join(__dirname, 'assets/.entrance-capture'),
      size: { width: 1440, height: 900 },
    },
  });
  const page = await context.newPage();
  const errors = [];
  page.on('pageerror', (error) => errors.push(error.message));
  await page.goto(pathToFileURL(path.join(__dirname, 'logo-entrance.html')).href);
  await page.waitForFunction(() => document.documentElement.classList.contains('intro'));
  await page.waitForFunction(() => !document.documentElement.classList.contains('intro'));
  await page.waitForTimeout(1100);
  await page.close();
  const recording = await page.video().path();
  await context.close();
  await browser.close();
  if (errors.length) throw new Error(errors.join('\n'));
  const output = path.join(__dirname, 'logo-entrance-recording.webm');
  await copyFile(recording, output);
  const details = await stat(output);
  console.log(JSON.stringify({ output, bytes: details.size }));
})().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});

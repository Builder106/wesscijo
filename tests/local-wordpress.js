async function bridgeLocalWordPressUrls(page, baseUrl) {
  const localUrl = new URL(baseUrl);
  if (localUrl.protocol !== 'http:' || !['127.0.0.1', 'localhost'].includes(localUrl.hostname)) return;

  await page.route(url => url.protocol === 'https:' && url.host === localUrl.host, async route => {
    const request = route.request();
    const target = new URL(request.url());
    target.protocol = 'http:';
    const response = await page.request.fetch(target.href, {
      method: request.method(),
      data: request.postDataBuffer() ?? undefined
    });
    await route.fulfill({ response });
  });
}

module.exports = { bridgeLocalWordPressUrls };

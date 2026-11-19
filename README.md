# Web Tools Core

[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/php-%5E8.4-777bb4.svg)](composer.json)
[![Laravel](https://img.shields.io/badge/laravel-13-ff2d20.svg)](composer.json)
[![Livewire](https://img.shields.io/badge/livewire-4-fb70a9.svg)](composer.json)
[![Live Demo](https://img.shields.io/badge/live%20demo-yu.net.ua%2Ftools-brightgreen.svg)](https://yu.net.ua/en/tools)

The shared base of the `yu-laravel` web tools: a page fetcher that cannot be used to reach your own network, a TLS certificate reader, a scored checklist report, a base Livewire component and the shared styles.

You don't need to install it yourself: every tool requires it. Install it directly only to build a tool of your own.

**🔗 Live tools built on it: [yu.net.ua/tools](https://yu.net.ua/en/tools)**

## Tools built on the core

| Package | What it checks |
|---|---|
| [`yu-laravel/seo-meta-analyzer`](https://github.com/yuriyakishin/laravel-seo-meta-analyzer) | Title and meta description, with AI suggestions |
| [`yu-laravel/webmcp-analyzer`](https://github.com/yuriyakishin/laravel-webmcp-analyzer) | Whether a site is ready for AI agents (WebMCP) |
| [`yu-laravel/link-preview`](https://github.com/yuriyakishin/laravel-link-preview) | How a link looks in messengers and social networks |
| [`yu-laravel/security-check`](https://github.com/yuriyakishin/laravel-security-check) | SSL certificate, HTTPS and security headers |
| [`yu-laravel/schema-validator`](https://github.com/yuriyakishin/laravel-schema-validator) | Schema.org structured data and Google rich results |
| [`yu-laravel/dns-check`](https://github.com/yuriyakishin/laravel-dns-check) | DNS and mail records: MX, SPF, DKIM, DMARC, CAA |
| [`yu-laravel/page-speed`](https://github.com/yuriyakishin/laravel-page-speed) | Page speed and Core Web Vitals |

## What's inside

- **`Http\SafeFetcher`**: downloads a URL with SSRF protection. Every address, redirects included, must resolve to public IPs on ports 80/443. The connection goes to the checked IP, and the download stops at a size limit.
- **`Http\TlsInspector`**: reads a site's certificate with SNI and still returns an untrusted certificate, so the tool can explain what's wrong with it.
- **`Checklist\Check` / `ChecklistReport`**: a scored checklist. Each check has a status, points and a group, and skipped checks don't count toward the score. The verdict is a word: ready (≥ 90), almost (≥ 70), partly (≥ 50) or not ready.
- **`Livewire\ToolComponent`**: the base Livewire component. It handles the URL form, the visitor's language, the per-IP rate limit, the result cache, translated error messages and the `ToolCheckedEvent`.
- **`Locale\LocaleResolver`**: picks the report language from the visitor's `Accept-Language` header, out of the translations a tool ships.
- **Shared views and CSS**: the form shell and the checklist report, themed with `--wt-*` CSS variables.

## Requirements

- PHP ^8.4
- Laravel ^13.0
- Livewire ^4.0

## Installation

```bash
composer require yu-laravel/web-tools-core
```

The service provider is discovered automatically.

## Styles

The tools don't publish assets. Import the core stylesheet, plus the stylesheet of each tool you use, into your CSS build (Vite, for example):

```css
@import "../../vendor/yu-laravel/web-tools-core/resources/css/web-tools-core.css";
```

Every colour and font is a `--wt-*` variable with a neutral default. To match your site, override them on `.wt`:

```css
.wt {
    --wt-accent: #1d4ed8;
    --wt-font: "Inter", system-ui, sans-serif;
}
```

The full list is `--wt-accent`, `--wt-accent-dark`, `--wt-accent-soft`, `--wt-bg`, `--wt-surface`, `--wt-raised`, `--wt-text`, `--wt-text-2`, `--wt-muted`, `--wt-line`, `--wt-line-strong`, `--wt-ok`, `--wt-warn`, `--wt-err`, `--wt-terminal`, `--wt-font` and `--wt-mono`.

## Configuration

`config/web-tools-core.php` sets how pages are fetched for every tool:

| Key | Default | Description |
|---|---|---|
| `fetch.connect_timeout` | `4` | Seconds to wait for a connection. |
| `fetch.timeout` | `8` | Seconds to wait for the whole response. |
| `fetch.max_redirects` | `3` | Redirects followed before giving up. |
| `fetch.user_agent` | `Mozilla/5.0 (compatible; YuWebTools/1.0; +https://yu.net.ua/tools)` | Set with `WEB_TOOLS_USER_AGENT`. |

The config is not publishable yet. To change a value without an env variable, set it in a service provider, for example `config(['web-tools-core.fetch.timeout' => 12]);`.

## Listening to checks

Every checklist tool dispatches `Yu\WebToolsCore\Events\ToolCheckedEvent` after a check, whether it succeeded, failed or came from the cache. SEO Meta Analyzer is the exception: it dispatches its own event. Use the event to log checks or collect statistics:

```php
use Illuminate\Support\Facades\Event;
use Yu\WebToolsCore\Events\ToolCheckedEvent;

Event::listen(function (ToolCheckedEvent $event) {
    // $event->tool       "dns-check"
    // $event->url        the checked URL
    // $event->locale     the report language
    // $event->report     the report array, or null when the check failed
    // $event->error      the failure reason, e.g. "timeout", or null
    // $event->fromCache  true when the report came from the cache
});
```

## Building your own tool

Extend `ToolComponent` and implement three methods:

```php
use Yu\WebToolsCore\Livewire\ToolComponent;
use Yu\WebToolsCore\Locale\LocaleResolver;

class MyToolComponent extends ToolComponent
{
    protected function name(): string
    {
        return 'my-tool'; // also the config key and the translation namespace
    }

    protected function locales(): LocaleResolver
    {
        return new LocaleResolver(dirname(__DIR__, 2).'/resources/lang');
    }

    protected function run(string $url, string $locale): array
    {
        // check the URL; throw ToolException('reason') for a translated error
        return $report->toArray();
    }
}
```

The tool's config must include `enabled`, `rate_limit.per_hour` and `cache.ttl`. Error messages are looked up in `my-tool::messages.errors.*` first, then in `web-tools-core::messages.errors.*`.

## Languages

The core ships 13 translations: cs, de, en, es, fr, it, ja, nl, pl, pt, tr, uk and zh.

## Author

Yuriy Akishin:
- 🌐 Website: https://yu.net.ua
- 📧 Email: yuriy.akishin@gmail.com
- 💼 LinkedIn: https://www.linkedin.com/in/yuriyakishin/
- 💻 GitHub: https://github.com/yuriyakishin

## License

MIT. See [LICENSE](LICENSE).

<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;
use Yu\WebToolsCore\Events\ToolCheckedEvent;
use Yu\WebToolsCore\Exceptions\ToolException;
use Yu\WebToolsCore\Locale\LocaleResolver;

/**
 * The shell of a web tool: the URL form, the visitor's language, the rate limit, the cache, translated errors
 * and the ToolCheckedEvent. A tool implements run(): check the URL and return the report as an array.
 *
 * The tool name is also its config key and translation namespace: config('{name}.rate_limit.per_hour'),
 * __('{name}::messages.errors.timeout'). An error without the tool's own text uses web-tools-core::messages.errors.
 */
abstract class ToolComponent extends Component
{
    public string $url = '';

    /** Language of the form and the report. */
    #[Locked]
    public string $locale = 'en';

    /** The visitor's browser language that has a translation; null when there is none. */
    #[Locked]
    public ?string $browserLocale = null;

    /** The report array; null until a check succeeds. */
    #[Locked]
    public ?array $report = null;

    #[Locked]
    public ?string $error = null;

    abstract protected function name(): string;

    abstract protected function locales(): LocaleResolver;

    /**
     * @return array<string, mixed>
     *
     * @throws ToolException with a reason that has a translated message
     */
    abstract protected function run(string $url, string $locale): array;

    public function mount(): void
    {
        // Here request() is the visitor's page request; Livewire AJAX requests do not carry the page's language
        $this->browserLocale = $this->locales()->fromBrowser(request());
        $this->locale = $this->browserLocale ?? app()->getLocale();
    }

    public function analyze(): void
    {
        $this->reset('report', 'error');

        $this->validate(
            ['url' => ['required', 'string', 'max:2048']],
            ['url.*' => $this->message('errors.invalid_url')],
        );

        $this->url = $this->normalize($this->url);

        if (!$this->isAvailable()) {
            $this->fail('disabled');

            return;
        }

        $cacheKey = $this->name().':'.sha1($this->url).':'.$this->locale;

        if (Cache::has($cacheKey)) {
            $this->report = Cache::get($cacheKey);
            ToolCheckedEvent::dispatch($this->name(), $this->url, $this->locale, $this->report, fromCache: true);

            return;
        }

        try {
            $this->hitRateLimit();
            $this->report = $this->run($this->url, $this->locale);
        } catch (ToolException $e) {
            $this->fail($e->reason, $e->replace);

            return;
        } catch (Throwable $e) {
            report($e);
            $this->fail('generic');

            return;
        }

        Cache::put($cacheKey, $this->report, (int) config($this->name().'.cache.ttl'));
        ToolCheckedEvent::dispatch($this->name(), $this->url, $this->locale, $this->report);
    }

    protected function isAvailable(): bool
    {
        return (bool) config($this->name().'.enabled');
    }

    /**
     * A text of the tool in the visitor's language; falls back to the shared core texts.
     *
     * @param  array<string, string|int>  $replace
     */
    protected function message(string $key, array $replace = []): string
    {
        $own = $this->name().'::messages.'.$key;

        return __(Lang::has($own, $this->locale) ? $own : 'web-tools-core::messages.'.$key, $replace, $this->locale);
    }

    /**
     * @param  array<string, string|int>  $replace
     */
    private function fail(string $reason, array $replace = []): void
    {
        $this->error = $this->message('errors.'.$reason, $replace);
        ToolCheckedEvent::dispatch($this->name(), $this->url, $this->locale, error: $reason);
    }

    /**
     * Trims, adds https:// when there is no scheme and drops the #fragment.
     */
    private function normalize(string $url): string
    {
        $url = trim($url);

        if (!preg_match('~^[a-z][a-z0-9+.-]*://~i', $url)) {
            $url = 'https://'.$url;
        }

        return Str::before($url, '#');
    }

    /**
     * Every check that reaches the network counts, failed ones too.
     */
    private function hitRateLimit(): void
    {
        $key = $this->name().':'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, (int) config($this->name().'.rate_limit.per_hour'))) {
            throw new ToolException('rate_limited', [
                'minutes' => (int) ceil(RateLimiter::availableIn($key) / 60),
            ]);
        }

        RateLimiter::hit($key, 3600);
    }
}

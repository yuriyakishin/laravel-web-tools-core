<?php

declare(strict_types=1);

namespace Yu\WebToolsCore\Locale;

use Illuminate\Http\Request;

/**
 * Picks the language of a tool: a translation exists for every folder in the tool's resources/lang.
 */
class LocaleResolver
{
    public function __construct(private string $langPath) {}

    /**
     * @return list<string>
     */
    public function supported(): array
    {
        return array_map(basename(...), glob($this->langPath.'/*', GLOB_ONLYDIR) ?: []);
    }

    /**
     * The visitor's most preferred Accept-Language that has a translation: "de-AT,de;q=0.9,en;q=0.8" → "de".
     */
    public function fromBrowser(Request $request): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $locale = $this->toSupported($language);

            if ($locale !== null) {
                return $locale;
            }
        }

        return null;
    }

    /**
     * "de_AT", "de-AT" and "DE" all become "de"; null when there is no such translation.
     */
    public function toSupported(?string $language): ?string
    {
        if ($language === null) {
            return null;
        }

        $locale = strtolower(explode('_', str_replace('-', '_', $language))[0]);

        return in_array($locale, $this->supported(), true) ? $locale : null;
    }
}

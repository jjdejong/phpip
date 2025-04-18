<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations;

trait HasTranslationsExtended
{
    use HasTranslations {
        setTranslation as parentSetTranslation;
    }

    public function setTranslation(string $key, string $locale, $value): self
    {
        // Always strip locale to base language for consistency
        $baseLocale = substr($locale, 0, 2);

        return $this->parentSetTranslation($key, $baseLocale, $value);
    }
}


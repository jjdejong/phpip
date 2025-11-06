<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations;

/**
 * Extended translation trait with base locale normalization.
 *
 * Extends Spatie's HasTranslations trait to automatically normalize locale codes
 * to their base language (e.g., 'en_US' becomes 'en') for consistency across the application.
 */
trait HasTranslationsExtended
{
    use HasTranslations {
        setTranslation as parentSetTranslation;
    }

    /**
     * Set a translation for a specific attribute and locale.
     *
     * Overrides the parent method to normalize locale codes to their base language
     * (first 2 characters) before storing, ensuring consistency when locales like
     * 'en_US', 'en_GB', etc. are used.
     *
     * @param string $key The attribute name to translate.
     * @param string $locale The locale code (will be normalized to base language).
     * @param mixed $value The translated value.
     * @return $this Returns the model instance for method chaining.
     */
    public function setTranslation(string $key, string $locale, $value): self
    {
        // Always strip locale to base language for consistency
        $baseLocale = substr($locale, 0, 2);

        return $this->parentSetTranslation($key, $baseLocale, $value);
    }
}


<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

trait HasTranslations
{
    /**
     * Get the translation for a given field.
     *
     * @param string $field The field to translate
     * @param string|null $locale The locale to use, null for current app locale
     * @return string|null
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        // Determine the locale to use - prefer explicit locale, fall back to session content_locale, then app locale, then user locale
        $locale = $locale ?: session('content_locale') ?: app()->getLocale() ?: $this->getUserLocale();
        
        
        // Get base language (e.g., 'en' from 'en_US')
        $baseLanguage = explode('_', $locale)[0];
        
        // Special handling for English variants - they all use the same translations
        if ($baseLanguage === 'en') {
            // First try to find a translation for any English variant
            $translation = $this->translations()
                ->where(function($query) {
                    $query->where('locale', 'en')
                          ->orWhere('locale', 'en_GB')
                          ->orWhere('locale', 'en_US');
                })
                ->first();
                
            // If we have a translation, return it
            if ($translation && !empty($translation->{$field})) {
                return $translation->{$field};
            }
        } else {
            // For non-English languages, first check the exact locale
            $translation = $this->translations()
                ->where('locale', $locale)
                ->first();
                
            // If we have a translation, return it
            if ($translation && !empty($translation->{$field})) {
                return $translation->{$field};
            }
            
            // Then check the base language
            $translation = $this->translations()
                ->where('locale', $baseLanguage)
                ->first();
                
            // If we have a translation, return it
            if ($translation && !empty($translation->{$field})) {
                return $translation->{$field};
            }
        }
        
        // If no translation in the requested locale, try the fallback locale
        $fallbackLocale = config('app.fallback_locale');
        $fallbackBaseLanguage = explode('_', $fallbackLocale)[0];
        
        if ($fallbackBaseLanguage === 'en') {
            // Try to find a translation for any English variant as fallback
            $fallbackTranslation = $this->translations()
                ->where(function($query) {
                    $query->where('locale', 'en')
                          ->orWhere('locale', 'en_GB')
                          ->orWhere('locale', 'en_US');
                })
                ->first();
        } else {
            $fallbackTranslation = $this->translations()
                ->where(function($query) use ($fallbackLocale, $fallbackBaseLanguage) {
                    $query->where('locale', $fallbackLocale)
                          ->orWhere('locale', $fallbackBaseLanguage);
                })
                ->first();
        }
                
        if ($fallbackTranslation && !empty($fallbackTranslation->{$field})) {
            return $fallbackTranslation->{$field};
        }
        
        // If no translation found, return the original field
        return $this->getRawOriginal($field);
    }
    
    /**
     * Get the user's locale or fallback to app locale
     *
     * @return string
     */
    protected function getUserLocale(): string
    {
        if (Auth::check() && Auth::user()->language) {
            // Get the language for translations (standardizes English variants to 'en')
            return Auth::user()->getLanguage(true);
        }
        
        // Standardize English variants in app locale
        $appLocale = App::getLocale();
        $baseLanguage = explode('_', $appLocale)[0];
        
        if ($baseLanguage === 'en') {
            return 'en';
        }
        
        return $appLocale;
    }
    
    /**
     * Determine if a translation exists for the given locale.
     *
     * @param string|null $locale
     * @return bool
     */
    public function hasTranslation(?string $locale = null): bool
    {
        $locale = $locale ?: $this->getUserLocale();
        $baseLanguage = explode('_', $locale)[0];
        
        // Special handling for English variants
        if ($baseLanguage === 'en') {
            return $this->translations()
                ->where(function($query) {
                    $query->where('locale', 'en')
                          ->orWhere('locale', 'en_GB')
                          ->orWhere('locale', 'en_US');
                })
                ->exists();
        }
        
        // For non-English languages, check both exact locale and base language
        return $this->translations()
            ->where(function($query) use ($locale, $baseLanguage) {
                $query->where('locale', $locale)
                      ->orWhere('locale', $baseLanguage);
            })
            ->exists();
    }
    
    /**
     * Create or update a translation.
     *
     * @param array $attributes
     * @param string|null $locale
     * @return mixed
     */
    public function setTranslation(array $attributes, ?string $locale = null)
    {
        $locale = $locale ?: $this->getUserLocale();
        $baseLanguage = explode('_', $locale)[0];
        
        // For English variants, standardize to just 'en' for storage
        if ($baseLanguage === 'en') {
            $attributes['locale'] = 'en';
            $locale = 'en';
        } else {
            $attributes['locale'] = $locale;
        }
        
        $translation = $this->translations()
            ->where('locale', $locale)
            ->first();
            
        if ($translation) {
            return $translation->update($attributes);
        }
        
        return $this->translations()->create($attributes);
    }
    
    /**
     * Handle update requests for translatable fields.
     * Used by controllers to intercept updates to translatable fields.
     *
     * @param array $data The data to update
     * @param array $translatableFields List of fields that should be stored in translations
     * @return array Non-translatable fields that should still be updated on the model
     */
    public function updateTranslationFields(array $data, array $translatableFields): array
    {
        $nonTranslatableData = $data;
        $translationData = [];
        
        // Extract translatable fields from the data
        foreach ($data as $field => $value) {
            if (in_array($field, $translatableFields)) {
                $translationData[$field] = $value;
                unset($nonTranslatableData[$field]);
            }
        }
        
        // If there are any translatable fields to update
        if (!empty($translationData)) {
            // Get user's preferred language
            $locale = $this->getUserLocale();
            
            // Update the translation
            $this->setTranslation($translationData, $locale);
        }
        
        return $nonTranslatableData;
    }
}
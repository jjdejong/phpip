<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

/**
 * Helper functions for date and number formatting with locale support
 */
class FormatHelper
{
    /**
     * Get the locale to use for date and number formatting
     *
     * @return string The locale to use (e.g., 'en_GB', 'en_US', 'fr')
     */
    public static function getFormattingLocale()
    {
        // First check for a locale in the session (set by middleware)
        $locale = Session::get('formatting_locale');
        
        // If not in session but user is logged in, get from user
        if (!$locale && Auth::check() && Auth::user()->language) {
            $locale = Auth::user()->language;
        }
        
        // Fall back to application locale if needed
        return $locale ?? config('app.locale');
    }
    
    /**
     * Format a date using the user's preferred locale
     *
     * @param mixed $date The date to format (string, Carbon, or DateTime)
     * @param string $format The format to use (default: 'L' = localized date format)
     * @return string Formatted date
     */
    public static function formatDate($date, $format = 'L')
    {
        if (!$date) {
            return '';
        }
        
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        $locale = self::getFormattingLocale();
        
        return $date->locale($locale)->isoFormat($format);
    }
    
    /**
     * Format a number using the user's preferred locale
     *
     * @param float $number The number to format
     * @param int $decimals Number of decimal places
     * @return string Formatted number
     */
    public static function formatNumber($number, $decimals = 2)
    {
        $locale = self::getFormattingLocale();
        
        // Get locale-specific separators
        $localeInfo = localeconv();
        $decimalPoint = $localeInfo['decimal_point'] ?? '.';
        $thousandsSep = $localeInfo['thousands_sep'] ?? ',';
        
        // Handle locale-specific formatting
        if (strpos($locale, 'en_US') === 0) {
            $decimalPoint = '.';
            $thousandsSep = ',';
        } elseif (strpos($locale, 'en_GB') === 0 || 
                 strpos($locale, 'fr') === 0 || 
                 strpos($locale, 'de') === 0 || 
                 strpos($locale, 'es') === 0) {
            $decimalPoint = ',';
            $thousandsSep = ' ';
        }
        
        return number_format($number, $decimals, $decimalPoint, $thousandsSep);
    }
}
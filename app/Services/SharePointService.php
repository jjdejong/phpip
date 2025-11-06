<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Service for interacting with Microsoft SharePoint.
 *
 * This service handles authentication and folder operations with SharePoint,
 * including access token management and folder URL lookups for case files.
 */
class SharePointService
{
    protected $accessToken;
    protected $baseUrl;
    protected $folderPath;
    protected $enabled;

    /**
     * Initialize the SharePoint service with configuration values.
     *
     * Loads SharePoint API URL, folder path, and enabled status from configuration.
     */
    public function __construct()
    {
        $this->baseUrl = config('services.sharepoint.api_url');
        $this->folderPath = config('services.sharepoint.folder_path');
        $this->enabled = config('services.sharepoint.enabled', false);
    }

    /**
     * Retrieve and cache an access token for SharePoint API authentication.
     *
     * Uses OAuth2 client credentials flow to obtain an access token. The token
     * is cached for its lifetime minus 30 seconds to prevent expiration issues.
     *
     * @return string The access token for SharePoint API requests.
     */
    protected function getAccessToken()
    {
        $token = Cache::get('sharepoint_access_token');
        if ($token) {
            return $token;
        }

        $response = Http::asForm()
            ->post(config('services.sharepoint.token_url'), [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.sharepoint.client_id'),
                'client_secret' => config('services.sharepoint.client_secret'),
                'resource' => config('services.sharepoint.resource')
            ]);

        $tokenData = $response->json();
        
        Cache::put(
            'sharepoint_access_token', 
            $tokenData['access_token'], 
            now()->addSeconds($tokenData['expires_in'] - 30)
        );

        return $tokenData['access_token'];
    }

    /**
     * Find the SharePoint web URL for a case folder.
     *
     * Searches for a folder matching the case reference in the configured SharePoint
     * folder path. Results are cached for one year, with optional cache refresh.
     *
     * @param string $caseref The case reference to search for.
     * @param bool $forceRefresh Whether to bypass and refresh the cache. Defaults to false.
     * @return string|null The web URL of the folder, or null if not found.
     */
    protected function findBaseFolderUrl($caseref, $forceRefresh = false)
    {
        $cacheKey = "sharepoint_base_url_{$caseref}";
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addYear(), function () use ($caseref, $forceRefresh) {
            $response = Http::withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/drive/root:" . $this->folderPath . ":/children", [
                    'select' => 'webUrl,name',
                    '$filter' => "startswith(name,'{$caseref}')",
                    '$top' => 1
                ]);

            if ($response->status() === 404 && !$forceRefresh) {
                // If we get 404 and haven't tried refresh yet, try one more time
                return $this->findBaseFolderUrl($caseref, true);
            }

            $items = $response->json()['value'];
            return !empty($items) ? $items[0]['webUrl'] : null;
        });
    }

    /**
     * Generate a SharePoint folder link for a specific case event.
     *
     * Constructs a full SharePoint URL by combining the base folder URL with
     * a suffix and event code. Returns null if SharePoint is disabled or folder not found.
     *
     * @param string $caseref The case reference to search for.
     * @param string $suffix The folder suffix to append to the base URL.
     * @param string $eventCode The event code to append as the final path segment.
     * @return string|null The complete SharePoint folder URL, or null if disabled or not found.
     */
    public function findFolderLink($caseref, $suffix, $eventCode)
    {
        if (!$this->enabled) {
            return null;
        }

        $baseFolderUrl = $this->findBaseFolderUrl($caseref);
        if (!$baseFolderUrl) {
            return null;
        }

        return $baseFolderUrl . '/' . str_replace('/', '', $suffix) . '/' . $eventCode;
    }

    /**
     * Check if SharePoint integration is enabled.
     *
     * @return bool True if SharePoint integration is enabled, false otherwise.
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
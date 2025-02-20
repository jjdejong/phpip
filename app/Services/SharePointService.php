<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SharePointService
{
    protected $accessToken;
    protected $baseUrl;
    protected $folderPath;
    protected $enabled;
    
    public function __construct()
    {
        $this->baseUrl = config('services.sharepoint.api_url');
        $this->folderPath = config('services.sharepoint.folder_path');
        $this->enabled = config('services.sharepoint.enabled', false);
    }

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

    public function findFolderLink($caseref, $suffix, $eventCode)
    {
        if (!$this->enabled) {
            return null;
        }

        $cacheKey = "sharepoint_link_{$caseref}_{$suffix}_{$eventCode}";
        
        return Cache::remember($cacheKey, 3600, function () use ($caseref, $suffix, $eventCode) {
            $response = Http::withToken($this->getAccessToken())
                ->get("{$this->baseUrl}/drive/root:" . $this->folderPath . ":/children", [
                    'select' => 'webUrl,name',
                    '$filter' => "startswith(name,'{$caseref}')",
                    '$top' => 1
                ]);

            $items = $response->json()['value'];
            return !empty($items) ? $items[0]['webUrl'] . '/' . 
                   str_replace('/', '', $suffix) . '/' . 
                   $eventCode : null;
        });
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}
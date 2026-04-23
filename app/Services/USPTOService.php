<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Service for retrieving US application details from USPTO ODP APIs.
 *
 * Endpoints are intentionally configurable because USPTO ODP products can vary
 * by account and dataset. This service normalizes whichever payload is returned
 * into phpIP's expected keys when possible.
 */
class USPTOService
{
    /**
     * Enrich family records with USPTO data for US applications.
     *
     * @param array $apps
     * @return array
     */
    public function enrichFamilyMembers(array $apps): array
    {
        foreach ($apps as $index => $app) {
            if (data_get($app, 'app.country') !== 'US') {
                continue;
            }

            $number = data_get($app, 'app.number');
            if (!$number) {
                continue;
            }

            $odData = $this->getApplicationData((string) $number);
            if (empty($odData)) {
                continue;
            }

            if (empty($apps[0]['title']) && !empty($odData['title'])) {
                $apps[0]['title'] = $odData['title'];
            }
            if (empty($apps[0]['applicants']) && !empty($odData['applicants'])) {
                $apps[0]['applicants'] = $odData['applicants'];
            }
            if (empty($apps[0]['inventors']) && !empty($odData['inventors'])) {
                $apps[0]['inventors'] = $odData['inventors'];
            }
            if (empty($apps[$index]['procedure']) && !empty($odData['procedure'])) {
                $apps[$index]['procedure'] = $odData['procedure'];
            }
        }

        return $apps;
    }

    /**
     * Fetch a single US application using configured USPTO ODP endpoints.
     *
     * @param string $applicationNumber
     * @return array
     */
    public function getApplicationData(string $applicationNumber): array
    {
        if (!config('services.uspto.enabled')) {
            return [];
        }

        $normalizedNumber = preg_replace('/\D/', '', $applicationNumber);
        if (!$normalizedNumber) {
            return [];
        }

        $apiKey = config('services.uspto.api_key');
        $headers = $apiKey ? ['X-Api-Key' => $apiKey] : [];

        // Preferred path: a direct endpoint template containing {applicationNumber}.
        $template = config('services.uspto.application_endpoint');
        if (!empty($template)) {
            $url = str_replace('{applicationNumber}', $normalizedNumber, $template);
            $response = Http::withHeaders($headers)->get($url);
            if ($response->successful()) {
                return $this->normalizeRecord($response->json());
            }
        }

        // Fallback path: generic search endpoint.
        $searchEndpoint = config('services.uspto.search_endpoint');
        if (empty($searchEndpoint)) {
            return [];
        }

        $queryField = config('services.uspto.search_field', 'applicationNumberText');
        $payload = [
            'q' => sprintf('%s:"%s"', $queryField, $normalizedNumber),
            'size' => 1,
        ];

        $response = Http::withHeaders($headers)->get($searchEndpoint, $payload);
        if (!$response->successful()) {
            return [];
        }

        return $this->normalizeRecord($response->json());
    }

    /**
     * Normalize a USPTO payload to phpIP expected fields.
     *
     * @param mixed $payload
     * @return array
     */
    private function normalizeRecord($payload): array
    {
        $record = Arr::first(data_get($payload, 'hits.hits', []), null, []);
        if (array_key_exists('_source', $record)) {
            $record = $record['_source'];
        } elseif (array_key_exists('record', $payload)) {
            $record = $payload['record'];
        } elseif (array_key_exists('results', $payload)) {
            $record = Arr::first($payload['results'], []);
        } elseif (!is_array($record) || empty($record)) {
            $record = is_array($payload) ? $payload : [];
        }

        $applicants = collect(
            data_get($record, 'applicants', data_get($record, 'applicantName', []))
        )
            ->map(function ($value) {
                if (is_string($value)) {
                    return $value;
                }

                return data_get($value, 'name', data_get($value, 'applicantName'));
            })
            ->filter()
            ->values()
            ->all();

        $inventors = collect(
            data_get($record, 'inventors', data_get($record, 'inventorName', []))
        )
            ->map(function ($value) {
                if (is_string($value)) {
                    return $value;
                }

                return data_get($value, 'name', data_get($value, 'inventorName'));
            })
            ->filter()
            ->values()
            ->all();

        return [
            'title' => data_get($record, 'inventionTitle', data_get($record, 'title')),
            'applicants' => $applicants,
            'inventors' => $inventors,
            'procedure' => data_get($record, 'events', data_get($record, 'legalEvents', [])),
        ];
    }
}


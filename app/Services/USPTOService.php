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

        // Preferred path: direct application endpoint (built-in default + optional override).
        $templates = array_filter(array_unique([
            config('services.uspto.application_endpoint'),
            '/api/v1/patent/applications/{applicationNumber}',
        ]));

        foreach ($templates as $template) {
            $url = $this->resolveEndpointUrl(
                str_replace('{applicationNumber}', $normalizedNumber, $template)
            );

            if (empty($url)) {
                continue;
            }

            $response = Http::withHeaders($headers)->acceptJson()->get($url);
            if ($response->successful()) {
                $normalized = $this->normalizeRecord($response->json());
                if (!empty(array_filter($normalized))) {
                    return $normalized;
                }
            }
        }

        // Fallback path: search endpoint (built-in default + optional override).
        $searchEndpoint = config('services.uspto.search_endpoint');
        $searchUrl = $this->resolveEndpointUrl(
            $searchEndpoint ?: '/api/v1/patent/applications/search'
        );
        if (empty($searchUrl)) {
            return [];
        }

        $queryField = config('services.uspto.search_field', 'applicationNumberText');
        $queryStringPayload = [
            'q' => sprintf('%s:"%s"', $queryField, $normalizedNumber),
            'size' => 1,
        ];

        $response = Http::withHeaders($headers)->acceptJson()->get($searchUrl, $queryStringPayload);
        if ($response->successful()) {
            $normalized = $this->normalizeRecord($response->json());
            if (!empty(array_filter($normalized))) {
                return $normalized;
            }
        }

        // Secondary fallback: POST JSON search payload.
        $jsonSearchPayload = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => [$queryField => $normalizedNumber]],
                    ],
                ],
            ],
            'size' => 1,
        ];

        $response = Http::withHeaders($headers)->acceptJson()->post($searchUrl, $jsonSearchPayload);
        if ($response->successful()) {
            return $this->normalizeRecord($response->json());
        }

        return [];
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
        } elseif (array_key_exists('items', $payload)) {
            $record = Arr::first($payload['items'], []);
        } elseif (array_key_exists('applications', $payload)) {
            $record = Arr::first($payload['applications'], []);
        } elseif (array_key_exists('data', $payload) && is_array($payload['data'])) {
            $data = $payload['data'];
            if (array_is_list($data)) {
                $record = Arr::first($data, []);
            } else {
                $record = $data;
            }
        } elseif (!is_array($record) || empty($record)) {
            $record = is_array($payload) ? $payload : [];
        }

        if (array_key_exists('applicationMetaData', $record) && is_array($record['applicationMetaData'])) {
            $record = array_merge($record['applicationMetaData'], $record);
        }

        $applicants = collect(
            data_get(
                $record,
                'applicants',
                data_get($record, 'applicantName', data_get($record, 'parties.applicants', []))
            )
        )
            ->map(function ($value) {
                if (is_string($value)) {
                    return $value;
                }

                return data_get(
                    $value,
                    'name',
                    data_get($value, 'applicantName', data_get($value, 'partyName'))
                );
            })
            ->filter()
            ->values()
            ->all();

        $inventors = collect(
            data_get(
                $record,
                'inventors',
                data_get($record, 'inventorName', data_get($record, 'parties.inventors', []))
            )
        )
            ->map(function ($value) {
                if (is_string($value)) {
                    return $value;
                }

                return data_get(
                    $value,
                    'name',
                    data_get($value, 'inventorName', data_get($value, 'partyName'))
                );
            })
            ->filter()
            ->values()
            ->all();

        return [
            'title' => data_get(
                $record,
                'inventionTitle',
                data_get($record, 'title', data_get($record, 'applicationTitleText'))
            ),
            'applicants' => $applicants,
            'inventors' => $inventors,
            'procedure' => data_get(
                $record,
                'events',
                data_get($record, 'legalEvents', data_get($record, 'transactions', []))
            ),
        ];
    }

    private function resolveEndpointUrl(?string $endpoint): ?string
    {
        if (empty($endpoint)) {
            return null;
        }

        if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
            return $endpoint;
        }

        $baseUrl = rtrim((string) config('services.uspto.base_url', 'https://api.uspto.gov'), '/');
        $path = '/' . ltrim($endpoint, '/');

        return $baseUrl . $path;
    }
}

<?php

namespace Tests\Unit;

use App\Services\USPTOService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class USPTOServiceTest extends TestCase
{
    public function testGetApplicationDataReturnsEmptyArrayWhenUsptoRequestTimesOut(): void
    {
        config([
            'services.uspto.enabled' => true,
            'services.uspto.api_key' => null,
            'services.uspto.application_endpoint' => null,
            'services.uspto.base_url' => 'https://api.uspto.test',
        ]);

        Http::fake(function () {
            throw new ConnectionException('Connection timed out.');
        });

        $this->assertSame([], app(USPTOService::class)->getApplicationData('17/123456'));
    }

    public function testGetApplicationDataContinuesToFallbackEndpointsAfterTimeout(): void
    {
        config([
            'services.uspto.enabled' => true,
            'services.uspto.api_key' => null,
            'services.uspto.application_endpoint' => '/api/v1/custom/{applicationNumber}',
            'services.uspto.base_url' => 'https://api.uspto.test',
        ]);

        Http::fake([
            'https://api.uspto.test/api/v1/custom/17123456' => function () {
                throw new ConnectionException('Connection timed out.');
            },
            'https://api.uspto.test/api/v1/patent/applications/17123456' => Http::response([
                'applicationMetaData' => [
                    'inventionTitle' => 'Fallback title',
                    'applicantName' => ['Fallback applicant'],
                    'inventorName' => ['Fallback inventor'],
                ],
            ]),
        ]);

        $this->assertSame([
            'title' => 'Fallback title',
            'applicants' => ['Fallback applicant'],
            'inventors' => ['Fallback inventor'],
            'procedure' => [],
        ], app(USPTOService::class)->getApplicationData('17/123456'));
    }
}

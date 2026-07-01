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
}

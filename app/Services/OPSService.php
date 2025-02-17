<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class OPSService
{
    private string $accessToken;
    private const BASE_URL = 'https://ops.epo.org/3.2';

    public function __construct()
    {
        $this->authenticate();
    }

    private function authenticate(): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode(env('OPS_APP_KEY') . ':' . env('OPS_SECRET'))
        ])->asForm()->post(self::BASE_URL . '/auth/accesstoken', [
            'grant_type' => 'client_credentials'
        ]);

        $this->accessToken = $response['access_token'];
    }

    public function getFamilyMembers(string $docnum): array
    {
        $ops_biblio = self::BASE_URL . "/rest-services/family/publication/docdb/$docnum/biblio.json";
        $ops_response = Http::withToken($this->accessToken)
            ->asForm()
            ->get($ops_biblio);

        if ($ops_response->clientError()) {
            return ['errors' => ['docnum' => ['Number not found']], 'message' => 'Number not found in OPS Family'];
        }
        if ($ops_response->serverError()) {
            return ['exception' => 'OPS server error', 'message' => 'OPS server error, try again'];
        }

        $members = data_get($ops_response, 'ops:world-patent-data.ops:patent-family.ops:family-member');
        
        if (Arr::isList($members)) {
            // Sort members by increasing filing date and doc-id, so that the first is the priority application
            $members = collect($members)->sortBy(
                fn($member) => $member['application-reference']['document-id']['date']['$'] 
                    . $member['application-reference']['@doc-id']
            );
            // Group all members by doc-id, so that publications and grants appear in a same record
            $members = collect($members)->groupBy(
                fn($member) => $member['application-reference']['@doc-id']
            );
        } else {
            // Turn single element into a list of one element
            $members = [$members['application-reference']['@doc-id'] => [0 => $members]];
        }

        $apps = [];
        $i = 0;

        foreach ($members as $key => $member) {
            // [0] is the item referring to the publication and [1] is the item referring to the grant
            $app = $member[0]['application-reference']['document-id'];
            // Don't want filings of EP translations
            if ($app['kind']['$'] == 'T') {
                continue;
            }
            // $key is the @doc-id
            $apps[$i]['id'] = $key;

            if (Arr::isList($member[0]['priority-claim'])) {
                $pri = collect($member[0]['priority-claim'])
                    ->where('priority-active-indicator.$', 'YES')
                    ->toArray();
            } else {
                // Turn single element into a list of one element
                $pri = [0 => $member[0]['priority-claim']];
            }

            foreach ($pri as $k => $p) {
                $apps[$i]['pri'][$k]['country'] = $p['document-id']['country']['$'];
                $apps[$i]['pri'][$k]['number'] = $p['document-id']['doc-number']['$'];
                $apps[$i]['pri'][$k]['kind'] = $p['document-id']['kind']['$'];
                $apps[$i]['pri'][$k]['date'] = date('Y-m-d', strtotime($p['document-id']['date']['$']));
            }

            $apps[$i]['app']['date'] = date('Y-m-d', strtotime($app['date']['$']));
            $apps[$i]['app']['kind'] = $app['kind']['$'];
            
            if ($app['kind']['$'] == 'W') {
                $country = 'WO';
                $app_number = $app['country']['$'] . $app['doc-number']['$'];
            } else {
                $country = $app['country']['$'];
                $app_number = $app['doc-number']['$'];
            }
            
            if ($country == 'US') {
                if (strlen($app_number) == 8) {
                    // Get only the first six digits, removing YY from the end
                    $app_number = substr($app_number, 0, 6);
                } else {
                    // Remove the YYYY prefix
                    $app_number = substr($app_number, 4);
                }
            }
            
            $apps[$i]['app']['country'] = $country;
            $apps[$i]['app']['number'] = $app_number;

            // Data taken from EP or PCT case
            if ((in_array($apps[$i]['app']['country'], ['EP', 'WO'])) && !data_get($apps, '0.pri.title')) {
                // Title (the last is the English title)
                $apps[0]['title'] = collect($member[0]['exchange-document']['bibliographic-data']['invention-title'])
                    ->last()['$'];

                // Each inventor is under [i]['inventor-name']['name']['$'] both in "epodoc" and "original" format
                $inventors = collect($member[0]['exchange-document']['bibliographic-data']['parties']['inventors']['inventor'])
                    ->where('@data-format', 'original');
                $apps[0]['inventors'] = $inventors->values()->pluck('inventor-name.name.$');

                // Each applicant is under [i]['applicant-name']['name']['$']
                $applicants = collect($member[0]['exchange-document']['bibliographic-data']['parties']['applicants']['applicant'])
                    ->where('@data-format', 'original');
                $apps[0]['applicants'] = $applicants->values()->pluck('applicant-name.name.$');

                $procedureSteps = $this->getProceduralSteps($app_number);
                if (!empty($procedureSteps)) {
                    $apps[$i]['procedure'] = $procedureSteps;
                }
            }

            if (in_array($apps[$i]['app']['country'], ['FR', 'US'])) {
                $legalStatus = $this->getLegalStatus($apps[$i]['app']['country'], $app_number);
                if (!empty($legalStatus)) {
                    $apps[$i]['procedure'] = $legalStatus;
                }
            }

            // Process publication references
            foreach ($member as $event) {
                // Take DOCDB format
                $pub = collect($event['publication-reference']['document-id'])
                    ->where('@document-id-type', 'docdb')
                    ->first();

                switch ($pub['kind']['$']) {
                    case 'A':
                    case 'A1':
                    case 'A2':
                        $apps[$i]['pub']['country'] = $pub['country']['$'];
                        $apps[$i]['pub']['number'] = $pub['doc-number']['$'];
                        $apps[$i]['pub']['date'] = date('Y-m-d', strtotime($pub['date']['$']));
                        break;
                    case 'B':
                    case 'B1':
                    case 'B2':
                        $apps[$i]['grt']['country'] = $pub['country']['$'];
                        $apps[$i]['grt']['number'] = $pub['doc-number']['$'];
                        $apps[$i]['grt']['date'] = date('Y-m-d', strtotime($pub['date']['$']));
                        break;
                }
            }

            // PCT origin
            if ($pct_nat = collect($member[0]['priority-claim'])->where('priority-linkage-type.$', 'W')->first()) {
                $apps[$i]['pct'] = $pct_nat['document-id']['country']['$'] . $pct_nat['document-id']['doc-number']['$'];
            } else {
                $apps[$i]['pct'] = null;
            }

            // Possible divisional
            if ($div = collect($member[0]['priority-claim'])->where('priority-linkage-type.$', '3')->first()) {
                $app_number = $div['document-id']['doc-number']['$'];
                if ($div['document-id']['country']['$'] == 'US') {
                    if (strlen($app_number) == 8) {
                        $app_number = substr($app_number, 0, 6);
                    } else {
                        $app_number = substr($app_number, 4);
                    }
                }
                $apps[$i]['div'] = $app_number;
            } else {
                $apps[$i]['div'] = null;
            }

            // Possible continuation
            if ($cnt = collect($member[0]['priority-claim'])
                ->whereIn('priority-linkage-type.$', ['1', '2', 'C'])
                ->first()
            ) {
                $app_number = $cnt['document-id']['doc-number']['$'];
                if ($cnt['document-id']['country']['$'] == 'US') {
                    if (strlen($app_number) == 8) {
                        $app_number = substr($app_number, 0, 6);
                    } else {
                        $app_number = substr($app_number, 4);
                    }
                }
                $apps[$i]['cnt'] = $app_number;
            } else {
                $apps[$i]['cnt'] = null;
            }

            $i++;
        }

        return $apps;
    }

    private function getProceduralSteps(string $appNumber): array
    {
        $ops_procedure = self::BASE_URL . "/rest-services/register/application/epodoc/EP$appNumber/procedural-steps";
        $response = Http::withToken($this->accessToken)
            ->asForm()
            ->get($ops_procedure);

        if (!$response->successful()) {
            return [];
        }

        $xml = new SimpleXMLElement($response);
        $steps = $xml->xpath('//reg:procedural-step');
        $proc = [];

        foreach ($steps as $k => $step) {
            $proc[$k]['code'] = (string)$step->xpath('reg:procedural-step-code')[0];
            
            if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_REQUEST"]/reg:date')) {
                $proc[$k]['request'] = date('Y-m-d', strtotime($date[0]));
            }
            if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_DISPATCH"]/reg:date')) {
                $proc[$k]['dispatched'] = date('Y-m-d', strtotime($date[0]));
            }
            if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_REPLY"]/reg:date')) {
                $proc[$k]['replied'] = date('Y-m-d', strtotime($date[0]));
            }
            if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="DATE_OF_PAYMENT"]/reg:date')) {
                $proc[$k]['ren_paid'] = date('Y-m-d', strtotime($date[0]));
            }
            if ($date = $step->xpath('reg:procedural-step-date[@step-date-type="GRANT_FEE_PAID"]/reg:date')) {
                $proc[$k]['grt_paid'] = date('Y-m-d', strtotime($date[0]));
            }
            if ($year = $step->xpath('reg:procedural-step-text[@step-text-type="YEAR"]')) {
                $proc[$k]['ren_year'] = (int)$year[0];
            }
        }

        return $proc;
    }

    private function getLegalStatus(string $country, string $appNumber): array
    {
        $ops_procedure = self::BASE_URL . "/rest-services/legal/application/docdb/{$country}$appNumber";
        $response = Http::withToken($this->accessToken)
            ->asForm()
            ->get($ops_procedure);

        if (!$response->successful()) {
            return [];
        }

        $xml = new SimpleXMLElement($response);
        // Get renewals. Code RFEE for FR and MAFP for US
        $steps = $xml->xpath('//ops:legal[@code="PLFP"] | //ops:legal[@code="MAFP"]');
        $proc = [];

        foreach ($steps as $k => $step) {
            // Code compatible with EP procedural steps
            $proc[$k]['code'] = 'RFEE';
            if ($date = $step->xpath('ops:L007EP')) {
                $proc[$k]['ren_paid'] = date('Y-m-d', strtotime($date[0]));
            }
            if ($year = $step->xpath('ops:L500EP/ops:L520EP')) {
                $proc[$k]['ren_year'] = (int)$year[0];
            }
        }

        return $proc;
    }
}

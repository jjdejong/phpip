<?php

namespace App\Services;

/**
 * Family data orchestrator with dynamic provider selection.
 *
 * Default behavior uses OPS for full family retrieval, then enriches US members
 * from USPTO ODP when enabled/configured.
 */
class FamilyDataService
{
    public function __construct(
        private OPSService $opsService,
        private USPTOService $usptoService
    ) {
    }

    /**
     * Get family members with OPS primary source and USPTO fallback/enrichment.
     *
     * @param string $docnum
     * @return array
     */
    public function getFamilyMembers(string $docnum): array
    {
        $apps = $this->opsService->getFamilyMembers($docnum);
        if (array_key_exists('errors', $apps) || array_key_exists('exception', $apps)) {
            // If the requested number looks US, return a synthetic single-member family
            // from USPTO ODP when possible.
            if ($this->isUSDocument($docnum)) {
                $member = $this->buildUSMemberFromODP($docnum);
                if (!empty($member)) {
                    return [$member];
                }
            }

            return $apps;
        }

        return $this->usptoService->enrichFamilyMembers($apps);
    }

    private function isUSDocument(string $docnum): bool
    {
        return str_starts_with(strtoupper(trim($docnum)), 'US');
    }

    private function buildUSMemberFromODP(string $docnum): array
    {
        $number = preg_replace('/\D/', '', $docnum);
        if (!$number) {
            return [];
        }

        $odData = $this->usptoService->getApplicationData($number);
        if (empty($odData)) {
            return [];
        }

        return [
            'id' => 'US' . $number,
            'app' => [
                'country' => 'US',
                'number' => ltrim($number, '0'),
                'kind' => 'A',
                'date' => null,
            ],
            'pri' => [],
            'pct' => null,
            'div' => null,
            'cnt' => null,
            'title' => $odData['title'] ?? null,
            'applicants' => $odData['applicants'] ?? [],
            'inventors' => $odData['inventors'] ?? [],
            'procedure' => $odData['procedure'] ?? [],
        ];
    }
}


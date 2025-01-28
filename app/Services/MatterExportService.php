<?php

namespace App\Services;

use Illuminate\Support\Collection;

class MatterExportService
{
    public function export(array $matters): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $captions = [
            'Our Ref',
            'Alt Ref',
            'Country',
            'Cat',
            'Origin',
            'Status',
            'Status date',
            'Client',
            'Client Ref',
            'Applicant',
            'Agent',
            'Agent Ref',
            'Title',
            'Title2',
            'Title3',
            'Inventor 1',
            'Filed',
            'FilNo',
            'Published',
            'Pub. No',
            'Granted',
            'Grt No',
            'ID',
            'container_ID',
            'parent_ID',
            'Type',
            'Responsible',
            'Delegate',
            'Dead',
            'Ctnr',
        ];

        $export_csv = fopen('php://memory', 'w');
        fputcsv($export_csv, $captions, ';');
        foreach ($matters as $row) {
            fputcsv($export_csv, array_map('utf8_decode', $row), ';');
        }
        rewind($export_csv);
        $filename = Now()->isoFormat('YMMDDHHmmss') . '_matters.csv';

        return response()->stream(
            function () use ($export_csv) {
                fpassthru($export_csv);
            },
            200,
            ['Content-Type' => 'application/csv', 'Content-Disposition' => 'attachment; filename=' . $filename]
        );
    }
}
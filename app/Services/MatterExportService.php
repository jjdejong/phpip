<?php

namespace App\Services;

use Illuminate\Support\Collection;

class MatterExportService
{
    /**
     * Export the matters to a CSV file.
     *
     * This method exports the provided matters array to a CSV file and returns
     * a streamed response for downloading the file.
     *
     * @param array $matters The array of matters to be exported.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse The streamed response for the CSV file download.
     */
    public function export(array $matters): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Define the column captions for the CSV file.
        $captions = [
            'Our Ref',
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
            'Alt Ref',
        ];

        // Open a memory stream for the CSV file.
        $export_csv = fopen('php://memory', 'w');

        // Write the column captions to the CSV file.
        fputcsv($export_csv, $captions, ';');

        // Write each row of matters to the CSV file.
        foreach ($matters as $row) {
            fputcsv($export_csv, array_map('utf8_decode', $row), ';');
        }

        // Rewind the memory stream to the beginning.
        rewind($export_csv);

        // Generate the filename for the CSV file.
        $filename = Now()->isoFormat('YMMDDHHmmss') . '_matters.csv';

        // Return a streamed response for downloading the CSV file.
        return response()->stream(
            function () use ($export_csv) {
                fpassthru($export_csv);
            },
            200,
            ['Content-Type' => 'application/csv', 'Content-Disposition' => 'attachment; filename=' . $filename]
        );
    }
}